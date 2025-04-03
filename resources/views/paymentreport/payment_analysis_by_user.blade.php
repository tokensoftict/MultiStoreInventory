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
                                    <label>Date</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $date }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-4">
                                    <label>Select User</label>
                                    <select class="form-control" name="user_id">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == $user_id ? 'selected' : '' }}>{{ $user->name }}</option>
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
                        @php
                            $all_total=0;
                            $totalCredit = 0;
                        @endphp
                        @foreach($payment_methods as $payment_method)

                            <h3>{{ $payment_method->name }} PAYMENTS</h3>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Store</th>
                                    <th>Method</th>
                                    <th>Invoice / Receipt Number</th>
                                    <th>Sub Total</th>
                                    <th>Total Paid</th>
                                    <th>Payment Time</th>
                                    <th>Payment Date</th>
                                    <th>User</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total=0;
                                @endphp
                                @foreach(\App\Models\PaymentMethodTable::where('payment_method_id',$payment_method->id)->where('user_id', $user_id)->where('payment_date',$date)->get() as $payment)
                                    @php
                                        $total+=$payment->amount;
                                        $all_total+=$payment->amount;
                                        if($payment_method->id === 4){
                                            $totalCredit+=$payment->amount;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->customer->firstname }} {{ $payment->customer->lastname }}</td>
                                        <td>{{ $payment->warehousestore->name }}</td>
                                        <td>{{ $payment->payment_method->name }}</td>
                                        <td>{{ optional($payment->invoice)->invoice_paper_number }}</td>
                                        <td>{{ number_format($payment->amount,2) }}</td>
                                        <td>{{ number_format($payment->amount,2) }}</td>
                                        <td>{{ date("h:i a",strtotime($payment->created_at)) }}</td>
                                        <td>{{ convert_date($payment->payment_date) }}</td>
                                        <td>{{ $payment->user->name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>{{ number_format($total,2) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        @endforeach
                        <div class="pull-right">
                            <h2>Total Credit : {{ number_format($totalCredit,2) }}</h2>
                            <h2>Grand Total : {{ number_format($all_total - $totalCredit,2) }}</h2>
                        </div>
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
