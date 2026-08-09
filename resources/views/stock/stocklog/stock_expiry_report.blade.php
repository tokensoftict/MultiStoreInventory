@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">
    <style>
        .badge-expired {
            background-color: #d9534f;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .badge-near-expiry {
            background-color: #f0ad4e;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading panel-border">
                        <span class="text-primary"><i class="fa fa-clock-o"></i> {{ $title }}</span>
                        <span class="pull-right text-muted" style="font-size: 13px; font-weight: normal;">
                            Near Expiry Threshold: <strong>{{ $near_expiry_days }} Days</strong>
                            <a href="{{ route('store_settings.view') }}" class="btn btn-xs btn-default ml-2" title="Change threshold in Store Settings"><i class="fa fa-cog"></i> Settings</a>
                        </span>
                    </header>
                    <div class="panel-body">

                        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                            <li role="presentation" class="active">
                                <a href="#near_expiry" aria-controls="near_expiry" role="tab" data-toggle="tab">
                                    <i class="fa fa-exclamation-triangle text-warning"></i> Expiring Soon (Next {{ $near_expiry_days }} Days)
                                    <span class="badge badge-warning">{{ $near_expiry->count() }}</span>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#expired" aria-controls="expired" role="tab" data-toggle="tab">
                                    <i class="fa fa-times-circle text-danger"></i> Expired Stock
                                    <span class="badge badge-danger">{{ $expired->count() }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Expiring Soon Tab -->
                            <div role="tabpanel" class="tab-pane active" id="near_expiry">
                                <table class="table table-bordered table-responsive table-striped convert-data-table" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Expiry Date</th>
                                        <th>Days Remaining</th>
                                        <th>Available Packed Qty</th>
                                        <th>Available Yard Qty</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($near_expiry as $batch)
                                        @php
                                            $daysLeft = $batch->expiry_date ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date), false) : 0;
                                            $packedQty = $batch->{ getActiveStore()->packed_column };
                                            $yardQty = $batch->{ getActiveStore()->yard_column };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ optional($batch->stock)->name }}</strong></td>
                                            <td>{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td><span class="badge-near-expiry">{{ $daysLeft }} day(s) left</span></td>
                                            <td>{{ $packedQty }}</td>
                                            <td>{{ $yardQty }}</td>
                                            <td><span class="label label-warning">Expiring Soon</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button">Action <span class="caret"></span></button>
                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                        @if(userCanView('stock.edit'))
                                                            <li><a href="{{ route('stock.edit', $batch->stock_id) }}"><i class="fa fa-edit"></i> Edit Product</a></li>
                                                        @endif
                                                        @if(userCanView('stock.quick'))
                                                            <li><a href="{{ route('stock.quick') }}?select_stock={{ $batch->stock_id }}"><i class="fa fa-refresh"></i> Quick Adjust Qty / Expiry</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Expired Tab -->
                            <div role="tabpanel" class="tab-pane" id="expired">
                                <table class="table table-bordered table-responsive table-striped convert-data-table" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Expiry Date</th>
                                        <th>Days Expired</th>
                                        <th>Available Packed Qty</th>
                                        <th>Available Yard Qty</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($expired as $batch)
                                        @php
                                            $daysExpired = $batch->expiry_date ? abs(\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date), false)) : 0;
                                            $packedQty = $batch->{ getActiveStore()->packed_column };
                                            $yardQty = $batch->{ getActiveStore()->yard_column };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ optional($batch->stock)->name }}</strong></td>
                                            <td>{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td><span class="badge-expired">{{ $daysExpired }} day(s) ago</span></td>
                                            <td>{{ $packedQty }}</td>
                                            <td>{{ $yardQty }}</td>
                                            <td><span class="label label-danger">Expired</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button">Action <span class="caret"></span></button>
                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                        @if(userCanView('stock.edit'))
                                                            <li><a href="{{ route('stock.edit', $batch->stock_id) }}"><i class="fa fa-edit"></i> Edit Product</a></li>
                                                        @endif
                                                        @if(userCanView('stock.quick'))
                                                            <li><a href="{{ route('stock.quick') }}?select_stock={{ $batch->stock_id }}"><i class="fa fa-refresh"></i> Quick Adjust Qty / Expiry</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
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
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
@endpush
