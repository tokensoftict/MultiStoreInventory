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
                        <form id="validate" action="{{ route('customer.store') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" value="{{ old('firstname',$customer->firstname) }}" required  class="form-control" name="firstname" placeholder="First Name"/>
                                @if ($errors->has('firstname'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('firstname') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" value="{{ old('lastname',$customer->lastname) }}" required  class="form-control" name="lastname" placeholder="Last Name"/>
                                @if ($errors->has('lastname'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('lastname') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" value="{{ old('email',$customer->email) }}"   class="form-control" name="email" placeholder="Email Address"/>
                                @if ($errors->has('email'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('email') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" value="{{ old('phone_number',$customer->phone_number) }}" required  class="form-control" name="phone_number" placeholder="Phone Number"/>
                                @if ($errors->has('phone_number'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('phone_number') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="address">{{ old('phone_number',$customer->address) }}</textarea>
                            </div>
                            <div class="pull-left">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Add</button>
                            </div>

                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
