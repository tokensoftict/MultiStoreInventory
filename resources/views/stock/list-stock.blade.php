@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">
@endpush


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading panel-border">
                        {{ $title }}
                        @if(userCanView('stock.create'))
                            <span class="tools pull-right">
                                            <a  href="{{ route('stock.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Stock</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                            <x-search-component placeholder="Search for available products.."/>
                            <table class="table table-bordered table-responsive table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Product Type</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Yard Selling Price</th>
                                <th>Yard Cost Price</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stock->name }}</td>
                                    <td>{{ array_search($stock->type,config('stock_type_name.'.config('app.store'))) }}</td>
                                    <td>{{ $stock->product_category ?  $stock->product_category->name : "No Category" }}</td>
                                    <td>{{ $stock->manufacturer ?  $stock->manufacturer->name : "No Manufacturer" }}</td>
                                    <td>{{ number_format($stock->selling_price,2) }}</td>
                                    <td>{{ number_format($stock->cost_price,2) }}</td>
                                    <td>{{ number_format($stock->yard_selling_price,2) }}</td>
                                    <td>{{ number_format($stock->yard_cost_price,2) }}</td>
                                    <td>{{ $stock->last_updated ? $stock->last_updated->name  : ""}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('stock.edit'))
                                                    <li><a href="{{ route('stock.edit',$stock->id) }}">Edit</a></li>
                                                @endif
                                                @if(userCanView('stock.toggle'))
                                                        <li><a href="{{ route('stock.toggle',$stock->id) }}">{{ $stock->status == 0 ? 'Enabled' : 'Disabled' }}</a></li>
                                                @endif
                                                    @if(userCanView('stock.stock_report'))
                                                        <li><a href="{{ route('stock.stock_report',$stock->id) }}">Product Report</a></li>
                                                    @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                            {!! $stocks->appends(request()->input())->links() !!}

                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection


@push('js')
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>

    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
@endpush
