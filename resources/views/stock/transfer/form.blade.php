@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')

    <div class="ui-container">

        <div class="row">
            @if(!isset($transfer->id))
                <div class="col-md-8">
                    <section class="panel">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <div class="panel-body">
                            <form action="" method="post">
                                {{ csrf_field() }}
                                <br/>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Transfer From</label>
                                        <select required id="store" name="from" class="form-control select2">
                                            @foreach($stores as $store)
                                                <option {{ $from == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Transfer To</label>
                                        <select required id="store" name="to" class="form-control select2">
                                            @foreach($stores as $store)
                                                <option  {{ $to == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-sm btn-primary"  style="margin-top: 25px;" type="submit">Go</button>
                                    </div>
                                </div>
                                <br/>
                            </form>
                        </div>

                    </section>
                </div>
            @endif
            @php
                if(isset($from) && isset($to)){
            @endphp
            <div class="col-md-12" >
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <form action="">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label>Select Stock</label>
                                        <select class="form-control" name="stock" id="products" placeholder="Search for Product"></select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label id="type_label">Select Unit / Type</label>
                                        <select id="type" name="type" class="form-control select2">
                                            @foreach(config('stock_type_name.'.config('app.store')) as $key=>$_type)
                                                <option {{ $_type == $type ? "selected" : ""  }}  value="{{ $_type }}">{{ $key }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Available Quantity</label>
                                        <span type="text" class="form-control" id="av_qty" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Quantity</label>
                                        <input type="number" step="any" min="0.0001" max="0" class="form-control" id="qty" placeholder="Qty"/>
                                    </div>
                                    <div class="col-sm-1">
                                        <button class="btn btn-sm btn-primary" onclick="return add_item();" style="margin-top: 25px;" type="button">Add Stock</button>
                                    </div>
                                </div>
                                <br/>
                            </form>
                            <br/>
                            @if(isset($transfer->id))
                                <form onsubmit="return checkform()" action="{{ route('stocktransfer.update',$transfer->id) }}" enctype="multipart/form-data" method="post">
                                    {{ method_field('PUT') }}
                                    @else
                                        <form onsubmit="return checkform()" action="" enctype="multipart/form-data" method="post">
                                            @endif
                                            {{ csrf_field() }}
                                            <input type="hidden" name="from" value="{{ $from }}"/>
                                            <input type="hidden" name="to" id="to" value="{{ $to }}"/>
                                            <input type="hidden" name="type" id="type" value="{{ $type }}"/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3 col-lg-offset-6">
                                                        <label>Transfer Date</label>
                                                        <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" name="transfer_date" value="{{ date('Y-m-d',strtotime($transfer->transfer_date)) }}"/>
                                                        @if ($errors->has('date_created'))
                                                            <label for="name-error" class="error"
                                                                   style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Transfer Invoice Number</label>
                                                        <input class="form-control" id="transfer_number" name="transfer_number" value="{{ old('transfer_number', $transfer->transfer_number) }}"/>
                                                        @if ($errors->has('date_created'))
                                                            <label for="name-error" class="error"
                                                                   style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <br/>  <br/>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-left">Name</th>
                                                                <th class="text-center">Unit / Type</th>
                                                                <th class="text-center">Quantity</th>
                                                                <th class="text-right">Selling Price</th>
                                                                <th class="text-right">Total</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="appender">
                                                            @foreach($transfer->stock_transfer_items()->get() as $item)
                                                                @php
                                                                    $isUnit = !empty($item->unit_id) || !empty($item->unit_name);
                                                                    $rowKey = $isUnit ? $item->stock_id . '-unit-' . $item->unit_id : $item->stock_id . '-' . $item->product_type;
                                                                    $rowQty = $isUnit && $item->unit_quantity ? $item->unit_quantity : $item->quantity;
                                                                    $rowPrice = $isUnit && $item->unit_price ? $item->unit_price : $item->selling_price;
                                                                    $rowTotal = $rowPrice * $rowQty;
                                                                @endphp
                                                                <tr id="{{ $rowKey }}">
                                                                    <input type="hidden" name="stock_id[]" value="{{ $item->stock_id }}"/>
                                                                    <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}"/>
                                                                    <input type="hidden" name="unit_name[]" value="{{ $item->unit_name }}"/>
                                                                    <input type="hidden" name="qty[]" value="{{ $rowQty }}"/>
                                                                    <input type="hidden" name="type[]" value="{{ $item->product_type }}"/>
                                                                    <td>{{ $item->stock->name }}</td>
                                                                    <td class="text-center">
                                                                        @if($isUnit)
                                                                            <span class="badge badge-info">{{ $item->unit_name }}</span>
                                                                        @else
                                                                            {{ array_flip(config('stock_type_name.'.config('app.store')))[$item->product_type] ?? $item->product_type }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($isUnit)
                                                                            <b>{{ $rowQty + 0 }} {{ $item->unit_name }}</b>
                                                                            @if($item->quantity && $item->quantity != $rowQty)
                                                                                <br><small class="text-muted">({{ $item->quantity + 0 }} pcs)</small>
                                                                            @endif
                                                                        @else
                                                                            {{ $item->quantity }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-right">{{ number_format($rowPrice, 2) }}</td>
                                                                    <td class="total_trans text-right" value="{{ $rowTotal }}">{{ number_format($rowTotal, 2) }}</td>
                                                                    <td><button class="btn btn-sm btn-danger" onclick="remove_item(this)"><i class="fa fa-trash"></i> Remove</button></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <th class="text-right">Total</th>
                                                                <th class="text-right" id="total_transfer">{{ number_format($transfer->total_price,2) }}</th>
                                                                <td></td>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                        @if(isset($transfer->id))
                                                            <button class="btn btn-primary btn-sm" type="submit" name="status" value="DRAFT"><i class="fa fa-arrow-right"></i> Update Transfer</button>
                                                            <button class="btn btn-success btn-sm" type="submit" name="status" value="COMPLETE"><i class="fa fa-check"></i> Complete Transfer</button>
                                                        @else
                                                            <button class="btn btn-primary btn-sm" type="submit" name="status" value="DRAFT"><i class="fa fa-arrow-right"></i> Draft Transfer Stock</button>
                                                            <button class="btn btn-success btn-sm" type="submit" name="status" value="COMPLETE"><i class="fa fa-check"></i> Complete Transfer Stock</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                        </div>
                    </div>
                </section>
            </div>
            @php
                }
            @endphp

        </div>

    </div>
@endsection


@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script    src="{{ asset('assets/js/init-datepicker.js') }}"></script>

    <script>
        var currentSelectedStock = null;
        var defaultLegacyOptions = null;

        $(document).ready(function(e){
            // Cache initial legacy options of #type
            defaultLegacyOptions = $('#type').html();

            var path = "{{ route('findselectstock') }}?select2=yes&store={{ $from }}";
            var select = $('#products').select2({
                placeholder: 'Search for product',
                ajax: {
                    url: path,
                    dataType: 'json',
                    delay: 250,
                    data: function (data) {
                        return {
                            searchTerm: data.term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                }
            });


            $("#products").on('change', function(eventData){
                var data = $(this).select2('data');
                data = data ? data[0] : null;
                currentSelectedStock = data;

                if (!data) {
                    $('#av_qty').html('');
                    $('#qty').val('').attr('max', 0);
                    return;
                }

                if (data.multi_level && data.units && data.units.length > 0) {
                    // Multi-level units
                    $('#type').empty();
                    var onlyPieces = (data.units.length === 1) || data.only_pieces;

                    if (!onlyPieces) {
                        // User requirement: no default selection when multiple units exist
                        $('#type').append(new Option('-- Select Unit --', ''));
                    }

                    $.each(data.units, function(i, u) {
                        var optText = u.name + ' (Avail: ' + u.available + ')';
                        var optVal = 'unit_' + u.id;
                        var opt = new Option(optText, optVal);
                        $(opt).data('unit', u);
                        $('#type').append(opt);
                    });

                    if (onlyPieces) {
                        // Default to pieces if only pieces is enabled
                        $('#type').val('unit_' + data.units[0].id).trigger('change');
                    } else {
                        $('#type').val('').trigger('change');
                    }
                } else {
                    // Legacy single-level store / product
                    $('#type').html(defaultLegacyOptions);

                    $('#qty').attr('max_bundle', data['available_quantity']);
                    $('#qty').attr('max_yard', data['available_yard_quantity']);
                    $('#qty').attr('data-cost-price', data['cost_price']);
                    $('#qty').attr('data-selling-price', data['selling_price']);
                    $('#qty').attr('data-cost-yard-price', data['yard_cost_price']);
                    $('#qty').attr('data-selling-yard-price', data['yard_selling_price']);

                    $('#type').trigger('change');
                }
            });

            $('#type').on('change', function(){
                if (!currentSelectedStock) return;

                if (currentSelectedStock.multi_level && currentSelectedStock.units) {
                    var selectedOpt = $('#type option:selected');
                    var unit = selectedOpt.data('unit');

                    if (unit) {
                        $('#av_qty').html(unit.available + ' ' + unit.name);
                        $('#qty').attr('max', unit.available);
                        $('#qty').attr('data-unit-id', unit.id);
                        $('#qty').attr('data-unit-name', unit.name);
                        $('#qty').attr('data-unit-price', unit.selling_price);
                        $('#qty').attr('data-unit-cost', unit.cost_price);
                        $('#qty').attr('data-multiplier', unit.multiplier);
                    } else {
                        $('#av_qty').html('-- Select unit --');
                        $('#qty').attr('max', 0);
                        $('#qty').removeAttr('data-unit-id');
                        $('#qty').removeAttr('data-unit-name');
                    }
                } else {
                    if ($(this).val() == "NORMAL") {
                        $('#av_qty').html($('#qty').attr('max_bundle'));
                        $('#qty').attr('max', $('#qty').attr('max_bundle'));
                    } else {
                        $('#av_qty').html($('#qty').attr('max_yard'));
                        $('#qty').attr('max', $('#qty').attr('max_yard'));
                    }
                }
            });

        });

        function add_item(){
            if (!currentSelectedStock || !$("#products option:selected").val()) {
                alert("Please select a product");
                return false;
            }

            var qtyVal = $("#qty").val();
            if (qtyVal === "" || parseFloat(qtyVal) <= 0) {
                alert("Please enter a valid transfer quantity");
                return false;
            }

            var enteredQty = parseFloat(qtyVal);
            var maxQty = parseFloat($("#qty").attr('max') || 0);

            if (enteredQty > maxQty) {
                alert("Insufficient quantity to transfer: only " + maxQty + " available");
                return false;
            }

            if (currentSelectedStock.multi_level && currentSelectedStock.units) {
                var selectedOpt = $('#type option:selected');
                var unit = selectedOpt.data('unit');

                if (!unit || !selectedOpt.val()) {
                    alert("Please select a unit to transfer");
                    return false;
                }

                var rowId = currentSelectedStock.id + "-unit-" + unit.id;
                if (document.getElementById(rowId)) {
                    alert("Item with " + unit.name + " unit already exists in transfer list");
                    return false;
                }

                var unitPrice = parseFloat(unit.selling_price);
                var lineTotal = unitPrice * enteredQty;
                var basePcs = Math.round(enteredQty * unit.multiplier * 10000) / 10000;

                var html = '<tr id="' + rowId + '">';
                html += '<input type="hidden" name="stock_id[]" value="' + currentStockId() + '"/>';
                html += '<input type="hidden" name="unit_id[]" value="' + unit.id + '"/>';
                html += '<input type="hidden" name="unit_name[]" value="' + unit.name + '"/>';
                html += '<input type="hidden" name="qty[]" value="' + enteredQty + '"/>';
                html += '<input type="hidden" name="type[]" value="yard_quantity"/>';
                html += '<td>' + currentSelectedStock.text + '</td>';
                html += '<td class="text-center"><span class="badge badge-info">' + unit.name + '</span></td>';
                html += '<td class="text-center"><b>' + enteredQty + ' ' + unit.name + '</b>' + (unit.multiplier > 1 ? '<br><small class="text-muted">(' + basePcs + ' pcs)</small>' : '') + '</td>';
                html += '<td class="text-right">' + formatMoney(unitPrice) + '</td>';
                html += '<td class="total_trans text-right" value="' + lineTotal + '">' + formatMoney(lineTotal) + '</td>';
                html += '<td><button class="btn btn-sm btn-danger" onclick="remove_item(this)"><i class="fa fa-trash"></i> Remove</button></td>';
                html += '</tr>';

                $("#appender").append(html);
            } else {
                var typeVal = $('#type').val();
                var rowId = currentStockId() + "-" + typeVal;
                if (document.getElementById(rowId)) {
                    alert("Item already exists in transfer list");
                    return false;
                }

                var isNormal = typeVal == "NORMAL";
                var price = isNormal ? parseFloat($('#qty').attr('data-selling-price')) : parseFloat($('#qty').attr('data-selling-yard-price'));
                var lineTotal = price * enteredQty;

                var html = '<tr id="' + rowId + '">';
                html += '<input type="hidden" name="stock_id[]" value="' + currentStockId() + '"/>';
                html += '<input type="hidden" name="unit_id[]" value=""/>';
                html += '<input type="hidden" name="unit_name[]" value=""/>';
                html += '<input type="hidden" name="qty[]" value="' + enteredQty + '"/>';
                html += '<input type="hidden" name="type[]" value="' + typeVal + '"/>';
                html += '<td>' + currentSelectedStock.text + '</td>';
                html += '<td class="text-center">' + $('#type option:selected').text() + '</td>';
                html += '<td class="text-center">' + enteredQty + '</td>';
                html += '<td class="text-right">' + formatMoney(price) + '</td>';
                html += '<td class="total_trans text-right" value="' + lineTotal + '">' + formatMoney(lineTotal) + '</td>';
                html += '<td><button class="btn btn-sm btn-danger" onclick="remove_item(this)"><i class="fa fa-trash"></i> Remove</button></td>';
                html += '</tr>';

                $("#appender").append(html);
            }

            $("#products").val('').trigger('change');
            currentSelectedStock = null;
            $("#qty").val("");
            $('#av_qty').html('');
            total();
            return false;
        }

        function currentStockId() {
            return $("#products option:selected").val() || (currentSelectedStock ? currentSelectedStock.id : '');
        }

        function remove_item(btn){
            $(btn).closest('tr').remove();
            total();
        }

        function total(){
            var totalVal = 0;
            $('.total_trans').each(function(id, elem){
                totalVal += parseFloat($(elem).attr('value') || 0);
            });
            $('#total_transfer').html(formatMoney(totalVal));
        }

        function checkform(){
            if($('#appender tr').length === 0){
                alert("Please add at least one item to continue");
                return false;
            } else if($('#transfer_number').val().trim() === ""){
                alert("Please add transfer invoice number");
                return false;
            } else {
                $('#save_and_create').attr('disabled', 'disabled');
            }
            return true;
        }
    </script>
@endpush
