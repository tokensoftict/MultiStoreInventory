@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link href="{{ asset('bower_components/datatables/media/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-tabletools/css/dataTables.tableTools.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-colvis/css/dataTables.colVis.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-responsive/css/responsive.dataTables.scss') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-scroller/css/scroller.dataTables.scss') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>To</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="To"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>Select Status</label>
                                    <select multiple class="form-control select-status" name="status_id[]">
                                        @foreach($statuses as $status)
                                            <option {{ in_array($status->id, $status_id) ? 'selected' : ''  }} value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </header>
                    <div class="panel-body">
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
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-tabletools/js/dataTables.tableTools.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-colvis/js/dataTables.colVis.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-responsive/js/dataTables.responsive.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-scroller/js/dataTables.scroller.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
