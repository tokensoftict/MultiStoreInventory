@extends('layouts.app')

@section('content')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <div class="row" style="margin-top: 12px;">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Log Date</label><br/>
                                    <span class="text-md">{{ str_date($log->log_date) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Log Type</label><br/>
                                    <span class="text-md">{{ $log->log_type }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Department</label><br/>
                                    <span class="text-md">{{ $log->department }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Status</label><br/>
                                    <span class="text-md">{{ number_format($log->total_worth,2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 12px;">
                            <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Selling Price</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach($log->stock_log_items as $item)
                                    @php
                                        $total += ($item->selling_price * $item->quantity);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->stock->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->selling_price,2) }}</td>
                                        <td>{{ number_format(($item->selling_price * $item->quantity),2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>{{ number_format($total,2) }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
