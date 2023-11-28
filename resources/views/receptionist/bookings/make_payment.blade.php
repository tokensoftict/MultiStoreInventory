@extends('layouts.app')


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <form action="{{ route('bookings_and_reservation.make_payment',$booking->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="exampleInputEmail1">Customer Information</label>
                                <span class="form-control">{{ $booking->customer->firstname }} {{ $booking->customer->lastname }}</span>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Booking / Reservation Number</label>
                                <span class="form-control">{{ $booking->reservation_number }}</span>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Total Amount Paid</label>
                                <input type="text" required name="total_amount_paid" id="total_amount_paid" class="form-control" value="{{ $booking->total - $booking->total_paid  }}"/>
                            </div>


                            <div class="form-group">
                                <label for="exampleInputEmail1">Booking date</label>
                                <span class="form-control">{{ str_date($booking->boking_date) }}</span>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Start Date</label>
                                <span class="form-control">{{ str_date($booking->start_date) }}</span>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">End Date</label>
                                <span class="form-control">{{ str_date($booking->end_date) }}</span>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Payment Method</label>
                                <select class="form-control" name="payment_method_id" id="payment_method">
                                    <option value="">Select Payment Method</option>
                                    @foreach($payments as $payment)
                                        <option  data-label="{{ strtolower( $payment->name) }}"  value="{{  $payment->id }}">{{  $payment->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="more_info_appender">
                            </div>

                            <button type="submit" class="btn btn-primary text-center"><i class="fa fa-save"></i> Pay Now</button>

                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>


@endsection


@push('js')
    <script>

        function getPaymentInfo(){
            if($('#payment_method').val() === ""){
                alert("Please enter payment Information to complete invoice");
                return false;
            }

            if($('#payment_method').val() == "1"){
                if($('#cash_tendered').val() == ""){
                    alert("Please enter cash tendered by customer");
                    return false;
                }
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

            }else if($('#payment_method').val() == "3"){
                if( $('#bank').val() === ""){
                    alert("Please select bank");
                    return false;
                }
                return {
                    'payment_method_id' : 3,
                    'bank_id' : $('#bank').val(),
                }

            }


            return false;
        }

        $(document).ready(function(){
            $("#payment_method").on("change",function () {
                if($(this).val() !=="") {
                    var selected = $("#payment_method option:selected").attr("data-label");
                    selected = selected.toLowerCase();
                    if (selected === "transfer") {
                        $("#more_info_appender").html('<div id="transfer"><div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="payment_info[bank]"><option value="">-Select Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div></div>')
                    } else if (selected === "cash") {
                        $("#more_info_appender").html('<div id="cash"> <br/><div class="form-group"> <label>Cash Tendered</label> <input class="form-control" type="number" step="0.00001" id="cash_tendered" name="payment_info[cash_tendered]" required placeholder="Cash Tendered"/></div><div class="form-group well"><center>Customer Change</center><h1 align="center" style="font-size: 55px; margin: 0; padding: 0 font-weight: bold;" id="customer_change">0.00</h1></div></div>')
                        handle_cash();
                    } else if (selected === "pos") {
                        $("#more_info_appender").html('<div class="form-group"> <label>Bank</label> <select class="form-control" required id="bank" name="payment_info[bank]"><option value="">-Select POS Bank-</option> @foreach($banks as $bank)<option value="{{ $bank->id }}">{{ $bank->account_number }} - {{ $bank->bank->name }}</option> @endforeach </select></div>')
                    } else if (selected === "split_method") {
                        $("#more_info_appender").html('<div id="split_method"> <br/><h5>Split Payment Method</h5><table class="table table-striped"> @foreach($payments as $pmthod)  @if((strtolower($pmthod->name) == "debit") && ($invoice->customer_id < 2)) @continue; @endif <tr><td style="font-size: 15px;">{{ ucwords($pmthod->name) }}</td><td class="text-right" align="right"><input value="0" step="0.00001" required class="form-control pull-right split_control" style="width: 100px;" type="number" name="split_method[{{ $pmthod->id }}]"</td></tr> @endforeach<tr><th style="font-size: 15px;">Total</th><th class="text-right" id="total_split" style="font-size: 26px;">0.00</th></tr></table></div>')
                        handle_split_method();
                    }else{
                        $("#more_info_appender").html('<br/><br/><button style="font-size: 24px;" name="payment" id="mybtn" value="payment" class="btn btn-lg btn-primary btn-block" id="mybtn" >Confirm Payment</button>')
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
                            var change = val - parseInt($('#total_amount_paid').val());
                            $("#customer_change").html(formatMoney(change));
                        }
                    }else{
                        $("#customer_change").html("{{ number_format(0,2) }}");
                    }
                })
            }

            function handle_split_method(){
                $('.split_control').on("keyup",function(){
                    var total = 0;
                    $('.split_control').each(function(index,elem){
                        if($(elem).val() !="") {
                            total += parseFloat($(elem).val());
                        }
                    });
                    $("#total_split").html(formatMoney(total));
                    if(total == {{ $booking->total }} ){
                        $("#payment_btn").removeAttr("disabled");
                    }else{
                        $("#payment_btn").removeAttr("disabled");
                    }
                })

            }


        });
    </script>
@endpush
