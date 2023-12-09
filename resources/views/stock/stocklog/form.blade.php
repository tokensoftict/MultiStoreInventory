@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

@endpush


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-9">
                <section class="panel">
                    @if(session('success'))
                        {!! alert_success(session('success')) !!}
                    @elseif(session('error'))
                        {!! alert_error(session('error')) !!}
                    @endif
                    <div class="panel-heading">
                        {{ $title2 }}
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 10px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Stock</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Product Type</th>
                                <th>Usage Type</th>
                                <th>Store</th>
                                <th>Selling Price</th>
                                <th>Total Selling</th>
                                <th>Cost Price</th>
                                <th>Total Cost</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->stock->name }}</td>
                                    <td>{{ $log->quantity }}</td>
                                    <td>{{ convert_date2($log->log_date) }}</td>
                                    <td>{{ array_search($log->product_type,config('stock_type_name.'.config('app.store'))) }}</td>
                                    <td>{{ $log->stock_log_usages_type->name ?? "" }}</td>
                                    <td>{{ $log->warehousestore->name }}</td>
                                    <td>{{ number_format($log->selling_price,2) }}</td>
                                    <td>{{ number_format(($log->selling_price * $log->quantity ),2) }}</td>
                                    <td>{{ number_format($log->cost_price,2) }}</td>
                                    <td>{{ number_format(($log->cost_price * $log->quantity ),2) }}</td>
                                    <td>{{ $log->user->name }}</td>
                                    <td>
                                        @if(userCanView('stocklog.edit',$log->id))
                                            <a href="{{ route('stocklog.edit',$log->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        @endif
                                        @if(userCanView('stocklog.delete_log',$log->id))
                                            <a href="{{ route('stocklog.delete_log',$log->id) }}" class="btn btn-danger btn-sm">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                            </tbody>
                            <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Total</th>
                            <th>{{ number_format($logs->sum(function($log){
                                        return $log->selling_price * $log->quantity;
                                    }),2)  }}</th>
                            <th></th>
                            <th>{{ number_format($logs->sum(function($log){
                                        return $log->cost_price * $log->quantity;
                                    }),2)  }}</th>
                            <th></th>
                            <th></th>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <form  action="{{ route('stocklog.add_log') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Select Stock</label>
                                <select name="stock_id"  class="form-control select2" id="products">
                                    <option value="">-Select One-</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label style="white-space: nowrap;">Quantity</label>
                                <input required type="number" name="qty" placeholder="Quantity" class="form-control" id="qty"/>
                            </div>

                            <div class="form-group" >
                                <label>Select Usage Type</label>
                                <select required id="usage_type" name="usage_type" class="form-control">
                                    @foreach($usages as $usage)
                                        <option value="{{ $usage->id }}">{{ $usage->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('usage_type'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('supplier') }}</label>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Select Product Type</label>
                                <select required id="product_type" name="product_type" class="form-control select2">
                                    @foreach(config('stock_type_name.'.config('app.store')) as $key=>$type)
                                        <option  value="{{ $type }}">{{ $key }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('product_type'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('product_type') }}</label>
                                @endif
                            </div>
                            <input type="hidden" id="department" name="department" value="STORE" />


                            <input type="hidden" id="store" name="store" value="{{ getActiveStore()->id }}" />

                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  name="date_created" value="{{ \Carbon\Carbon::today()->toDateString() }}"/>
                                @if ($errors->has('date_created'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                @endif
                            </div>
                            <button class="btn btn-primary btn-lg" type="submit" name="save" value="save_and_create"><i class="fa fa-save"></i> Log Stock</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>




    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
    <script>
        $(document).ready(function(e){
            var path = "{{ route('findselectstock') }}?select2=yes";
            var select =  $('#products').select2({
                placeholder: 'Search for product',
                ajax: {
                    url: path,
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


            $("#products").on('change',function(eventData){
                var data = $(this).select2('data');
                data = data[0];
            });

        });
    </script>
@endpush
