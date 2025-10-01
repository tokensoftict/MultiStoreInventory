@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

@endpush

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <br/>
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Store</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($incentives as $incentive)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $incentive->payment_date }}</td>
                                        <td>{{ number_format($incentive->amount, 2)}}</td>
                                        <td>{{ $incentive->warehousestore->name }}</td>
                                        <td>No Action</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>{{ number_format($incentives->sum('amount')) }}</th>
                                    <th></th
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        @endsection

        @push('js')
            <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
            <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
            <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
            <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
            <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
            <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
    @endpush