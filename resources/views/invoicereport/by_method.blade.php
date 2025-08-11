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
                                <div class="col-sm-5">
                                    <label>Select Date</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $date }}" name="date" placeholder="Date"/>
                                </div>
                                <div class="col-sm-5">
                                    <label>Select Payment Method</label>
                                    <select class="form-control" name="payment_method">
                                        @foreach($methods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
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
                        <div>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice/Receipt No</th>
                                    <th>Customer</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Sub Total</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Total Paid</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>By</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total = 0;
                                    $total_discount= 0;
                                    $totalPaymentMethod = [];
                                    foreach (\App\Models\PaymentMethod::where('id', $payment_method)->get() as $paymentMthd) {
                                         $totalPaymentMethod[$paymentMthd->name] = 0;
                                    }
                                @endphp
                                @foreach($invoices as $invoice)
                                    @php
                                        $total += $invoice->total_amount_paid;
                                        $total_discount+=$invoice->discount_amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_paper_number }}</td>
                                        <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
                                        <td>{{ $invoice->warehousestore->name }}</td>
                                        <td>{!! invoice_status($invoice->status) !!}</td>
                                        <td>{{ number_format($invoice->sub_total,2) }}</td>
                                        <td>{{ number_format($invoice->discount_amount,2) }}</td>
                                        <td>{{ number_format(($invoice->sub_total -$invoice->discount_amount) ,2) }}</td>
                                        <td>{{ number_format($invoice->total_amount_paid ,2) }}</td>
                                        <td>
                                            @if($invoice->status === "PAID" || $invoice->status === "COMPLETE")
                                                @foreach($invoice->paymentMethodTable as $method)
                                                    @if($method == $payment_method)
                                                    <b>{{ $method->payment_method->name }}</b> : {{  money($method->amount) }}<br/>
                                                    @php
                                                        if(isset($totalPaymentMethod[$method->payment_method->name])) {
                                                            $totalPaymentMethod[$method->payment_method->name] += $method->amount;
                                                        } else {
                                                             $totalPaymentMethod[$method->payment_method->name] = 0;
                                                              $totalPaymentMethod[$method->payment_method->name] +=$method->amount;
                                                        }
                                                    @endphp
                                                   @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ convert_date2($invoice->invoice_date) }}</td>
                                        <td>{{ $invoice->sales_time->format('h:i a') }}</td>
                                        <td>{{ $invoice->created_user->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    @if(userCanView('invoiceandsales.view'))
                                                        <li><a href="{{ route('invoiceandsales.view',$invoice->id) }}">View Invoice</a></li>
                                                    @endif
                                                    @if(userCanView('invoiceandsales.apply_invoice_discount') && $invoice->status === "DISCOUNT")
                                                        <li><a href="{{ route('invoiceandsales.apply_invoice_discount',$invoice->id) }}">Apply Discount</a></li>
                                                    @endif
                                                    @if($invoice->status !== "DISCOUNT" && $invoice->status !== "DISCOUNT-APPLIED")

                                                        @if(userCanView('invoiceandsales.edit') && $invoice->sub_total > -1 && $invoice->status =="DRAFT")
                                                            <li><a href="{{ route('invoiceandsales.edit',$invoice->id) }}">Edit Invoice</a></li>
                                                        @endif

                                                        @if(userCanView('invoiceandsales.destroy'))
                                                            <li><a data-msg="Are you sure, you want to delete this invoice" class="confirm_action" href="{{ route('invoiceandsales.destroy',$invoice->id) }}" >Delete Invoice</a></li>
                                                        @endif

                                                        @if(userCanView('invoiceandsales.edit')  && ($invoice->status =="PAID" || $invoice->status == "COMPLETE") && config('app.uses_edit_to_return_stocks') == true)
                                                            <li><a href="{{ route('invoiceandsales.edit',$invoice->id) }}">Edit Invoice</a></li>
                                                        @endif

                                                        @if(userCanView('invoiceandsales.pos_print') && $invoice->sub_total > -1)
                                                            <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.pos_print',$invoice->id) }}">Print Invoice Pos</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.print_afour') && $invoice->sub_total > -1)
                                                            <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.print_afour',$invoice->id) }}">Print Invoice A4</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.print_afour') && $invoice->sub_total > -1)
                                                            <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.print_afive',$invoice->id) }}">Print Small A5</a></li>
                                                        @endif
                                                        @if(userCanView('invoiceandsales.print_way_bill') && $invoice->sub_total > -1)
                                                            <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.print_way_bill',$invoice->id) }}">Print Waybill</a></li>
                                                        @endif
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
                                    <th></th>
                                    <th>Total Discount</th>
                                    <th>{{ number_format($total_discount,2) }}</th>
                                    <th>Total Paid</th>
                                    <th>{{ number_format($total,2) }}</th>
                                    <th>@php
                                            $allTotal =0;
                                            foreach ($totalPaymentMethod as $key => $value) {
                                                if($value > 0) {
                                                    $allTotal+=$value;
                                                echo '<b>TOTAL '.$key.'</b> : '.money($value).'<br/>';
                                                }
                                            }
                                           echo ' <hr/ style="margin:0;padding:0;padding-top:2px;padding-bottom:2px;">';
                                            echo '<span style="color:red"><b>TOTAL</b> : '.money($allTotal).'</span><br/>';
                                        @endphp</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
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
    <script   src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script   src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
