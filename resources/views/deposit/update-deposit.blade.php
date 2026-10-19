@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <style>

        input {
            margin-bottom: 0 !important;
        }

        ul.bs-autocomplete-menu {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-height: 460px;
            overflow: auto;
            z-index: 9999;
            border: 1px solid #eeeeee;
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0px 1px 6px 1px rgba(0, 0, 0, 0.4);
        }

        ul.bs-autocomplete-menu a {
            font-weight: normal;
            color: #333333;
        }

        .ui-helper-hidden-accessible {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        .ui-state-active,
        .ui-state-focus {
            color: #23527c;
            background-color: #eeeeee;
        }

        .bs-autocomplete-feedback {
            width: 1.5em;
            height: 1.5em;
            overflow: hidden;
            margin-top: .5em;
            margin-right: .5em;
        }
    </style>
    <style>
        /** SPINNER CREATION **/
        .modal-dialog-center {
            height: 100% !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
        }
        .modal-content {
            margin: 0 auto;
            border: none !important;
            box-shadow: none !important;
        }
        .mloader {
            position: relative;
            text-align: center;
            margin: 15px auto 35px auto;
            z-index: 9999;
            display: block;
            width: 80px;
            height: 80px;
            border: 10px solid rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            border-top-color: #000;
            animation: spin 0.5s ease-in-out infinite;
            -webkit-animation: spin 0.5s ease-in-out infinite;
        }
        @keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }
        @-webkit-keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }
        /** MODAL STYLING **/
        .modal-content {
            border-radius: 0px;
            box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
        }
        .modal-backdrop.show {
            opacity: 0.75;
        }
        .loader-txt p {
            font-size: 13px;
            color: #666;
        }
        .loader-txt p small {
            font-size: 11.5px;
            color: #999;
        }
        #output {
            padding: 25px 15px;
            background: #222;
            border: 1px solid #222;
            max-width: 350px;
            margin: 35px auto;
            font-family: 'Roboto', sans-serif !important;
        }
        #output p.subtle {
            color: #555;
            font-style: italic;
            font-family: 'Roboto', sans-serif !important;
        }
        #output h4 {
            font-weight: 300 !important;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif !important;
        }
        #output p {
            font-family: 'Roboto', sans-serif !important;
            font-size: 0.9em;
        }
        #output p b {
            text-transform: uppercase;
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')

    <div class="ui-container">
        <form id="mainform">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-8">
                            <section class="panel">
                                <section class="panel-body panel-border">
                                    <div class="input-group srch-lg" style="width: 100%" >
                                        <input type="text"  class="form-control bs-autocomplete" id="ac-demo" placeholder="Search for Product or Scan Barcode...">
                                    </div>
                                </section>
                            </section>
                            <section class="panel" style="height: 73vh;overflow: scroll">
                                <section class="panel-body panel-border">
                                    <table class="table table-condensed table-bordered" style="font-size: 12px">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th style="width: 35%;">Name</th>
                                            <th>Quantity</th>
                                            <th style="width: 15%;">Type</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="appender">
                                        @foreach($deposit->invoice_items as $items)
@php
    $isMulti = \App\Services\StockUnitService::isMulti();
    $ml = ($isMulti && \App\Services\StockUnitService::hasUnits($items->stock_id))
        ? \App\Services\StockUnitService::unitsForUi($items->stock_id, (float)$items->stock->available_yard_quantity + (float)$items->quantity, !empty($items->unit_id) ? [(int)$items->unit_id] : [])
        : null;
    $selUnit = null;
    if ($ml) {
        if (!empty($items->unit_id)) {
            $selUnit = collect($ml)->firstWhere('id', (int)$items->unit_id);
        }
        if (!$selUnit && (count($ml) === 1 || \App\Models\QuantityUnit::isOnlyPiecesEnabled())) {
            $selUnit = collect($ml)->last();
        }
    }
    $itemPrice = !empty($items->unit_price) ? $items->unit_price : ($selUnit ? $selUnit['selling_price'] : $items->selling_price);
    $itemQty = !empty($items->unit_quantity) ? $items->unit_quantity : $items->quantity;
@endphp
@if($ml && $selUnit)
    <tr style="cursor: pointer" id="product_{{ $items->stock->id }}_{{ getActiveStore()->yard_column }}__u{{ $selUnit['id'] }}">
        <th class="text-center"><input data-image="{{ $items->stock->image }}" name="picture" class="picture" value="1" type="radio"></th>
        <th>{{ $items->stock->name }}<div id="error_{{ $items->stock->id }}" class="errors alert alert-danger" style="display:none;"></div></th>
        <td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number" data-invoice-item-id="{{ $items->id }}" data-id="{{ $items->stock->id }}" data-cost-price="{{ $selUnit['cost_price'] }}" data-price="{{ $itemPrice }}" style="width:100px;display: block;" required="" max="{{ $selUnit['available'] }}" min="0.01" step="0.01" type="number" value="{{ $itemQty }}"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div></td>
        <td>
            <select class="form-control product_type">
                @foreach($ml as $u)
                    <option value="{{ getActiveStore()->yard_column }}__u{{ $u['id'] }}" {{ (int)$u['id'] === (int)$selUnit['id'] ? 'selected' : '' }} data-unit-id="{{ $u['id'] }}" data-cost-price="{{ $u['cost_price'] }}" data-price="{{ $u['selling_price'] }}" data-av-qty="{{ $u['available'] }}">{{ $u['name'] }} ({{ $u['available'] }} avail.)</option>
                @endforeach
            </select>
        </td>
        <th class="text-right item_price">
            @if($settings['allow_edit_price'] == "Yes")
                <input type="text" step="0.00000001" class="item_text_price form-control" value="{{ $itemPrice }}">
            @else
                <span class="item_text_price form-control" value="{{ $itemPrice }}">{{ money($itemPrice) }}</span>
            @endif
        </th>
        <th class="text-right item_total">{{ number_format($items->total_selling_price, 2) }}</th>
        <td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td>
    </tr>
@else
    <tr style="cursor: pointer" id="product_{{ $items->stock->id }}_{{ $items->store }}">
        <th  class="text-center"><input data-image="{{ $items->stock->image }}" name="picture" class="picture" value="1" type="radio"></th>
        <th>{{ $items->stock->name }}<div id="error_{{ $items->stock->id }}" class="errors alert alert-danger" style="display:none;"></div></th>
        <td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number" data-invoice-item-id="{{ $items->id }}"  data-id="{{ $items->stock->id }}" data-cost-price="{{  $items->cost_price }}" data-price="{{ $items->selling_price }}" style="width:100px;display: block;" required="" max="{{ $items->store == "quantity" ? $items->stock->available_quantity + $items->quantity  :  $items->stock->available_yard_quantity+ $items->quantity }}" min="0.01" step="0.01" type="number" value="{{ $items->quantity }}"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div></td>
        <td>
            <select class="form-control product_type {{ $items->stock->type }}">
                @if($items->stock->type == "PACKED")
                    @if($items->store == getActiveStore()->packed_column)
                        <option value="{{ getActiveStore()->packed_column }}" {{ $items->store == getActiveStore()->packed_column ? "selected" : "" }} data-cost-price="{{  $items->stock->cost_price }}" data-price="{{ $items->stock->selling_price }}" data-av-qty="{{ $items->stock->available_quantity+ $items->quantity }}">Packed</option>
                        <option value="{{ getActiveStore()->yard_column }}" {{ $items->store == getActiveStore()->yard_column ? "selected" : "" }} data-cost-price="{{  $items->stock->yard_cost_price }}" data-price="{{ $items->stock->yard_selling_price }}" data-av-qty="{{ $items->stock->available_yard_quantity }}">Pieces / Yards</option>
                    @else
                        <option value="{{ getActiveStore()->packed_column }}" data-cost-price="{{  $items->stock->cost_price }}" data-price="{{ $items->stock->selling_price }}" data-av-qty="{{ $items->stock->available_quantity }}">Packed</option>
                        <option value="{{ getActiveStore()->yard_column }}" data-cost-price="{{  $items->stock->yard_cost_price }}" selected data-price="{{ $items->stock->yard_selling_price }}" data-av-qty="{{ $items->stock->available_yard_quantity+ $items->quantity }}">Pieces / Yards</option>
                    @endif
                @else
                    <option value="{{ getActiveStore()->packed_column }}" {{ $items->store == getActiveStore()->packed_column ? "selected" : "" }} data-cost-price="{{  $items->stock->cost_price }}" data-price="{{ $items->stock->selling_price }}" data-av-qty="{{ $items->stock->available_quantity+ $items->quantity }}">Packed</option>
                @endif
            </select>
        </td>
        <th class="text-right item_price">
            @if($settings['allow_edit_price'] == "Yes")
                <input type="text" step="0.00000001" class="item_text_price form-control"  value="{{ $items->selling_price }}">
            @else
                <span  class="item_text_price form-control"  value="{{ $items->selling_price }}">{{ money($items->selling_price) }}</span>
            @endif
        </th>
        <th class="text-right item_total">{{ number_format($items->total_selling_price,2) }}</th>
        <td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td>
    </tr>
@endif
@endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-center"></th>
                                            <th class="text-right" colspan="2">Sub Total</th>
                                            <th class="text-right"  colspan="2" id="sub_total">{{ number_format($deposit->sub_total,2) }}</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-center" ></th>
                                            <th class="text-center" ></th>
                                            <th class="text-right" colspan="2">Total</th>
                                            <th class="text-right total_invoice" colspan="2" style="font-size: 15px;">{{ number_format($deposit->sub_total,2) }}</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </section>
                            </section>
                        </div>
                        <div class="col-sm-4">
                            @if(userCanView('deposit.update'))
                                <section class="panel">
                                    <section class="panel-body panel-border text-center">
                                        <div class="col-sm-10 col-lg-offset-1">
                                            <div class="row mtop-10">
                                                @if(userCanView('deposit.update'))
                                                    <button type="button"  data-status="DISCOUNT" class="btn  btn-dark btn-lg" onclick="return ProcessInvoice(this);">Update Deposit</button>
                                                    <br/><br/>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                </section>
                            @endif
                            <section class="panel">
                                <header class="panel-heading panel-border total_invoice text-center" style="font-size: 25px">{{ number_format($deposit->sub_total,2) }}</header>
                            </section>
                            <section class="panel">
                                <header class="panel-heading panel-border">Deposit Info.</header>
                                <section class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                @if(userCanView('deposit.allow_user_to_change_deposit_date'))
                                                    <input value="{{ date('Y-m-d',strtotime($deposit->deposit_date)) }}" data-min-view="2" data-date-format="yyyy-mm-dd" class="form-control datepicker js-datepicker" id="invoice_date" placeholder="Deposit Date" type="text">
                                                @else
                                                    <input type="hidden" value="{{ date('Y-m-d',strtotime($deposit->deposit_date)) }}"  id="invoice_date">
                                                    <span class="form-control">{{ date('Y-m-d',strtotime($deposit->deposit_date)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Customer Name</label>
                                                <select class="form-control  select-customer"  name="customer" id="customer_id">
                                                    @foreach($customers as $customer)
                                                        <option {{ $deposit->customer_id == $customer->id ? "selected" : "" }} value="{{ $customer->id }}">{{ $customer->firstname }} {{ $customer->lastname }}</option>
                                                    @endforeach
                                                </select>
                                                <a href="#" data-toggle="modal" data-target="#newCustomer" class="text-success" style="display: block;text-align: center">Add New Customer</a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <img id="imageThumb" src="{{ asset('assets/products.jpg') }}" class="img-thumbnail">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Deposit Number</label>
                                                <input class="form-control" id="invoice_paper_number" value="{{ $deposit->invoice_paper_number }}"  placeholder="Deposit Number" type="text">
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </section>

                        </div>


                    </div>

                </div>

            </div>
        </form>
    </div>

    <div class="modal fade" id="successInvoice" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
        <div class="modal-dialog modal-dialog-center modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body text-center" id="success_div">

                </div>
            </div>
        </div>
    </div>

    @if(userCanView('customer.store'))
        <div class="modal fade" id="newCustomer" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">New Customer</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" id="error_reg" style="display: none;"></div>
                        <form id="new_customer_form" action="{{ route('customer.store') }}?ajax=true"  enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text"  required  class="form-control" name="firstname" placeholder="First Name"/>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text"  required  class="form-control" name="lastname" placeholder="Last Name"/>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text"    class="form-control" name="email" placeholder="Email Address"/>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" required  class="form-control" name="phone_number" placeholder="Phone Number"/>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" placeholder="Address" name="address"></textarea>
                            </div>
                            <div>
                                <button type="submit" id="add_customer" class="btn btn-success btn-sm">Add Customer</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection


@push('js')
    <script>
        productfindurl = "{{ route('findstock') }}"
    </script>
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
    <script  type='text/javascript' src="{{asset('assets/js/autocomplete.js?v='.mt_rand())}}"></script>


    <script>

        // Multi-level quantity helpers (only used when a product carries units)
        const BASE_YARD_COLUMN = "{{ getActiveStore()->yard_column }}";
        function unitOptValue(u) { return BASE_YARD_COLUMN + '__u' + u.id; }
        function defaultUnit(stock) { return stock.units[stock.units.length - 1]; } // Piece (smallest)

        function bindAllTr(){
            $('#appender').find('tr').off('click');
            $('#appender').find('tr').on('click',function(){
                $('.picture').prop('checked',false);
                $(this).find('.picture').prop('checked', true);
                $('#imageThumb').attr('src',$(this).find('.picture').attr('data-image'));
            });
        }

        function appendToTable(data){
            let packedQty = parseInt(data.stock.available_quantity) || 0;
            let yardQty   = parseInt(data.stock.available_yard_quantity) || 0;
            if (packedQty <= 0 && yardQty <= 0) {
                toastr.error('"' + data.stock.name + '" has no available quantity.', 'Out of Stock');
                return;
            }

            let defaultType = "{{ getActiveStore()->packed_column }}";
            if (data.stock.type != "Normal" && parseInt(data.stock.available_quantity) == 0 && parseInt(data.stock.available_yard_quantity) > 0) {
                defaultType = "{{ getActiveStore()->yard_column }}";
            }
            if (data.stock.multi_level && data.stock.units && data.stock.units.length) {
                let onlyPieces = (data.stock.units.length === 1) || data.stock.only_pieces;
                if (onlyPieces) {
                    defaultType = unitOptValue(defaultUnit(data.stock));
                } else {
                    defaultType = "";
                }
            }

            if (defaultType === "") {
                // If a pending row already exists for this product without a selected unit, prompt the user
                var pendingRow = $('#appender').find('.input-number[data-id="' + data.stock.id + '"]').filter(function () {
                    return !$(this).closest('tr').find('.product_type').val();
                });
                if (pendingRow.length > 0) {
                    alert('Please select a unit for ' + data.stock.name + ' in the cart before adding another.');
                    pendingRow.closest('tr').find('.product_type').focus();
                    return;
                }
            }

            var existingInput = defaultType === "" ? $() : $('#appender').find('.input-number[data-id="' + data.stock.id + '"]').filter(function () {
                return $(this).closest('tr').find('.product_type').val() === defaultType;
            });

            if (existingInput.length > 0) {
                var currentVal = parseFloat(existingInput.val());
                var maxVal = parseFloat(existingInput.attr('max'));
                if (currentVal < maxVal) {
                    existingInput.val(currentVal + 1).change();
                } else {
                    alert('Not enough quantity to add more of ' + data.stock.name);
                }
            } else {
                $('#appender').prepend(cartTemplate(data));
                $('.picture').prop('checked',false);
                var trId = defaultType === "" ? ('product_' + data.stock.id + '_pending_' + Date.now()) : ('product_' + data.stock.id + '_' + defaultType);
                $('#appender').find('tr').first().attr('id', trId);
                $('#' + trId).find('.picture').prop('checked', true);
                $('#imageThumb').attr('src', $('#' + trId).find('.picture').attr('data-image'));
                bindIncrement();
                bindAllTr();
                bindproductType();
                calculateTotal();
            }
        }


        function bindproductType(){
            $('.product_type').off('change');
            $('.product_type').on('change',function(){
                const price = $(this).parent().parent().find('.input-number');
                const td_price = $(this).parent().parent().find('.item_price');
                const newType = $(this).val();
                if (!newType) return;
                const stockId = price.attr('data-id');
                const tr = $(this).closest('tr');

                // Check if another row with same stockId and newType exists
                var duplicateInput = $('#appender').find('.input-number[data-id="' + stockId + '"]').filter(function () {
                    return $(this).closest('tr')[0] !== tr[0] && $(this).closest('tr').find('.product_type').val() === newType;
                });

                if (duplicateInput.length > 0) {
                    var currentVal = parseFloat(price.val());
                    var duplicateVal = parseFloat(duplicateInput.val());
                    var duplicateMax = parseFloat(duplicateInput.attr('max'));
                    if (duplicateVal + currentVal <= duplicateMax) {
                        duplicateInput.val(duplicateVal + currentVal).change();
                        tr.remove();
                        calculateTotal();
                        return;
                    } else {
                        alert('Merging would exceed the available stock limit for this unit. Cannot change unit.');
                        $(this).val('');
                        return;
                    }
                }

                price.attr('max', $('option:selected', this).attr('data-av-qty'));
                price.attr('data-price', $('option:selected', this).attr('data-price'));
                price.attr('data-cost-price', $('option:selected', this).attr('data-cost-price'));
                @if($settings['allow_edit_price'] == "Yes")
                td_price.html('<input type="text" step="0.00000001" class="item_text_price form-control" value="'+parseFloat($('option:selected', this).attr('data-price')).toFixed(2)+'"/>');
                @else
                td_price.html('<span class="item_text_price form-control" value="'+$('option:selected', this).attr('data-price')+'">'+formatMoney($('option:selected', this).attr('data-price'))+'</span>');
                @endif
                tr.attr('id', 'product_' + stockId + '_' + newType);
                bindproductType();
                bindIncrement();
                calculateTotal();
            });
        }

        function bindIncrement(){
            let qty_before = 1;

            $('.item_text_price').off('keyup input');
            $('.item_text_price').on('keyup input',function(){
                const textb = $(this).parent().parent().find('.input-number');
                let val = parseFloat($(this).val()) || 0;
                textb.attr('data-price', val);
                calculateTotal();
            });
            $('.btn-number').off("click");
            $('.btn-number').click(function(e){
                e.preventDefault();
                fieldName = $(this).attr('data-field');
                type      = $(this).attr('data-type');
                var input = $(this).parent().parent().find('.form-control');
                var currentVal = parseFloat(input.val());
                if (!isNaN(currentVal)) {
                    if(type == 'minus') {

                        if(currentVal > parseFloat(input.attr('min'))) {
                            input.val(currentVal - 1).change();
                        }
                        if(parseFloat(input.val()) <= parseFloat(input.attr('min'))) {
                            $(this).attr('disabled', true);
                        }
                        if(parseFloat(input.val()) < parseFloat(input.attr('max'))){
                            $(this).parent().parent().find('.plus').removeAttr('disabled')
                        }

                    } else if(type == 'plus') {

                        if(currentVal < parseFloat(input.attr('max'))) {
                            input.val(currentVal + 1).change();
                        }
                        if(parseFloat(input.val()) >= parseFloat(input.attr('max'))) {
                            $(this).attr('disabled', true);
                        }

                        if(parseFloat(input.val()) > parseFloat(input.attr('min'))){
                            $(this).parent().parent().find('.minus').removeAttr('disabled')
                        }

                    }
                } else {
                    input.val(0);
                }
                calculateTotal();
            });

            $('.input-number').off('keyup');
            $('.input-number').on('keyup',function () {
                if(parseFloat($(this).val()) < parseFloat($(this).attr('max'))) {
                    qty_before = $(this).val();
                }
            })

            $('.input-number').off('change');
            $('.input-number').on('change',function(){
                if(parseFloat($(this).val()) > parseFloat($(this).attr('max'))){
                    alert('Not enough quantity, Please add more quantity and re-add the product');
                    $(this).val(qty_before);
                }
                calculateTotal();
            })
        }

        function removeItem(elem){
            $(elem).parent().parent().remove();
            calculateTotal();
            return false;
        }

        function calculateTotal(){
            let total_invoice = 0;
            $('.input-number').each(function(index, elem){
                const total = $(this).closest('tr');
                const total_td = total.find('.item_total');
                const _total = (parseFloat($(this).val()) * parseFloat($(this).attr('data-price')));
                total_invoice +=_total;
                total_td.html(formatMoney(_total));
            });
            $('#sub_total').html(formatMoney(total_invoice));
            $('.total_invoice').html("&#8358;"+formatMoney(total_invoice));
            return total_invoice;
        }


        function wrapItemIncart(){
            const  product = [];
            let error_status = false;
            let unselected_unit = false;
            $('.input-number').each(function(index, elem){
                const type = $(this).closest('tr').find('.product_type');
                let typeVal = type.val();
                if (!typeVal) {
                    unselected_unit = true;
                }
                let unitId = null;
                if (typeVal && typeVal.indexOf('__u') !== -1) {
                    const parts = typeVal.split('__u');
                    typeVal = parts[0];
                    unitId = parts[1];
                }
                product.push(
                    {
                        id: $(this).attr('data-id'),
                        qty: $(this).val(),
                        type: typeVal,
                        unit_id: unitId,
                        price : $(this).attr('data-price'),
                        invoice_item_id : $(this).attr('data-invoice-item-id'),
                        cost_price : $(this).attr('data-cost-price')
                    }
                );
                if(parseFloat($(elem).val()) > parseFloat($(elem).attr('max'))){
                    error_status = true;
                }
            });
            if (unselected_unit === true) {
                alert('Please select a unit for all items in the cart before submitting.');
                return false;
            }
            if(error_status === true){
                alert('One or more Item in your invoice is out of stock, please cross check before submitting the invoice')
            }
            return product;
        }

        (function() {
            Date.prototype.toYMD = Date_toYMD;
            function Date_toYMD() {
                var year, month, day;
                year = String(this.getFullYear());
                month = String(this.getMonth() + 1);
                if (month.length == 1) {
                    month = "0" + month;
                }
                day = String(this.getDate());
                if (day.length == 1) {
                    day = "0" + day;
                }
                return year + "-" + month + "-" + day;
            }
        })();



        function cartTemplate(data){
            let type_select = "";
            if(data.stock.type != "NORMAL"){
                type_select += '<select class="form-control product_type">';

                if(parseInt(data.stock.available_quantity) > 0) {
                    type_select += '<option selected value="{{ getActiveStore()->packed_column }}" data-cost-price="'+data.stock.cost_price+'" data-price="'+data.stock.selling_price+'" data-av-qty="'+data.stock.available_quantity+'">Packed</option>';
                }
                if(parseInt(data.stock.available_yard_quantity) > 0) {
                    type_select += '<option value="{{ getActiveStore()->yard_column }}" data-cost-price="'+data.stock.yard_cost_price+'" data-price="'+data.stock.yard_selling_price+'" data-av-qty="'+data.stock.available_yard_quantity+'">Pieces / Yards</option>';
                }


                type_select +='</select>';
            }else{
                type_select += '<select class="form-control product_type">';
                type_select +='<option selected value="{{ getActiveStore()->packed_column }}" data-cost-price="'+data.stock.cost_price+'" data-price="'+data.stock.selling_price+'" data-av-qty="'+data.stock.available_quantity+'">Packed</option>';
                type_select +='</select>';
            }

            if(!data.stock.multi_level && data.stock.available_quantity == 0 && data.stock.available_yard_quantity >0){
                data.stock.available_quantity = data.stock.available_yard_quantity;
                data.stock.selling_price = data.stock.yard_selling_price
            }

            let defaultType = "{{ getActiveStore()->packed_column }}";
            let currentPrice = data.stock.selling_price;
            let currentCost = data.stock.cost_price;
            let currentMax = data.stock.available_quantity;

            if (data.stock.multi_level && data.stock.units && data.stock.units.length) {
                let onlyPieces = (data.stock.units.length === 1) || data.stock.only_pieces;
                if (onlyPieces) {
                    const du = defaultUnit(data.stock);
                    defaultType = unitOptValue(du);
                    type_select = '<select class="form-control product_type">';
                    data.stock.units.forEach(function (u) {
                        type_select += '<option ' + (u.id === du.id ? 'selected ' : '') + 'value="' + unitOptValue(u) + '" data-unit-id="' + u.id + '" data-cost-price="' + u.cost_price + '" data-price="' + u.selling_price + '" data-av-qty="' + u.available + '">' + u.name + ' (' + u.available + ' avail.)</option>';
                    });
                    type_select += '</select>';
                    currentMax = du.available;
                    currentPrice = du.selling_price;
                    currentCost = du.cost_price;
                } else {
                    defaultType = "pending";
                    type_select = '<select class="form-control product_type">';
                    type_select += '<option value="" disabled selected>-- Select Unit --</option>';
                    data.stock.units.forEach(function (u) {
                        type_select += '<option value="' + unitOptValue(u) + '" data-unit-id="' + u.id + '" data-cost-price="' + u.cost_price + '" data-price="' + u.selling_price + '" data-av-qty="' + u.available + '">' + u.name + ' (' + u.available + ' avail.)</option>';
                    });
                    type_select += '</select>';
                    currentMax = 0;
                    currentPrice = 0;
                    currentCost = 0;
                }
            }

            @if($settings['allow_edit_price'] == "Yes")
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`_`+defaultType+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-price="`+currentPrice+`" data-cost-price="`+currentCost+`" style="width:100px;display: block;" required="" max="`+currentMax+`" min="0.01" step="0.01" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td><th class="text-right item_price"><input type="text" step="0.00000001" class="item_text_price form-control" value="`+currentPrice+`"/></th><th class="text-right item_total">`+formatMoney(currentPrice)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
            @else
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`_`+defaultType+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-price="`+currentPrice+`" data-cost-price="`+currentCost+`" style="width:100px;display: block;" required="" max="`+currentMax+`" min="0.01" step="0.01" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td><th class="text-right item_price"><span class="item_text_price form-control" value="`+currentPrice+`">`+formatMoney(currentPrice)+`</span></th><th class="text-right item_total">`+formatMoney(currentPrice)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
            @endif

        }

        function ProcessInvoice(btn){

            if($('#invoice_paper_number').val() == ""){
                alert('Please enter Deposit from the manual invoice');
                return false;
            }

            let payment_payment = false;

            let status = $(btn).attr('data-status');

            const stock = wrapItemIncart();

            if (stock === false) {
                return false;
            }

            if(stock.length === 0){
                alert('You can not submit an empty cart, please add product to continue');
                return false;
            }

            if(!document.getElementById('customer_id')){
                alert('Please select a customer to proceed..');
                return false;
            }

            $('.submit_btn').attr('disabled','disabled');
            $('.errors').attr('style','display:none');
            showMask('Updating Deposit, Please wait...');

            var timeout = setTimeout(function(){
                hideMask();
                alert('error - request timeout, please try generating the invoice again');
                $('.submit_btn').removeAttr('disabled');
            },30000);
            $.ajax({
                url: '{{ route('deposit.update',$deposit->id) }}',
                method : 'PUT',
                data: {
                    'data' : JSON.stringify(stock),
                    "_token": "{{ csrf_token() }}",
                    'status': status,
                    'customer_id' :$('#customer_id').val(),
                    'date':$('#invoice_date').val(),
                    'invoice_paper_number' : $('#invoice_paper_number').val(),
                    'payment' : payment_payment
                },
                success: function(returnData){
                    hideMask();
                    clearTimeout(timeout);
                    var res = returnData;
                    $('.submit_btn').removeAttr('disabled');
                    if(res.status === true){
                        $('#success_div').html(res.html);
                        $('#appender').html('');
                        calculateTotal();
                        $('#mainform').trigger("reset");
                        $('#successInvoice').modal({
                            backdrop: "static", //remove ability to close modal with click
                            keyboard: false, //remove option to close with keyboard
                            show: true //Display loader!
                        });
                    }else{
                        //HoldInvoice(document.getElementById('hold_invoice_hold'));
                        hideMask();
                        var errors = res.error;
                        for (let key in errors) {
                            if (document.getElementById('error_' + key)) {
                                $(document.getElementById('error_' + key)).html(errors[key]).removeAttr('style');
                            }
                        }
                    }
                },
                error: function(xhr, status, error){
                    hideMask();
                    clearTimeout(timeout);
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    alert('Error - ' + errorMessage);
                    $('.submit_btn').removeAttr('disabled');
                    hideMask();
                }
            });
            return false;
        }

        function IsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }


        $(document).ready(function(){

            bindIncrement();
            bindAllTr();
            bindproductType();
            calculateTotal();

            $("#new_customer_form").on("submit",function(){
                var form__ = $(this);
                $('#error_reg').html("").attr('style','display: none');
                $(this).find(".form-control").attr('disabled','disabled');
                $(this).attr('style','opacity:0.8;');
                var form = $(this);
                var data ={};
                var _data = new Array();
                form.find('.form-control').each(function(id,elem){
                    data[$(elem).attr('name')]= $(elem).val();
                    _data.push($(elem).attr('name'));
                });
                data['_token'] = "{{ csrf_token() }}";
                $('#add_customer').attr("disabled","disabled");
                $.post($(this).attr('action'),data,function(response,status){
                    $('#add_customer').removeAttr("disabled");
                    form__.find(".form-control").removeAttr('disabled');
                    form__.removeAttr('style');
                    if(response.status === true){
                        var newCustomer = new Option(response.value,response.id,true,true);
                        $('#customer_id').append(newCustomer).trigger('change');
                        $('#newCustomer').modal('hide');
                        form__.find(".form-control").val('');
                        form__.find(".form-control").html('');
                    }else{
                        $err = "<li style='list-style: none;color: #FFF; font-size: 11px'>" + response.message + "</li>";
                        $('#error_reg').html($err).attr('style','display: block');
                    }
                }) .done(function() {
                }).fail(function(status) {
                    form__.find(".form-control").removeAttr('disabled');
                    form__.removeAttr('style');

                    $('#add_customer').removeAttr("disabled");
                    var form = $("#new_customer_form");
                    var _data = new Array();
                    form.find('.form-control').each(function(id,elem){
                        _data.push($(elem).attr('name'));
                    });
                    var errors =  status.responseJSON.errors;
                    var html = "";
                    for(var i =0; i < _data.length; i++){
                        if(errors[_data[i]] !== undefined) {
                            html += "<li style='list-style: none;color: #FFF; font-size: 11px'>" + errors[_data[i]] + "</li>";
                        }
                    }
                    $('#error_reg').html(html).removeAttr('style');
                });
                return false;
            });

        });

    </script>


@endpush
