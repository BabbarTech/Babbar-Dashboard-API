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

namespace App\View\Components;

use App\Enums\StatusEnum;
use App\Models\Benchmark;
use App\Models\Project;
use Illuminate\View\Component;

class BenchmarkCard extends Component
{
    public string $benchmarkServiceClassname;

    public string $action;

    public string $btnLabel;

    public ?Benchmark $lastBenchmarkInstance;

    public Project $project;

    public bool $btnDisabled = false;

    public bool $hasProcessedData = false;

    public bool $isDone = false;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $benchmarkServiceClassname, string $btnLabel = null)
    {
        $this->benchmarkServiceClassname = $benchmarkServiceClassname;
        $this->project = app('currentProject');
        $this->action = route('projects.benchmarks.fire', [
            $this->project,
            'benchmarkServiceName' => $benchmarkServiceClassname
        ]);

        $this->lastBenchmarkInstance = $this->getCurrentLatestBenchmark();


        if (
            in_array($this->lastBenchmarkInstance?->status, [
            StatusEnum::DONE,
            StatusEnum::PROCESSING,
            StatusEnum::PENDING,
            ])
        ) {
            $this->btnDisabled = true;
        }

        if ($this->lastBenchmarkInstance && $this->lastBenchmarkInstance->status !== StatusEnum::PENDING) {
            $this->hasProcessedData = true;
        }

        if ($this->lastBenchmarkInstance && $this->lastBenchmarkInstance->status === StatusEnum::DONE) {
            $this->isDone = true;
        }

        $this->btnLabel = $btnLabel ?? $this->getBtnLabel();
    }

    protected function getBtnLabel(): string
    {
        $status = $this->lastBenchmarkInstance?->status;

        if ($status && $status === \App\Enums\StatusEnum::PROCESSING) {
            return ___('benchmark.processing');
        }

        if ($status && $status === \App\Enums\StatusEnum::DONE) {
            return ___('benchmark.processed');
        }

        return ___('benchmark.process');
    }


    protected function getCurrentLatestBenchmark(): ?Benchmark
    {
        return $this->project->host->benchmarks()
            ->where('type', 'like', '%' . $this->benchmarkServiceClassname)
            ->latest()
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.benchmark-card');
    }
}
