<div class="collapse mb-3 text-start fst-italic row justify-content-center" id="collapseHelp">
    <div class="col-9">
        <dl class="row mb-0 chart-legend">
            {{ $slot }}
        </dl>
    </div>
</div>

<a class="btn btn-sm btn-outline me-2" data-bs-toggle="collapse" href="#collapseHelp" role="button" aria-expanded="false" aria-controls="collapseHelp" data-html2canvas-ignore>
    @include('partials.icons.question')
    {{ __('chart.help.btn') }}
</a>
