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

                        <table class="table table-hover">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ ucfirst($category->price_type) }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td>{!! $category->status == 1 ? label("Active","success") : label("Inactive","danger") !!}</td>
                                    <td>
                                        @if (userCanView('price-category.toggle'))
                                            @if($category->status == 1)
                                                <a href="{{ route('price-category.toggle',$category->id) }}" class="btn btn-danger btn-sm">Disable</a>
                                            @else
                                                <a href="{{ route('price-category.toggle',$category->id) }}" class="btn btn-success btn-sm">Enable</a>
                                            @endif
                                        @endif
                                        @if (userCanView('price-category.edit'))
                                            <a href="{{ route('price-category.edit',$category->id) }}" class="btn btn-success btn-sm">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                    </div>
                </section>
            </div>
            @if(userCanView('price-category.create'))
                <div class="col-md-4">
                    <section class="panel">
                        <header class="panel-heading">
                            {{ $title2 }}
                        </header>
                        <div class="panel-body">
                            <form id="validate" action="{{ route('price-category.store') }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" value="{{ old('name') }}" required class="form-control" name="name" placeholder="Price Category Name"/>
                                    @if ($errors->has('name'))
                                        <label class="error" style="display: inline-block;">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Price Type</label>
                                    <select class="form-control" name="price_type" required>
                                        <option value="packed">Packed (Bundle)</option>
                                        <option value="yard">Yard (Piece)</option>
                                    </select>
                                    @if ($errors->has('price_type'))
                                        <label class="error" style="display: inline-block;">{{ $errors->first('price_type') }}</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" placeholder="Description">{{ old('description') }}</textarea>
                                </div>
                                <input type="hidden" name="status" value="1"/>
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
