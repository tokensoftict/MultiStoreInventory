@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link href="{{ asset('bower_components/datatables/media/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-tabletools/css/dataTables.tableTools.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-colvis/css/dataTables.colVis.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-responsive/css/responsive.dataTables.scss') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-scroller/css/scroller.dataTables.scss') }}" rel="stylesheet">
    <style>
        input {
            margin-bottom: 0 !important;
        }
    </style>
@endpush

@section('content')

    <div class="ui-container">
        <div class="row">
            <div class="col-md-6 col-lg-offset-3">
                <section class="panel">
                    @if(session('success'))
                        {!! alert_success(session('success')) !!}
                    @elseif(session('error'))
                        {!! alert_error(session('error')) !!}
                    @endif
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        @if(isset($expenses->id))
                            <form  action="{{ route('expenses.update',$expenses->id) }}" enctype="multipart/form-data" method="post">
                                {{ method_field('PUT') }}
                        @else
                        <form  action="{{ route('expenses.store') }}" enctype="multipart/form-data" method="post">
                        @endif
                            <div class="panel-body">
                                {{ csrf_field() }}
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Date <span  style="color:red;">*</span></label>
                                        <input type="text" value="{{ old('expense_date', (isset($expenses->expense_date) ? $expenses->expense_date : date('Y-m-d'))) }}" required  value="{{ date('Y-m-d') }}" data-min-view="2" data-date-format="yyyy-mm-dd" class="form-control datepicker js-datepicker" id="invoice_date" name="expense_date" placeholder="Date"/>
                                        @if ($errors->has('expense_date'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('expense_date') }}</label>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label>Amount <span  style="color:red;">*</span></label>
                                        <input type="text" value="{{ old('amount',$expenses->amount) }}" required  class="form-control" name="amount" placeholder="Amount"/>
                                        @if ($errors->has('amount'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('amount') }}</label>
                                        @endif
                                    </div>

                                        <input type="hidden" name="department" value="STORE"/>

                                    <div class="form-group">
                                        <label>Expenses Type<span  style="color:red;">*</span></label>
                                        <select required class="form-control" name="expenses_type_id">
                                            <option value="">-Select Type-</option>
                                            @foreach($expenses_types as $type)
                                                <option {{ old('expenses_type_id',$expenses->expenses_type_id) ? 'selected' : '' }} value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Description / Purpose <span  style="color:red;">*</span></label>
                                        <textarea class="form-control" style="height: 100px" required name="purpose">{{ old('purpose',$expenses->purpose) }}</textarea>
                                        @if ($errors->has('purpose'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('purpose') }}</label>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="panel-footer">
                                <center> <input class="btn btn-info btn-sm" type="submit" name="save" value="Save Expense"></center>
                            </div>
                        </form>
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
