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
                                    <th>Name</th>
                                    <th>Phone Numbers</th>
                                    <th>Email Address</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->phonenumber }}</td>
                                        <td>{{ $supplier->email }}</td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>{!! $supplier->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                        <td>
                                            @if (userCanView('supplier.toggle'))
                                                @if($supplier->status == 1)
                                                    <a href="{{ route('supplier.toggle',$supplier->id) }}" class="btn btn-danger btn-sm">Disable</a>
                                                @else
                                                    <a href="{{ route('supplier.toggle',$supplier->id) }}" class="btn btn-success btn-sm">Enable</a>
                                                @endif
                                            @endif

                                            @if (userCanView('supplier.edit'))
                                                <a href="{{ route('supplier.edit',$supplier->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                    </div>
                </section>
            </div>
            @if(userCanView('supplier.create'))
                <div class="col-md-4">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title2 }}
                    </header>
                    <div class="panel-body">
                        <form id="validate" action="{{ route('supplier.store') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{ old('name') }}" required  class="form-control" name="name" placeholder="Name"/>
                                @if ($errors->has('name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" value="{{ old('phonenumber') }}" required  class="form-control" name="phonenumber" placeholder="Phone Number"/>
                                @if ($errors->has('phonenumber'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('phonenumber') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" value="{{ old('address') }}"  class="form-control" name="address" placeholder="Address"/>
                                @if ($errors->has('address'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('address') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="text" value="{{ old('email') }}"  class="form-control" name="email" placeholder="Email Address"/>
                                @if ($errors->has('email'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('email') }}</label>
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
