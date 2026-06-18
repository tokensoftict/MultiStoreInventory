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
                        <form id="validate" action="{{ route('price-category.update', $category->id) }}" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{ old('name', $category->name) }}" required class="form-control" name="name" placeholder="Price Category Name"/>
                                @if ($errors->has('name'))
                                    <label class="error" style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Price Type</label>
                                <select class="form-control" name="price_type" required>
                                    <option value="packed" {{ $category->price_type == 'packed' ? 'selected' : '' }}>Packed (Bundle)</option>
                                    <option value="yard" {{ $category->price_type == 'yard' ? 'selected' : '' }}>Yard (Piece)</option>
                                </select>
                                @if ($errors->has('price_type'))
                                    <label class="error" style="display: inline-block;">{{ $errors->first('price_type') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" placeholder="Description">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <input type="hidden" name="status" value="{{ $category->status }}"/>
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
