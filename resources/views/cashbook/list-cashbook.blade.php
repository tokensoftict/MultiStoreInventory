@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @if(session('success'))
                    {!! alert_success(session('success')) !!}
                @elseif(session('error'))
                    {!! alert_error(session('error')) !!}
                @endif
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
                                    <label>Select Bank</label>
                                    <select class="form-control" name="bank_account_id">
                                        @foreach($banks as $bank)
                                            <option {{ $bank_account_id == $bank->id ? "selected" : "" }} value="{{ $bank->id }}">{{ $bank->bank->name }} - {{ $bank->account_number }}</option>
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
                        <h5><b>Opening Balance</b> : {{ number_format($opening,2) }}</h5>
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Bank</th>
                                <th>Balance</th>
                                <th>Added By</th>
                                <th>Last Updated By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $transaction)
                                @if($transaction->type == "Debit")
                                    @php
                                        $opening -=$transaction->amount;
                                    @endphp
                                @else
                                    @php
                                        $opening +=$transaction->amount
                                    @endphp
                                @endif
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->type == "Debit" ? number_format($transaction->amount,2) : "" }}</td>
                                    <td>{{ $transaction->type == "Credit" ? number_format($transaction->amount,2) : ""}}</td>
                                    <td>{{ $transaction->comment }}</td>
                                    <td>{{ convert_date($transaction->transaction_date) }}</td>
                                    <td>{{ $transaction->bank_account->bank->name }} - {{  $transaction->bank_account->account_number }}</td>
                                    <td>{{ number_format($opening,2) }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->last_update->name }}</td>
                                    <td>
                                        @if(is_null($transaction->cashbookable_id))
                                            @if(userCanView('cashbook.edit',$transaction->id))
                                                <a href="{{ route('cashbook.edit',$transaction->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                            @if(userCanView('cashbook.destroy',$transaction->id))
                                                <a href="{{ route('cashbook.destroy',$transaction->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                            @endif

                                            @else

                                            {{ 'No Action' }}
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
                                <td></td>
                                <td>{{ number_format($opening,2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
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
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
