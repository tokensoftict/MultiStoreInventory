@extends('layouts.app')

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-lg-7 col-lg-offset-3">
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
                        <form id="validate" action="{{ route('store_settings.update') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Store Name</label>
                                <input type="text" value="{{ old('name',@$store->name) }}"  required class="form-control" name="name" placeholder="Store Name"/>
                                @if ($errors->has('name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Branch Name</label>
                                <input  type="text"  value="{{ old('branch_name',@$store->branch_name) }}" required class="form-control" name="branch_name" placeholder="Branch Name"/>
                                @if ($errors->has('branch_name'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('branch_name') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>VAT</label>
                                <input  type="text"  value="{{ old('tax',@$store->tax) }}"  class="form-control" name="tax" placeholder="VAT"/>
                            </div>
                            <div class="form-group">
                                <label>Near Expiry Days</label>
                                <input  type="number" value="{{ old('near_expiry_days',@$store->near_expiry_days) }}" required class="form-control" name="near_expiry_days" placeholder="Near Expiry Days"/>
                                @if ($errors->has('near_expiry_days'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('near_expiry_days') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Store Address Line</label>
                                <textarea name="first_address"  required class="form-control" placeholder="Store Address">{{ old('first_address',@$store->first_address) }}</textarea>
                                @if ($errors->has('first_address'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('first_address') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Store Address Line 2</label>
                                <textarea name="second_address" class="form-control" placeholder="Store Address Line 2">{{ old('second_address',@$store->second_address) }}</textarea>
                                @if ($errors->has('second_address'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('second_address') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Store Contact Numbers</label>
                                <textarea name="contact_number" required class="form-control" placeholder="Store Contact Numbers">{{ old('contact_number',@$store->contact_number) }}</textarea>
                                @if ($errors->has('contact_number'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('contact_number') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Store Logo</label>
                                <input type="file"  name="logo" class="form-control">
                                @if ($errors->has('logo'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('logo') }}</label>
                                @endif
                            </div>
                            @if(!empty($store->logo))
                            <img src="{{ asset('img/'.$store->logo) }}"  class="img-responsive" style="width:30%; margin: auto; display: block;"/>
                            @endif
                            <div class="form-group">
                                <label>Footer Receipt Notes</label>
                                <textarea name="footer_notes" class="form-control" placeholder="Footer Receipt Notes">{{ old('footer_notes',@$store->footer_notes) }}</textarea>
                                @if ($errors->has('footer_notes'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('footer_notes') }}</label>
                                @endif
                            </div>
                            @if(userCanView("store_settings.update"))
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save Changes</button>
                            @endif
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
