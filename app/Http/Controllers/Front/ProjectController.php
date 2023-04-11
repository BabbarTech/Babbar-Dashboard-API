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

use App\Enums\SerpEnum;
use App\Http\Controllers\Controller;
use App\Models\Benchmark;
use App\Models\Project;
use App\Models\User;
use App\Repositories\HostRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');

        $this->middleware('tenant')
            ->except('index', 'create', 'store');
    }


    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $projects = $user->projects()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('resources.project.index', [
            'projects' => $projects
        ]);
    }


    public function create(): View
    {
        return view('resources.project.create');
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'serp' => ['required', new Enum(SerpEnum::class)],
            'description' => 'nullable|sometimes|string',
            'benchmarks' => 'nullable|sometimes|array',
        ]);

        /** @var User $user */
        $user = $request->user();

        try {
            $project = $user->projects()->create($validated);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withDanger($e->getMessage());
        }

        if (isset($validated['benchmarks'])) {
            foreach ($validated['benchmarks'] as $benchmarkClass) {
                $benchmark = $project->host->benchmarks()->create([
                    'type' => $benchmarkClass,
                ]);

                $benchmark->process();
            }
        }

        return redirect()->route('projects.show', $project)
            ->withSuccess(__('resources.projects.message.created'));
    }


    public function show(Project $project): View
    {
        return view('resources.project.show', [
            'project' => $project,
            'latestBenchmarks' => Benchmark::latest()->limit(5)->get(),
        ]);
    }


    public function edit(Project $project): View
    {
        return view('resources.project.edit', [
            'project' => $project
        ]);
    }


    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'sometimes|string|nullable',
        ]);

        $project->update($validated);

        return redirect()->route('projects.edit', $project)
            ->withSuccess(__('resources.projects.message.updated'));
    }


    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->withSuccess(__('resources.projects.message.deleted'));
    }
}
