<div>
    <br> <br>
    <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 10px">
        <thead>
        <tr>
            <th>#</th>
            <th>Transfer Number</th>
            <th>From</th>
            <th>To</th>
            <th>Status</th>
            <th>Date</th>
            <th>Total Cost Price</th>
            <th>By</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @forelse($lists as $transfer)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transfer->transfer_number }}</td>
                <td>{{ $transfer->store_from->name }}</td>
                <td>{{ $transfer->store_to->name }}</td>
                <td>{!! $transfer->status == "COMPLETE" ? label( $transfer->status,'success') : label( $transfer->status,'primary') !!}</td>
                <td>{{ convert_date2($transfer->transfer_date) }}</td>
                <td>{{ number_format($transfer->total_price,2) }}</td>
                <td>{{ $transfer->user->name }}</td>
                <td>
                    @if(userCanView('stocktransfer.delete_transfer') && $transfer->status =="DRAFT")
                        <a data-msg="Are you sure, you want to delete this transfer" href="{{ route('stocktransfer.delete_transfer',$transfer->id) }}" class="btn btn-danger btn-sm confirm_action">Delete</a>
                    @endif
                    @if(userCanView('stocktransfer.edit') && $transfer->status =="DRAFT")
                        <a  href="{{ route('stocktransfer.edit',$transfer->id) }}" class="btn btn-success btn-sm">Edit</a>
                    @endif
                    @if(userCanView('stocktransfer.complete') && $transfer->status =="DRAFT")
                        <a  href="{{ route('stocktransfer.complete',$transfer->id) }}" class="btn btn-success btn-sm">Complete</a>
                    @endif
                    @if(userCanView('stocktransfer.show'))
                        <a  href="{{ route('stocktransfer.show',$transfer->id) }}" class="btn btn-primary btn-sm">View</a>
                    @endif
                        <a  href="{{ route('stocktransfer.print_afour',$transfer->id) }}" class="btn btn-success btn-sm">Print</a>
                </td>
            </tr>
        @empty

        @endforelse
        </tbody>
    </table>
</div>