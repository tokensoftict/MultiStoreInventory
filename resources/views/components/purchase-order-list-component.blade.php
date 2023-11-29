<div>
    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
        <thead>
        <tr>
            <th>#</th>
            <th>Supplier</th>
            <th>Store</th>
            <th>Total Items</th>
            <th>Total Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Created By</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($purchaseorders as $purchase_order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $purchase_order->supplier->name ?? "" }}</td>
                <td>{{ $purchase_order->warehousestore->name ?? "" }}</td>
                <td>{{ $purchase_order->purchase_order_items->count() }}</td>
                <td>{{ number_format($purchase_order->purchase_order_items()->sum(DB::raw('cost_price * qty')),2) }}</td>
                <td>{{ convert_date2($purchase_order->date_created) }}</td>
                <td>{!! $purchase_order->status == "DRAFT" ? label('DRAFT','primary') :   label('COMPLETE','success') !!}</td>
                <td>{{ $purchase_order->user->name }}</td>
                <td>{{ $purchase_order->created_user->name }}</td>
                <td>
                    <div class="btn-group">
                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                        <ul role="menu" class="dropdown-menu">
                            @if(userCanView('purchaseorders.show'))
                                <li><a href="{{ route('purchaseorders.show',$purchase_order->id) }}">View Purchase Order</a></li>
                            @endif
                            @if(userCanView('purchaseorders.edit') && $purchase_order->status == "DRAFT")
                                <li><a href="{{ route('purchaseorders.edit',$purchase_order->id) }}">Edit Purchase Order</a></li>
                            @endif
                            @if(userCanView('purchaseorders.markAsComplete') && $purchase_order->status == "DRAFT")
                                <li><a href="{{ route('purchaseorders.markAsComplete',$purchase_order->id) }}">Complete Purchase Order</a></li>
                            @endif
                            @if(userCanView('purchaseorders.destroy') && $purchase_order->status == "DRAFT")
                                <li><a href="{{ route('purchaseorders.destroy',$purchase_order->id) }}">Delete Purchase Order</a></li>
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>