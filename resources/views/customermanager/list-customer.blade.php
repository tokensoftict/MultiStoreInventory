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
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Full name</th>
                                <th>Email Address</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Credit Balance</th>
                                <th>Credit Limit</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($customers as $customer)
                                @php
                                    $total += $customer->credit_balance;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->firstname }} {{ $customer->lastname }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->phone_number }}</td>
                                    <td>{{ number_format($customer->credit_balance,2) }}</td>
                                    <td>{{ number_format($customer->credit_limit,2) }}</td>
                                    <td>{{ $customer->created_at  }}</td>
                                    <td>

                                        @if (userCanView('customer.edit'))
                                            <a href="{{ route('customer.edit',$customer->id) }}" class="btn btn-success btn-sm">Edit</a>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>{{ number_format($total,2) }}</th>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        <!--
                <div class="col-md-4">
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $title2 }}
                </header>
                <div class="panel-body">
                    <form id="validate" action="{{ route('customer.store') }}" enctype="multipart/form-data" method="post">
                                {{ csrf_field() }}

        {{ csrf_field() }}
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" value="{{ old('firstname') }}" required  class="form-control" name="firstname" placeholder="First Name"/>
                                        @if ($errors->has('firstname'))
            <label for="name-error" class="error"
                   style="display: inline-block;">{{ $errors->first('firstname') }}</label>
                                        @endif
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" value="{{ old('lastname') }}" required  class="form-control" name="lastname" placeholder="Last Name"/>
                                        @if ($errors->has('lastname'))
            <label for="name-error" class="error"
                   style="display: inline-block;">{{ $errors->first('lastname') }}</label>
                                        @endif
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" value="{{ old('email') }}"   class="form-control" name="email" placeholder="Email Address"/>
                                        @if ($errors->has('email'))
            <label for="name-error" class="error"
                   style="display: inline-block;">{{ $errors->first('email') }}</label>
                                        @endif
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" value="{{ old('phone_number') }}" required  class="form-control" name="phone_number" placeholder="Phone Number"/>
                                        @if ($errors->has('phone_number'))
            <label for="name-error" class="error"
                   style="display: inline-block;">{{ $errors->first('phone_number') }}</label>
                                        @endif
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="address">{{ old('phone_number') }}</textarea>
                                    </div>
                                    <div class="pull-left">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Add</button>
                                    </div>

                                </form>


                                <br/>

                        </div>
                    </section>
                </div>
                -->
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
