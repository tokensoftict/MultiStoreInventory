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
                                                    <option {{ old('type', $stock->type) == "NORMAL" ? "selected" : "" }} value="{{ $type }}">{{ $key }}</option>
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

@endsection
@push('js')
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

@endpush
