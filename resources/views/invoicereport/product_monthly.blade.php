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
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}

                        <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
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
                                <div class="col-sm-3"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                        <table class="table table-bordered table-responsive table convert-data-table table-striped" id="invoice-list" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice/Receipt No</th>
                                <th>Customer</th>
                                <th>Stock</th>
                                <th>Qty Sold</th>
                                <th>Total Cost Price</th>
                                <th>Total Selling Price</th>
                                <th>Profit</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total_cost =0;
                                $total_selling =0;
                                $total_profit =0;
                            @endphp
                            @foreach($invoices as $invoice)
                                @php
                                    $total_cost+=($invoice->quantity * $invoice->cost_price);
                                    $total_selling+=($invoice->quantity * $invoice->selling_price);
                                    $total_profit += (($invoice->quantity * $invoice->selling_price)-($invoice->quantity * $invoice->cost_price));
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invoice->invoice->invoice_paper_number }}</td>
                                    <td>{{ $invoice->customer->firstname }} {{ $invoice->customer->lastname }}</td>
                                    <td>{{ $invoice->stock->name }}</td>
                                    <td>{{ $invoice->quantity }}</td>
                                    <td>{{ number_format(($invoice->quantity * $invoice->cost_price),2) }}</td>
                                    <td>{{ number_format(($invoice->quantity * $invoice->selling_price),2) }}</td>
                                    <td>{{ number_format((($invoice->quantity * $invoice->selling_price)-($invoice->quantity * $invoice->cost_price)),2) }}</td>
                                    <td>{!! invoice_status($invoice->status) !!}</td>
                                    <td>{{ convert_date2($invoice->invoice->invoice_date) }}</td>
                                    <td>{{ $invoice->invoice->sales_time }}</td>
                                    <td>{{ $invoice->invoice->created_user->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('invoiceandsales.view'))
                                                    <li><a href="{{ route('invoiceandsales.view',$invoice->invoice->id) }}">View Invoice</a></li>
                                                @endif
                                                @if(userCanView('invoiceandsales.pos_print'))
                                                    <li><a href="{{ route('invoiceandsales.pos_print',$invoice->invoice->id) }}">Print Invoice Pos</a></li>
                                                @endif
                                                @if(userCanView('invoiceandsales.print_afour'))
                                                    <li><a href="{{ route('invoiceandsales.print_afour',$invoice->invoice->id) }}">Print Invoice A4</a></li>
                                                @endif
                                                @if(userCanView('invoiceandsales.print_way_bill'))
                                                    <li><a href="{{ route('invoiceandsales.print_way_bill',$invoice->invoice->id) }}">Print Waybill</a></li>
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
                                <th></th>
                                <th></th>
                                <th>{{ number_format($total_cost,2) }}</th>
                                <th>{{ number_format($total_selling,2) }}</th>
                                <th>{{ number_format($total_profit,2) }}</th>
                                <th></th>
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
        });
    </script>
@endpush
