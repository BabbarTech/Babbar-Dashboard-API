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

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\View\Component;
use Illuminate\Support\Collection;

class FetchCompetitorKeywords extends Component
{
    public Project $project;

    public int $rankMax;

    public Collection $competitorsNotAlreadyBenchmarked;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(ProjectRepository $repository, Project $project)
    {
        $this->project = $project;

        $this->rankMax = intval(config('benchmarks.keywords-ranks.max', 100));

        $this->competitorsNotAlreadyBenchmarked = $repository->getCompetitorHostnameNotAlreadyBenchmarkedQuery($project)
            ->select('hostname')
            ->get()
            ->pluck('hostname');
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.fetch-competitor-keywords');
    }
}
