<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\Jobs\BenchmarkSteps;

use App\Events\BenchmarkStepBatchProcessingFailed;
use App\Events\BenchmarkStepBatchProcessingStart;
use App\Exceptions\GatewayApiFetchUnauthorized;
use App\Exceptions\GatewayApiNotFound;
use App\Exceptions\GatewayApiInternalServerError;
use App\Exceptions\GatewayApiTooManyRequestsException;
use App\Jobs\Contracts\GatewayFetchJob;
use App\Jobs\Middleware\PreventApiTooManyRequests;
use App\Models\BenchmarkStep;
use App\Models\BenchmarkStepBatch;
use App\Models\Gateway;
use App\Models\Project;
use App\Services\Api\Contracts\GatewayApiClient;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Jobs\TenantAware;
use Throwable;

abstract class AbstractGatewayFetchJob implements ShouldQueue, GatewayFetchJob, TenantAware
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Gateway $gatewayApiAccount;

    protected BenchmarkStep $benchmarkStep;

    protected BenchmarkStepBatch $benchmarkStepBatch;

    protected GatewayApiClient $client;

    protected Response $response;

    protected int $benchmarkStepId;

    protected array $requestBody;

    protected string $uri;

    protected string $method = GatewayFetchJob::METHOD_POST;

    protected int $offset = 0;

    protected int $nbItemsRequested = 500;

    protected int $nbItemsReturned;

    /** @var int|null */
    protected ?int $limit;

    protected string $gatewayName;

    protected string $gatewayClientClass;

    protected bool $throwApiException = true;

    protected bool $allowFailures = false;

    protected int $maxApiExceptionsAllowed = 5;

    public function middleware(): array
    {
        return [
            new PreventApiTooManyRequests($this->getApiTooManyRequestsCacheKey()),
            new RateLimited(GatewayApiClient::class),
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */

    public function backoff(): array
    {
        //return [1, 2, 3]; // for testing
        return [10, 30, 60, 180];
    }

    public function retryUntil(): Carbon
    {
        //return now()->addMinute(); // for testing
        return now()->addDay();
    }


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $benchmarkStepId, array $requestBody)
    {
        $this->benchmarkStepId = $benchmarkStepId;
        $this->requestBody = $requestBody;
    }

    abstract protected function process(array $data): void;

    protected function getDefaultRequestBody(): array
    {
        return [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Skip job if batch was cancelled
        if ($this->isCancelled()) {
            return;
        }

        $this->start();

        try {
            $data = $this->fetchApi();
            $this->process($data);
        } catch (GatewayApiFetchUnauthorized $e) {
            $this->job->fail($e); /** @phpstan-ignore-line */
            $this->processError($e);
        } catch (GatewayApiTooManyRequestsException $e) {
            $this->release($e->getSecondsRemaining());
            return;
        } catch (GatewayApiNotFound $e) {
            $this->processError($e, false);
        } catch (GatewayApiInternalServerError $e) {
            $this->processError($e, $this->throwingExceptionUntil());
        } catch (\Exception $e) {
            $this->processError($e);
        }

        $this->finish();
    }

    protected function isCancelled(): bool
    {
        if ($this->batch()?->cancelled()) {
            $this->batch()->delete();
            return true;
        }

        if ($this->getBenchmarkStep()->isCancelled()) {
            return true;
        }

        if ($this->getBenchmarkStepBatch()->isCancelled()) {
            return true;
        }

        return false;
    }

    protected function start(): void
    {
        $this->getBenchmarkStepBatch()->increment('attempts');

        $batch = $this->batch();

        if ($batch && $batch->processedJobs() === 0) {
            event(new BenchmarkStepBatchProcessingStart($batch));
        }

        /*
        if ($this->getBenchmarkStep()->status === StatusEnum::ERROR) {
            $this->getBenchmarkStep()->retryProcessing();
            $this->getBenchmarkStep()->benchmark->retryProcessing();
        }
        */
    }

    protected function finish(): void
    {
        $this->getBenchmarkStep()->increment('finished_jobs');

        $this->getBenchmarkStepBatch()->increment('batch_finished_jobs');

        if ($this->checkIfApiHasMoreResults()) {
            $this->addAnotherJobToBatch();
        }
    }

    /**
     * @param \Exception $exception
     * @param bool $throwingException
     * @throws \Exception
     */
    protected function processError(\Exception $exception, bool $throwingException = true): void
    {
        $this->getBenchmarkStepBatch()->errors()->create([
            'error' => Str::limit($exception->getMessage(), 15000),
            'payload' => $this->getRequestBody(),
        ]);

        if ($throwingException) {
            throw $exception;
        }
    }

    protected function throwingExceptionUntil(): bool
    {
        if ($this->allowFailures) {
            return $this->job->attempts() <= $this->maxApiExceptionsAllowed; /** @phpstan-ignore-line */
        }

        return true;
    }

    protected function getUri(): string
    {
        return $this->uri;
    }

    protected function fetchApi(): array
    {
        $response = $this->getClient()
            ->setApiToken($this->getGatewayApiAccount()->api_token)
            ->fetch($this->getUri(), $this->getRequestBody(), $this->method);

        $this->handleApiErrors($response);

        /** @var array $data */
        $data = $response->json();

        $this->response = $response;

        return $data;
    }

    public function getClient(): GatewayApiClient
    {
        if (! isset($this->client)) {
            $this->client = resolve($this->gatewayClientClass);
        }

        return $this->client;
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    protected function handleApiErrors(Response $response): void
    {
        $this->updateGatewayApiAccountRateLimits($response);

        if ($response->failed() && $response->status() == 429) {
            $secondsRemaining = (int) $response->header('retry-after');
            Cache::put(
                $this->getApiTooManyRequestsCacheKey(),
                now()->addSeconds($secondsRemaining)->timestamp,
                $secondsRemaining
            );

            throw GatewayApiTooManyRequestsException::until(
                $secondsRemaining,
                class_basename($this->gatewayClientClass)
            );
        }

        if ($response->failed() && $response->status() == 401) {
            $errorMessage = data_get($response->json(), 'error');
            if (! is_string($errorMessage)) {
                $errorMessage = 'Unauthenticated';
            }

            throw new GatewayApiFetchUnauthorized($errorMessage);
        }

        if ($response->failed() && $response->status() == 404) {
            throw new GatewayApiNotFound('Result not found');
        }

        if ($response->failed() && $response->status() == 500) {
            throw new GatewayApiInternalServerError('API Internal Server error');
        }

        if ($response->failed()) {
            Log::error($this->getRequestBody(), [
                'projet_id' => $this->getProject()->getKey(),
                'step' => $this->getBenchmarkStep()->getKey(),
                'client' => class_basename($this->getClient()),
                'uri' => $this->getUri(),
            ]);
        }

        $response->throwIf($this->throwApiException);
    }

    protected function getApiTooManyRequestsCacheKey(): string
    {
        return 'ApiTooManyRequestsForAccount:' . $this->getGatewayApiAccount()->getKey();
    }


    protected function updateGatewayApiAccountRateLimits(Response $response): void
    {
        $gatewayApiAccount = $this->getGatewayApiAccount();

        $limit = (int) $response->header('x-ratelimit-limit');

        if (!empty($limit)) {
            $gatewayApiAccount->x_ratelimit_limit = $limit;
        }

        $remaining = (int) $response->header('x-ratelimit-remaining');
        if (! empty($remaining)) {
            if ($remaining < 0) {
                $remaining = 0;
            }
            $gatewayApiAccount->x_ratelimit_remaining = $remaining;
        }

        $gatewayApiAccount->save();
    }

    public function getRateLimit(): Limit
    {
        if ($this->isCancelled()) {
            return Limit::none();
        }

        /** @var Gateway $gatewayApiAccount */
        $gatewayApiAccount = $this->getGatewayApiAccount();
        //var_dump('REMAINING RATE LIMIT PER MINUTE : ' . $gatewayApiAccount->getRemainingRateLimitPerMinute() );

        return Limit::perMinute($gatewayApiAccount->getRemainingRateLimitPerMinute())
            ->by('gatewayApiAccount:' . $gatewayApiAccount->id);
    }

    protected function checkIfApiHasMoreResults(): bool
    {
        if (!isset($this->nbItemsReturned)) {
            return false;
        }

        $requestBody = $this->getRequestBody();

        // Checks if the response has fewer entries requested
        if ($this->nbItemsReturned < $requestBody['n']) {
            return false;
        }


        $totalItemsFetched = $this->nbItemsReturned + $requestBody['offset'] * $requestBody['n'];

        if ($this->hasReachLimit($totalItemsFetched)) {
            return false;
        }

        return true;
    }

    protected function setNbItemsReturned(int $nbItemsReturned): GatewayFetchJob
    {
        $this->nbItemsReturned = $nbItemsReturned;

        return $this;
    }

    protected function hasReachLimit(int $totalItemsFetched): bool
    {
        if (! isset($this->limit)) {
            return false;
        }

        if ($totalItemsFetched < $this->limit) {
            return false;
        }

        return true;
    }


    protected function addAnotherJobToBatch(): void
    {
        // Add new job into batch
        $newBatchJob = $this->batch()?->add(
            $this->createNextBatchJob()
        );

        $this->getBenchmarkStep()->increment('total_jobs');

        $this->getBenchmarkStepBatch()->increment('batch_total_jobs');
    }

    protected function createNextBatchJob(): GatewayFetchJob
    {
        $nextRequestBody = $this->getRequestBody();
        $nextRequestBody['offset']++;

        return new static($this->benchmarkStepId, $nextRequestBody);
    }

    public function getBenchmarkStep(): BenchmarkStep
    {
        if (!isset($this->benchmarkStep)) {
            $this->benchmarkStep = BenchmarkStep::findOrFail($this->benchmarkStepId);
        }

        return $this->benchmarkStep;
    }

    public function getBenchmarkStepBatch(): BenchmarkStepBatch
    {
        if (!isset($this->benchmarkStepBatch)) {
            /** @var BenchmarkStepBatch $benchmarkStepBatch */
            $benchmarkStepBatch = $this->getBenchmarkStep()
                ->batches()
                ->where('batch_id', $this->batchId)
                ->firstOrFail();

            $this->benchmarkStepBatch = $benchmarkStepBatch;
        }

        return $this->benchmarkStepBatch;
    }


    public function getGatewayApiAccount(): Gateway
    {
        if (! isset($this->gatewayApiAccount)) {
            $this->gatewayApiAccount = $this->getBenchmarkStep()
                ->getGatewayApiAccount($this->getGatewayName());
        }

        return $this->gatewayApiAccount;
    }


    protected function getProject(): Project
    {
        return $this->getBenchmarkStep()->getProject();
    }

    protected function getRequestBody(): array
    {
        return array_merge($this->getDefaultRequestBody(), $this->requestBody);
    }

    public function failed(Throwable $exception): void
    {
        $batch = $this->batch();

        if (! $batch) {
            $batch = $this->getBenchmarkStepBatch()->getQueueBatch();
        }

        if ($batch) {
            event(new BenchmarkStepBatchProcessingFailed($batch, $exception));
        }
    }
}
