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
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            @foreach($stock_log_usage_types as $stock_log_usage_type)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stock_log_usage_type->name }}</td>
                                    <td>{!! $stock_log_usage_type->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                    <td>
                                        @if (userCanView('expenses_type.toggle'))
                                            @if($stock_log_usage_type->status == 1)
                                                <a href="{{ route('stock_log_usage_type.toggle',$stock_log_usage_type->id) }}" class="btn btn-danger btn-sm">Disable</a>
                                            @else
                                                <a href="{{ route('stock_log_usage_type.toggle',$stock_log_usage_type->id) }}" class="btn btn-success btn-sm">Enable</a>
                                            @endif
                                        @endif
                                        @if (userCanView('stock_log_usage_type.edit'))
                                            <a href="{{ route('stock_log_usage_type.edit',$stock_log_usage_type->id) }}" class="btn btn-success btn-sm">Edit</a>
                                    @endif
                                </tr>
                            @endforeach
                        </table>

                    </div>
                </section>
            </div>
            @if(userCanView('expenses_type.create'))
                <div class="col-md-4">
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $title2 }}
                        </header>
                        <div class="panel-body">
                            <form id="validate" action="{{ route('stock_log_usage_type.store') }}" enctype="multipart/form-data" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" value="{{ old('name') }}" required  class="form-control" name="name" placeholder="Name"/>
                                    @if ($errors->has('name'))
                                        <label for="name-error" class="error"
                                               style="display: inline-block;">{{ $errors->first('name') }}</label>
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
