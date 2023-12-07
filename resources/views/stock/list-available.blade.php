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
                        <x-search-component placeholder="Search for available products.."/>
                        <table class="table table-bordered table-responsive table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Product Type</th>
                                <th>{{ $store->name }} Quantity</th>
                                    <th>{{ $store->name }} Yards Quantity</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Yard Selling Price</th>
                                <th>Yard Cost Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $batch->stock->name }}</td>
                                    <td>{{ $batch->stock->type }}</td>
                                    <td>{{ $batch->stock->available_quantity }}</td>
                                        <td>{{ $batch->stock->available_yard_quantity }}</td>

                                    <td>{{ number_format($batch->stock->selling_price,2) }}</td>
                                    <td>{{ number_format($batch->stock->cost_price,2) }}</td>

                                    <td>{{ number_format($batch->stock->yard_selling_price,2) }}</td>
                                    <td>{{ number_format($batch->stock->yard_cost_price,2) }}</td>


                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('stock.edit'))
                                                    <li><a href="{{ route('stock.edit',$batch->stock->id) }}">Edit</a></li>
                                                @endif
                                                @if(userCanView('stock.toggle'))
                                                    <li><a href="{{ route('stock.toggle',$batch->stock->id) }}">{{ $batch->stock->status == 0 ? 'Enabled' : 'Disabled' }}</a></li>
                                                @endif
                                                    @if(userCanView('stock.stock_report'))
                                                        <li><a href="{{ route('stock.stock_report',$batch->stock->id) }}">Product Report</a></li>
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
