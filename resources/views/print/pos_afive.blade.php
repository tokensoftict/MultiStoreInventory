<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Print Invoice #{{ $invoice->id }}</title>
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
                <h1  class="text-center">{{ $store->name}}</h1>
                <p align="center">
                    {!! $store->first_address !!}
                    @if(!empty($store->second_address))
                        <br/>
                        {!! $store->second_address !!}
                    @endif
                    @if(!empty($store->contact_number))
                        <br/>
                        {!! $store->contact_number !!}
                    @endif
                </p>
            </td>
        </tr>
        <td valign="top" width="35%" class="text-left" align="right">
            <img style="max-height:100px;float: right;" src="{{ public_path("img/". $store->logo) }}" alt='Logo'>
        </td>
    </table>


    <table  class="inv_info" width="100%">

        <tr>
            <th align="left" class="text-left">Invoice Number</th>
            <td>{{ $invoice->invoice_number }}</td>
            <th align="left" class="text-left">Invoice Date</th>
            <td>{{ convert_date($invoice->invoice_date)  }}</td>
        </tr>
        <tr>
            <th align="left" class="text-left">Customer</th>
            <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
            <th align="left" class="text-left">Time</th>
            <td>{{ date("h:i a",strtotime($invoice->sales_time)) }}</td>
        </tr>
        <tr>
            <th align="left" class="text-left">Customer Address</th>
            <td colspan="3">{{ $invoice->customer->address }}</td>
        </tr>
        <tr>
            <th align="left" class="text-left">Sales Representative</th>
            <td>{{ $invoice->created_user->name }}</td>
            <th align="left" class="text-left">Status</th>
            <td>{{ $invoice->status }}</td>
        </tr>
        <tr>
            <th align="left" class="text-left">Method Of Payment</th>
            <td colspan="3">
            @foreach($invoice->paymentMethodTable as $method)
                <b>{{ $method->payment_method->name }}</b> : <b>{{  money($method->amount) }}</b>
            @endforeach
            </td>
        </tr>
        <tr>
            <th align="left" class="text-left">Credit Balance: </th>
            <td>{{ money($invoice->customer->credit_balance) }}</td>
        </tr>
    </table>


    <h2 style="margin-top:0" class="text-center">{{ $invoice->status === "DRAFT" ? "Proforma Invoice" : "Sales Invoice / Receipt" }}</h2>

    <table id="products" width="100%">
        <tr class="product_row" style="background: #0a0a0a; color: #FFF">
            <td style="color: #FFF">#</td>
            <td align="left" style="color: #FFF"><b>Name</b></td>
            <td align="center" style="color: #FFF"><b>Quantity</b></td>
            <td align="center" style="color: #FFF"><b>Price</b></td>
            <td align="right" style="color: #FFF"><b>Total</b></td>
        </tr>
        <tbody id="appender">
        @foreach($invoice->invoice_items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td align="left" class="text-left">{{ $item->stock->name }}</td>
                <td align="center" class="text-center">{{ $item->quantity }} {{ $item->store == "yard_quantity" ? "pcs" : "carton" }}</td>
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
            <th  align="right" class="text-right">Sub Total</th>
            <th  align="right" class="text-right" style="font-size: 9pt;">{{ number_format($invoice->sub_total,2) }}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <th  align="right" class="text-right">Discount</th>
            <th  align="right" class="text-right" style="font-size: 9pt;">{{ number_format($invoice->discount_amount,2) }}</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <th   align="right" class="text-right">Total</th>
            <th  align="right" class="text-right" style="font-size: 9pt;"><b>{{ number_format(($invoice->sub_total -$invoice->discount_amount),2) }}</b></th>
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

