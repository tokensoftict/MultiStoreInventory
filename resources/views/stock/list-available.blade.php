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
                        @if(config('app.dont_show_all_product'))
                            <x-search-component placeholder="Search for available products.."/>
                        @endif

                        @php
                            $isMultiStore = \App\Services\StockUnitService::isMulti();
                            $activeQtyUnits = $isMultiStore ? \App\Models\QuantityUnit::activeUnits() : collect();
                        @endphp
                        <table class="table table-bordered table-responsive table-striped @if(config('app.dont_show_all_product') === FALSE) convert-data-table  @endif" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Product Type</th>
                                @if(!empty($isMultiStore))
                                <th>{{ $store->name }} Quantity</th>
                                @foreach($activeQtyUnits as $aqu)
                                    <th>{{ $aqu->name }} Selling Price</th>
                                @endforeach
                                @else
                                <th>{{ $store->name }} Quantity</th>
                                <th>{{ $store->name }} Yards Quantity</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Yard Selling Price</th>
                                <th>Yard Cost Price</th>
                                @endif
                                @if(!empty($isMultiStore))
                                <th>Total (Cost / Selling)</th>
                                @else
                                <th>Total Cost</th>
                                <th>Total Selling</th>
                                @endif
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($batches as $batch)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $batch->stock->name }}
                                        @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                                            <div style="margin-top: 5px;">
                                                @foreach($active_price_categories as $price_category)
                                                    @php
                                                        $stockPrice = $batch->stock->stockPrices->firstWhere('price_category_id', $price_category->id);
                                                        $priceVal = $stockPrice ? $stockPrice->price : 0;
                                                    @endphp
                                                    @if(userCanView('stock.edit'))
                                                        <span class="label label-info price-category-badge" 
                                                              style="cursor: pointer; display: inline-block; margin-bottom: 2px;" 
                                                              data-stock-id="{{ $batch->stock->id }}"
                                                              data-price-category-id="{{ $price_category->id }}"
                                                              data-price-category-name="{{ $price_category->name }}"
                                                              data-price="{{ $priceVal }}">
                                                            {{ $price_category->name }}: {{ number_format($priceVal, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="label label-info" 
                                                              style="display: inline-block; margin-bottom: 2px;">
                                                            {{ $price_category->name }}: {{ number_format($priceVal, 2) }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $batch->stock->type }}</td>
                                    @if($isMultiStore)
                                        @php $mlUnits = \App\Services\StockUnitService::hasUnits($batch->stock_id) ? \App\Services\StockUnitService::unitsForUi($batch->stock_id) : null; @endphp
                                        <td>
                                            @if($mlUnits)
                                                {{ \App\Services\StockUnitService::breakdownLabel($batch->stock_id, $batch->stock->available_yard_quantity) }} <!-- <small class="text-muted">({{ $batch->stock->available_yard_quantity + 0 }} pcs)</small> -->
                                            @else
                                                {{ $batch->stock->available_quantity }} / {{ $batch->stock->available_yard_quantity }}
                                            @endif
                                        </td>
                                        @php $unitByLevel = []; if ($mlUnits) { foreach ($mlUnits as $uu) { $unitByLevel[$uu['level']] = $uu; } } @endphp
                                        @foreach($activeQtyUnits as $aqu)
                                            <td>@php $pu = $unitByLevel[$aqu->level] ?? null; @endphp@if($pu){{ number_format($pu['selling_price'],2) }}<br><small class="text-muted">cost {{ number_format($pu['cost_price'],2) }}</small>@else - @endif</td>
                                        @endforeach
                                    @else
                                    <td>{{ $batch->stock->available_quantity }}</td>
                                    <td>@if(\App\Services\StockUnitService::isMulti() && \App\Services\StockUnitService::hasUnits($batch->stock_id)){{ \App\Services\StockUnitService::breakdownLabel($batch->stock_id, $batch->stock->available_yard_quantity) }} <small class="text-muted">({{ $batch->stock->available_yard_quantity + 0 }} pcs)</small>@else{{ $batch->stock->available_yard_quantity }}@endif</td>
                                    <td>{{ number_format($batch->stock->selling_price,2) }}</td>
                                    <td>{{ number_format($batch->stock->cost_price,2) }}</td>
                                    <td>{{ number_format($batch->stock->yard_selling_price,2) }}</td>
                                    <td>{{ number_format($batch->stock->yard_cost_price,2) }}</td>
                                    @endif
                                    @if($isMultiStore)
                                        <td>
                                            @if($mlUnits)
                                                @php $mlVal = \App\Services\StockUnitService::valuation($batch->stock_id, $batch->stock->available_yard_quantity); @endphp
                                                @foreach(\App\Services\StockUnitService::valuationBreakdown($batch->stock_id, $batch->stock->available_yard_quantity) as $vr)
                                                    <div>{{ $vr['name'] }} ({{ $vr['quantity'] + 0 }}): {{ number_format($vr['cost'],2) }} / {{ number_format($vr['selling'],2) }}</div>
                                                @endforeach
                                                <div style="border-top:1px solid #ddd;"><strong>Total: {{ number_format($mlVal[0],2) }} / {{ number_format($mlVal[1],2) }}</strong></div>
                                            @else
                                                {{ number_format(($batch->stock->cost_price * $batch->stock->available_quantity),2) }} / {{ number_format(($batch->stock->selling_price * $batch->stock->available_quantity),2) }}
                                            @endif
                                        </td>
                                    @else
                                    <td>@if(\App\Services\StockUnitService::isMulti() && \App\Services\StockUnitService::hasUnits($batch->stock_id)){{ number_format(\App\Services\StockUnitService::valuation($batch->stock_id, $batch->stock->available_yard_quantity)[0],2) }}@else{{ number_format(($batch->stock->cost_price * $batch->stock->available_quantity),2) }}@endif</td>
                                    <td>@if(\App\Services\StockUnitService::isMulti() && \App\Services\StockUnitService::hasUnits($batch->stock_id)){{ number_format(\App\Services\StockUnitService::valuation($batch->stock_id, $batch->stock->available_yard_quantity)[1],2) }}@else{{ number_format(($batch->stock->selling_price * $batch->stock->available_quantity),2) }}@endif</td>
                                    @endif
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
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                @if(!empty($isMultiStore))
                                    @for($i = 0; $i < count($activeQtyUnits); $i++)
                                        <td></td>
                                    @endfor
                                @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                @endif
                                @if(!empty($isMultiStore))
                                @php
                                    // overall worth per unit level (Carton / Packet / Piece) across all listed products
                                    $mlTotals = [];
                                    $mlGrandCost = 0;
                                    $mlGrandSell = 0;
                                    foreach ($batches as $tb) {
                                        if (!\App\Services\StockUnitService::hasUnits($tb->stock_id)) {
                                            $mlGrandCost += $tb->stock->cost_price * $tb->stock->available_quantity;
                                            $mlGrandSell += $tb->stock->selling_price * $tb->stock->available_quantity;
                                            continue;
                                        }
                                        foreach (\App\Services\StockUnitService::valuationBreakdown($tb->stock_id, $tb->stock->available_yard_quantity) as $tr) {
                                            $lv = $tr['level'];
                                            if (!isset($mlTotals[$lv])) { $mlTotals[$lv] = ['name' => $tr['name'], 'quantity' => 0, 'cost' => 0, 'selling' => 0]; }
                                            $mlTotals[$lv]['quantity'] += $tr['quantity'];
                                            $mlTotals[$lv]['cost'] += $tr['cost'];
                                            $mlTotals[$lv]['selling'] += $tr['selling'];
                                            $mlGrandCost += $tr['cost'];
                                            $mlGrandSell += $tr['selling'];
                                        }
                                    }
                                    ksort($mlTotals);
                                @endphp
                                <th>
                                    @foreach($mlTotals as $mt)
                                        <div>{{ $mt['name'] }} ({{ $mt['quantity'] + 0 }}): {{ money($mt['cost']) }} / {{ money($mt['selling']) }}</div>
                                    @endforeach
                                    <div style="border-top:1px solid #ddd;">Total: {{ money($mlGrandCost) }} / {{ money($mlGrandSell) }}</div>
                                </th>
                                @else
                                <th>{{ money($total_available_cost) }}</th>
                                <th>{{ money($total_available_selling) }}</th>
                                @endif
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                        @if(config('app.dont_show_all_product'))
                            {!! $stocks->appends(request()->input())->links() !!}
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updatePriceModal" tabindex="-1" role="dialog" aria-labelledby="updatePriceModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="updatePriceModalLabel">Update Price</h4>
                </div>
                <form id="updatePriceForm">
                    <div class="modal-body">
                        <input type="hidden" id="modal_stock_id">
                        <input type="hidden" id="modal_price_category_id">
                        <div class="form-group">
                            <label for="modal_price" id="modal_price_label">Price</label>
                            <input type="number" step="0.00000001" class="form-control" id="modal_price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>

    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.price-category-badge', function() {
                var badge = $(this);
                var stockId = badge.data('stock-id');
                var categoryId = badge.data('price-category-id');
                var categoryName = badge.data('price-category-name');
                var currentPrice = badge.data('price');
                
                $('#modal_stock_id').val(stockId);
                $('#modal_price_category_id').val(categoryId);
                $('#modal_price_label').text("Enter price for " + categoryName);
                $('#modal_price').val(currentPrice);
                $('#updatePriceModal').modal('show');
            });

            $('#updatePriceForm').on('submit', function(e) {
                e.preventDefault();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.attr('disabled', 'disabled');
                
                var stockId = $('#modal_stock_id').val();
                var categoryId = $('#modal_price_category_id').val();
                var newPrice = $('#modal_price').val();
                
                $.ajax({
                    url: "{{ route('update_price_category_price') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        stock_id: stockId,
                        price_category_id: categoryId,
                        price: parseFloat(newPrice)
                    },
                    success: function(response) {
                        if (response.status) {
                            var badge = $('.price-category-badge[data-stock-id="' + stockId + '"][data-price-category-id="' + categoryId + '"]');
                            badge.data('price', newPrice);
                            badge.text(badge.data('price-category-name') + ": " + parseFloat(newPrice).toFixed(2));
                            $('#updatePriceModal').modal('hide');
                        } else {
                            alert(response.message || "Failed to update price");
                        }
                    },
                    error: function() {
                        alert("An error occurred. Please try again.");
                    },
                    complete: function() {
                        submitBtn.removeAttr('disabled');
                    }
                });
            });

            $('#updatePriceModal').on('hidden.bs.modal', function () {
                $(this).find('button[type="submit"]').removeAttr('disabled');
            });
        });
    </script>
@endpush

