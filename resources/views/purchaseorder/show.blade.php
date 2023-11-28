@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            @if(session('success'))
                                {!! alert_success(session('success')) !!}
                            @elseif(session('error'))
                                {!! alert_error(session('error')) !!}
                            @endif
                        </div>
                        <div class="col-xs-12">
                            <div class="invoice-title">
                                <h2>Purchase Order</h2><h3 class="pull-right"> # {{ $porder->id }}</h3>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>{{ $settings->name }}:</strong><br>
                                        {{ $settings->first_address  }}<br>
                                        Created By : {{ $porder->created_user->name }}<br>
                                        Date : {{ str_date($porder->date_created) }}<br>
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right"><strong>Supplier:</strong><br>
                                    <address>
                                        {{ $porder->supplier->name }}<br>
                                        {{ $porder->supplier->address }}<br>
                                        {{ $porder->supplier->email }}<br>
                                        {{ $porder->supplier->phonenumber }}
                                    </address>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="panel-title"><strong>Purchase Order Items</strong></h3>
                                </div>
                                <div class="col-md-12">
                                    <br/>
                                    <div class="table-responsive">
                                        <table class="table table-condensed" id="stock-list">
                                            <thead>
                                            <tr>
                                                <td><strong>Name</strong></td>
                                                <td class="text-center"><strong>Quantity</strong></td>
                                                <td class="text-center"><strong>Cost Price</strong></td>
                                                <td class="text-right"><strong>Total Cost Price</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php
                                                $alltotal = 0;
                                            @endphp
                                            @foreach($porder->purchase_order_items as $item)
                                                @php
                                                    $alltotal+= $total = $item->qty * $item->cost_price;
                                                @endphp
                                                <tr>
                                                    <td>{{ $item->stock()->get()->first()->name }}</td>
                                                    <td class="text-center">{{ $item->qty }}</td>
                                                    <td class="text-center">{{ number_format($item->cost_price,2) }}</td>
                                                    <td class="text-right">{{ number_format($total,2) }}</td>
                                                </tr>

                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right">Total :</th>
                                                <th class="text-right">{{ number_format($alltotal,2) }}</th>
                                            </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
