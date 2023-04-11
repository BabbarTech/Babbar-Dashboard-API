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

namespace App\Http\Controllers\Front;

use App\Enums\StatusEnum;
use App\Events\BenchmarkProcessingCanceled;
use App\Http\Controllers\Controller;
use App\Http\Resources\BenchmarkStepBatchErrorResource;
use App\Models\Benchmark;
use App\Models\BenchmarkStep;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectBenchmarkController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        return view('resources.benchmark.index', [
            'project' => $project,
            'benchmarks' => Benchmark::latest()->paginate(),
        ]);
    }

    public function show(Request $request, Project $project, Benchmark $benchmark): View
    {
        $benchmark->load([
            'steps',
        ]);

        return view('resources.benchmark.show', [
            'project' => $project,
            'benchmark' => $benchmark,
        ]);
    }

    public function cancel(Request $request, Project $project, Benchmark $benchmark): mixed
    {
        event(new BenchmarkProcessingCanceled($benchmark));

        return redirect()
            ->route('projects.benchmarks.show', [$project, $benchmark])
            ->withSuccess(__('Benchmark cancelled'));
    }

    public function batchErrors(
        Request $request,
        Project $project,
        Benchmark $benchmark,
        BenchmarkStep $benchmarkStep
    ): JsonResource {

        $collection = $benchmarkStep->batchErrors()->get();
        return BenchmarkStepBatchErrorResource::collection($collection)
            ->additional([
                'benchmarkStep' => $benchmarkStep,
            ]);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param string $benchmarkServiceName
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function fire(Request $request, Project $project, string $benchmarkServiceName)
    {
        $class = 'App\Services\BenchmarkService\Benchmarks\\' . $benchmarkServiceName;
        if (! class_exists($class)) {
            throw new \Exception($benchmarkServiceName . ' do not exists');
        }

        $benchmark = $project->host->benchmarks()->create([
            'type' => 'App\Services\BenchmarkService\Benchmarks\\' . $benchmarkServiceName,
        ]);

        $benchmark->process();

        return redirect()->route('projects.benchmarks.show', [$project, $benchmark]);
    }
}
