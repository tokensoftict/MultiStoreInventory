@extends('layouts.app')

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title."- Invoice ".$invoice->id }}
                        <span class="pull-right">
                            @if($invoice->status != "DISCOUNT")
                                @if(userCanView('invoiceandsales.pos_print'))
                                    <a href="{{ route('invoiceandsales.pos_print',$invoice->id) }}" onclick="return open_print_window(this);" class="btn btn-success btn-sm" ><i class="fa fa-file"></i> Print POS</a>
                                @endif
                                @if(userCanView('invoiceandsales.print_afour'))
                                    <a href="{{ route('invoiceandsales.print_afour',$invoice->id) }}"  onclick="return open_print_window(this);" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> Print A4</a>
                                @endif
                                @if(userCanView('invoiceandsales.print_way_bill'))
                                    <a href="{{ route('invoiceandsales.print_way_bill',$invoice->id) }}"  onclick="return open_print_window(this);" class="btn btn-primary btn-sm" ><i class="fa fa-print"></i> Print Waybill</a>
                                @endif
                            @endif
                        </span>
                    </header>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            @if(session('success'))
                                {!! alert_success(session('success')) !!}
                            @elseif(session('error'))
                                {!! alert_error(session('error')) !!}
                            @endif
                            <div class="row">
                                <div class="col-sm-4">
                                    <h2>Bill To</h2>
                                    <div class="form-group">
                                        <strong>Customer Information:</strong><br>
                                        <div id="customer_info">
                                            <address>
                                                {{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}<br>
                                                @if(!empty($invoice->customer->address))
                                                    {{ $invoice->customer->address }}<br>
                                                @endif
                                                @if(!empty($invoice->customer->email))
                                                    {{ $invoice->customer->email }}<br>
                                                @endif
                                                {{ $invoice->customer->phone_number }}
                                            </address>
                                        </div>
                                    </div>
                                    <h2>Invoice Property</h2>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Invoice / Receipt Number</label><br/>
                                        <label style="font-size: 15px">{{ $invoice->invoice_paper_number }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Status</label><br/>
                                        <label style="font-size: 15px">{!! invoice_status($invoice->status) !!}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Invoice Date</label><br/>
                                        <label style="font-size: 15px"> {{ convert_date($invoice->invoice_date) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Time</label><br/>
                                        <label style="font-size: 15px"> {{ date("h:i a",strtotime($invoice->sales_time)) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Sales Representative</label><br/>
                                        <label style="font-size: 15px">{{ $invoice->created_user->name }}</label>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <br/>
                                            <h2>Invoice Item(s)</h2>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <td>#</td>
                                                        <td align="left"><b>Name</b></td>
                                                        <td align="center"><b>Quantity</b></td>
                                                        <td align="center"><b>Price</b></td>
                                                        <td align="right"><b>Total</b></td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($invoice->invoice_items as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td align="left" class="text-left">{{ $item->stock->name }}</td>
                                                            <td align="center" class="text-center">{{ $item->quantity }}</td>
                                                            <td align="center" class="text-center">{{ number_format($item->selling_price,2) }}</td>
                                                            <td align="right" class="text-right">{{ number_format(($item->total_selling_price),2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Sub Total</td>
                                                        <td  align="right" class="text-right">{{ number_format($invoice->sub_total,2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Total</td>
                                                        <td  align="right" class="text-right"><b>{{ number_format(($invoice->sub_total -$invoice->discount_amount),2) }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Invoice Discount</td>
                                                        <td  align="right" class="text-right">
                                                            <form action="" method="post">
                                                                @csrf
                                                                <input type="text" placeholder="Enter Discount Amount" value="{{ $invoice->discount_amount }}"  name="discount" class="form-control"><br/>
                                                                <button type="submit"  class="btn btn-sm btn-primary">Apply Discount</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                                @if(userCanView("invoiceandsales.cancel_discount"))
                                                <a href="{{ route('invoiceandsales.cancel_discount', $invoice->id) }}" class="btn btn-sm btn-danger">Cancel Discount Request and send back to Draft</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
