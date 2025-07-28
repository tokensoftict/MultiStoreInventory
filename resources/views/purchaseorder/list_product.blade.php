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
                        @if(userCanView('purchaseorders.create'))
                            <span class="tools pull-right">
                                  <a  href="{{ route('purchaseorders.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Purchase Orders</a>
                            </span>
                        @endif
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
                            <br/> <br/>
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice Number</th>
                                    <th>Supplier</th>
                                    <th>Stock</th>
                                    <th>Quantity</th>
                                    @if(userCanView('purchaseorders.showpo_total'))
                                    <th>Total Cost Price</th>
                                    <th>Total Selling Price</th>
                                    @endif
                                    <th>Date</th>
                                    <th>By</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($purchase_orders as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $data->purchase_order->purchase_order_invoice_number }}</td>
                                        <td>{{ $data->purchase_order->supplier->name }}</td>
                                        <td>{{ $data->stock->name }}</td>
                                        <td>{{ $data->qty }}</td>
                                        @if(userCanView('purchaseorders.showpo_total'))
                                        <td>{{ number_format(($data->cost_price * $data->qty),2) }}</td>
                                        <td>{{ number_format(($data->selling_price * $data->qty),2) }}</td>
                                        @endif
                                        <td>{{ convert_date2($data->purchase_order->date_created) }}</td>
                                        <td>{{ $data->user->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    @if(userCanView('purchaseorders.show'))
                                                        <li><a href="{{ route('purchaseorders.show',$data->purchase_order->id) }}">View Purchase Order</a></li>
                                                    @endif
                                                    @if(userCanView('purchaseorders.edit') && $data->purchase_order->status == "DRAFT")
                                                        <li><a href="{{ route('purchaseorders.edit',$data->purchase_order->id) }}">Edit Purchase Order</a></li>
                                                    @endif
                                                    @if(userCanView('purchaseorders.markAsComplete') && $data->purchase_order->status == "DRAFT")
                                                        <li><a href="{{ route('purchaseorders.markAsComplete',$data->purchase_order->id) }}">Complete Purchase Order</a></li>
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
                                    <th>Total</th>
                                    @if(userCanView('purchaseorders.showpo_total'))
                                    <th>{{ $purchase_orders->sum('qty') }}</th>
                                        <th>{{ number_format($purchase_orders->sum(function($data){
                                                return ($data->cost_price * $data->qty);
                                            }),2) }}</th>
                                    <th>{{ number_format($purchase_orders->sum(function($data){
                                                return ($data->selling_price * $data->qty);
                                            }),2) }}</th>
                                    @endif
                                    <th></th>
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
