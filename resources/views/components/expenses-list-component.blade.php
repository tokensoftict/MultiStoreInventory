<div>

    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
        <thead>
        <tr>
            <th>#</th>
            <th>By</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Store</th>
            <th>Date</th>
            <th>Purpose</th>
            <th>Last Updated</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php
            $total =0;
        @endphp
        @forelse($lists as $expense)
            @php
                $total +=$expense->amount;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $expense->user->name }}</td>
                <td>{{ $expense->expenses_type->name }}</td>
                <td>{{ number_format($expense->amount,2) }}</td>
                <td>{{ $expense->warehousestore->name ?? "" }}</td>
                <td>{{ convert_date($expense->expense_date) }}</td>
                <td>{{ $expense->purpose }}</td>
                <td>{{ str_date($expense->updated_at) }}</td>
                <td>
                    @if(userCanView('expenses.edit'))
                        <a href="{{ route('expenses.edit',$expense->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    @endif
                    @if(userCanView('expenses.destroy'))
                        <a href="{{ route('expenses.destroy',$expense->id) }}" class="btn btn-sm btn-danger">Delete</a>
                    @endif
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th>Total</th>
            <th>{{ number_format($total,2) }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>

</div>