<x-app-layout title="{{ $project->hostname }} Benchmarks">
    <div id="mainContent" class="container">
        <x-header title="{{ __('Benchmarks') }}" :project="$project"></x-header>

        <div class="card d-block">
            <div class="card-body">

                @include('partials.benchmarks-table')

                {{ $benchmarks->links() }}

            </div>
        </div>


    </div>
</x-app-layout>
