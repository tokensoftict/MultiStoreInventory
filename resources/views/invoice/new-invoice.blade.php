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
                                    <table class="table table-condensed table-bordered" style="font-size: 11px">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th style="width: 25%;">Name</th>
                                            <th>Quantity</th>
                                            <th style="width: 15%;">Type</th>
                                            @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                                            <th style="width: 15%;">Price Category</th>
                                            @endif
                                            <th style="width: 15%;">Price</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="appender">

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-center"></th>
                                            @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                                            <th class="text-left"></th>
                                            @endif
                                            <th class="text-right" colspan="2">Sub Total</th>
                                            <th class="text-right"  colspan="2" id="sub_total">0.00</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-center" ></th>
                                            <th class="text-center" ></th>
                                            @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                                            <th class="text-left"></th>
                                            @endif
                                            <th class="text-right" colspan="2">Total</th>
                                            <th class="text-right total_invoice" colspan="2" style="font-size: 15px;">0.00</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </section>
                            </section>
                        </div>
                        <div class="col-sm-4">
                            <section class="panel">
                                <header class="panel-heading panel-border total_invoice text-center" style="font-size: 25px">0.00</header>
                            </section>

                            <section class="panel">
                                <header class="panel-heading panel-border">Payment Info.</header>
                                <section class="panel-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Payment Method</label>
                                        <select class="form-control" name="payment_method" id="payment_method">
                                            <option value="">Select Payment Method</option>
                                            @foreach($payments as $payment)
                                                <option  data-label="{{ strtolower( $payment->name) }}"  value="{{  $payment->id }}">{{  $payment->name }}</option>
                                            @endforeach
                                            <option  data-label="split_method"  value="split_method">MULTIPLE PAYMENT METHOD</option>
                                        </select>
                                    </div>
                                    <div id="more_info_appender">
                                    </div>
                                </section>
                            </section>

                            <section class="panel">
                                <header class="panel-heading panel-border" style="padding: 8px 15px;">Invoice Info.</header>
                                <section class="panel-body" style="padding: 10px 15px;">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" style="margin-bottom: 5px;">
                                                <label for="invoice_date" style="font-size: 11px; margin-bottom: 2px;">Invoice / Sales date</label>
                                                @if(userCanView('invoiceandsales.allow_user_to_change_invoice_date'))
                                                    <input value="{{ date('Y-m-d') }}" data-min-view="2" data-date-format="yyyy-mm-dd" class="form-control datepicker js-datepicker" id="invoice_date" placeholder="Invoice / Sales date" type="text" style="height: 30px; font-size: 11px;">
                                                @else
                                                    <input type="hidden" value="{{ date('Y-m-d') }}"   id="invoice_date">
                                                    <span class="form-control" style="height: 30px; font-size: 11px; line-height: 20px;">{{ date('Y-m-d') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" style="margin-bottom: 5px;">
                                                <label for="exampleInputEmail1" style="font-size: 11px; margin-bottom: 2px;">Customer Name</label>
                                                <select class="form-control  select-customer"  name="customer" id="customer_id" style="height: 30px; font-size: 11px; padding: 2px 6px;">
                                                    @foreach($customers as $customer)
                                                        <option {{ $customer->id == $c->id ? "selected" : "" }} value="{{ $customer->id }}">{{ $customer->firstname }} {{ $customer->lastname }}</option>
                                                    @endforeach
                                                </select>
                                                <a href="#" data-toggle="modal" data-target="#newCustomer" class="text-success" style="display: block; text-align: center; font-size: 10px; margin-top: 2px;">Add New Customer</a>
                                            </div>

                                    </div>
                                    <input type="hidden" name="price_category_id" id="price_category_id" value="">
                                    <div class="row" style="margin-top: 5px;">
                                        <div class="col-sm-6" style="margin-top: 0px; text-align: center;">
                                            <img id="imageThumb" src="{{ asset('assets/products.jpg') }}" class="img-thumbnail" style="max-height: 50px; max-width: 100px; object-fit: contain;">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" style="margin-bottom: 5px;">
                                                <label for="exampleInputEmail1" style="font-size: 11px; margin-bottom: 2px;">Invoice / Receipt No</label>
                                                <input class="form-control" value="{{ $invoice_number }}" id="invoice_paper_number"  placeholder="Invoice / Receipt No" type="text" style="height: 30px; font-size: 11px;">
                                            </div>
                                        </div>
                                    </div>

                                    @if(userCanView('invoiceandsales.create'))
                                        <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                                            <div class="row">
                                                <div class="col-sm-12" style="margin-bottom: 5px;">
                                                    @if(userCanView('invoiceandsales.request_for_discount'))
                                                        <button type="button" data-status="DISCOUNT" class="btn btn-dark btn-sm btn-block" onclick="return ProcessInvoice(this);">Request For Discount</button>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    @if(userCanView('invoiceandsales.draft_invoice'))
                                                        <button type="button" data-status="DRAFT" class="btn btn-success btn-sm btn-block" onclick="return ProcessInvoice(this);">Save Draft</button>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6">
                                                    @if(userCanView('invoiceandsales.complete_invoice'))
                                                        <button type="button" data-status="COMPLETE" class="btn btn-primary btn-sm btn-block" onclick="return ProcessInvoice(this);">Complete</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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

        function bindAllTr(){
            $('#appender').find('tr').off('click');
            $('#appender').find('tr').on('click',function(){
                $('.picture').prop('checked',false);
                $(this).find('.picture').prop('checked', true);
                $('#imageThumb').attr('src',$(this).find('.picture').attr('data-image'));
            });
        }

        function appendToTable(data){
            let defaultType = "{{ getActiveStore()->packed_column }}";
            if(data.stock.type != "Normal" && parseInt(data.stock.available_quantity) == 0 && parseInt(data.stock.available_yard_quantity) > 0){
                defaultType = "{{ getActiveStore()->yard_column }}";
            }

            // Check if there is already a row for this product with the defaultType
            var existingInput = $('#appender').find('.input-number[data-id="' + data.stock.id + '"]').filter(function() {
                return $(this).closest('tr').find('.product_type').val() === defaultType;
            });

            if (existingInput.length > 0) {
                var currentVal = parseInt(existingInput.val());
                var maxVal = parseInt(existingInput.attr('max'));
                if (currentVal < maxVal) {
                    existingInput.val(currentVal + 1).change();
                } else {
                    alert('Not enough quantity to add more of ' + data.stock.name);
                }
            } else {
                $('#appender').prepend(cartTemplate(data));
                $('.picture').prop('checked',false);
                var trId = 'product_' + data.stock.id + '_' + defaultType;
                $('#appender').find('tr').first().attr('id', trId);
                $('#'+trId).find('.picture').prop('checked', true);
                $('#imageThumb').attr('src', $('#'+trId).find('.picture').attr('data-image'));
                bindIncrement();
                bindAllTr();
                bindproductType();
                calculateTotal();
            }
        }


        function updateRowPrice(tr) {
            let input = tr.find('.input-number');
            let productType = tr.find('.product_type').val(); // packed_column or yard_column
            let headerCategory = $('#price_category_id').val();
            let itemCategory = tr.find('.item_price_category').val();
            let selectedCategoryId = itemCategory === 'default' ? headerCategory : itemCategory;
            
            let price = 0;
            let found = false;
            
            if (selectedCategoryId) {
                let stockPrices = [];
                try {
                    stockPrices = JSON.parse(input.attr('data-stock-prices') || '[]');
                } catch(e) {}
                
                let matched = stockPrices.find(function(sp) {
                    return sp.price_category_id == selectedCategoryId;
                });
                
                if (matched) {
                    price = parseFloat(matched.price);
                    found = true;
                }
            }
            
            if (!found) {
                if (productType === "{{ getActiveStore()->yard_column }}") {
                    price = parseFloat(input.attr('data-yard-price') || 0);
                } else {
                    price = parseFloat(input.attr('data-packed-price') || 0);
                }
            }
            
            input.attr('data-price', price);
            let td_price = tr.find('.item_price');
            @if($settings['allow_edit_price'] == "Yes")
                td_price.html('<input type="text" step="0.00000001" class="item_text_price form-control" value="'+formatMoney(price)+'"/>');
            @else
                td_price.html('<span class="item_text_price form-control" value="'+price+'">'+formatMoney(price)+'</span>');
            @endif
        }

        function bindproductType(){
            $('.product_type').off('change');
            $('.product_type').on('change',function(){
                const price = $(this).parent().parent().find('.input-number');
                const td_price = $(this).parent().parent().find('.item_price');
                const newType = $(this).val();
                const stockId = price.attr('data-id');
                const tr = $(this).closest('tr');

                // Check if another row with same stockId and newType exists
                var duplicateInput = $('#appender').find('.input-number[data-id="' + stockId + '"]').filter(function() {
                    return $(this).closest('tr')[0] !== tr[0] && $(this).closest('tr').find('.product_type').val() === newType;
                });

                if (duplicateInput.length > 0) {
                    // Merge quantities
                    var currentVal = parseInt(price.val());
                    var duplicateVal = parseInt(duplicateInput.val());
                    var duplicateMax = parseInt(duplicateInput.attr('max'));
                    if (duplicateVal + currentVal <= duplicateMax) {
                        duplicateInput.val(duplicateVal + currentVal).change();
                        tr.remove();
                        calculateTotal();
                        return;
                    } else {
                        alert('Merging would exceed the available stock limit for this type. Cannot change type.');
                        // revert dropdown selection
                        $(this).val(newType === "{{ getActiveStore()->packed_column }}" ? "{{ getActiveStore()->yard_column }}" : "{{ getActiveStore()->packed_column }}");
                        return;
                    }
                }

                price.attr('max',$('option:selected', this).attr('data-av-qty'));
                price.attr('data-cost-price',$('option:selected', this).attr('data-cost-price'));
                updateRowPrice(tr);
                tr.attr('id', 'product_' + stockId + '_' + newType);

                bindproductType();
                bindIncrement();
                calculateTotal();
            });

            $('.item_price_category').off('change');
            $('.item_price_category').on('change', function() {
                let tr = $(this).closest('tr');
                updateRowPrice(tr);
                calculateTotal();
            });
        }

        function bindIncrement(){
            let qty_before = 1;

            $('.item_text_price').off('keyup');
            $('.item_text_price').on('keyup',function(){
                const textb = $(this).parent().parent().find('.input-number');
                let val = $(this).val().replace(/,/g, '');
                textb.attr('data-price', val);
                calculateTotal();
            });

            $('.item_text_price').off('blur');
            $('.item_text_price').on('blur', function(){
                let val = $(this).val().replace(/,/g, '');
                if(isNaN(val) || val === '') {
                    val = 0;
                }
                $(this).val(formatMoney(val));
            });
            $('.btn-number').off("click");
            $('.btn-number').click(function(e){
                e.preventDefault();
                fieldName = $(this).attr('data-field');
                type      = $(this).attr('data-type');
                var input = $(this).parent().parent().find('.form-control');
                var currentVal = parseInt(input.val());
                if (!isNaN(currentVal)) {
                    if(type == 'minus') {

                        if(currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if(parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }
                        if(parseInt(input.val()) < input.attr('max')){
                            $(this).parent().parent().find('.plus').removeAttr('disabled')
                        }

                    } else if(type == 'plus') {

                        if(currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if(parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        }

                        if(parseInt(input.val()) > input.attr('min')){
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
                if(parseInt($(this).val()) < parseInt($(this).attr('max'))) {
                    qty_before = $(this).val();
                }
            })

            $('.input-number').off('change');
            $('.input-number').on('change',function(){
                if(parseInt($(this).val()) > parseInt($(this).attr('max'))){
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
                const total = $(this).parent().parent().parent().parent();
                const total_td = total.find('.item_total');
                const _total = (parseInt($(this).val()) * parseFloat($(this).attr('data-price')));
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
            $('.input-number').each(function(index, elem){
                const type = $(this).parent().parent().parent().parent().find('.product_type');
                const tr = $(this).closest('tr');
                const headerCategory = $('#price_category_id').val();
                const itemCategory = tr.find('.item_price_category').val();
                const selectedCategory = itemCategory === 'default' ? headerCategory : itemCategory;

                product.push(
                    {
                        id: $(this).attr('data-id'),
                        qty: $(this).val(),
                        type: type.val(),
                        price : $(this).attr('data-price'),
                        cost_price : $(this).attr('data-cost-price'),
                        price_category_id : selectedCategory || null
                    }
                );
                if(parseInt($(elem).val()) > parseInt($(elem).attr('max'))){
                    error_status = true;
                }
            });
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
            if(data.stock.type != "Normal"){
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

            if(data.stock.available_quantity == 0 && data.stock.available_yard_quantity >0){
                data.stock.available_quantity = data.stock.available_yard_quantity;
                data.stock.selling_price = data.stock.yard_selling_price
            }

            let price_category_select = "";
            @if(!empty($active_price_categories) && count($active_price_categories) > 0)
                price_category_select += '<td><select class="form-control item_price_category">';
                price_category_select += '<option value="default">Default</option>';
                @foreach($active_price_categories as $pc)
                    price_category_select += '<option value="{{ $pc->id }}" data-price-type="{{ $pc->price_type }}">{{ $pc->name }}</option>';
                @endforeach
                price_category_select += '</select></td>';
            @endif

            let currentPrice = data.stock.selling_price;
            let headerCategory = $('#price_category_id').val();
            if (headerCategory) {
                let matched = (data.stock.stock_prices || []).find(function(sp) {
                    return sp.price_category_id == headerCategory;
                });
                if (matched) {
                    currentPrice = matched.price;
                }
            }

            @if($settings['allow_edit_price'] == "Yes")
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-packed-price="`+data.stock.selling_price+`" data-yard-price="`+data.stock.yard_selling_price+`" data-stock-prices=\'`+JSON.stringify(data.stock.stock_prices || [])+`\' data-price="`+currentPrice+`" data-cost-price="`+data.stock.cost_price+`" style="width:100px;display: block;" required="" max="`+data.stock.available_quantity+`" min="1" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td>`+price_category_select+`<th class="text-right item_price"><input type="text" step="0.00000001" class="item_text_price form-control" value="`+formatMoney(currentPrice)+`"/></th><th class="text-right item_total">`+formatMoney(currentPrice)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
                    @else
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-packed-price="`+data.stock.selling_price+`" data-yard-price="`+data.stock.yard_selling_price+`" data-stock-prices=\'`+JSON.stringify(data.stock.stock_prices || [])+`\' data-price="`+currentPrice+`" data-cost-price="`+data.stock.cost_price+`" style="width:100px;display: block;" required="" max="`+data.stock.available_quantity+`" min="1" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td>`+price_category_select+`<th class="text-right item_price"><span class="item_text_price form-control" value="`+currentPrice+`">`+formatMoney(currentPrice)+`</span></th><th class="text-right item_total">`+formatMoney(currentPrice)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
                    @endif
        }

        function ProcessInvoice(btn){

            const stock = wrapItemIncart();

            if(stock.length === 0){
                alert('You can not submit an empty cart, please add product to continue');
                return false;
            }


            if($('#invoice_paper_number').val() == ""){
                alert('Please enter Invoice / Receipt No from the manual invoice');
                return false;
            }


            let payment_payment = false;

            let status = $(btn).attr('data-status');

            if(status === "COMPLETE"){

                payment_payment = getPaymentInfo();

                if(payment_payment == false) return ;

            }



            if(!document.getElementById('customer_id')){
                alert('Please select a customer to proceed..');
                return false;
            }

            $('.submit_btn').attr('disabled','disabled');
            $('.errors').attr('style','display:none');
            showMask('Creating Invoice, Please wait...');

            var timeout = setTimeout(function(){
                hideMask();
                alert('error - request timeout, please try generating the invoice again');
                $('.submit_btn').removeAttr('disabled');
            },30000);
            $.ajax({
                url: '{{ route('invoiceandsales.create') }}',
                method : 'POST',
                data: {
                    'data' : JSON.stringify(stock),
                    "_token": "{{ csrf_token() }}",
                    'status': status,
                    'customer_id' :$('#customer_id').val(),
                    'date':$('#invoice_date').val(),
                    'invoice_paper_number' : $('#invoice_paper_number').val(),
                    'payment' : JSON.stringify(payment_payment),
                    'price_category_id': $('#price_category_id').val()
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
                        if(res.error === "Customer has reached the credit limit, transaction can not continue"){
                            alert(res.error)
                        }else {
                            var errors = res.error;
                            for (let key in errors) {
                                if (document.getElementById('error_' + key)) {
                                    $(document.getElementById('error_' + key)).html(errors[key]).removeAttr('style');
                                }
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

        function getTotalSplitPayemnt(){
            let total =0;
            $('.split_control').each(function(){
                total+= parseFloat($(this).val());
            })
            return total;
        }

        function getPaymentInfo(){
            if($('#payment_method').val() === ""){
                alert("Please enter payment Information to complete invoice");
                return false;
            }

            if($('#payment_method').val() == "1"){
                /*
                if($('#cash_tendered').val() == ""){
                    alert("Please enter cash tendered by customer");
                    return false;
                }

                 */
                return {
                    'cash_tendered':$('#cash_tendered').val(),
                    'customer_change':$('#customer_change').html(),
                    'payment_method_id' : 1
                };
            }else if($('#payment_method').val() == "2"){
                if( $('#bank').val() === ""){
                    alert("Please select bank");
                    return false;
                }
                return {
                    'payment_method_id' : 2,
                    'bank_id' : $('#bank').val(),
                }

            }else if($('#payment_method').val() === "3"){
                if( $('#bank').val() === ""){
                    alert("Please select bank");
                    return false;
                }
                return {
                    'payment_method_id' : 3,
                    'bank_id' : $('#bank').val(),
                }

            }else if($('#payment_method').val() === "split_method"){
                let data = [];
                let payment_info_data = [];
                let total = 0;
                let error = false;
                $('.split_control').each(function(){


                    //&& $('#customer_id').val() === "1"
                    if(getTotalSplitPayemnt() !== calculateTotal() )
                    {
                        alert("Total amount entered is not equal to total invoice amount");
                        error = true;
                        return false;
                    }



                    data[$(this).attr('data-key')] = $(this).val();

                    payment_info_data[$(this).attr('data-key')] = {};

                    if($(this).attr('data-key') == "3"){
                        if( $('#bank_id_3').val() === "" &&  parseFloat($(this).val()) > 0){
                            alert("Please select Transfer Bank");
                            $('#bank_id_3').focus();
                            error = true;
                            return false;
                        }
                        payment_info_data[$(this).attr('data-key')] = {
                            'payment_method_id' : 3,
                            'bank_id' : $('#bank_id_3').val(),
                        }
                    }else if($(this).attr('data-key') == "2"){
                        if( $('#bank_id_2').val() === "" &&  parseFloat($(this).val()) > 0){
                            alert("Please select POS Bank");
                            $('#bank_id_2').focus();
                            error = true;
                            return false;
                        }
                        payment_info_data[$(this).attr('data-key')] = {
                            'payment_method_id' : 3,
                            'bank_id' : $('#bank_id_2').val(),
                        }
                    }else if($(this).attr('data-key') == "1"){
                        payment_info_data[$(this).attr('data-key')] = {
                            'payment_method_id' : 1,
                            'method' : "cash",
                        }
                    }else if($(this).attr('data-key') == "4"){
                        payment_info_data[$(this).attr('data-key')] = {
                            'payment_method_id' : 4,
                            'credit' : "credit"
                        }
                    }

                    total+=parseFloat($(this).val());
                });
                if( error === true){
                    return false;
                }

                return {
                    'split_method':data,
                    'payment_method_id':$('#payment_method').val(),
                    'payment_info_data' : payment_info_data
                };

            }else if($('#payment_method').val() ==="4")
            {
                if($('#customer_id').val() === "1"){
                    alert("You can not sell credit to a Generic Customer, Please select real customer");
                    return false;
                }

                return {
                    'payment_method_id' : 4,
                    'credit' : "credit"
                }
            }else{
                return {
                    'payment_method_id' : $('#payment_method').val(),
                    'other' : "other"
                }
            }
        }


        $(document).ready(function(){
            $("#payment_method").on("change",function () {
                if($(this).val() !=="") {
                    var selected = $("#payment_method option:selected").attr("data-label");
                    selected = selected.toLowerCase();
                    if (selected === "transfer") {
                        $("#more_info_appender").html('<div id="transfer"><div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="bank"><option value="">-Select Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div></div>')
                    } else if (selected === "cash") {
                        /*
                        <div class="form-group"> <label>Cash Tendered</label> <input class="form-control" type="number" step="0.00001" id="cash_tendered" name="cash_tendered" required placeholder="Cash Tendered"/></div><div class="form-group well"><center>Customer Change</center><h1 align="center" style="font-size: 55px; margin: 0; padding: 0 font-weight: bold;" id="customer_change">0.00</h1></div>
                         */
                        $("#more_info_appender").html('<div id="cash"> <br/></div>')
                        handle_cash();
                    } else if (selected === "pos") {
                        $("#more_info_appender").html('<div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="bank"><option value="">-Select POS Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div>')
                    } else if (selected === "split_method") {
                        $("#more_info_appender").html('<div id="split_method"> <br/><h5>MULTIPLE PAYMENT METHOD</h5><table class="table table-striped"> @foreach($payments as $pmthod) @if($pmthod->id==4) @continue @endif<tr><td style="font-size: 15px;">{{ ucwords($pmthod->name) }}</td><td class="text-right" align="right"><input value="0" step="0.00001" required class="form-control pull-right split_control" style="width: 100px;" type="number" data-key="{{ $pmthod->id }}" name="split_method[{{ $pmthod->id }}]"</td><td>@if($pmthod->id == 2 || $pmthod->id==3)<select class="form-control" id="bank_id_{{ $pmthod->id }}"><option value="">Select Bank</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select>@endif</td></tr> @endforeach<tr><th style="font-size: 15px;" colspan="2">Total</th><th class="text-right" id="total_split" style="font-size: 26px;">0.00</th></tr></table></div>')
                        handle_split_method();
                    }else{
                        $("#more_info_appender").html('')
                    }
                }else{
                    $("#more_info_appender").html("");
                }
            });

            function handle_cash(){
                $("#cash_tendered").on("keyup",function(){
                    if($(this).val() !="") {
                        var val = parseFloat($(this).val());
                        if (val > 0) {
                            var change = val - calculateTotal() ;
                            $("#customer_change").html(formatMoney(change));
                        }
                    }else{
                        $("#customer_change").html("{{ number_format(0,2) }}");
                    }
                })
            }

            function handle_split_method(){
                $('.split_control').on("keyup",function(){
                    var total = calculateTotal();
                    $('.split_control').each(function(index,elem){
                        if($(elem).val() !="") {
                            total -= parseFloat($(elem).val());
                        }
                    });
                    $("#total_split").html(formatMoney(total));
                    if(total == calculateTotal()){
                        $("#payment_btn").removeAttr("disabled");
                    }else{
                        $("#payment_btn").removeAttr("disabled");
                    }
                })

            }



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

            $('#price_category_id').on('change', function() {
                $('#appender tr').each(function() {
                    updateRowPrice($(this));
                });
                calculateTotal();
            });


        });

    </script>


@endpush
