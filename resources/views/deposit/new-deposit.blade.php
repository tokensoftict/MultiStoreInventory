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
                                            <th class="text-right" colspan="2">Sub Total</th>
                                            <th class="text-right"  colspan="2" id="sub_total">0.00</th>
                                            <th class="text-right"></th>
                                        </tr>
                                        <tr>
                                            <th class="text-left"></th>
                                            <th class="text-left"></th>
                                            <th class="text-center" ></th>
                                            <th class="text-center" ></th>
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
                            @if(userCanView('deposit.create'))
                                <section class="panel">
                                    <section class="panel-body panel-border text-center">
                                        <div class="col-sm-10 col-lg-offset-1">
                                            <div class="row mtop-10">
                                                <div class="col-sm-12">
                                                    @if(userCanView('deposit.create'))
                                                        <button type="button"  data-status="PENDING" class="btn  btn-dark btn-lg" onclick="return ProcessDeposit(this);">Create Deposit</button>
                                                        <br/><br/>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </section>
                            @endif
                            <section class="panel">
                                <header class="panel-heading panel-border total_invoice text-center" style="font-size: 25px">0.00</header>
                            </section>
                            <section class="panel">
                                <header class="panel-heading panel-border">Deposit Info.</header>
                                <section class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="invoice_date">Deposit Date</label>
                                                @if(userCanView('deposit.allow_user_to_change_deposit_date'))
                                                    <input value="{{ date('Y-m-d') }}" data-min-view="2" data-date-format="yyyy-mm-dd" class="form-control datepicker js-datepicker" id="invoice_date" placeholder="Deposit Date" type="text">
                                                @else
                                                    <input type="hidden" value="{{ date('Y-m-d') }}"   id="invoice_date">
                                                    <span class="form-control">{{ date('Y-m-d') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Customer Name</label>
                                                <select class="form-control  select-customer"  name="customer" id="customer_id">
                                                    @foreach($customers as $customer)
                                                        <option {{ $customer->id == $c->id ? "selected" : "" }} value="{{ $customer->id }}">{{ $customer->firstname }} {{ $customer->lastname }}</option>
                                                    @endforeach
                                                </select>
                                                <a href="#" data-toggle="modal" data-target="#newCustomer" class="text-success" style="display: block;text-align: center">Add New Customer</a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6" style="margin-top: 0px">
                                            <img id="imageThumb" src="{{ asset('assets/products.jpg') }}" class="img-thumbnail">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Deposit Number</label>
                                                <input class="form-control" value="{{ $deposit_number }}" id="invoice_paper_number"  placeholder="Deposit Number" type="text">
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

        function bindAllTr(){
            $('#appender').find('tr').off('click');
            $('#appender').find('tr').on('click',function(){
                $('.picture').prop('checked',false);
                $(this).find('.picture').prop('checked', true);
                $('#imageThumb').attr('src',$(this).find('.picture').attr('data-image'));
            });
        }

        function appendToTable(data){
            //if(!document.getElementById('product_'+data.stock.id)) {
            $('#appender').prepend(cartTemplate(data));
            $('.picture').prop('checked',false);
            $('#'+'product_'+data.stock.id).find('.picture').prop('checked', true);
            $('#imageThumb').attr('src', $('#'+'product_'+data.stock.id).find('.picture').attr('data-image'));
            bindIncrement();
            bindAllTr();
            bindproductType();
            calculateTotal();
            // }else{
            //     alert(data.stock.name+' already exist in cart')
            // }

        }


        function bindproductType(){
            $('.product_type').off('change');
            $('.product_type').on('change',function(){
                const price = $(this).parent().parent().find('.input-number');
                const td_price = $(this).parent().parent().find('.item_price');
                price.attr('max',$('option:selected', this).attr('data-av-qty'));
                price.attr('data-price',$('option:selected', this).attr('data-price'));
                price.attr('data-cost-price',$('option:selected', this).attr('data-cost-price'));
                td_price.html('<input type="text" step="0.00000001" class="item_text_price form-control" value="'+$('option:selected', this).attr('data-price')+'"/>')
                bindproductType();
                bindIncrement();
                calculateTotal();
            });
        }

        function bindIncrement(){
            let qty_before = 1;

            $('.item_text_price').off('keyup');
            $('.item_text_price').on('keyup',function(){
                const textb = $(this).parent().parent().find('.input-number');
                textb.attr('data-price',$(this).val());
                calculateTotal();
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
                product.push(
                    {
                        id: $(this).attr('data-id'),
                        qty: $(this).val(),
                        type: type.val(),
                        price : $(this).attr('data-price'),
                        cost_price : $(this).attr('data-cost-price')
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
            @if($settings['allow_edit_price'] == "Yes")
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-price="`+data.stock.selling_price+`" data-cost-price="`+data.stock.cost_price+`" style="width:100px;display: block;" required="" max="`+data.stock.available_quantity+`" min="1" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td><th class="text-right item_price"><input type="text" step="0.00000001" class="item_text_price form-control" value="`+data.stock.selling_price+`"/></th><th class="text-right item_total">`+formatMoney(data.stock.selling_price)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
                    @else
                    {!! 'return `<tr style="cursor: pointer" id="product_`+data.stock.id+`"><th  class="text-center"><input data-image="`+data.stock.image+`" name="picture" class="picture" value="1" type="radio"></th><th>`+data.stock.name+`<div id="error_`+data.stock.id+`" class="errors alert alert-danger" `+(data[`error`] ? `` : `style="display:none;"`)+`>`+(data[`error`] ? data[`error`] : ``)+`</div>`+`</th><td><div class="col-md-4"><div class="input-group"> <span class="input-group-btn input-group-sm"> <button  data-field="quant[1]" type="button" class="btn btn-danger btn-number minus" data-type="minus"> <i class="fa fa-minus"></i></button></span><input class="form-control text-center input-number"  data-id="`+data.stock.id+`" data-price="`+data.stock.selling_price+`" data-cost-price="`+data.stock.cost_price+`" style="width:100px;display: block;" required="" max="`+data.stock.available_quantity+`" min="1" type="number" value="1"> <span class="input-group-btn"> <button type="button" class="btn btn-primary btn-number plus" data-type="plus"><i class="fa fa-plus"></i> </button> </span></div></div><td>`+type_select+`</td><th class="text-right item_price"><span class="item_text_price form-control" value="`+data.stock.selling_price+`">`+formatMoney(data.stock.selling_price)+`</span></th><th class="text-right item_total">`+formatMoney(data.stock.selling_price)+`</th><td class="text-right"> <a href="#" onclick="return removeItem(this);" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td></tr>`;' !!}
                    @endif
        }

        function ProcessDeposit(btn){

            const stock = wrapItemIncart();

            if(stock.length === 0){
                alert('You can not submit an empty cart, please add product to continue');
                return false;
            }


            if($('#invoice_paper_number').val() == ""){
                alert('Please enter Deposit Number from the manual invoice');
                return false;
            }


            if($('#customer_id').val() === "1" || $('#customer_id').val() === ""){
                alert("Please select a customer to proceed");
                return false;
            }


            let payment_payment = false;

            let status = $(btn).attr('data-status');


            if(!document.getElementById('customer_id')){
                alert('Please select a customer to proceed..');
                return false;
            }

            $('.submit_btn').attr('disabled','disabled');
            $('.errors').attr('style','display:none');
            showMask('Creating Deposit, Please wait...');

            var timeout = setTimeout(function(){
                hideMask();
                alert('error - request timeout, please try generating the invoice again');
                $('.submit_btn').removeAttr('disabled');
            },30000);
            $.ajax({
                url: '{{ route('deposit.create') }}',
                method : 'POST',
                data: {
                    'data' : JSON.stringify(stock),
                    "_token": "{{ csrf_token() }}",
                    'status': status,
                    'customer_id' :$('#customer_id').val(),
                    'date':$('#invoice_date').val(),
                    'invoice_paper_number' : $('#invoice_paper_number').val(),
                    'payment' : JSON.stringify(payment_payment)
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


    </script>


@endpush
