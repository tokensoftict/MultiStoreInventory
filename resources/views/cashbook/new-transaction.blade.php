@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-5">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        @if(isset($transaction->id))
                            <form action="{{ route('cashbook.update',$transaction->id) }}"   method="post">
                                {{ method_field('PUT') }}
                                @else
                                    <form action="{{ route('cashbook.store') }}" method="post">
                                        @endif
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="price">Date</label>
                                            <input id="transaction_date" type="text" value="<?php echo date('Y-m-d'); ?>" name="transaction_date" data-min-view="2" data-date-format="yyyy-mm-dd"  placeholder="Date" class="form-control datepicker js-datepicker">
                                        </div>
                                        <div class="form-group">
                                            <label>Transaction Type</label>
                                            <select required class="form-control" name="type">
                                                <option {{ old('type',$transaction->type) == "Credit" ? "selected" : "" }} value="Credit">Credit</option>
                                                <option {{ old('type',$transaction->type) == "Debit" ? "selected" : "" }} value="Debit">Debit</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Bank</label>
                                            <select required class="form-control input-sm" name="bank_account_id">
                                                <option value="">Select Bank</option>
                                                @foreach($banks as $bank)
                                                    <option {{ old('bank_account_id',$transaction->bank_account_id) ==  $bank->id ? "selected" : "" }} value="{{ $bank->id }}">{{ $bank->bank->name }} - {{ $bank->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Transaction Amount</label>
                                            <input class="form-control input-sm" value="{{ old('amount',$transaction->amount) }}" name="amount" placeholder="Transaction Amount"  step="0.00000000000000000000" type="number"/>
                                        </div>
                                        <div class="form-group">
                                            <label>More Description / Reference</label>
                                            <textarea class="form-control input-sm" tabindex="-6" required placeholder="More Description / Reference" name="comment">{{ old('comment',$transaction->comment) }}</textarea>
                                        </div>
                                        <button type="submit"  class="btn btn-primary"> Save</button>
                                    </form>
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
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
