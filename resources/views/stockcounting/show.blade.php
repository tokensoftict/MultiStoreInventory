@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('table/datatables.css') }}">

    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-sm-12">
                @if(session('success'))
                    {!! alert_success(session('success')) !!}
                @elseif(session('error'))
                    {!! alert_error(session('error')) !!}
                @endif
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        <span class="tools pull-right">
                            @if(userCanView('counting.destroy') && $counting->status == "Pending")
                                <a href="{{ route('counting.destroy',$counting->id) }}" class="btn btn-sm btn-danger">Delete</a>
                                &nbsp; &nbsp; &nbsp;
                            @endif

                            @if(userCanView('counting.export_excel'))
                                <a  href="#" data-toggle="modal" data-target="#export_excel" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Export Excel</a>
                                &nbsp; &nbsp; &nbsp;
                            @endif
                            @if(userCanView('counting.import_excel'))
                                <a  href="#" data-toggle="modal" data-target="#myModal"  class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Import Excel</a>
                                &nbsp; &nbsp; &nbsp;
                            @endif
                        </span>
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Name</label><br/>
                                    <span class="form-control">{{ $counting->name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Date</label><br/>
                                    <span class="form-control">{{ convert_date2($counting->date) }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Created By</label><br/>
                                    <span class="form-control">{{ $counting->user->name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Status</label><br/>
                                    <span class="form-control">{!! showStatus($counting->status) !!}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 12px;">
                            <div class="col-sm-12">
                                <h4>Stock List</h4>
                                <table class="table table-bordered table-responsive table convert-data-table table-striped" style="font-size: 12px">
                                    <thead>
                                    <tr>
                                        <th>Stock Name</th>
                                        <th>Available Quantity</th>
                                        <th>Available Yard Quantity</th>
                                        <th>Counted Quantity</th>
                                        <th>Counted Yard Quantity</th>
                                        <th>Quantity Difference</th>
                                        <th>Quantity Yard Difference</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($counting->stock_taking_items as $count)
                                        <tr>
                                            <td>{{ $count->stock->name }}</td>
                                            <td>{{ $count->available_quantity }}</td>
                                            <td>{{ $count->available_yard_quantity }}</td>
                                            <td>{{ $count->counted_available_quantity }}</td>
                                            <td>{{ $count->counted_yard_quantity }}</td>
                                            <td>{{ $count->available_quantity_diff }}</td>
                                            <td>{{ $count->available_yard_quantity_diff }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @if(userCanView('counting.import_excel'))
        <div id="myModal" class="modal fade" role="dialog">
            <form action="{{ route('counting.import_excel',$counting->id) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Imported Counted Stock</h4>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="excel_file"/>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-default">Upload</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

    @if(userCanView('counting.export_excel'))
        <div id="export_excel" class="modal fade" role="dialog">
            <form action="{{ route('counting.export_excel',$counting->id) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Export Stock</h4>
                        </div>
                        <div class="modal-body">
                           <div class="form-group">
                               <label>Select Categories</label>
                               <select name="categories[]" multiple class="form-control  select-category">
                                   <option value="all">All Products</option>
                                   @foreach($categories as $category)
                                       <option value="{{ $category->id }}">{{ $category->name }}</option>
                                   @endforeach
                               </select>
                           </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-default">Export</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection


@push('js')


    <script type="text/javascript" src="{{ asset('table/datatables.js') }}"></script>

    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script src="{{ asset('assets/js/init-datatables.js') }}"></script>
@endpush
