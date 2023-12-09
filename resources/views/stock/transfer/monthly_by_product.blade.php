@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        <form action=""  class="tools pull-right"  method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>To</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>Search Product</label>
                                    <select id="products" class="form-control" name="product">
                                        <option selected value="{{ $product }}">{{ $product_name }}</option>
                                    </select>
                                </div>
                                <div class="col-sm-1"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </header>
                    <div class="panel-body">
                        <br/>  <br/><br/>
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Transfer Number</th>
                                <th>Stock</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Product Type</th>
                                <th>Selling Price</th>
                                <th>Quantity</th>
                                <th>Total Selling Price</th>
                                <th>By</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transfer->stock_transfer->transfer_number }}</td>
                                    <td>{{ $transfer->stock->name }}</td>
                                    <td>{{ $transfer->store_from->name }}</td>
                                    <td>{{ $transfer->store_to->name }}</td>
                                    <td>{!! $transfer->stock_transfer->status == "COMPLETE" ? label( $transfer->stock_transfer->status,'success') : label( $transfer->status,'primary') !!}</td>
                                    <td>{{ convert_date2($transfer->transfer_date) }}</td>
                                    <td>{{ array_flip(config('stock_type_name.'.config('app.store')))[$transfer->product_type] }}</td>
                                    <td>{{ number_format($transfer->selling_price,2) }}</td>
                                    <td>{{ $transfer->quantity }}</td>
                                    <td>{{ number_format(($transfer->selling_price * $transfer->quantity),2) }}</td>
                                    <td>{{ $transfer->user->name }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th>{{ number_format($transfers->sum('selling_price'),2) }}</th>
                                <th>{{ $transfers->sum('quantity') }}</th>
                                <th>{{ number_format($transfers->sum(function($transfer){
                                                        return $transfer->selling_price * $transfer->quantity;
                                                }),2) }}</th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
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
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
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
        });
    </script>
@endpush
