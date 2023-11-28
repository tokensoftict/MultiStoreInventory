@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link href="{{ asset('bower_components/datatables/media/css/jquery.dataTables.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-tabletools/css/dataTables.tableTools.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-colvis/css/dataTables.colVis.css') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-responsive/css/responsive.dataTables.scss') }}" rel="stylesheet">
    <link href="{{ asset('bower_components/datatables-scroller/css/scroller.dataTables.scss') }}" rel="stylesheet">
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
                                <th>From</th>
                                <th>To</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Product Type</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transfer->stock->name }}</td>
                                    <td>{{ $transfer->store_from->name }}</td>
                                    <td>{{ $transfer->store_to->name }}</td>
                                    <td>{{ $transfer->quantity }}</td>
                                    <td>{{ convert_date($transfer->transfer_date) }}</td>
                                    <td>{{ convert_date($transfer->product_type) }}</td>
                                    <td>{{ number_format($transfer->selling_price,2) }}</td>
                                    <td>{{ number_format($transfer->cost_price,2) }}</td>
                                    <td>{{ $transfer->user->name }}</td>
                                    <td>
                                        @if(userCanView('stocktransfer.delete_transfer',$transfer->id))
                                            <a data-msg="Are you sure, you want to delete this transfer" href="{{ route('stocktransfer.delete_transfer',$transfer->id) }}" class="btn btn-danger btn-sm confirm_action">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                            </tbody>
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
                        <form  action="{{ route('stocktransfer.add_transfer') }}" enctype="multipart/form-data" method="post">
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

                            <div class="form-group" style="margin-top: -20px">
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

                            <div class="form-group">
                                <label>Transfer From</label>
                                <select required id="store" name="from" class="form-control select2">
                                    @foreach($stores as $store)
                                        <option  value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('store'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('store') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Transfer To</label>
                                <select required id="store" name="to" class="form-control select2">
                                    @foreach($stores as $store)
                                        <option  value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('store'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('store') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Transfer Date</label>
                                <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  name="date_created" value="{{ \Carbon\Carbon::today()->toDateString() }}"/>
                                @if ($errors->has('date_created'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                @endif
                            </div>
                            <button class="btn btn-primary btn-lg" type="submit" name="save" value="save_and_create"><i class="fa fa-arrow-right"></i> Transfer Stock</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-tabletools/js/dataTables.tableTools.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-colvis/js/dataTables.colVis.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-responsive/js/dataTables.responsive.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('bower_components/datatables-scroller/js/dataTables.scroller.js') }}"></script>
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
