@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading panel-border">
                        {{ $title }}
                        @if(userCanView('counting.create'))
                            <span class="tools pull-right">
                                            <a  href="{{ route('counting.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Stock Counting</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Store</th>
                                    <th>Date</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($countings as $counting)
                                    <tr>
                                        <td>{{ $counting->name }}</td>
                                        <td>{{ $counting->warehousestore->name }}</td>
                                        <td>{{ convert_date2($counting->date) }}</td>
                                        <td>{{ $counting->user->name }}</td>
                                        <td>{!! showStatus($counting->status) !!}</td>
                                        <td>
                                            @if(userCanView('counting.destroy') && $counting->status == "Pending")
                                                <a href="{{ route('counting.destroy',$counting->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                            @endif

                                                @if(userCanView('counting.show'))
                                                    <a href="{{ route('counting.show',$counting->id) }}" class="btn btn-sm btn-primary">View</a>
                                                @endif
                                        </td>
                                    </tr>
                                 @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection


@push('js')

    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
@endpush
