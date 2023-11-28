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
                                <th>Type</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Action</th>
                            </tr>
                            @foreach($warehouse_and_shops as $warehouse_and_shop)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $warehouse_and_shop->name }}</td>
                                    <td>{{ $warehouse_and_shop->type }}</td>
                                    <td>{!! $warehouse_and_shop->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                    <td>{!! $warehouse_and_shop->default == 1 ? label("Default","success") : "" !!}</td>
                                    <td>
                                        @if (userCanView('warehouse_and_shop.toggle'))
                                            @if($warehouse_and_shop->status == 1)
                                                <a href="{{ route('warehouse_and_shop.toggle',$warehouse_and_shop->id) }}" class="btn btn-danger btn-sm">Disable</a>
                                            @else
                                                <a href="{{ route('warehouse_and_shop.toggle',$warehouse_and_shop->id) }}" class="btn btn-success btn-sm">Enable</a>
                                            @endif
                                        @endif

                                        @if (userCanView('warehouse_and_shop.edit'))
                                            <a href="{{ route('warehouse_and_shop.edit',$warehouse_and_shop->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        @endif

                                        @if (userCanView('warehouse_and_shop.set_as_default') && $warehouse_and_shop->default == "0" &&  $warehouse_and_shop->status == "1")
                                            <a href="{{ route('warehouse_and_shop.set_as_default',$warehouse_and_shop->id) }}" class="btn btn-success btn-sm">Set Default</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </section>
            </div>
            @if(userCanView('warehouse_and_shop.create'))
                <div class="col-md-4">
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $title2 }}
                        </header>
                        <div class="panel-body">
                            <form id="validate" action="{{ route('warehouse_and_shop.store') }}" enctype="multipart/form-data" method="post">
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
                                    <label>Type</label>
                                    <select class="form-control" name="type">
                                        <option>SHOP</option>
                                        <option>WAREHOUSE</option>
                                    </select>
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
