@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-8">
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
                            <table class="table table-hover table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>Account Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $account->bank->name }}</td>
                                        <td>{{ $account->account_number }}</td>
                                        <td>{{ $account->account_name }}</td>
                                        <td>{!! $account->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                        <td>
                                            @if (userCanView('bank.toggle'))
                                                @if($account->status == 1)
                                                    <a href="{{ route('bank.toggle',$account->id) }}" class="btn btn-danger btn-sm">Disable</a>
                                                @else
                                                    <a href="{{ route('bank.toggle',$account->id) }}" class="btn btn-success btn-sm">Enable</a>
                                                @endif
                                            @endif

                                                @if (userCanView('bank.edit'))
                                                    <a href="{{ route('bank.edit',$account->id) }}" class="btn btn-success btn-sm">Edit</a>
                                                @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                    </div>
                </section>
            </div>
            @if(userCanView('bank.create'))
                <div class="col-md-4">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title2 }}
                    </header>
                    <div class="panel-body">
                        <form id="validate" action="{{ route('bank.store') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control" name="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                    @if ($errors->has('bank_id'))
                                        <label for="name-error" class="error"
                                               style="display: inline-block;">{{ $errors->first('bank_id') }}</label>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Bank Account Name</label>
                                <input type="text" value="{{ old('account_name') }}" required  class="form-control" name="account_name" placeholder="Bank Account Name"/>
                                @if ($errors->has('account_name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('account_name') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" value="{{ old('account_number') }}" required  class="form-control" name="account_number" placeholder="Bank Account Number"/>
                                @if ($errors->has('account_number'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('account_number') }}</label>
                                @endif
                            </div>

                            <div class="pull-left">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                            </div>
                            <br/> <br/>
                        </form>
                    </div>
                </section>
            </div>
            @endif
        </div>
    </div>
@endsection
