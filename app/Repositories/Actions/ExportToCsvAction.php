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

namespace App\Repositories\Actions;

use App\Models\Project;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ExportToCsvAction extends AbstractAction
{
    protected string $csvSeparator;

    protected ?string $transformerClassname;

    /** @var ?resource $file */
    protected $file;

    protected int $delta = 0;

    public function handle(Builder $builder): mixed
    {
        $this->csvSeparator = strval(config('export.csv.separator'));

        // create csv
        return response()->streamDownload(function () use ($builder) {
            return $builder->cursor()
                ->each(function ($data) {
                    $transformedData = $this->transformData($data);

                    //dd($transformedData);
                    if (! $transformedData) {
                        return ;
                    }

                    $this->exportLine($transformedData);
                });

            fclose($this->getFile()); // @phpstan-ignore-line
        }, $this->getFilename());
    }

    protected function exportLine(array $transformedData): void
    {
        // Push columns header
        if ($this->delta === 0) {
            $columns = array_keys($transformedData);
            fputcsv($this->getFile(), $columns, $this->csvSeparator);
        }

        fputcsv($this->getFile(), $transformedData, $this->csvSeparator);

        $this->delta++;
    }

    /**
     * @return resource
     * @throws \Exception
     */
    protected function getFile()
    {
        if (! isset($this->file)) {
            $resource = fopen('php://output', 'w+');

            if ($resource === false) {
                throw new \Exception('Can not open stream');
            }

            $this->file = $resource;
        }

        return $this->file;
    }


    protected function transformData(mixed $data): ?array
    {
        $transformerClassname = $this->getTransformerClassname();

        if ($transformerClassname === null) {
            return (array) $data;
        }

        $transformer = new $transformerClassname($data);

        if ($transformer instanceof JsonResource) {
            return $transformer->resolve();
        }

        return null;
    }

    protected function getTransformerClassname(): ?string
    {
        if (! isset($this->transformerClassname)) {
            /** @var ?string $transformerClassname */
            $transformerClassname = Arr::get($this->actionPayload ?? [], 'params.transformer');
            $this->transformerClassname = $transformerClassname;
        }

        return $this->transformerClassname;
    }

    protected function getFilename(): string
    {
        /** @var Project $project */
        $project = request()->route('project');

        $routeName = Route::currentRouteName();
        $uri = request()->getUri();

        if ($routeName) {
            $name = Str::afterLast($routeName, '.');
        } elseif ($uri) {
            $name = Str::afterLast(request()->getUri(), '/');
        } else {
            $name = 'export';
        }

        $date = date('Y-m-d');

        return implode('__', [
                $project->hostname,
                $name,
                $date
            ]) . '.csv';
    }
}
