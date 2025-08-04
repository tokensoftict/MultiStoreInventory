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
                            <form action="" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label>Select Date</label>
                                        <input type="text" autocomplete="off" class="form-control datepicker js-datepicker" value="{{ $date }}" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"   name="date" placeholder="From"/>
                                    </div>
                                    <div class="col-sm-2"><br/>
                                        <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            <br/>
                            @if(session('success'))
                                {!! alert_success(session('success')) !!}
                            @elseif(session('error'))
                                {!! alert_error(session('error')) !!}
                            @endif
                            @php
                                $pMthods = \App\Models\PaymentMethod::all();
                            @endphp
                            @if($allInvoices->count() > 0)
                            @foreach($allInvoices as $in)
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice/Receipt No</th>
                                        <th>Customer</th>
                                        <th>Stock</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Selling Price</th>
                                        <th>Total Cost Price</th>
                                        <th>Total Selling Price</th>
                                        <th>Profit</th>
                                        <th style="width:150px;">Status</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total_cost =0;
                                        $total_selling =0;
                                        $total_profit =0;
                                        $total_qty = 0;
                                    @endphp
                                    @foreach($in->invoice_items as $invoice)
                                        @php
                                            $total_cost+=($invoice->quantity * $invoice->cost_price);
                                            $total_selling+=($invoice->quantity * $invoice->selling_price);
                                            $total_profit += (($invoice->quantity * $invoice->selling_price)-($invoice->quantity * $invoice->cost_price));
                                            $total_qty+=$invoice->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $invoice->invoice->invoice_paper_number }}</td>
                                            <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
                                            <td>{{ $invoice->stock->name }}</td>
                                            <td>{{ $invoice->quantity }}</td>
                                            <td>{{ number_format($invoice->cost_price, 2) }}</td>
                                            <td>{{ number_format($invoice->selling_price, 2) }}</td>
                                            <td>{{ number_format(($invoice->quantity * $invoice->cost_price),2) }}</td>
                                            <td>{{ number_format(($invoice->quantity * $invoice->selling_price),2) }}</td>
                                            <td>{{ number_format((($invoice->quantity * $invoice->selling_price)-($invoice->quantity * $invoice->cost_price)),2) }}</td>
                                            <td>{!! invoice_status($invoice->status) !!}</td>
                                            <td>{{ convert_date2($invoice->invoice->invoice_date) }}</td>
                                            <td>{{ $invoice->invoice->sales_time }}</td>
                                            <td>{{ $invoice->invoice->created_user->name }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                                    <ul role="menu" class="dropdown-menu">
                                                        @if(userCanView('invoiceandsales.view'))
                                                            <li><a href="{{ route('invoiceandsales.view',$invoice->invoice->id) }}">View Invoice</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.pos_print') && $invoice->sub_total > -1)
                                                            <li><a href="{{ route('invoiceandsales.pos_print',$invoice->invoice->id) }}">Print Invoice Pos</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.print_afour') && $invoice->sub_total > -1)
                                                            <li><a href="{{ route('invoiceandsales.print_afour',$invoice->invoice->id) }}">Print Invoice A4</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.print_way_bill') && $invoice->sub_total > -1)
                                                            <li><a href="{{ route('invoiceandsales.print_way_bill',$invoice->invoice->id) }}">Print Waybill</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ number_format($total_qty,0) }}</th>
                                        <th></th>
                                        <th></th>
                                        <th>{{ number_format($total_cost,2) }}</th>
                                        <th>{{ number_format($total_selling,2) }}</th>
                                        <th>{{ number_format($total_profit,2) }}</th>
                                        <th>
                                            @if($in->status === "PAID" || $in->status === "COMPLETE")
                                                @foreach($in->paymentMethodTable as $method)
                                                    <b>{{ $method->payment_method->name }}</b> : {{  money($method->amount) }}<br/>
                                                    @php
                                                        if(isset($totalPaymentMethod[$method->payment_method->name])) {
                                                            $totalPaymentMethod[$method->payment_method->name] += $method->amount;
                                                        } else {
                                                             $totalPaymentMethod[$method->payment_method->name] = 0;
                                                              $totalPaymentMethod[$method->payment_method->name] +=$method->amount;
                                                        }
                                                    @endphp
                                                @endforeach
                                            @endif
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            @endforeach
                            @else
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice/Receipt No</th>
                                        <th>Customer</th>
                                        <th>Stock</th>
                                        <th>Quantity</th>
                                        <th>Cost Price</th>
                                        <th>Selling Price</th>
                                        <th>Total Cost Price</th>
                                        <th>Total Selling Price</th>
                                        <th>Profit</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            @endif
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
