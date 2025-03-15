<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice #{{ $invoice->id }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-size: 10pt;
            background-color: #fff;
        }

        #products {
            width: 100%;
        }

        #products tr td {
            font-size: 10pt;
        }

        #printbox {
            width: 80mm;
            margin: 5pt;
            padding: 5px;
            text-align: justify;
        }

        .inv_info tr td {
            padding-right: 14pt;
        }

        .product_row {
            margin: 13pt;
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
<h3 id="logo" class="text-center"><br><img style="max-height:30px;" src="{{ public_path("img/". $store->logo) }}" alt='Logo'></h3>
<div id="printbox">
    <h2 style="margin:-2px;padding: 0px" class="text-center">{{ $store->name}}</h2>
    <div align="center" >
        {{ $store->first_address }}
        @if(!empty($store->second_address))
            <br/>
            {{ $store->second_address }}
        @endif
        @if(!empty($store->contact_number))
            <br/>
            {{ $store->contact_number }}
        @endif
    </div>
    <table class="inv_info">
        <tr>
            <td>Invoice / Receipt No</td>
            <td>{{ $invoice->invoice_paper_number }}</td>
        </tr>
        <tr>
            <td>Invoice Number</td>
            <td>{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td>Invoice Date</td>
            <td>{{ convert_date2($invoice->invoice_date)  }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>{{ $invoice->status }}</td>
        </tr>
        <tr>
            <td>Sales Representative</td>
            <td>{{ $invoice->created_user->name }}</td>
        </tr>
    </table>
    <hr>
    <table id="products">
        <tr class="product_row">
            <td>#</td>
            <td align="left"><b>Name</b></td>
            <td align="center"><b>Quantity</b></td>
            <td align="center"><b>Price</b></td>
            <td align="right"><b>Total</b></td>
        </tr>
        <tr>
            <td colspan="5">
                <hr>
            </td>
        </tr>
        <tbody id="appender">
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
            <td class="text-right">Sub Total</td>
            <td class="text-right">{{ number_format($invoice->sub_total,2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right">Discount</td>
            <td class="text-right">{{ number_format($invoice->discount_amount,2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td  align="right" class="text-right">Total</td>
            <td  align="right" class="text-right"><b>{{ number_format(($invoice->sub_total - $invoice->discount_amount),2) }}</b></td>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div class="text-center">  {{ $store->footer_notes }}</div>
    <div class="text-center"> {!! softwareStampWithDate() !!}</div>
</div>
</body>
</html>

