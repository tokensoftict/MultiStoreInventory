@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@endpush

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
                        @if(isset($user->id))
                            <form  action="{{ route('user.update',$user->id) }}" enctype="multipart/form-data" method="post">
                                {{ method_field('PUT') }}
                                @else
                                    <form  action="{{ route('user.store') }}" enctype="multipart/form-data" method="post">
                                        @endif
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
                                                    <label class="col-md-3 col-xs-12 control-label">Select Store<span class="star">*</span>:</label>
                                                    <div class="col-md-9 col-xs-12">
                                                        <select class="form-control select-store" name="store[]" multiple>
                                                            @foreach($stores as $store)
                                                                <option {{ in_array($store->id, $mystores) ? 'selected' : '' }} value="{{ $store->id }}">{{ $store->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <br/>  <br/>
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
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-3 col-xs-12 control-label">User Group<span class="star">*</span>:</label>
                                                    <div class="col-md-9 col-xs-12">
                                                        <select class="form-control" name="group_id" required>
                                                            <option value="">-Select Group-</option>
                                                            @foreach($groups as $group)
                                                                <option {{ (old('group_id',$user->group_id) == $group->id ) ? 'selected' : "" }} value="{{ $group->id }}">{{ $group->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('group_id'))
                                                            <label for="name-error" class="error"
                                                                   style="display: inline-block;">{{ $errors->first('group_id') }}</label>
                                                        @endif
                                                    </div>
                                                </div>

                                                <br/>
                                            </div>
                                        </div>
                                        <input type="hidden" name="customer_type" value="0"/>
                                        <input type="hidden" name="customer_id" value="0"/>
                                        <div class="panel-footer">
                                            <center>
                                                @if(!isset($user->id))
                                                    <button class="btn btn-info btn-lg" type="submit"><i class="fa fa-save"></i> Add User</button>
                                                @else
                                                    <button class="btn btn-info btn-lg" type="submit"><i class="fa fa-save"></i> Update User</button>
                                                @endif
                                            </center>
                                        </div>
                                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
@endpush