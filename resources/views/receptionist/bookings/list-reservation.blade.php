@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">


@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        @if(userCanView('bookings_and_reservation.create'))
                            <span class="tools pull-right">
                                  <a  href="{{ route('bookings_and_reservation.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Reservation / Booking</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
                                <th>Nights</th>
                                <th>Rooms</th>
                                <th>Booking Date</th>
                                <th>Total</th>
                                <th>Total Paid</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total =0;
                            @endphp
                            @foreach($bookings as $booking)
                                @php
                                    $total+=$booking->total_paid;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $booking->customer->firstname }} {{ $booking->customer->lastname }}</td>
                                    <td>{{ str_date($booking->start_date) }}</td>
                                    <td>{{ str_date($booking->end_date) }}</td>
                                    <td>{!! label($booking->status->name, $booking->status->label) !!}</td>
                                    <td>{{ $booking->no_of_days }}</td>
                                    <td>{{ $booking->no_of_rooms }}</td>
                                    <td>{{ str_date($booking->booking_date) }}</td>
                                    <td>{{ number_format($booking->total) }}</td>
                                    <td>{{ number_format($booking->total_paid) }}</td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('bookings_and_reservation.show'))
                                                    <li><a href="{{ route('bookings_and_reservation.show',$booking->id) }}">View Booking</a></li>
                                                @endif
                                                @if(userCanView('bookings_and_reservation.edit') && $booking->status->name!="Checked-out")
                                                    <li><a href="{{ route('bookings_and_reservation.edit',$booking->id) }}">Edit Booking</a></li>
                                                @endif
                                                @if(userCanView('bookings_and_reservation.destroy') && $booking->status->name!="Checked-out")
                                                    <li><a href="{{ route('bookings_and_reservation.destroy',$booking->id) }}" class="confirm_action" data-msg="Are you sure, you want to delete this booking?">Delete Booking</a></li>
                                                @endif
                                                @if(userCanView('bookings_and_reservation.make_payment') && ($booking->status->name!="Checked-out" && $booking->status->name!="Paid" ))
                                                    <li><a href="{{ route('bookings_and_reservation.make_payment',$booking->id) }}">Add Payment</a></li>
                                                @endif
                                                @if(userCanView('bookings_and_reservation.check_out') && $booking->status->name!="Checked-out")
                                                    <li><a href="{{ route('bookings_and_reservation.check_out',$booking->id) }}" class="confirm_action" data-msg="Are you sure, you want to checkout this booking?">Checkout Guest</a></li>
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
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th>{{ number_format($total,2) }}</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>

    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
