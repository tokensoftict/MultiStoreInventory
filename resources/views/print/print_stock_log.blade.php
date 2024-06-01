@extends('layouts.app-pdf')

@section('content')
    <div class="">
        <br/><br/>
        <div class="clear">
            <div class="pull-left" style="width: 30%;">
                <img style="max-height:60px;float: right;margin-top: -10px" src="{{ public_path("img/". $store->logo) }}" alt='Logo'><br/>  <br/>
                <h5>Invoice Number</h5>
                <h4>{{ $log->invoice_number }}</h4>
                <br/>
                <h5>Store</h5>
                <h4>{{ $log->warehousestore->name }}</h4>
                <br/>
                <h5>Product Type</h5>
                <h4>{{ array_search($log->product_type,config('stock_type_name.'.config('app.store'))) }}</h4>
                <h5>Usage Type</h5>
                <h4>{{ $log->stock_log_usages_type->name ?? ""  }}</h4>
                <h5>User</h5>
                <h4>{{ $log->user->name }}</h4>
            </div>


            <div class="clear">
                <br/>
                <br/>
                <h4>Item Log</h4>

                <table style="width: 100%; font-size: 14px" class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th class="text-left">Stock</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Product Type</th>
                        <th class="text-right">Usage Type</th>
                        <th class="text-right">Selling Price</th>
                        <th class="text-right">Cost Price</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $log->stock->name }}</td>
                            <td>{{ $log->quantity }}</td>
                            <td>{{ array_search($log->product_type,config('stock_type_name.'.config('app.store'))) }}</td>
                            <td>{{ $log->stock_log_usages_type->name ?? "" }}</td>
                            <td>{{ number_format($log->selling_price,2) }}</td>
                            <td>{{ number_format($log->cost_price,2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="clear">
                    <div class="pull-left">
                        {!! softwareStampWithDate() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_js_scripts_files')
            <script>
                window.onload = function(){
                    window.print();
                    setTimeout(function(){
                        window.close();
                    },800)
                }
            </script>
@endpush
