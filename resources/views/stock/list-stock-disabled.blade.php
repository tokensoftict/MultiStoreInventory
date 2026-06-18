@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading panel-border">
                        {{ $title }}
                        @if(userCanView('user.group.create'))
                            <span class="tools pull-right">
                                            <a  href="{{ route('stock.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Stock</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(config('app.dont_show_all_product'))
                            <x-search-component placeholder="Search for disabled products.."/>
                        @endif
                        <table class="table table-bordered table-responsive table @if(config('app.dont_show_all_product') === FALSE) convert-data-table  @endif table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Created By</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $stock->name }}
                                        @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                                            <div style="margin-top: 5px;">
                                                @foreach($active_price_categories as $price_category)
                                                    @php
                                                        $stockPrice = $stock->stockPrices->firstWhere('price_category_id', $price_category->id);
                                                        $priceVal = $stockPrice ? $stockPrice->price : 0;
                                                    @endphp
                                                    @if(userCanView('stock.edit'))
                                                        <span class="label label-info price-category-badge" 
                                                              style="cursor: pointer; display: inline-block; margin-bottom: 2px;" 
                                                              data-stock-id="{{ $stock->id }}"
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
                                    <td>{{ $stock->product_category ?  $stock->product_category->name : "No Category" }}</td>
                                    <td>{{ $stock->manufacturer ?  $stock->manufacturer->name : "No Manufacturer" }}</td>
                                    <td>{{ number_format($stock->selling_price,2) }}</td>
                                    <td>{{ number_format($stock->cost_price,2) }}</td>
                                    <td>{{ $stock->user ? $stock->user->name : "" }}</td>
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
                        @if(config('app.dont_show_all_product'))
                            {!! $stocks->links() !!}
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
    <script    src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script    src="{{ asset('bower_components/datatables-tabletools/js/dataTables.tableTools.js') }}"></script>
    <script    src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script    src="{{ asset('bower_components/datatables-colvis/js/dataTables.colVis.js') }}"></script>
    <script    src="{{ asset('bower_components/datatables-responsive/js/dataTables.responsive.js') }}"></script>
    <script    src="{{ asset('bower_components/datatables-scroller/js/dataTables.scroller.js') }}"></script>
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
