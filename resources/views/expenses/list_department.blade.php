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
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>Store</label>
                                    <select class="form-control" name="warehousestore_id">
                                        @foreach($stores as $store)
                                            <option {{ $store['id'] == $warehousestore_id ? "selected" : "" }} value="{{ $store['id'] }}">{{ $store['name'] }}</option>
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
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <br/> <br/> <br/>
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>By</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Department</th>
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
                            @forelse($expenses as $expense)
                                @php
                                    $total +=$expense->amount;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $expense->user->name }}</td>
                                    <td>{{ $expense->expenses_type->name }}</td>
                                    <td>{{ $expense->department }}</td>
                                    <td>{{ number_format($expense->amount,2) }}</td>
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
                                <th></th>
                                <th>Total</th>
                                <th>{{ number_format($total,2) }}</th>
                                <th></th>
                                <th></th>
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
