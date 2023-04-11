<table class="table table-striped">
    @foreach($benchmarks as $benchmark)
        <tr>
            <td>
                <a href="{{ route('projects.benchmarks.show', [$project, $benchmark]) }}">
                    {{ class_basename($benchmark->type) }}
                </a>
            </td>
            <td>
                {{ $benchmark->getBenchmarkableTitle() }}
            </td>
            <td>
                <span class="ms-3 badge rounded-pill {{ $benchmark->status->color() }}">{{ $benchmark->status->label() }}</span>
            </td>
            <td>
                @if(! empty($benchmark->error))
                    <small>{{ $benchmark->error }}</small>
                @endif
            </td>
            <td class="text-nowrap text-end">{{ $benchmark->created_at }}</td>
            <td class="text-end">
                <a href="{{ route('projects.benchmarks.show', [$project, $benchmark]) }}" class="btn btn-outline-dark btn-sm">
                    {{ __('More Info') }}
                </a>
            </td>
        </tr>
    @endforeach
</table>
