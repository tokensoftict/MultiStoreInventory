@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <style>
        input {
            margin-bottom: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="ui-container">
        @if(isset($porder->id))
            <form onsubmit="return checkform()" action="{{ route('purchaseorders.update',$porder->id) }}" enctype="multipart/form-data" method="post">
                {{ method_field('PUT') }}
        @else
            <form onsubmit="return checkform()" action="{{ route('purchaseorders.store') }}" enctype="multipart/form-data" method="post">
        @endif
            {{ csrf_field() }}
            <input type="hidden" value="{{ $type }}" name="type">

            <div class="row">
                <!-- Left Column (Product addition & Items list) -->
                <div class="col-md-8">
                    <!-- Quick Add Item Panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading" style="padding: 10px 15px;">
                            <h4 class="panel-title" style="margin: 0; font-size: 14px; font-weight: 600;">
                                <i class="fa fa-plus-circle text-info"></i> Add Item to Purchase Order
                            </h4>
                        </div>
                        <div class="panel-body" style="padding: 15px;">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 11px; margin-bottom: 3px; font-weight: 600;">Select Stock</label>
                                        <select id="products" class="form-control select2">
                                            <option value="">-Select One-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 11px; margin-bottom: 3px; font-weight: 600;">Recent Cost Price</label>
                                        <input style="background-color: #FFF; color: #000; height: 30px; font-size: 11px;" type="number" step="any" placeholder="Cost Price" class="form-control" id="cost_price"/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 11px; margin-bottom: 3px; font-weight: 600;">Selling Price</label>
                                        <input style="background-color: #FFF; color: #000; height: 30px; font-size: 11px;" type="number" step="any" placeholder="Selling Price" class="form-control" id="selling_price"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label style="font-size: 11px; margin-bottom: 3px; font-weight: 600;">Quantity</label>
                                        <input type="number" placeholder="Quantity" class="form-control" id="qty" style="height: 30px; font-size: 11px;"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 12px;">
                                <div class="col-sm-12 text-right">
                                    <button class="btn btn-sm btn-info" onclick="add_item()" type="button" style="padding: 5px 15px; font-size: 11px;">
                                        <i class="fa fa-plus"></i> Add Item to List
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items List Table -->
                    <div class="panel panel-default">
                        <div class="panel-heading" style="padding: 10px 15px;">
                            <h4 class="panel-title" style="margin: 0; font-size: 14px; font-weight: 600;">
                                <i class="fa fa-shopping-cart text-info"></i> Purchase Order Items List
                            </h4>
                        </div>
                        <div class="panel-body" style="padding: 0;">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="margin-bottom: 0; font-size: 11px;">
                                    <thead>
                                    <tr>
                                        <th style="width: 45%;">Product Name</th>
                                        <th class="text-center" style="width: 10%;">Quantity</th>
                                        <th class="text-right" style="width: 15%;">Selling Price</th>
                                        <th class="text-right" style="width: 15%;">Cost Price</th>
                                        <th class="text-right" style="width: 15%;">Total</th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="appender">
                                    @foreach($porder->purchase_order_items as $items)
                                        <tr id='row_{{ $items->stock->id }}'>
                                            <input type="hidden" name="stock_id[]" value="{{ $items->stock->id }}"/>
                                            <input type="hidden" name="qty[]" value="{{ $items->qty }}"/>
                                            <input type="hidden" name="cost_price[]" value="{{ $items->cost_price }}"/>
                                            <input type="hidden" name="selling_price[]" value="{{ $items->selling_price }}"/>
                                            <td>{{ $items->stock->name }}</td>
                                            <td class='text-center'>{{ $items->qty }}</td>
                                            <td class='text-right'>{{ number_format($items->selling_price,2) }}</td>
                                            <td class='text-right'>{{ number_format($items->cost_price,2) }}</td>
                                            <td class='total_attr text-right' data-value='{{ $items->cost_price * $items->qty }}'>{{ number_format(($items->cost_price * $items->qty),2) }}</td>
                                            <td class="text-center"><button class="btn btn-xs btn-danger" onclick="remove_item(this)"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (PO Details and Actions) -->
                <div class="col-md-4">
                    <div class="panel panel-default" style="position: -webkit-sticky; position: sticky; top: 10px;">
                        <div class="panel-heading" style="padding: 10px 15px; background-color: #fcfcfc;">
                            <h4 class="panel-title" style="margin: 0; font-size: 14px; font-weight: 600;"><i class="fa fa-file-text-o text-info"></i> PO Details</h4>
                        </div>
                        <div class="panel-body" style="padding: 15px;">
                            <!-- Total Display Box -->
                            <div class="well well-sm text-center" style="background-color: #2b303a; color: #fff; margin-bottom: 15px; border-radius: 4px; padding: 10px;">
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #a0aab5; margin-bottom: 3px;">Total PO Value</div>
                                <div style="font-size: 24px; font-weight: bold;" id="total_po">₦{{ number_format($porder->total,2) }}</div>
                            </div>

                            <div class="form-group" style="margin-bottom: 10px;">
                                <label style="font-size: 11px; margin-bottom: 2px; font-weight: 600;">Select Supplier</label>
                                <select required id="supplier" name="supplier_id" class="form-control select2" style="width: 100%;">
                                    <option value="">-Select One-</option>
                                    @foreach($suppliers as $suppllier)
                                        <option {{ old('supplier_id',$porder->supplier_id) == $suppllier->id ? "selected" : "" }}  value="{{ $suppllier->id }}">{{ $suppllier->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('supplier'))
                                    <label class="error" style="display: inline-block; font-size: 10px; color: red;">{{ $errors->first('supplier') }}</label>
                                @endif
                            </div>

                            @if(app(\App\Classes\Settings::class)->store()->allow_store_to_share_the_same_product == "0")
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; font-weight: 600;">Store</label>
                                    <span class="form-control" style="height: 30px; font-size: 11px; line-height: 20px;">{{ getActiveStore()->name }}</span>
                                    <input type="hidden" name="store" value="{{ getActiveStore()->packed_column }}">
                                </div>
                            @else
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label style="font-size: 11px; margin-bottom: 2px; font-weight: 600;">Select Store</label>
                                    <select required id="store" name="store" class="form-control select2" style="width: 100%;">
                                        @foreach($stores as $store)
                                            <option  {{ old('store',($porder->store ?? getActiveStore()->packed_column)) == $store->packed_column ? "selected" : "" }} value="{{ $store->packed_column }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('store'))
                                        <label class="error" style="display: inline-block; font-size: 10px; color: red;">{{ $errors->first('store') }}</label>
                                    @endif
                                </div>
                            @endif

                            <div class="form-group" style="margin-bottom: 10px;">
                                <label style="font-size: 11px; margin-bottom: 2px; font-weight: 600;">Date</label>
                                <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000; height: 30px; font-size: 11px;" name="date_created" value="{{ old('date_created', \Carbon\Carbon::today()->toDateString()) }}"/>
                                @if ($errors->has('date_created'))
                                    <label class="error" style="display: inline-block; font-size: 10px; color: red;">{{ $errors->first('date_created') }}</label>
                                @endif
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 11px; margin-bottom: 2px; font-weight: 600;">Purchase Invoice Number</label>
                                <input required id="purchase_order_invoice_number" value="{{ old('purchase_order_invoice_number', $porder->purchase_order_invoice_number) }}" placeholder="Purchase Invoice Number" class="form-control" name="purchase_order_invoice_number" style="height: 30px; font-size: 11px;"/>
                                @if ($errors->has('purchase_order_invoice_number'))
                                    <label class="error" style="display: inline-block; font-size: 10px; color: red;">{{ $errors->first('purchase_order_invoice_number') }}</label>
                                @endif
                            </div>

                            <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                <div class="row">
                                    <div class="col-sm-6" style="padding-right: 5px;">
                                        <button class="btn btn-success btn-sm btn-block" type="submit" name="status" value="DRAFT">
                                            <i class="fa fa-save"></i> Save Draft
                                        </button>
                                    </div>
                                    <div class="col-sm-6" style="padding-left: 5px;">
                                        <button class="btn btn-primary btn-sm btn-block" type="submit" name="status" value="COMPLETE">
                                            <i class="fa fa-check"></i> Complete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script src="{{ asset('assets/js/init-datepicker.js') }}"></script>

    <script>
        $(document).ready(function(e){
            var path = "{{ route('findpurchaseorderstock') }}?select2=yes";
            var select = $('#products').select2({
                placeholder: 'Search for product',
                ajax: {
                    url: path,
                    dataType: 'json',
                    delay: 250,
                    data: function (data) {
                        return {
                            searchTerm: data.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results:response
                        };
                    },
                }
            });

            $("#products").on('change',function(eventData){
                var data = $(this).select2('data');
                data = data[0];
                if (data) {
                    $("#cost_price").val(data['cost_price']);
                    $("#selling_price").val(data['selling_price']);
                    $("#quantity_qty").val(data['qty']);
                }
            });

            $( "#supplier" ).select2();
        });

        function add_item(){
            if(($("#products option:selected").val() === "") || ($("#qty").val() === "") || ($("#cost_price").val() === "") || ($("#selling_price").val() === "")){
                alert('Please fill out all product fields before adding.');
                return false;
            }
            if(document.getElementById('row_'+$("#products option:selected").val())){
                alert('Item already exists in the Purchase Order List');
                return false;
            }
            var html = "<tr id='row_"+$("#products option:selected").val()+"'>";
            html += '<input type="hidden" name="stock_id[]" value="'+$("#products option:selected").val()+'"/>';
            html += '<input type="hidden" name="qty[]" value="'+$("#qty").val()+'"/>';
            html += '<input type="hidden" name="cost_price[]" value="'+$("#cost_price").val()+'"/>';
            html += '<input type="hidden" name="selling_price[]" value="'+$("#selling_price").val()+'"/>';
            html += "<td>"+$("#products option:selected").text()+"</td>";
            html += "<td class='text-center'>"+$("#qty").val()+"</td>";
            html += "<td class='text-right'>"+formatMoney($("#selling_price").val())+"</td>";
            html += "<td class='text-right'>"+formatMoney($("#cost_price").val())+"</td>";
            html += "<td class='total_attr text-right' data-value='"+(parseFloat($("#cost_price").val()) * parseInt($("#qty").val()))+"'>"+formatMoney(parseFloat($("#cost_price").val()) * parseInt($("#qty").val()))+"</td>";
            html += '<td class="text-center"><button class="btn btn-xs btn-danger" onclick="remove_item(this)"><i class="fa fa-trash"></i></button></td>';
            html += "</tr>";

            $('#products').val('').trigger('change');
            $("#qty").val("");
            $("#cost_price").val("");
            $("#selling_price").val("");
            $("#appender").append(html);
            total_po();
            return false;
        }

        function total_po(){
            var total = 0;
            $('.total_attr').each(function(id,elem){
                var value = parseFloat($(elem).attr('data-value'));
                total += value;
            });
            $("#total_po").html('₦' + formatMoney(total));
        }

        function remove_item(btn){
            $(btn).closest('tr').remove();
            total_po();
        }

        function checkform(){
            if($('#appender tr').length === 0){
                alert("Please add at least one item to continue");
                return false;
            }

            if($('#supplier').val() == ""){
                alert("Please select a supplier to continue");
                return false;
            }
            return true;
        }
    </script>
@endpush
