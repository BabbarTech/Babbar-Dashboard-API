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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Screenshot extends Component
{
    public string $filename;

    public string $capture;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $capture, string $filenamePart)
    {
        $this->capture = $capture;
        $this->filename = $this->makeFilename($filenamePart);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.screenshot');
    }


    protected function makeFilename(string $filenamePart): string
    {
        /** @var Project $project */
        $project = app('currentProject');

        $routeName = Route::currentRouteName();
        $uri = request()->getUri();

        if ($routeName) {
            $name = Str::afterLast($routeName, '.');
        } elseif ($uri) {
            $name = Str::afterLast(request()->getUri(), '/');
        } else {
            $name = 'export';
        }

        $latestBacklink = $project->host->backlinkUrls()->latest()->first();
        $date = $latestBacklink ? $latestBacklink->updated_at?->format('Y-m-d') : date('Y-m-d');

        return implode('__', array_filter([
                $project->hostname,
                $name,
                $filenamePart,
                $date
            ])) . '.png';
    }
}
