@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class=" col-md-offset-1 col-md-6">
                @if(session('success'))
                    {!! alert_success(session('success')) !!}
                @elseif(session('error'))
                    {!! alert_error(session('error')) !!}
                @endif
                <form action="" method="post">
                    {{ csrf_field() }}
                    <section class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-10">

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Select Stock to Convert</label>
                                        <select class="form-control  select-stock"  name="select_stock" id="customer_id">
                                            @if(isset($stock))
                                                <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <br/>
                                    <input class="btn btn-info btn-sm" style="margin-top: 5px;" type="submit" name="save" value="Fetch Stock">
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
            @if(isset($convert_stock))
                <div class="col-md-10 col-md-offset-1">
                    <form action="" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="stock_id" value="{{ $convert_stock->id }}"/>
                        <section class="panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4>Stock Information</h4>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Name</label><br/>
                                            <label style="font-size: 15px">{{ $convert_stock->name }}</label>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Available Quantity</label><br/>
                                            <label style="font-size: 15px">{{ $convert_stock->available_quantity }}</label>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Selling Price</label><br/>
                                            <label style="font-size: 15px">{{ number_format($convert_stock->selling_price,2) }}</label>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Cost Price</label><br/>
                                            <label style="font-size: 15px">{{ number_format($convert_stock->cost_price,2) }}</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h4>Stock Yards / Pieces Information</h4>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Name</label><br/>
                                            <label style="font-size: 15px">{{ $convert_stock->name }}</label>
                                        </div>
                                        <div class="form-group">
                                            <label style="font-size: 12px">Available Yards Quantity</label><br/>
                                            <label style="font-size: 15px;">{{ $convert_stock->available_yard_quantity }}</label>
                                        </div>
                                        <div class="form-group" style="margin-top: -10px">
                                            <label style="font-size: 12px">Yards Selling Price</label><br/>
                                            <input type="text" value="{{ $convert_stock->yard_selling_price }}"   class="form-control" name="yard_selling_price" placeholder="Yards Selling Price"/>
                                        </div>
                                        <div class="form-group" >
                                            <label style="font-size: 12px"> Yards Cost Price</label><br/>
                                            <input type="text" value="{{ $convert_stock->yard_cost_price }}"   class="form-control" name="yard_cost_price" placeholder="Yards Cost Price"/>
                                        </div>
                                        <div class="form-group" >
                                            <label style="font-size: 12px">Total Quantity in One Bundle</label><br/>
                                            <input type="number"   class="form-control" name="tt_bundle" placeholder="Total Quantity in One Bundle"/>
                                        </div>
                                        <div class="form-group" >
                                            <label style="font-size: 12px"> Quantity to Convert</label><br/>
                                            <input type="number"   class="form-control" name="tt_convert" placeholder="Quantity to Convert"/>
                                        </div>

                                        <input class="btn btn-info btn-block btn-lg" type="submit" name="save" value="Convert">

                                    </div>
                                </div>
                            </div>
                        </section>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endsection;


@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script  src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script>
        $(".select-stock").select2({
            placeholder: 'Search for product',
            ajax: {
                url: '{{ route('findstock') }}',
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        searchTerm: data.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results:response
                    };
                },
            }
        });
    </script>
@endpush
