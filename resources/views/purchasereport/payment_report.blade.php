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
                        <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-4">
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-4">
                                    <label>To</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                                </div>
                                <div class="col-sm-3"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <br/>   <br/>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier</th>
                                    <th>Phone Number</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>By</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total = 0;
                                    @endphp
                                    @foreach($histories as $history)
                                        @php
                                            $total+=$history->amount;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $history->supplier->name }}</td>
                                            <td>{{ $history->supplier->phonenumber }}</td>
                                            <td>{{ $history->payment_method->name ?? "RETURN" }}</td>
                                            <td>{{ number_format($history->amount,2) }}</td>
                                            <td>{{ convert_date($history->payment_date) }}</td>
                                            <td>{{ $history->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total :</th>
                                        <th>{{ number_format($total,2) }}</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                    </div>
                </section>
            </div>
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
