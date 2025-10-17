<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Credit Payment Receipt</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 6pt;
            background-color: #fff;
        }

        #products {
            width: 100%;
        }
        #products th, #products td {
            padding-top:5px;
            padding-bottom:5px;
            border: 1px solid black;
        }
        #products tr td {
            font-size: 8pt;
        }

        #printbox {
            width: 98%;
            margin: 5pt;
            padding: 5px;
            margin: 0px auto;
            text-align: justify;
        }

        .inv_info tr td {
            padding-right: 10pt;
        }

        .product_row {
            margin: 15pt;
        }

        .stamp {
            margin: 5pt;
            padding: 3pt;
            border: 3pt solid #111;
            text-align: center;
            font-size: 20pt;
            color:#000;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<div id="printbox">
    <table width="100%">
        <tr><td valign="top">
                <h1  class="text-center">{{ $creditPaymentLog->payment->warehousestore->name}}</h1>
                <p align="center">
                    {!! $creditPaymentLog->payment->warehousestore->first_address !!}
                    @if(!empty($creditPaymentLog->payment->warehousestore->second_address))
                        <br/>
                        {!! $creditPaymentLog->payment->warehousestore->second_address !!}
                    @endif
                    @if(!empty($creditPaymentLog->payment->warehousestore->contact_number))
                        <br/>
                        {!! $creditPaymentLog->payment->warehousestore->contact_number !!}
                    @endif
                </p>
            </td>
        </tr>
        <td valign="top" width="35%" class="text-left" align="right">
            <img style="max-height:100px;float: right;margin-top: -20px;" src="{{ public_path("img/". $creditPaymentLog->payment->warehousestore->logo) }}" alt='Logo'>
        </td>
    </table>


    <table  class="inv_info" width="100%">
        <tr>
            <th align="left" class="text-left">Payment Date</th>
            <td>{{ convert_date($creditPaymentLog->payment->payment_date)  }}</td>
            <th align="left" class="text-left">Customer</th>
            <td>{{ $creditPaymentLog->customer->firstname }} {{ $creditPaymentLog->customer->lastname }}</td>
        </tr>
        <tr>
            <th align="left" class="text-left">Phone Number</th>
            <td>{{ $creditPaymentLog->customer->phone_number }}</td>
            <th align="left" class="text-left">Customer Address</th>
            <td colspan="3">{{ $creditPaymentLog->customer->address }}</td>
        </tr>
        <tr>

            <th align="left" class="text-left">Sales Representative</th>
            <td>{{ $creditPaymentLog->user->name }}</td>
            <th align="left" class="text-left">Credit Balance: </th>
            <td>{{ money($creditPaymentLog->customer->credit_balance) }}</td>
        </tr>
    </table>

<br/>
    <h2 style="margin-top:0" class="text-center">Payment Receipt</h2>
    <table id="products">
        <tr class="product_row">
            <td align="left"><b>Amount</b></td>
            <td align="right">{{ number_format($creditPaymentLog->amount,2) }}</td>
        </tr>
    </table>
    <h3 align="center">Payment Method(s)</h3>
    @php
        $total = 0;
    @endphp
    <table id="products">
        @foreach($creditPaymentLog->payment->payment_method_tables as $pm)
            <tr class="product_row">
                <td align="left"><b>{{ $pm->payment_method->name }}</b></td>
                <td align="right">{{ number_format($pm->amount,2) }}</td>
            </tr>
            @php
                $total = $total + $pm->amount;
            @endphp
        @endforeach
        <tfoot>
        <tr>
            <th align="right"></th>
            <th align="right">Total : {{ number_format($total,2) }}</th>
        </tr>
        </tfoot>
    </table>
    <br/><br/>
    <table class="inv_info" width="100%">
        <tr>
            <td style="width: 50%" class="text-left">
                <div style="width: 20%">
                    <hr style="background: #000; width: 100%"/> <br/>
                    <center><strong>Company's Signature</strong></center>
                </div>
            </td>
            <td style="width: 50%" class="text-right" align="right">
                <div style="width: 20%">
                    <hr style="background: #000; width: 100%"/> <br/>
                    <center><strong>Customer's Signature</strong></center>
                </div>
            </td>
        </tr>
    </table>

    <div class="text-center"> {!! softwareStampWithDate() !!}</div>
</div>
</body>
</html>

