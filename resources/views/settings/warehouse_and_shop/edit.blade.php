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
                            <div class="form-group">
                                <label>Address Line 1</label>
                                <input type="text" value="{{ old('first_address', $warehouse_and_shop->first_address) }}"   class="form-control" name="first_address" placeholder="Address Line 1"/>
                            </div>
                            <div class="form-group">
                                <label>Address Line 2</label>
                                <input type="text" value="{{ old('second_address', $warehouse_and_shop->second_address) }}"   class="form-control" name="second_address" placeholder="Address Line 2"/>
                            </div>
                            <div class="form-group">
                                <label>Contact Phone Number</label>
                                <input type="text" value="{{ old('contact_number', $warehouse_and_shop->contact_number) }}"   class="form-control" name="contact_number" placeholder="Contact Phone Number"/>
                            </div>
                            <div class="form-group">
                                <label>Logo</label>
                                <input type="file"  name="logo" class="form-control">
                                @if ($errors->has('logo'))
                                    <label for="name-error" class="error" style="display: inline-block;">{{ $errors->first('logo') }}</label>
                                @endif
                            </div>
                            @if(!empty($warehouse_and_shop->logo))
                                <img src="{{ asset('img/'.$warehouse_and_shop->logo) }}"  class="img-responsive" style="width:30%; margin: auto; display: block;"/>
                            @endif
                            <div class="form-group">
                                <label>Footer Notes</label>
                                <input type="text" value="{{ old('footer_notes',  $warehouse_and_shop->footer_notes) }}"   class="form-control" name="footer_notes" placeholder="Footer Notes"/>
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
