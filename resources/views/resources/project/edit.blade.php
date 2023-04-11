<x-app-layout title="{{ __('resources.projects.edit.title', ['title' => $project->hostname]) }} {{ $project->hostname }}">
    <div class="container">
        <x-header title="{{ __('resources.projects.edit.title') }}" :project="$project" />
        <div class="card d-block">
            <div class="card-body">
                <x-form method="PUT" :action="route('projects.update', $project)" class="mb-3">

                    <x-form.input name="url" :value="$project->url" disabled />

                    <x-form.input name="host" :value="$project->hostname" disabled />

                    <x-form.input name="domain" :value="$project->domain" disabled />

                    <x-form.input name="tenant_key" :value="$project->tenant_key" disabled />

                    <x-form.input name="database" :value="$project->database" disabled />

                    <x-form.select name="serp" :options="\App\Enums\SerpEnum::dropdownOptions()" disabled />

                    <x-form.textarea name="description" :value="$project->description" />

                    <button class="btn btn-primary" type="submit">
                        {{ __('Update') }}
                    </button>
                </x-form>
            </div>
        </div>
        <div class="mt-3 text-end">
            @include('partials.buttons.delete-project')
        </div>
    </div>
</x-app-layout>
