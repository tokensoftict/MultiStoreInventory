<div>
    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
        <thead>
        <tr>
            <th>#</th>
            <th>Invoice/Receipt No</th>
            <th>Customer</th>
            <th>Store</th>
            <th>Status</th>
            <th>Sub Total</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Total Paid</th>
            <th>Date</th>
            <th>Time</th>
            <th>By</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $total = 0;
            $total_discount= 0;
        @endphp
        @foreach($lists as $invoice)
            @php
                $total += $invoice->total_amount_paid;
                $total_discount+=$invoice->discount_amount;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $invoice->invoice_paper_number }}</td>
                <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
                <td>{{ $invoice->warehousestore->name }}</td>
                <td>{!! invoice_status($invoice->status) !!}</td>
                <td>{{ number_format($invoice->sub_total,2) }}</td>
                <td>{{ number_format($invoice->discount_amount,2) }}</td>
                <td>{{ number_format(($invoice->sub_total -$invoice->discount_amount) ,2) }}</td>
                <td>{{ number_format($invoice->total_amount_paid,2) }}</td>
                <td>{{ convert_date2($invoice->invoice_date) }}</td>
                <td>{{ $invoice->sales_time->format('h:i a') }}</td>
                <td>{{ $invoice->created_user->name }}</td>
                <td>
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                            @if(userCanView('invoiceandsales.view'))
                                <li><a href="{{ route('invoiceandsales.view',$invoice->id) }}">View Invoice</a></li>
                            @endif
                            @if(userCanView('invoiceandsales.apply_invoice_discount') && $invoice->status === "DISCOUNT")
                                <li><a href="{{ route('invoiceandsales.apply_invoice_discount',$invoice->id) }}">Apply Discount</a></li>
                            @endif
                            @if($invoice->status !== "DISCOUNT" && $invoice->status !== "DISCOUNT-APPLIED")
                                @if(userCanView('invoiceandsales.edit') && $invoice->sub_total > -1 && $invoice->status =="DRAFT")
                                    <li><a href="{{ route('invoiceandsales.edit',$invoice->id) }}">Edit Invoice</a></li>
                                @endif

                                @if(userCanView('invoiceandsales.edit') && $invoice->sub_total > -1 && ($invoice->status =="PAID" || $invoice->status == "COMPLETE") && config('app.uses_edit_to_return_stocks') == true)
                                    <li><a href="{{ route('invoiceandsales.edit',$invoice->id) }}">Edit Invoice</a></li>
                                @endif

                                @if(userCanView('invoiceandsales.pos_print') && $invoice->sub_total > -1)
                                    <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.pos_print',$invoice->id) }}">Print Invoice Pos</a></li>
                                @endif
                                @if(userCanView('invoiceandsales.print_afour') && $invoice->sub_total > -1)
                                    <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.print_afour',$invoice->id) }}">Print Invoice A4</a></li>
                                @endif
                                @if(userCanView('invoiceandsales.print_way_bill') && $invoice->sub_total > -1)
                                    <li><a onclick="open_print_window(this); return false" href="{{ route('invoiceandsales.print_way_bill',$invoice->id) }}">Print Waybill</a></li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total Discount</th>
            <th>{{ number_format($total_discount,2) }}</th>
            <th>Total Paid</th>
            <th>{{ number_format($total,2) }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>