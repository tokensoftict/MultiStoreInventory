<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice #{{ $transfer->id }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 9pt;
            background-color: #fff;
        }

        #products {
            width: 90%;
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
        <tr>
            <td valign="top" width="30%">
                <h5>Transfer From<br/>{{ $transfer->store_from->name }}</h5>
                <h5>Status<br/><b>{{ strtoupper($transfer->status) }}</b></h5>
                <h5>Transfer By<br/><b>{{ strtoupper($transfer->user->name) }}</b></h5>
            </td>
            <td valign="top" align="center" width="40%">
                    <img style="max-height:60px;float: right;margin-top: -10px" src="{{ public_path("img/". $store->logo) }}" alt='Logo'>
                <h2 align="center">{{ $store->name }}</h2>
            </td>
            <td valign="top" width="30%" align="right">
                <h5  align="right">Transfer To <br/> <b>{{ $transfer->store_to->name }}</b></h5>
                <h5 align="right">Date <br/><b>{!!  convert_date($transfer->transfer_date)  !!}</b></h5>
            </td>
        </tr>
    </table>

    <h4 align="center">TRANSFER ITEMS</h4>
    <table id="products" align="center" style="width: 100%">
        <tr>
            <th class="text-left">Name</th>
            <th class="text-center">Selling Price</th>
            <th class="text-center">Quantity</th>
            <th class="text-right">Total Selling Price</th>
        </tr>
        <tbody>
        @php
            $total = 0;
            $errors = session('errors')
        @endphp
        @foreach($transfer->stock_transfer_items()->get() as $trans)
            @php
                $total +=($trans->quantity * $trans->selling_price)
            @endphp
            <tr>
                <td class="text-left">{{ $trans->stock->name }}</td>
                <td class="text-center">{{ number_format($trans->cost_price,2) }}</td>
                <td class="text-center">{{ $trans->quantity }}</td>
                <td class="text-right">{{ number_format(($trans->quantity * $trans->cost_price),2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <th class="text-right">Total</th>
            <th class="text-right">{{ number_format($total,2) }}</th>
        </tr>
        </tfoot>
    </table>
    <center>  <span  style="text-align: right">{!! softwareStampWithDate() !!}</span></center>
</div>
</body>
</html>