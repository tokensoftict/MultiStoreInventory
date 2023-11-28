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
                        <form id="validate" action="{{ route('warehouse_and_shop.update',$warehouse_and_shop->id) }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{ old('name',$warehouse_and_shop->name) }}" required  class="form-control" name="name" placeholder="Name"/>
                                @if ($errors->has('name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select class="form-control" name="type">
                                    <option {{ old('type',$warehouse_and_shop->type) == "SHOP" ? "selected" : "" }}>SHOP</option>
                                    <option {{ old('type',$warehouse_and_shop->type) == "WAREHOUSE" ? "selected" : "" }}>WAREHOUSE</option>
                                </select>
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
