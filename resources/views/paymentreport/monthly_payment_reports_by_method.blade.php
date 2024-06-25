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
                                <div class="col-sm-3">
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>To</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>Payment Method</label>
                                    <select class="form-control" name="payment_method">
                                        @foreach($pmthods as $pmthod)
                                            <option {{ $payment_method == $pmthod->id ? "selected" : "" }} value="{{ $pmthod->id }}">{{ $pmthod->name }}</option>
                                         @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </header>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Store</th>
                                <th>Invoice / Receipt Number</th>
                                <th>Sub Total</th>
                                <th>Total Paid</th>
                                <th>Payment Time</th>
                                <th>Payment Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total=0;
                            @endphp
                            @forelse($payments as $payment)
                                @php
                                    $total+=$payment->amount;
                                      if(is_null(optional($payment->invoice)->invoice_paper_number)){
                                             $payment->delete();
                                             continue;
                                         }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $payment->customer->firstname }} {{ $payment->customer->lastname }}</td>
                                    <td>{{ $payment->warehousestore->name }}</td>
                                    <td>{{ optional($payment->invoice)->invoice_paper_number }}</td>
                                    <td>{{ number_format($payment->amount,2) }}</td>
                                    <td>{{ number_format($payment->amount,2) }}</td>
                                    <td>{{ date("h:i a",strtotime($payment->payment_time)) }}</td>
                                    <td>{{ convert_date($payment->payment_date) }}</td>
                                </tr>

                            @empty

                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th>{{ number_format($total,2) }}</th>
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
