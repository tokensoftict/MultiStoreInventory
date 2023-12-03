@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link href="{{ asset('bower_components/datatables/media/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-tabletools/css/dataTables.tableTools.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-colvis/css/dataTables.colVis.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-responsive/css/responsive.dataTables.scss') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-scroller/css/scroller.dataTables.scss') }}" rel="stylesheet">
@endpush
@section('content')
<div class="ui-container">
    <div class="row">

        <div class="col-md-12">
            <section class="panel">
                <header class="panel-heading">
                    {{ $title }}
                    <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-3">
                                <label>From</label>
                                <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                            </div>
                            <div class="col-sm-3">
                                <label>To</label>
                                <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                            </div>
                            <div class="col-sm-3">
                                <label>Select Customer</label>
                                <select class="form-control" name="customer_id">
                                    @foreach($customers as $customer)
                                        <option {{ $customer_id == $customer->id ? "selected" : "" }} value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2"><br/>
                                <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </header>
                <section class="panel-body">
                    <h5><b>Opening Balance</b> : {{ number_format($opening,2) }}</h5>
                    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase Order Amount</th>
                            <th>Purchase Order Payment</th>
                            <th>Date</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($histories as $history)
                            @php
                                $opening =  ($opening+$history->amount)
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $history->amount < 0  ? number_format($history->amount,2) : "" }}</td>
                                <td>{{ $history->amount > 0  ? number_format($history->amount,2) : "" }}{{  ($history->amount > 0 ?  "(".($history->payment_method->name ?? "").")"  : "") }}</td>
                                <td>{{ convert_date2($history->payment_date) }}</td>
                                <td>{{ number_format(($opening),2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>Total Balance</th>
                                <th>{{ number_format($opening,2) }}</th>
                            </tr>
                        </tfoot>
                        <tfoot>
                        </tfoot>
                    </table>
                </section>
            </section>
        </div>
    </div>
</div>

@endsection


@push('js')
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-tabletools/js/dataTables.tableTools.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-colvis/js/dataTables.colVis.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-responsive/js/dataTables.responsive.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-scroller/js/dataTables.scroller.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
