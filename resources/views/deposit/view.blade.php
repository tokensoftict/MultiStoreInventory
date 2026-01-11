@extends('layouts.app')

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title."- Deposit ".$deposit->id }}
                        <span class="pull-right">
                            @if(userCanView('deposit.pos_print'))
                                <a href="{{ route('deposit.pos_print',$deposit->id) }}" onclick="return open_print_window(this);" class="btn btn-success btn-sm" ><i class="fa fa-file"></i> Print POS</a>
                            @endif
                            @if(userCanView('deposit.print_afour'))
                                <a href="{{ route('deposit.print_afour',$deposit->id) }}"  onclick="return open_print_window(this);" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> Print A4</a>
                            @endif
                            @if(userCanView('deposit.print_afive'))
                                <a href="{{ route('deposit.print_afive',$deposit->id) }}"  onclick="return open_print_window(this);" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> Print A5</a>
                            @endif
                            @if(userCanView('deposit.print_way_bill'))
                                <a href="{{ route('deposit.print_way_bill',$deposit->id) }}"  onclick="return open_print_window(this);" class="btn btn-primary btn-sm" ><i class="fa fa-print"></i> Print Waybill</a>
                            @endif

                            @if(userCanView('deposit.edit'))
                                <a href="{{ route('deposit.edit',$deposit->id) }}" class="btn btn-success btn-sm">Edit Deposit</a>
                            @endif

                            @if(userCanView('deposit.destroy'))
                                <a href="{{ route('deposit.destroy',$deposit->id) }}" class="btn btn-danger btn-sm">Delete Deposit</a>
                            @endif

                            @if(userCanView('deposit.add_depsoit_payment'))
                                <a href="{{ route('deposit.add_depsoit_payment',$deposit->id) }}" class="btn btn-danger btn-sm">Add Deposit Payment</a>
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
                                                {{ $deposit->customer->firstname }} {{ $deposit->customer->lastname }}<br>
                                                @if(!empty($deposit->customer->address))
                                                    {{ $deposit->customer->address }}<br>
                                                @endif
                                                @if(!empty($deposit->customer->email))
                                                    {{ $deposit->customer->email }}<br>
                                                @endif
                                                {{ $deposit->customer->phone_number }}
                                            </address>
                                        </div>
                                    </div>
                                    <h2>Deposit Property</h2>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Deposit Number</label><br/>
                                        <label style="font-size: 15px">{{ $deposit->deposit_number }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Status</label><br/>
                                        <label style="font-size: 15px">{!! invoice_status($deposit->status) !!}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Deposit Date</label><br/>
                                        <label style="font-size: 15px"> {{ convert_date($deposit->deposit_date) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Time</label><br/>
                                        <label style="font-size: 15px"> {{ date("h:i a",strtotime($deposit->sales_time)) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Sales Representative</label><br/>
                                        <label style="font-size: 15px">{{ $deposit->created_user->name }}</label>
                                    </div>

                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <br/>
                                            <h2>Deposit Item(s)</h2>
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
                                                    @foreach($deposit->deposit_items as $item)
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
                                                        <td  align="right" class="text-right">{{ number_format($deposit->sub_total,2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Discount</td>
                                                        <td  align="right" class="text-right">-{{ number_format($deposit->discount_amount,2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Total</td>
                                                        <td  align="right" class="text-right" id="total_amount_paid" total="{{ $deposit->sub_total -$deposit->discount_amount }}"><b>{{ number_format(($deposit->sub_total -$deposit->discount_amount),2) }}</b></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>

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
