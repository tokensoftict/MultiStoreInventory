@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush


@section('content')

    <div class="ui-container">
        @if(isset($stock->id))
            <form role="form"  action="{{ route('stock.update',$stock->id) }}" enctype="multipart/form-data" method="post">
                {{ method_field('PUT') }}
                @else
                    <form role="form"  action="{{ route('stock.store') }}" enctype="multipart/form-data" method="post">
                        @endif
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4">
                                @if(session('success'))
                                    {!! alert_success(session('success')) !!}
                                @elseif(session('error'))
                                    {!! alert_error(session('error')) !!}
                                @endif
                                <section class="panel">
                                    <header class="panel-heading panel-border">
                                        Stock Information
                                    </header>
                                    <div class="panel-body" >

                                        <div class="form-group">
                                            <label>Name <span  style="color:red;">*</span></label>
                                            <div class="input-group col-md-12">
                                                <input type="text" value="{{ old('name', $stock->name) }}" required  class="form-control" id="stock_name" name="name" placeholder="Stock Name"/>
                                                <div class="input-group-btn">
                                                    <button id="lfm" data-input="thumbnail" data-preview="holder" type="button" class="btn btn-primary">Get Image</button>
                                                </div>
                                            </div>
                                            @if ($errors->has('name'))
                                                <label for="name-error" class="error" style="display: inline-block;">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label>Stock Type  <span style="color:red;">*</span></label>
                                            <select class="form-control" required name="type">
                                                @foreach(config('stock_type')[config('app.store')] as $key=>$type)
                                                    <option {{ old('type', $stock->type) == $type ? "selected" : "" }} value="{{ $type }}">{{ $key }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Stock Status  <span style="color:red;">*</span></label>
                                            <select class="form-control" required name="status">
                                                <option {{ old('status', $stock->status) == "1" ? "selected" : "" }} value="1">Enabled</option>
                                                <option {{ old('status', $stock->status) == "0" ? "selected" : "" }} value="0">Disabled</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Stock Code</label>
                                            <input type="text" value="{{ old('name', $stock->code) }}"   class="form-control" name="code" placeholder="Stock Code"/>
                                        </div>

                                        <div class="form-group">
                                            <label>Location</label>
                                            <input type="text" value="{{ old('location', $stock->location) }}"   class="form-control" name="location" placeholder="Stock Location"/>
                                        </div>

                                        <div class="form-group">
                                            <label>Stock Description</label>
                                            <textarea style="height: 100px;" placeholder="Stock Description" class="form-control" name="description">{{ old('description',$stock->description) }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Manufacturer</label>
                                            <select class="form-control select2" name="manufacturer_id">
                                                <option value="">-Select Manufacturer-</option>
                                                @foreach($manufactures as $manufacturer)
                                                    <option {{ old('manufacturer_id', $stock->manufacturer_id) == $manufacturer->id ? "selected" : ""  }} value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control select2" name="product_category_id">
                                                <option value="">-Select Category-</option>
                                                @foreach($categories as $category)
                                                    <option {{ old('product_category_id', $stock->product_category_id) == $category->id ? "selected" : ""  }} value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Can this Product Expiry  <span style="color:red;">*</span></label>
                                            <select class="form-control" id="expire_stock" name="expiry">
                                                <option {{ old('expiry', $stock->type) == "0" ? "selected" : "" }} value="0">No</option>
                                                <option {{ old('expiry', $stock->type) == "1" ? "selected" : "" }} value="1">Yes</option>
                                            </select>
                                        </div>

                                    </div>
                                </section>
                            </div>
                            <div class="col-md-4">
                                <section class="panel">
                                    <header class="panel-heading panel-border">
                                        Stock Price Settings
                                    </header>
                                    <div class="panel-body" >
                                        <div class="form-group">
                                            <label>Selling Price <span  style="color:red;">*</span></label>
                                            <input type="number" step="0.00001" value="{{ old('selling_price',$stock->selling_price) }}"   class="form-control" name="selling_price" placeholder="Selling Price"/>
                                        </div>

                                        <div class="form-group">
                                            <label>Cost Price <span  style="color:red;">*</span></label>
                                            <input type="number" step="0.00001" value="{{ old('cost_price',$stock->cost_price) }}"   class="form-control" name="cost_price" placeholder="Cost Price"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Yard / Pieces Selling Price</label>
                                            <input type="number" step="0.00001" value="{{ old('yard_selling_price',$stock->yard_selling_price) }}"   class="form-control" name="yard_selling_price" placeholder="Yard Selling Price"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Yard / Pieces Cost Price</label>
                                            <input type="number" step="0.00001" value="{{ old('cost_price',$stock->yard_cost_price) }}" required  class="form-control" name="yard_cost_price" placeholder="Yard Cost Price"/>
                                        </div>
                                        @if(!empty($active_price_categories))
                                            <hr/>
                                            <h5><strong>Dynamic Price Categories</strong></h5>
                                            @foreach($active_price_categories as $price_category)
                                                <div class="form-group">
                                                    <label>{{ $price_category->name }} Price ({{ ucfirst($price_category->price_type) }})</label>
                                                    <input type="number" step="0.00001" value="{{ old('dynamic_prices.' . $price_category->id, $stock_prices[$price_category->id] ?? '') }}" class="form-control" name="dynamic_prices[{{ $price_category->id }}]" placeholder="{{ $price_category->name }} Price"/>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </section>
                                @if(!isset($stock->id))
                                    <section class="panel">
                                        <header class="panel-heading panel-border">
                                            Stock Quantity Settings
                                        </header>
                                        <div class="panel-body" >
                                            <div class="form-group">
                                                <label>Initial Quantity <span  style="color:red;">*</span></label>
                                                <input type="number"  value=""  class="form-control" name="stock_batch[quantity]" placeholder="Initial Quantity"/>
                                                @if ($errors->has('name'))
                                                    <label for="name-error" class="error"
                                                           style="display: inline-block;">{{ $errors->first('name') }}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Supplier</label>
                                                <select class="form-control select2" name="stock_batch[supplier_id]">
                                                    <option value="">-Select Supplier-</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option  value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('stock_batch.supplier_id'))
                                                    <label for="name-error" class="error"
                                                           style="display: inline-block;">{{ $errors->first('stock_batch.supplier_id') }}</label>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input type="text" value="" id="expiry_date" name="stock_batch[expiry_date]"  data-min-view="2" data-date-format="yyyy-mm-dd" class="form-control datepicker js-datepicker" name="stock_batch[expiry_date]" placeholder="Expiry Date"/>
                                            </div>
                                        </div>
                                    </section>
                                @else
                                    <section class="panel">
                                        <header class="panel-heading panel-border">
                                            Available Quantity
                                        </header>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Available Bundle Quantity <span  style="color:red;">*</span></label>
                                                <span class="form-control">{{ $stock->available_quantity }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label>Available Yards Quantity <span  style="color:red;">*</span></label>
                                                <span class="form-control">{{ $stock->available_yard_quantity }}</span>
                                            </div>
                                        </div>
                                    </section>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <section class="panel">
                                    <header class="panel-heading panel-border">
                                        Stock Re-order Level
                                    </header>
                                    <div class="panel-body" >
                                        <div class="form-group">
                                            <label>Re-Order Level</label>
                                            <input type="number" value="{{ old('reorder_level', $stock->reorder_level) }}"   class="form-control" name="reorder_level" placeholder="Reorder Level"/>
                                        </div>

                                        <div class="form-group">
                                            <label>Stock Limit</label>
                                            <input type="number" value="{{ old('stock_limit', $stock->stock_limit) }}"   class="form-control" name="stock_limit" placeholder="Stock Limit"/>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-md-4">
                                <section class="panel">
                                    <header class="panel-heading panel-border">
                                        Stock Barcode
                                    </header>
                                    <div class="panel-body" >
                                        <div class="form-group">
                                            <label>Barcode</label>
                                            <div class="input-group col-md-12">
                                                <input readonly style="background-color: #FFF;color:#000" value="{{ $stock->barcode }}" id="text_barcode" type="text" name="barcode" class="form-control">
                                                <div class="input-group-btn">
                                                    <button data-toggle="modal" data-target="#myModal" type="button" class="btn btn-primary">Capture Barcode</button>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isset($stock->id))
                                        <div class="form-group" style="margin-top:10px;">
                                            <button type="button"
                                                    id="btnOpenPrintModal"
                                                    data-stock-id="{{ $stock->id }}"
                                                    data-barcode="{{ $stock->barcode }}"
                                                    data-generate-url="{{ route('generate_barcode', $stock->id) }}"
                                                    data-print-url="{{ route('print_barcode', $stock->id) }}"
                                                    data-csrf="{{ csrf_token() }}"
                                                    data-toggle="modal"
                                                    data-target="#printBarcodeModal"
                                                    class="btn btn-success btn-block">
                                                <i class="fa fa-barcode"></i>&nbsp; Print Barcode Label
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </section>

                                <section class="panel">
                                    <header class="panel-heading panel-border">
                                        Stock Image
                                    </header>
                                    <div class="panel-body" id="holder">
                                        @if(isset($stock->image))
                                            <img src="{{ $stock->image }}"  class="img-thumbnail"/>
                                            <br/>
                                        @else
                                            <img src="{{ asset('assets/products.jpg') }}"  class="img-thumbnail"/>
                                            <br/>
                                        @endif
                                    </div>
                                    <input id="thumbnail" type="hidden" class="form-control" name="image">
                                </section>

                                <section class="panel">

                                    <div class="panel-body" >
                                        <input class="btn btn-info btn-block btn-lg" type="submit" name="save" value="Save Stock">
                                    </div>
                                </section>
                            </div>
                        </div>
                    </form>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Capture Stock Barcode</h4>
                </div>
                <div class="modal-body">
                    <div class="well" id="barcode">{{ $stock->barcode }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         PRINT BARCODE MODAL
    ============================================================ --}}
    <div id="printBarcodeModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius:10px;overflow:hidden;">

                {{-- Header --}}
                <div class="modal-header" style="background:#1a1a2e;color:#fff;border-radius:0;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                    <h4 class="modal-title" style="color:#e94560;">
                        <i class="fa fa-barcode"></i>&nbsp; Print Barcode Label
                        <small style="color:#aaa;font-size:12px;margin-left:8px;" id="pbl-stock-name"></small>
                    </h4>
                </div>

                <div class="modal-body" style="background:#f5f6fa;padding:20px;">

                    {{-- ── ALERT: No barcode assigned ─────────────── --}}
                    <div id="pbl-no-barcode-alert" style="display:none;">
                        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;
                                    padding:16px 20px;margin-bottom:18px;">
                            <strong style="color:#856404;"><i class="fa fa-exclamation-triangle"></i>
                                No barcode assigned to this product.</strong>
                            <p style="color:#6c5700;margin:8px 0 12px;">Please generate a unique code or scan an existing barcode first.</p>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                <button type="button" id="pbl-btn-generate"
                                        class="btn btn-success">
                                    <i class="fa fa-magic"></i>&nbsp; Generate Code
                                </button>
                                <button type="button" id="pbl-btn-scan"
                                        class="btn btn-primary"
                                        data-dismiss="modal"
                                        data-toggle="modal"
                                        data-target="#myModal">
                                    <i class="fa fa-barcode"></i>&nbsp; Scan Code
                                </button>
                            </div>
                            <div id="pbl-generate-result" style="margin-top:10px;display:none;">
                                <div class="alert alert-success" style="margin:0;">
                                    <strong>Barcode assigned:</strong>
                                    <span id="pbl-generated-code" style="font-family:monospace;font-size:15px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Controls ────────────────────────────────── --}}
                    <div id="pbl-controls">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-weight:600;">Paper / Label Size</label>
                                    <select id="pbl-size" class="form-control">
                                        <optgroup label="Small Labels">
                                            <option value="38x25mm">38 &times; 25 mm</option>
                                            <option value="50x25mm" selected>50 &times; 25 mm (Default)</option>
                                            <option value="50x30mm">50 &times; 30 mm</option>
                                            <option value="57x32mm">57 &times; 32 mm (Dymo)</option>
                                            <option value="62x29mm">62 &times; 29 mm (Brother DK)</option>
                                        </optgroup>
                                        <optgroup label="Medium Labels">
                                            <option value="76x51mm">76 &times; 51 mm</option>
                                            <option value="100x50mm">100 &times; 50 mm</option>
                                            <option value="100x75mm">100 &times; 75 mm</option>
                                        </optgroup>
                                        <optgroup label="Large / Sheet">
                                            <option value="100x150mm">100 &times; 150 mm</option>
                                            <option value="a4">A4 (210 &times; 297 mm)</option>
                                            <option value="letter">Letter (216 &times; 279 mm)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-weight:600;">Barcode Type</label>
                                    <select id="pbl-type" class="form-control">
                                        <option value="code128" selected>CODE 128 (Universal)</option>
                                        <option value="code39">CODE 39</option>
                                        <option value="ean13">EAN-13 (12 digits)</option>
                                        <option value="qr">QR Code</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label style="font-weight:600;">Number of Copies</label>
                                    <input type="number" id="pbl-copies" class="form-control"
                                           value="1" min="1" max="500" placeholder="Copies">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Preview ─────────────────────────────────── --}}
                    <div id="pbl-preview-wrap" style="margin-top:12px;">
                        <label style="font-weight:600;margin-bottom:8px;display:block;">
                            <i class="fa fa-eye"></i>&nbsp; Label Preview
                        </label>
                        <div style="border:2px dashed #ccc;border-radius:8px;background:#fff;
                                    min-height:160px;display:flex;align-items:center;
                                    justify-content:center;overflow:hidden;">
                            <iframe id="pbl-preview-frame"
                                    src="about:blank"
                                    style="width:100%;height:220px;border:none;"
                                    scrolling="no"
                                    title="Barcode label preview">
                            </iframe>
                        </div>
                        <p style="color:#888;font-size:11px;margin-top:4px;">
                            Preview shows 1 copy at chosen size. Actual print scale may differ.
                        </p>
                    </div>

                </div>{{-- /modal-body --}}

                {{-- Footer --}}
                <div class="modal-footer" style="background:#f0f0f0;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="pbl-btn-print" class="btn btn-danger btn-lg">
                        <i class="fa fa-print"></i>&nbsp; Open Print Page
                    </button>
                </div>

            </div>{{-- /modal-content --}}
        </div>{{-- /modal-dialog --}}
    </div>{{-- /#printBarcodeModal --}}

@endsection
@push('js')
    {{-- milon/barcode styles (inline table cells need colour fix) --}}
    <style>
        /* ── Print Barcode Modal Premium Styles ── */
        #printBarcodeModal .modal-dialog { max-width: 780px; }
        #pbl-btn-print { background: #e94560; border-color: #e94560; }
        #pbl-btn-print:hover { background: #c73652; border-color: #c73652; }
        #pbl-btn-generate { min-width: 150px; }
    </style>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-datepicker.js') }}"></script>
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script>
        $(document).ready(function(){
            $(".select2").select2({
                placeholder: "Select Item"
            });
        });
    </script>
    <script type='text/javascript' src="{{asset('assets/js/barcode.js')}}"></script>
    <script src="{{ asset('/vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('#lfm').filemanager('image', {prefix: '/filemanager'});
            $(document).scannerDetection({
                timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
                avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
                ignoreIfFocusOn: 'input', // turn off scanner detection if an input has focus
                startChar: [16], // Prefix character for the cabled scanner (OPL6845R)
                endChar: [40],
                onComplete: function(barcode){
                    $('#barcode').html(barcode);
                    $('#text_barcode').val(barcode);
                    setTimeout(function(){
                        $('#myModal').modal('hide');
                    },1200)
                }, // main callback function
                scanButtonKeyCode: 116, // the hardware scan button acts as key 116 (F5)
                scanButtonLongPressThreshold: 5, // assume a long press if 5 or more events come in sequence
                onScanButtonLongPressed: function(){
                    alert('key pressed');
                }, // callback for long pressing the scan button
                onError: function(string){}
            });

            if($("#expire_stock").val() == "0"){
                $("#expiry_date").removeAttr("required").attr("style","display:none").attr("disabled","disabled");
                $("#expiry_date").parent().find('label').attr("style","display:none");
            }else if($("#expire_stock").val() == "1" && $("#expiry_date").attr('style') === "display:none"){
                $("#expiry_date").attr("required","required").removeAttr('style').removeAttr("disabled");
                $("#expiry_date").parent().find('label').removeAttr("style");
            }

            $("#expire_stock").on("change",function(e){
                if($(this).val() == "0"){
                    $("#expiry_date").removeAttr("required").attr("style","display:none").attr("disabled","disabled");
                    $("#expiry_date").parent().find('label').attr("style","display:none");
                }else if($(this).val() == "1" && $("#expiry_date").attr('style') === "display:none"){
                    $("#expiry_date").attr("required","required").removeAttr('style').removeAttr("disabled");
                    $("#expiry_date").parent().find('label').removeAttr("style");
                }
            });

        });


        function getImage(){
            if($('#stock_name').val() !="") {
                $.ajax({
                    url: '{{ route('findimage') }}' + '?name=' + $('#stock_name').val(),
                    method: 'GET',
                    success: function (returnData) {
                        if (returnData.status == true) {
                            alert('Image Found');
                            $('#product_image').attr('src', returnData.link);
                        } else {
                            alert('image not found!');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred, unable to fetch image')
                    }
                });
            }else{
                alert('Please enter stock name');
            }
        }

    </script>

    {{-- ============================================================
         PRINT BARCODE MODAL — JavaScript
    ============================================================ --}}
    <script>
    (function () {
        'use strict';

        var currentBarcode = '';
        var stockId        = '';
        var generateUrl    = '';
        var printUrl       = '';
        var csrfToken      = '';

        // ── On modal shown ────────────────────────────────────────────
        $('#printBarcodeModal').on('show.bs.modal', function () {
            var btn       = $('#btnOpenPrintModal');
            stockId       = btn.data('stock-id');
            currentBarcode = $.trim(btn.data('barcode') || '');
            generateUrl   = btn.data('generate-url');
            printUrl      = btn.data('print-url');
            csrfToken     = btn.data('csrf');

            // Stock name in header
            $('#pbl-stock-name').text($('#stock_name').val() || '');

            // If barcode field in form was updated (scan after generate), pick it up
            var formBarcode = $.trim($('#text_barcode').val() || '');
            if (formBarcode) currentBarcode = formBarcode;

            if (!currentBarcode) {
                // No barcode — show alert, hide preview & print button
                $('#pbl-no-barcode-alert').show();
                $('#pbl-preview-wrap').hide();
                $('#pbl-btn-print').hide();
                $('#pbl-generate-result').hide();
                $('#pbl-generated-code').text('');
            } else {
                $('#pbl-no-barcode-alert').hide();
                $('#pbl-preview-wrap').show();
                $('#pbl-btn-print').show();
                refreshPreview();
            }
        });

        // ── Generate Code ─────────────────────────────────────────────
        $('#pbl-btn-generate').on('click', function () {
            var $btn = $(this).prop('disabled', true).text('Generating...');

            $.ajax({
                url:    generateUrl,
                method: 'POST',
                data:   { _token: csrfToken },
                success: function (res) {
                    if (res.success) {
                        currentBarcode = res.barcode;

                        // Update form field & modal barcode display
                        $('#text_barcode').val(currentBarcode);
                        $('#barcode').html(currentBarcode);        // scanner modal display
                        $('#btnOpenPrintModal').data('barcode', currentBarcode);

                        // Show result pill
                        $('#pbl-generated-code').text(currentBarcode);
                        $('#pbl-generate-result').show();

                        // Reveal preview & print button
                        $('#pbl-preview-wrap').show();
                        $('#pbl-btn-print').show();
                        refreshPreview();
                    } else {
                        alert('Could not generate barcode. Please try again.');
                    }
                },
                error: function () {
                    alert('Server error while generating barcode.');
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-magic"></i>&nbsp; Generate Code');
                }
            });
        });

        // ── Live preview refresh on control change ────────────────────
        $('#pbl-size, #pbl-type, #pbl-copies').on('change input', function () {
            if (currentBarcode) refreshPreview();
        });

        function refreshPreview() {
            var size   = $('#pbl-size').val();
            var type   = $('#pbl-type').val();
            var copies = 1; // always preview with 1 copy
            var url    = printUrl
                            + '?size='   + encodeURIComponent(size)
                            + '&type='   + encodeURIComponent(type)
                            + '&copies=' + copies
                            + '&preview=1';
            $('#pbl-preview-frame').attr('src', url);
        }

        // ── Open print page in new tab ────────────────────────────────
        $('#pbl-btn-print').on('click', function () {
            var size   = $('#pbl-size').val();
            var type   = $('#pbl-type').val();
            var copies = parseInt($('#pbl-copies').val()) || 1;
            var url    = printUrl
                            + '?size='   + encodeURIComponent(size)
                            + '&type='   + encodeURIComponent(type)
                            + '&copies=' + copies;
            window.open(url, '_blank');
        });

        // ── After scanner captures a barcode, update modal state ──────
        // The existing scanner code sets #text_barcode. We watch it.
        var _origComplete = null; // extend scanner callback safely
        $(document).on('barcodeScannerCaptured', function (e, code) {
            if (code) {
                currentBarcode = code;
                $('#btnOpenPrintModal').data('barcode', code);
            }
        });

    }());
    </script>

@endpush
