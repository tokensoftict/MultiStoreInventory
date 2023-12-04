@extends('layouts.app')

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title." - Transfer ".$transfer->id }}
                        <span class="pull-right">
                            @if(userCanView('stocktransfer.print_afour'))
                                <a href="{{ route('stocktransfer.print_afour',$transfer->id) }}"  onclick="return open_print_window(this);" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> Print Transfer</a>
                            @endif
                            @if(userCanView('stocktransfer.edit') && $transfer->status =="DRAFT")
                                <a  href="{{ route('stocktransfer.edit',$transfer->id) }}" class="btn btn-success btn-sm">Edit</a>
                            @endif
                            @if(userCanView('stocktransfer.complete') && $transfer->status =="DRAFT")
                                <a  href="{{ route('stocktransfer.complete',$transfer->id) }}" class="btn btn-success btn-sm">Complete</a>
                            @endif
                            @if(userCanView('stocktransfer.delete_transfer') && $transfer->status =="DRAFT")
                                <a data-msg="Are you sure, you want to delete this transfer" href="{{ route('stocktransfer.delete_transfer',$transfer->id) }}" class="btn btn-danger btn-sm confirm_action">Delete</a>
                            @endif
                        </span>
                    </header>
                    <div class="panel panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6>Transfer From</h6>
                                <h5>{{ $transfer->store_from->name }}</h5>
                                <br/>
                                <h6>Status</h6>
                                <h5>{!! $transfer->status == "COMPLETE" ? label( $transfer->status,'success') : label( $transfer->status,'primary') !!}</h5>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="text-right">Transfer To</h6>
                                <h5 class="text-right">{{ $transfer->store_to->name }}</h5>
                                <br/>
                                <h6 class="text-right">Date</h6>
                                <h5 class="text-right">{!!  convert_date($transfer->transfer_date)  !!}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <h4>Product Transfer List</h4>

                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-left">Name</th>
                                        <th class="text-center">Selling Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Total Selling Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $total = 0;
                                        $errors = session('errors')
                                    @endphp
                                    @foreach($transfer->stock_transfer_items()->get() as $trans)
                                        @php
                                            $total +=($trans->quantity * $trans->selling_price)
                                        @endphp
                                        <tr>
                                            <td class="text-left">{{ $trans->stock->name }}</td>
                                            <td class="text-center">{{ number_format($trans->cost_price,2) }}</td>
                                            <td class="text-center">{{ $trans->quantity }}</td>
                                            <td class="text-right">{{ number_format(($trans->quantity * $trans->cost_price),2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">{{ number_format($total,2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

@endsection
