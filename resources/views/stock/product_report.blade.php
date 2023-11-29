@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link href="{{ asset('bower_components/datatables/media/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-tabletools/css/dataTables.tableTools.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-colvis/css/dataTables.colVis.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-responsive/css/responsive.dataTables.scss') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-scroller/css/scroller.dataTables.scss') }}" rel="stylesheet">
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
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-5">
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
                        <br/>  <br/>
                        <div class="row">
                            <div class="col-sm-4">
                                <h4>Report Summary</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Total Quantity Transfer</th>
                                        <th>{{ $transfers->sum('quantity') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Quantity Sold</th>
                                        <th>{{ $sales->sum('quantity') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Quantity Purchases</th>
                                        <th>{{ $purchases->sum('qty') }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Quantity  Returns</th>
                                        <th>{{ $returns->sum('quantity_before') }}</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4"></div>
                            <div class="col-sm-4">
                                <h4>Quantity By Shop</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>Warehouse & Shops</th>
                                        <th>Quantity Packed</th>
                                        <th>Quantity Yards</th>
                                    </tr>
                                    @foreach(\App\Models\Warehousestore::all() as $shop)
                                        <tr>
                                            <th>{{ $loop->iteration }}</th>
                                            <th>{{ $shop->name }}</th>
                                            <th>{{ $stock->getCustomPackedStockQuantity($shop->id) }}</th>
                                            <th>{{ $stock->getCustomYardStockQuantity($shop->id) }}</th>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <br/>  <br/>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Product Transfer History</h4>
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Product Type</th>
                                        <th>Total Cost Price</th>
                                        <th>By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($transfers as $transfer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $transfer->store_from->name }}</td>
                                            <td>{{ $transfer->store_to->name }}</td>
                                            <td>{!! $transfer->stock_transfer->status == "COMPLETE" ? label( $transfer->stock_transfer->status,'success') : label( $transfer->status,'primary') !!}</td>
                                            <td>{{ convert_date($transfer->transfer_date) }}</td>
                                            <td>{{ $transfer->product_type }}</td>
                                            <td>{{ number_format($transfer->total_price,2) }}</td>
                                            <td>{{ $transfer->user->name }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>  <br/>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Product Sales History</h4>
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice/Receipt No</th>
                                        <th>Customer</th>
                                        <th>Stock</th>
                                        <th>Qty Sold</th>
                                        <th>Total Cost Price</th>
                                        <th>Total Selling Price</th>
                                        <th>Profit</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sales as $invoice)
                                        <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice->invoice_paper_number }}</td>
                                        <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
                                        <td>{{ $invoice->stock->name }}</td>
                                        <td>{{ $invoice->quantity }}</td>
                                        <td>{{ number_format(($invoice->quantity * $invoice->cost_price),2) }}</td>
                                        <td>{{ number_format(($invoice->quantity * $invoice->selling_price),2) }}</td>
                                        <td>{{ number_format((($invoice->quantity * $invoice->selling_price)-($invoice->quantity * $invoice->cost_price)),2) }}</td>
                                        <td>{!! invoice_status($invoice->status) !!}</td>
                                        <td>{{ convert_date2($invoice->invoice->invoice_date) }}</td>
                                        <td>{{ $invoice->invoice->sales_time }}</td>
                                        <td>{{ $invoice->invoice->created_user->name }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>  <br/>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Product Purchase History</h4>
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-center"><strong>Quantity</strong></th>
                                        <th class="text-center"><strong>Cost Price</strong></th>
                                        <th class="text-right"><strong>Total Cost Price</strong></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchases as $item)
                                        <tr>
                                            <td>{{ $item->purchase_order->supplier->name }}</td>
                                            <td>{{ convert_date2($item->purchase_order->date_created) }}</td>
                                            <td>{!! $item->purchase_order->status == "DRAFT" ? label('DRAFT','primary') :   label('COMPLETE','success') !!}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">{{ number_format($item->cost_price,2) }}</td>
                                            <td class="text-right">{{ number_format(($item->cost_price * $item->qty),2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>  <br/>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Product Returns History</h4>
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Stock</th>
                                        <th>Customer</th>
                                        <th>Invoice / Receipt No</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Selling Price</th>
                                        <th>Return By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($returns as $log)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $log->stock->name }}</td>
                                            <td>{{ $log->customer->firstname }} {{ $log->customer->lastname }}</td>
                                            <td>{{ $log->invoice_paper_number }}</td>
                                            <td>{{ $log->quantity_before }}</td>
                                            <td>{{ convert_date2($log->date_added) }}</td>
                                            <td>{{ $log->store_after == "quantity" ? "Packed" : "Pieces / Yards" }}</td>
                                            <td>{{ number_format($log->selling_price,2) }}</td>
                                            <td>{{ $log->user->name }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection


@push('js')
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-tabletools/js/dataTables.tableTools.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-colvis/js/dataTables.colVis.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-responsive/js/dataTables.responsive.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-scroller/js/dataTables.scroller.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush

