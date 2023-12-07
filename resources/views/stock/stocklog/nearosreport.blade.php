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

                        <form action=""  class="tools pull-right" style="margin-right: 80px" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-8">
                                    <label>Store</label>
                                    <select class="form-control" name="warehousestore_id">
                                        @foreach($stores as $store_)
                                            <option {{ $store_['id'] == $warehousestore_id ? "selected" : "" }} value="{{ $store_['id'] }}">{{ $store_['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3"><br/>
                                    <button type="submit" style="margin-top: 5px;" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </header>
                    <div class="panel-body">
                        <br/> <br/>
                        <table class="table table-bordered table-responsive table-striped convert-data-table" style="font-size: 12px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Product Type</th>
                                <th>Reorder Level</th>
                                <th>{{ $store->name }} Quantity</th>
                                <th>{{ $store->name }} Yards Quantity</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nearos as $near)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $near->name }}</td>
                                    <td>{{ $near->type }}</td>
                                    <td>{{ $near->reorder_level }}</td>
                                    <td>{{ $near->available_quantity }}</td>
                                    <td>{{ $near->available_yard_quantity }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button" aria-expanded="false">Action <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                @if(userCanView('stock.edit'))
                                                    <li><a href="{{ route('stock.edit',$near->stock_id) }}">Edit</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
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
@endpush
