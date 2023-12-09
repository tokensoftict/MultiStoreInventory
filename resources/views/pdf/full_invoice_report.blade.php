<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Invoices</title>
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
@php
$total = 0;
$totalSalesCount =0;
@endphp
@foreach($invoices as $invoice)
    <table width="100%">
        <tr>
            <td valign="top" width="65%">
                <address>
                    <b>Sold To :</b><br/>
                    {{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}<br>
                    @if(!empty($invoice->customer->address))
                        {{ $invoice->customer->address }}<br>
                    @endif
                    @if(!empty($invoice->customer->email))
                        {{ $invoice->customer->email }}<br>
                    @endif
                    {{ $invoice->customer->phone_number }}
                </address>
                <br/>

                <label style="font-size: 12px">Invoice / Receipt Number</label><br/>
                <label style="font-size: 15px">{{ $invoice->invoice_paper_number }}</label>
                <br/><br/>
                <label style="font-size: 12px">Invoice Date</label><br/>
                <label style="font-size: 15px"> {{ convert_date($invoice->invoice_date) }}</label>
                <br/><br/>
                <label style="font-size: 12px">Time</label><br/>
                <label style="font-size: 15px"> {{ date("h:i a",strtotime($invoice->sales_time)) }}</label>
                <br/><br/>
                <label style="font-size: 12px">Sales Representative</label><br/>
                <label style="font-size: 15px">{{ $invoice->created_user->name }}</label>
            </td>
            <td valign="top" width="35%">
            </td>
        </tr>
    </table>
    <div id="printbox">
        <table id="products">
            <thead>
            <tr>
                <td>#</td>
                <td align="left"><b>Name</b></td>
                <td align="center"><b>Quantity</b></td>
                <td align="right"><b>Price</b></td>
                <td align="right"><b>Total</b></td>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->invoice_items as $item)
                @php
                    $total+=$item->total_selling_price;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td align="left" class="text-left">{{ $item->stock->name }}</td>
                    <td align="center" class="text-center">{{ $item->quantity }}</td>
                    <td align="right" class="text-center">{{ number_format($item->selling_price,2) }}</td>
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
                <td  align="right" class="text-right">Total</td>
                <td  align="right" class="text-right"><b>{{ number_format(($invoice->sub_total -$invoice->discount_amount),2) }}</b></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <hr/>
@endforeach

<table width="100%">
    <tr>
        <td valign="top" width="50%"></td>
        <td valign="top" width="50%">
            <table  width="100%">
                <tr>
                    <th>TOTAl SALES COUNT</th>
                    <th>{{ number_format($invoices->count(), 0) }}</th>
                </tr>
                <tr>
                    <th>GRAND TOTAL</th>
                    <th>{{ number_format($total, 2) }}</th>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>

