<x-form
    action="{{ route('projects.actions.fetch-competitor-keywords', $project) }}"
    method="POST"
    class="row row-cols-lg-auto g-3 align-items-center"
>
    <label for="chooseHostname">{{ __('actions.babbar_fetch_host_keywords') }}</label>
    <div class="col-12">
        <input
            type="text"
            name="hostname"
            class="form-control @error('hostname') is-invalid @enderror"
            id="chooseHostname"
            placeholder="{{ __('Paste hostname or type to search...') }}"
            list="hostname-list"
            value="{{ old('hostname') }}"
            style="width: 80vh" />

        @error('hostname')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <datalist id="hostname-list">
            @foreach($competitorsNotAlreadyBenchmarked as $competitor)
                <option value="{{ $competitor }}"></option>
            @endforeach
        </datalist>
    </div>

    <label for="chooseSize">in Top</label>
    <div class="col-12">
        <select class="form-select" name="type" id="chooseSize">
            <option value="normal">20</option>
            <option value="full">{{ $rankMax }}</option>
        </select>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</x-form>
