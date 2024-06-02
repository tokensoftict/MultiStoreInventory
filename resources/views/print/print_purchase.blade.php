<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice #{{ $porder->id }}</title>
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
                <h5><br/>{{ $settings->name }}:</h5>
                <h5>  {{ $settings->first_address  }}</h5>
                <h5>Created By : {{ $porder->created_user->name }}</h5>
                <h5>Date : {{ str_date($porder->date_created) }}</h5>
                <h5>Store :{{ $porder->warehousestore->name ?? "" }}</h5>
                <h5>Status :{{ $porder->status ?? "" }}</h5>
            </td>
            <td valign="top" align="center" width="40%">
                <img style="max-height:60px;float: right;margin-top: -10px" src="{{ public_path("img/". $settings->logo) }}" alt='Logo'>
                <h2 align="center">{{ $settings->name }}</h2>
            </td>
            <td valign="top" width="30%" align="right">
                <h5> {{ $porder->supplier->name }}</h5>
                <h5>{{ $porder->supplier->address }}</h5>
                <h5>{{ $porder->supplier->email }}</h5>
                <h5>{{ $porder->supplier->phonenumber }}</h5>
            </td>
        </tr>
    </table>

    <h4 align="center">PURCHASE ORDER ITEMS</h4>
    <table id="products" align="center" style="width: 100%">
        <tr>
            <td><strong>Name</strong></td>
            <td class="text-center"><strong>Quantity</strong></td>
            <td class="text-center"><strong>Cost Price</strong></td>
            <td class="text-right"><strong>Total Cost Price</strong></td>
        </tr>
        <tbody>
        @php
            $alltotal = 0;
        @endphp
        @foreach($porder->purchase_order_items as $item)
            @php
                $alltotal+= $total = $item->qty * $item->cost_price;
            @endphp
            <tr>
                <td>{{ $item->stock()->get()->first()->name }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-center">{{ number_format($item->cost_price,2) }}</td>
                <td class="text-right">{{ number_format($total,2) }}</td>
            </tr>

        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th class="text-right">Total :</th>
            <th class="text-right">{{ number_format($alltotal,2) }}</th>
        </tr>
        </tfoot>
    </table>
    <center>  <span  style="text-align: right">{!! softwareStampWithDate() !!}</span></center>
</div>
</body>
</html>