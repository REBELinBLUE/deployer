@extends('layout')

@section('content')
    <div class="box">
        @if (!count($revisions))
        <div class="box-body">
            <p>{{ trans('revisions.none') }}</p>
        </div>
        @else
        <div class="box-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="15%">{{ trans('revisions.item') }}</th>
                        <th>{{ trans('revisions.name') }}</th>
                        <th>{{ trans('revisions.event') }}</th>
                        <th>{{ trans('revisions.when') }}</th>
                        <th>{{ trans('revisions.user') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($revisions as $revision)
                    <tr>
                        <td>{{ $revision->item_type }}</td>
                        <td>{{ $revision->identifiable_name }}</td>
                        <td>{{ $revision->event }}</td>
                        <td>{{ $revision->created_at->format('jS F Y g:i:s A') }}</td>
                        <td>{{ $revision->creator }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $revisions->render() !!}
        </div>
        @endif
    </div>
@stop

@section('right-buttons')
    <div class="pull-right">
        <form method="get" action="" id="filter_log">
            <label for="filter_type">Filter by</label>
            <select name="filter_type" id="filter_type">
                <option value="">All Items</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->revisionable_type }}" @if ($filter['type'] === $class->revisionable_type) selected @endif>{{ $class->item_type }}</option>
                @endforeach
            </select>

            @if (count($instances) > 1)
            <select name="filter_id" id="filter_id">
                <option value="">All Occurances</option>
                @foreach ($instances as $instance)
                    <option value="{{ $instance->revisionable_id }}" @if ($filter['instance'] === $instance->revisionable_id) selected @endif>{{ $instance->identifiable_name }}</option>
                @endforeach
            </select>
            @endif
        </form>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        app.views.Audit();
    </script>
@endpush
