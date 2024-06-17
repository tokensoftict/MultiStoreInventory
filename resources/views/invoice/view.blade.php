@extends('layouts.app')

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title."- Invoice ".$invoice->id }}
                        <span class="pull-right">
                            @if($invoice->status != "DISCOUNT" && $invoice->status !== "DISCOUNT-APPLIED")
                                @if(userCanView('invoiceandsales.pos_print'))
                                    <a href="{{ route('invoiceandsales.pos_print',$invoice->id) }}" onclick="return open_print_window(this);" class="btn btn-success btn-sm" ><i class="fa fa-file"></i> Print POS</a>
                                @endif
                                @if(userCanView('invoiceandsales.print_afour'))
                                    <a href="{{ route('invoiceandsales.print_afour',$invoice->id) }}"  onclick="return open_print_window(this);" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> Print A4</a>
                                @endif
                                @if(userCanView('invoiceandsales.print_way_bill'))
                                    <a href="{{ route('invoiceandsales.print_way_bill',$invoice->id) }}"  onclick="return open_print_window(this);" class="btn btn-primary btn-sm" ><i class="fa fa-print"></i> Print Waybill</a>
                                @endif
                            @endif
                        </span>
                    </header>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            @if(session('success'))
                                {!! alert_success(session('success')) !!}
                            @elseif(session('error'))
                                {!! alert_error(session('error')) !!}
                            @endif
                            <div class="row">
                                <div class="col-sm-4">
                                    <h2>Bill To</h2>
                                    <div class="form-group">
                                        <strong>Customer Information:</strong><br>
                                        <div id="customer_info">
                                            <address>
                                                {{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}<br>
                                                @if(!empty($invoice->customer->address))
                                                    {{ $invoice->customer->address }}<br>
                                                @endif
                                                @if(!empty($invoice->customer->email))
                                                    {{ $invoice->customer->email }}<br>
                                                @endif
                                                {{ $invoice->customer->phone_number }}
                                            </address>
                                        </div>
                                    </div>
                                    <h2>Invoice Property</h2>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Invoice / Receipt Number</label><br/>
                                        <label style="font-size: 15px">{{ $invoice->invoice_paper_number }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Status</label><br/>
                                        <label style="font-size: 15px">{!! invoice_status($invoice->status) !!}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Invoice Date</label><br/>
                                        <label style="font-size: 15px"> {{ convert_date($invoice->invoice_date) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Time</label><br/>
                                        <label style="font-size: 15px"> {{ date("h:i a",strtotime($invoice->sales_time)) }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 12px">Sales Representative</label><br/>
                                        <label style="font-size: 15px">{{ $invoice->created_user->name }}</label>
                                    </div>

                                    @if($invoice->status == "DISCOUNT-APPLIED")
                                        <form id="complete_payment"  action="{{ route('invoiceandsales.create')."?no_stock=true" }}" method="post">
                                           @csrf
                                            <br/>
                                            <hr/>
                                            <br/>
                                            <h2>Complete Invoice</h2>
                                            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}"/>
                                            <input type="hidden" name="payment_info" value="" id="payment_info">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Payment Method</label>
                                                <select  class="form-control" name="payment_method_id" required id="payment_method">
                                                    <option value="">Select Payment Method</option>
                                                    @foreach($payments as $payment)
                                                        <option  data-label="{{ strtolower( $payment->name) }}"  value="{{  $payment->id }}">{{  $payment->name }}</option>
                                                    @endforeach
                                                    <option  data-label="split_method"  value="split_method">MULTIPLE PAYMENT METHOD</option>
                                                </select>
                                            </div>
                                            <div id="more_info_appender">
                                            </div>
                                            <button type="button" id="payment_btn" disabled onclick="submitForm()" class="btn btn-primary text-center"><i class="fa fa-save"></i> Complete Invoice</button>
                                        </form>
                                    @endif


                                </div>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <br/>
                                            <h2>Invoice Item(s)</h2>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <td>#</td>
                                                        <td align="left"><b>Name</b></td>
                                                        <td align="center"><b>Quantity</b></td>
                                                        <td align="center"><b>Price</b></td>
                                                        <td align="right"><b>Total</b></td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($invoice->invoice_items as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td align="left" class="text-left">{{ $item->stock->name }}</td>
                                                            <td align="center" class="text-center">{{ $item->quantity }}</td>
                                                            <td align="center" class="text-center">{{ number_format($item->selling_price,2) }}</td>
                                                            <td align="right" class="text-right">{{ number_format(($item->total_selling_price),2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Sub Total</td>
                                                        <td  align="right" class="text-right">{{ number_format($invoice->sub_total,2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Discount</td>
                                                        <td  align="right" class="text-right">-{{ number_format($invoice->discount_amount,2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td  align="right" class="text-right">Total</td>
                                                        <td  align="right" class="text-right" id="total_amount_paid" total="{{ $invoice->sub_total -$invoice->discount_amount }}"><b>{{ number_format(($invoice->sub_total -$invoice->discount_amount),2) }}</b></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>

                                                @if(userCanView("invoiceandsales.cancel_discount") && $invoice->status === "DISCOUNT-APPLIED")
                                                    <a href="{{ route('invoiceandsales.cancel_discount', $invoice->id) }}" class="btn btn-sm btn-danger">Remove Discount and send back to Draft</a>
                                                @endif
                                            </div>
                                        </div>
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

@push('js')
    <script>

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


                    if(getTotalSplitPayemnt() != '{{ $invoice->sub_total - $invoice->discount_amount }}' && "{{ $invoice->customer_id }}" == "1")
                    {
                        alert("You can not sell credit to a Generic Customer, Please select real customer");
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
                if("{{ $invoice->customer_id }}"== "1"){
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


        function submitForm()
        {
            const payment_payment = getPaymentInfo();
            if(payment_payment === false) {
                $('#payment_info').val("");
                return ;
            }

            $('#payment_info').val(JSON.stringify(payment_payment));

            if($('#payment_info').val() != ""){
                $('#complete_payment').submit();
                $('#payment_btn').attr('disabled', 'disabled');
            }
        }


        $(document).ready(function(){
            $("#payment_method").on("change",function () {
                if($(this).val() !=="") {
                    var selected = $("#payment_method option:selected").attr("data-label");
                    selected = selected.toLowerCase();
                    if (selected === "transfer") {
                        $("#payment_btn").removeAttr("disabled");
                        $("#more_info_appender").html('<div id="transfer"><div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="bank"><option value="">-Select Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div></div>')
                    } else if (selected === "cash") {
                        $("#payment_btn").removeAttr("disabled");
                        $("#more_info_appender").html('<div id="cash"> <br/><div class="form-group"> <label>Cash Tendered</label> <input class="form-control" type="number" step="0.00001" id="cash_tendered" name="cash_tendered" required placeholder="Cash Tendered"/></div><div class="form-group well"><center>Customer Change</center><h1 align="center" style="font-size: 55px; margin: 0; padding: 0 font-weight: bold;" id="customer_change">0.00</h1></div></div>')
                        handle_cash();
                    } else if (selected === "pos") {
                        $("#payment_btn").removeAttr("disabled");
                        $("#more_info_appender").html('<div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="bank"><option value="">-Select POS Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div>')
                    } else if (selected === "split_method") {
                        $("#more_info_appender").html('<div id="split_method"> <br/><h5>MULTIPLE PAYMENT METHOD</h5><table class="table table-striped"> @foreach($payments as $pmthod) @if($pmthod->id==4 && config('app.store') == "inventory") @continue @endif<tr><td style="font-size: 15px;">{{ ucwords($pmthod->name) }}</td><td class="text-right" align="right"><input value="0" step="0.00001" required class="form-control pull-right split_control" style="width: 100px;" type="number" data-key="{{ $pmthod->id }}" name="split_method[{{ $pmthod->id }}]"</td><td>@if($pmthod->id != 4 && $pmthod->id!=1)<select class="form-control" name="payment_info_data[{{ $pmthod->id }}]" id="bank_id_{{ $pmthod->id }}"><option value="">Select Bank</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select>@endif @if($pmthod->id==1) <input type="hidden" value="CASH" name="payment_info_data[{{ $pmthod->id }}]"/> @endif</td></tr> @endforeach<tr><th style="font-size: 15px;" colspan="2">Total</th><th class="text-right" id="total_split" style="font-size: 26px;">0.00</th></tr></table></div>')
                        handle_split_method();
                    }else{
                        $("#payment_btn").removeAttr("disabled");
                        $("#more_info_appender").html('')
                    }
                }else{
                    $("#payment_btn").removeAttr("disabled");
                    $("#more_info_appender").html("");
                }
            });

            function handle_cash(){
                $("#payment_btn").removeAttr("disabled");
                $("#cash_tendered").on("keyup",function(){
                    if($(this).val() !="") {
                        var val = parseFloat($(this).val());
                        if (val > 0) {
                            var change = val - parseInt($('#total_amount_paid').attr("total"));
                            $("#customer_change").html(formatMoney(change));
                        }
                    }else{
                        $("#customer_change").html("{{ number_format(0,2) }}");
                    }
                })
            }

            function handle_split_method(){
                $("#payment_btn").removeAttr("disabled");
                $('.split_control').on("keyup",function(){
                    var total = 0;
                    $('.split_control').each(function(index,elem){
                        if($(elem).val() !="") {
                            total += parseFloat($(elem).val());
                        }
                    });
                    $("#total_split").html(formatMoney(total));
                    if(total > {{ ($invoice->sub_total -$invoice->discount_amount) }} ){
                        $("#payment_btn").attr("disabled","disabled");
                    }else{
                        $("#payment_btn").removeAttr("disabled");
                    }
                })

            }


        });
    </script>
@endpush