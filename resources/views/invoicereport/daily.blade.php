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

                        <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-5">
                                    <label>Select Date</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $date }}" name="date" placeholder="Date"/>
                                </div>
                                <div class="col-sm-5">
                                    <label>Select Status</label>
                                    <select class="form-control" name="status">
                                        <option {{ $status == "COMPLETE" ? "selected" : "" }} value="COMPLETE">COMPLETE</option>
                                        <option {{ $status == "DRAFT" ? "selected" : "" }} value="DRAFT">DRAFT</option>
                                        <option {{ $status == "DISCOUNT" ? "selected" : "" }} value="DISCOUNT">PENDING DISCOUNT</option>
                                        <option {{ $status == "DISCOUNT-APPLIED" ? "selected" : "" }} value="DISCOUNT-APPLIED">DISCOUNT APPLIED</option>
                                    </select>
                                </div>
                                <div class="col-sm-2"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                            <x-invoice-list-component :lists="$invoices"/>
                    </div>
                </section>
            </div>
        </div>
    </div>


@endsection


@push('js')
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script   src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
