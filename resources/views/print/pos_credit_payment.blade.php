<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Receipt</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
        }

        #products {
            width: 100%;
        }

        #products tr td {
            font-size: 8pt;
        }

        #printbox {
            width: 80mm;
            margin: 5pt;
            padding: 5px;
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
<h3 id="logo" class="text-center"><br><img style="max-height:30px;" src="{{ public_path("img/". $creditPaymentLog->payment->warehousestore->logo) }}" alt='Logo'></h3>
<div id="printbox">
    <h2 style="margin-top:0" class="text-center">{{ $creditPaymentLog->payment->warehousestore->name}}</h2>
    <p align="center">
        {{ $creditPaymentLog->payment->warehousestore->first_address }}
        @if(!empty($creditPaymentLog->payment->warehousestore->second_address))
            <br/>
            {{ $creditPaymentLog->payment->warehousestore->second_address }}
        @endif
        @if(!empty($creditPaymentLog->payment->warehousestore->contact_number))
            <br/>
            {{ $creditPaymentLog->payment->warehousestore->contact_number }}
        @endif
    </p>
    <table class="inv_info">
        <tr>
            <td>Payment Date</td>
            <td>{{ convert_date2($creditPaymentLog->payment->payment_date)  }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>{{ $creditPaymentLog->customer->firstname }} {{ $creditPaymentLog->customer->lastname }}</td>
        </tr>
        <tr>
            <td>Customer Phone</td>
            <td>{{ $creditPaymentLog->customer->phone_number }}</td>
        </tr>
        <tr>
            <td>Customer Address</td>
            <td>{{ $creditPaymentLog->customer->address }}</td>
        </tr>
        <tr>
            <td>Sales Representative</td>
            <td>{{ $creditPaymentLog->user->name }}</td>
        </tr>
        <tr>
            <td>Credit Balance:</td>
            <td>{{ money($creditPaymentLog->customer->credit_balance) }}</td>
        </tr>
    </table>
    <hr>
    <h2 align="center">Payment Receipt</h2>
    <hr>
    <table id="products">
        <tr class="product_row">
            <td align="left"><b>Amount</b></td>
            <td align="right">{{ number_format($creditPaymentLog->amount,2) }}</td>
        </tr>
    </table>
    @php
    $total = 0;
    @endphp
    <h3 align="center">Payment Method(s)</h3>
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
    <hr>
    <div class="text-center"> {!! softwareStampWithDate() !!}</div>
</div>
</body>
</html>

