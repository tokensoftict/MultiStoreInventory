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

                        <span class="tools pull-right">
                            @if(userCanView('bookings_and_reservation.edit',$booking->id))
                                <a  href="{{ route('bookings_and_reservation.edit',$booking->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit Booking</a>
                                &nbsp; &nbsp;
                            @endif
                            @if(userCanView('bookings_and_reservation.destroy',$booking->id))
                                <a  href="{{ route('bookings_and_reservation.destroy',$booking->id) }}" class="btn btn-sm btn-danger confirm_action" data-msg="Are you sure, you want to delete this booking?"><i class="fa fa-trash"></i> Delete Booking</a>
                            @endif

                            @if(userCanView('bookings_and_reservation.make_payment') && ($booking->status->name!="Checked-out" && $booking->status->name!="Paid" ))
                                <a class="btn btn-sm btn-primary" href="{{ route('bookings_and_reservation.make_payment',$booking->id) }}">Add Payment</a>
                            @endif
                            @if(userCanView('bookings_and_reservation.check_out') && $booking->status->name!="Checked-out")
                                <a class="btn btn-sm btn-success confirm_action" data-msg="Are you sure, you want to checkout this booking?" href="{{ route('bookings_and_reservation.check_out',$booking->id) }}">Checkout Guest</a>
                            @endif

                            </span>
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Customer Name</label><br/>
                                    <span class="text-md">{{ $booking->customer->firstname }} {{ $booking->customer->lastname }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Start Date</label><br/>
                                    <span class="text-md">{{ str_date($booking->start_date) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>End Date</label><br/>
                                    <span class="text-md">{{ str_date($booking->end_date) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Status</label><br/>
                                    <span class="text-md">{!! label($booking->status->name, $booking->status->label) !!}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 16px">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>No of Nights</label><br/>
                                    <span class="text-md">  {{ $booking->no_of_days }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>No of Rooms</label><br/>
                                    <span class="text-md">  {{ $booking->no_of_rooms }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Booking Date</label><br/>
                                    <span class="text-md">{{ str_date($booking->booking_date) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Total</label><br/>
                                    <span class="text-md">{{ number_format($booking->total,2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 16px">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Total Paid</label><br/>
                                    <span class="text-md"> {{ number_format($booking->total_paid,2) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Created By</label><br/>
                                    <span class="text-md">  {{ $booking->user->name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <hr/>
                                <h4>Customer Information</h4>
                                <hr/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Customer Name</label><br/>
                                    <span class="text-md">{{ $booking->customer->firstname }} {{ $booking->customer->lastname }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Address</label><br/>
                                    <span class="text-md">{{ $booking->customer->address }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Email address</label><br/>
                                    <span class="text-md">{{ $booking->customer->email }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Phone Number</label><br/>
                                    <span class="text-md">{{  $booking->customer->phone_number }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 16px">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>NOK Full Name</label><br/>
                                    <span class="text-md">{{ $booking->customer->nok_firstname }} {{ $booking->customer->nok_lastname }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>NOK Phone Number</label><br/>
                                    <span class="text-md">{{ $booking->customer->nok_phone_number }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Nationality</label><br/>
                                    <span class="text-md">{{ $booking->customer->nationality }}</span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Passport Number / Expiry Date</label><br/>
                                    <span class="text-md">{{  $booking->customer->passport_no }} / {{  $booking->customer->passport_expire_date }}</span>
                                </div>
                            </div>
                        </div>


                        <div class="row" style="margin-top: 16px">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Occupation</label><br/>
                                        <span class="text-md">{{ $booking->customer->occupation }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Purpose Of Visit</label><br/>
                                        <span class="text-md">{{ $booking->customer->purpose_of_visit }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Vehicle Reg Number</label><br/>
                                        <span class="text-md">{{ $booking->customer->vehicle_reg_number }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Arriving From</label><br/>
                                        <span class="text-md">{{  $booking->customer->arriving_from }}</span>
                                    </div>
                                </div>
                            </div>

                        <div class="row" style="margin-top: 16px">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>City</label><br/>
                                        <span class="text-md">{{ $booking->customer->city }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>State</label><br/>
                                        <span class="text-md">{{ $booking->customer->state }}</span>
                                    </div>
                                </div>

                            </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <hr/>
                                <h4>Room Information</h4>
                                <hr/>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 16px">
                            <div class="col-md-12">
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Room Name</th>
                                        <th>Price</th>
                                        <th>No of Nights</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($booking->booking_reservation_items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->room->name }}</td>
                                            <td>{{ number_format($item->price,2) }}</td>
                                            <td>{{ $item->noOfDays }}</td>
                                            <td>{{ number_format($item->total,2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Sub Total</th>
                                        <th>{{ number_format($booking->total,2) }}</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>{{ number_format($booking->total,2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
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
