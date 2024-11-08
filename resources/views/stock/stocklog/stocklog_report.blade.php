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
                    @if(session('success'))
                        {!! alert_success(session('success')) !!}
                    @elseif(session('error'))
                        {!! alert_error(session('error')) !!}
                    @endif
                    <div class="panel-heading">
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
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 10px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Stock</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Invoice Number</th>
                                <th>Product Type</th>
                                <th>Usage Type</th>
                                <th>Store</th>
                                <th>Selling Price</th>
                                <th>Total Selling</th>
                                <th>Cost Price</th>
                                <th>Total Cost</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->stock->name }}</td>
                                    <td>{{ $log->quantity }}</td>
                                    <td>{{ convert_date2($log->log_date) }}</td>
                                    <td>{{ $log->invoice_number }}</td>
                                    <td>{{ array_search($log->product_type,config('stock_type_name.'.config('app.store'))) }}</td>
                                    <td>{{ $log->stock_log_usages_type->name ?? "" }}</td>
                                    <td>{{ $log->warehousestore->name }}</td>
                                    <td>{{ number_format($log->selling_price,2) }}</td>
                                    <td>{{ number_format(($log->selling_price * $log->quantity ),2) }}</td>
                                    <td>{{ number_format($log->cost_price,2) }}</td>
                                    <td>{{ number_format(($log->cost_price * $log->quantity ),2) }}</td>
                                    <td>{{ $log->user->name }}</td>
                                    <td>
                                        @if(userCanView('stocklog.edit',$log->id))
                                            <a href="{{ route('stocklog.edit',$log->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        @endif
                                        @if(userCanView('stocklog.delete_log',$log->id))
                                            <a href="{{ route('stocklog.delete_log',$log->id) }}" class="btn btn-danger btn-sm">Delete</a>
                                        @endif
                                        @if(userCanView('stocklog.print_log'))
                                            <a href="{{ route('stocklog.print_log',$log->id) }}" onclick="return open_print_window(this);" class="btn btn-success btn-sm">Print</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                            </tbody>
                            <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Total</th>
                            <th>{{ number_format($logs->sum(function($log){
                                        return $log->selling_price * $log->quantity;
                                    }),2)  }}</th>
                            <th></th>
                            <th>{{ number_format($logs->sum(function($log){
                                        return $log->cost_price * $log->quantity;
                                    }),2)  }}</th>
                            <th></th>
                            <th></th>
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
