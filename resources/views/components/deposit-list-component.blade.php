<div>
    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
        <thead>
        <tr>
            <th>#</th>
            <th>Deposit No</th>
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
            $totalPaymentMethod = [];
        @endphp
        @foreach($lists as $deposit)
            @php
                $total += $deposit->total_amount_paid;
                $total_discount+=$deposit->discount_amount;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $deposit->deposit_number }}</td>
                <td>{{ $deposit->customer->firstname }} {{ $deposit->customer->lastname }}</td>
                <td>{{ $deposit->warehousestore->name }}</td>
                <td>{!! invoice_status($deposit->status) !!}</td>
                <td>{{ number_format($deposit->sub_total,2) }}</td>
                <td>{{ number_format($deposit->discount_amount,2) }}</td>
                <td>{{ number_format(($deposit->sub_total -$deposit->discount_amount) ,2) }}</td>
                <td>{{ number_format($deposit->total_amount_paid ,2) }}</td>
                <td>{{ convert_date2($deposit->deposit_date) }}</td>
                <td>{{ $deposit->deposit_time->format('h:i a') }}</td>
                <td>{{ $deposit->created_user->name }}</td>
                <td>
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                            @if(userCanView('deposit.view'))
                                <li><a href="{{ route('deposit.view',$deposit->id) }}">View Deposit</a></li>
                            @endif
                            @if(userCanView('deposit.edit'))
                                <li><a href="{{ route('deposit.edit',$deposit->id) }}">Edit Deposit</a></li>
                            @endif
                            @if(userCanView('deposit.add_depsoit_payment'))
                                <li><a href="{{ route('deposit.add_depsoit_payment',$deposit->id) }}">Add Deposit Payment</a></li>
                            @endif

                            @if(userCanView('deposit.pos_print') && $deposit->sub_total > -1)
                                <li><a onclick="open_print_window(this); return false" href="{{ route('deposit.pos_print',$deposit->id) }}">Print Deposit Pos</a></li>
                            @endif
                            @if(userCanView('deposit.print_afour') && $deposit->sub_total > -1)
                                <li><a onclick="open_print_window(this); return false" href="{{ route('deposit.print_afour',$deposit->id) }}">Print Deposit A4</a></li>
                            @endif
                            @if(userCanView('deposit.print_afour') && $deposit->sub_total > -1)
                                <li><a onclick="open_print_window(this); return false" href="{{ route('deposit.print_afive',$deposit->id) }}">Print Small A5</a></li>
                            @endif
                            @if(userCanView('deposit.print_way_bill') && $deposit->sub_total > -1)
                                <li><a onclick="open_print_window(this); return false" href="{{ route('deposit.print_way_bill',$deposit->id) }}">Print Waybill</a></li>
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