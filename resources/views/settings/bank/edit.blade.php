@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
                <div class="col-md-4">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        <form id="validate" action="{{ route('bank.update',$account->id) }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Bank</label>
                                <select class="form-control" name="bank_id">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $b)
                                        <option {{ old('bank_id',$account->bank_id) == $b->id ? "selected" : "" }} value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                    @if ($errors->has('bank_id'))
                                        <label for="name-error" class="error"
                                               style="display: inline-block;">{{ $errors->first('bank_id') }}</label>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Bank Account Name</label>
                                <input type="text" value="{{ old('account_name',$account->account_name) }}" required  class="form-control" name="account_name" placeholder="Bank Account Name"/>
                                @if ($errors->has('account_name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('account_name') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" value="{{ old('account_number',$account->account_number) }}" required  class="form-control" name="account_number" placeholder="Bank Account Number"/>
                                @if ($errors->has('account_number'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('account_number') }}</label>
                                @endif
                            </div>

                            <div class="pull-left">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                            </div>
                            <br/> <br/>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
