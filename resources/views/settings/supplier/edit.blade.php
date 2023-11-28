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
                        <form id="validate" action="{{ route('supplier.update',$supplier->id) }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{ old('name',$supplier->name) }}" required  class="form-control" name="name" placeholder="Name"/>
                                @if ($errors->has('name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" value="{{ old('phonenumber',$supplier->phonenumber) }}" required  class="form-control" name="phonenumber" placeholder="Phone Number"/>
                                @if ($errors->has('phonenumber'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('phonenumber') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" value="{{ old('address',$supplier->address) }}"  class="form-control" name="address" placeholder="Address"/>
                                @if ($errors->has('address'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('address') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="text" value="{{ old('email',$supplier->email) }}"  class="form-control" name="email" placeholder="Email Address"/>
                                @if ($errors->has('email'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('email') }}</label>
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
