<x-app-layout title="{{ __('resources.projects.index.title') }}">

    <div class="container">
        <div class="row">
            <div class="col">
                <h1>{{ __('resources.projects.index.title') }}</h1>
            </div>
            <div class="col text-end">
                <a href="{{ route('projects.create') }}" class="btn btn-primary">{{ __('resources.projects.create.title') }}</a>
            </div>
        </div>

        <div class="card d-block">
            <div class="card-body">

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Host') }}</th>
                        <th scope="col">{{ __('Serp') }}</th>
                        <th scope="col">{{ __('Created at') }}</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <th scope="row">{{ $project->id }}</th>
                            <td>
                                <a href="{{ route('projects.show', $project) }}">
                                    {{ $project->hostname }}
                                </a>
                            </td>
                            <td>
                                {{ $project->serp->label() }}
                            </td>
                            <td>{{ $project->created_at }}</td>
                            <td class="text-end">
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>

                {{ $projects->links() }}

            </div>
        </div>

    </div>

</x-app-layout>
