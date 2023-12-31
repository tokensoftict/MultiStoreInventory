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
                                <div class="col-sm-2">
                                    <label>From</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $from }}" name="from" placeholder="From"/>
                                </div>
                                <div class="col-sm-2">
                                    <label>To</label>
                                    <input type="text" class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  value="{{ $to }}" name="to" placeholder="TO"/>
                                </div>
                                <div class="col-sm-3">
                                    <label>Select Type</label>
                                    <select class="form-control" name="type">
                                        <option value="PURCHASE" {{ $type == "PURCHASE" ? 'selected' : "" }}>PURCHASE</option>
                                        <option value="RETURN" {{ $type == "RETURN" ? 'selected' : "" }}>RETURNS</option>
                                    </select>
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
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Purchase Invoice Number</th>
                                <th>Supplier</th>
                                <th>Stock</th>
                                <th>Qty Purchase / Returns</th>
                                <th>Total Selling Price</th>
                                <th>Total Cost Price</th>
                                <th>Date</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($datas as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->purchase_order->purchase_order_invoice_number }}</td>
                                    <td>{{ $data->purchase_order->supplier->name }}</td>
                                    <td>{{ $data->stock->name }}</td>
                                    <td>{{ $data->qty }}</td>
                                    <td>{{ number_format(($data->cost_price * $data->qty),2) }}</td>
                                    <td>{{ number_format(($data->selling_price * $data->qty),2) }}</td>
                                    <td>{{ convert_date2($data->purchase_order->date_created) }}</td>
                                    <td>{{ $data->user->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('purchaseorders.show'))
                                                    <li><a href="{{ route('purchaseorders.show',$data->purchase_order->id) }}">View Purchase Order</a></li>
                                                @endif
                                                @if(userCanView('purchaseorders.edit') && $data->purchase_order->status == "DRAFT")
                                                    <li><a href="{{ route('purchaseorders.edit',$data->purchase_order->id) }}">Edit Purchase Order</a></li>
                                                @endif
                                                @if(userCanView('purchaseorders.markAsComplete') && $data->purchase_order->status == "DRAFT")
                                                    <li><a href="{{ route('purchaseorders.markAsComplete',$data->purchase_order->id) }}">Complete Purchase Order</a></li>
                                                @endif

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>{{ $datas->sum('qty') }}</th>
                                        <th>{{ number_format($datas->sum(function($data){
                                                return ($data->selling_price * $data->qty);
                                            }),2) }}</th>
                                        <th>{{ number_format($datas->sum(function($data){
                                                return ($data->cost_price * $data->qty);
                                            }),2) }}</th>
                                        <th></th>
                                        <th></th>
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
