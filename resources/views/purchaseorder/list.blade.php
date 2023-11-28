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
                        @if(userCanView('purchaseorders.create'))
                            <span class="tools pull-right">
                                  <a  href="{{ route('purchaseorders.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Purchase Order</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier</th>
                                <th>Total Items</th>
                                <th>Total Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchase_orders as $purchase_order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $purchase_order->supplier->name }}</td>
                                    <td>{{ $purchase_order->purchase_order_items->count() }}</td>
                                    <td>{{ number_format($purchase_order->purchase_order_items()->sum(DB::raw('cost_price * qty')),2) }}</td>
                                    <td>{{ convert_date2($purchase_order->date_created) }}</td>
                                    <td>{!! $purchase_order->status == "DRAFT" ? label('DRAFT','primary') :   label('COMPLETE','success') !!}</td>
                                    <td>{{ $purchase_order->user->name }}</td>
                                    <td>{{ $purchase_order->created_user->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('purchaseorders.show'))
                                                    <li><a href="{{ route('purchaseorders.show',$purchase_order->id) }}">View Purchase Order</a></li>
                                                @endif
                                                @if(userCanView('purchaseorders.edit') && $purchase_order->status == "DRAFT")
                                                    <li><a href="{{ route('purchaseorders.edit',$purchase_order->id) }}">Edit Purchase Order</a></li>
                                                @endif
                                                @if(userCanView('purchaseorders.markAsComplete') && $purchase_order->status == "DRAFT")
                                                    <li><a href="{{ route('purchaseorders.markAsComplete',$purchase_order->id) }}">Complete Purchase Order</a></li>
                                                @endif
                                                @if(userCanView('purchaseorders.destroy') && $purchase_order->status == "DRAFT")
                                                    <li><a href="{{ route('purchaseorders.destroy',$purchase_order->id) }}">Delete Purchase Order</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
