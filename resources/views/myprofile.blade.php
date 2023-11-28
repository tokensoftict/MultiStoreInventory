@extends('layouts.app')


@section('content')

    <style>
        input,select {
            margin-bottom: 20px !important;
        }
    </style>
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
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

                        <form  action="" enctype="multipart/form-data" method="post">

                            <div class="panel-body">
                                <div class="col-sm-7 col-lg-offset-2">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Full Name<span class="star">*</span>:</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" value="{{ old('name',$user->name) }}" required  class="form-control" name="name" placeholder="Full Name"/>
                                            @if ($errors->has('name'))
                                                <label for="name-error" class="error"
                                                       style="display: inline-block;">{{ $errors->first('name') }}</label>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Username<span class="star">*</span>:</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" value="{{ old('username',$user->username) }}" required  class="form-control" name="username" placeholder="Username"/>
                                            @if ($errors->has('username'))
                                                <label for="name-error" class="error"
                                                       style="display: inline-block;">{{ $errors->first('username') }}</label>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Email address<span class="star">*</span>:</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" value="{{ old('email',$user->email) }}" required  class="form-control" name="email" placeholder="Email Address"/>
                                            @if ($errors->has('email'))
                                                <label for="name-error" class="error"
                                                       style="display: inline-block;">{{ $errors->first('email') }}</label>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Password<span class="star">*</span>:</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="password" value="{{ old('password') }}" {{ !isset($user->id) ? "required" : "" }}  class="form-control" name="password" placeholder="Password"/>
                                            @if ($errors->has('password'))
                                                <label for="name-error" class="error"
                                                       style="display: inline-block;">{{ $errors->first('password') }}</label>
                                            @endif
                                            <small class="text-danger">Leave Blank, if you dont want to change your password</small>
                                        </div>
                                    </div>

                                    <br/>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <center>
                                    <button class="btn btn-info btn-lg" type="submit"><i class="fa fa-save"></i> Update Profile</button>
                                </center>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
