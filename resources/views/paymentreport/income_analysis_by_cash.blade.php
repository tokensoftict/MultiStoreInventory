@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

@endpush

@section('content')
    @php
        $tt_payment =0;
        $tt_expenses = 0;
    @endphp
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
                                <div class="col-sm-2"><br/>
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
                        <br/> <br/> <br/>
                        <div class="col-sm-6">
                            <h3>Payment Report</h3>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Method</th>
                                    <th>Invoice / Receipt Number</th>
                                    <th>Sub Total</th>
                                    <th>Total Paid</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total_=0;
                                @endphp
                                @forelse($payments as $payment)
                                    @php
                                        $total_+=$payment->amount;
                                        $tt_payment+=$payment->amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->customer->firstname }} {{ $payment->customer->lastname }}</td>
                                        <td>Cash</td>
                                        <td>{{ optional($payment->invoice)->invoice_paper_number }}</td>
                                        <td>{{ number_format($payment->invoice->sub_total,2) }}</td>
                                        <td>{{ number_format($payment->amount,2) }}</td>
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
                                    <th>{{ number_format($total_,2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <h3>Expenses Report</h3>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>By</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total =0;
                                @endphp
                                @forelse($expenses as $expense)
                                    @php
                                        $total +=$expense->amount;
                                        $tt_expenses+=$expense->amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $expense->user->name }}</td>
                                        <td>{{ $expense->expenses_type->name }}</td>
                                        <td>{{ number_format($expense->amount,2) }}</td>
                                        <td>{{ $expense->department }}</td>
                                        <td>{{ convert_date($expense->expense_date) }}</td>

                                    </tr>
                                @empty

                                @endforelse
                                </tbody>
                                <tfoot>
                                <tr>
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
                        </div>
                        <div class="col-sm-6 col-sm-offset-6">
                            <h3>Analysis</h3>
                            <table class="table table-striped table-bordered dataTable" id="DataTables_Table_3">
                                <tbody><tr>
                                    <td>Total Sales</td>
                                    <th>{{ number_format($tt_payment,2) }}</th>
                                </tr>
                                <tr>
                                    <td>Total Expenses</td>
                                    <th> {{ number_format($tt_expenses,2) }}</th>
                                </tr>
                                <tr>
                                    <td>Grand Total</td>
                                    <th style="font-size: 16px;">{{ number_format($tt_payment - $tt_expenses,2) }}</th>
                                </tr>
                                </tbody></table>
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

