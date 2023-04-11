<div class="row align-items-center">
    <div class="col-10">
        <h1>
            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none project-name">
                <small class="text-muted">
                    @include('partials.icons.box-arrow-in-left', ['size' => 24])
                    {{ $project->hostname }}
                </small>
            </a>
            <br>
            {{ $title }}
        </h1>
    </div>
    <div class="col-2 text-end">
        {{ $right ?? '' }}
    </div>
</div>
