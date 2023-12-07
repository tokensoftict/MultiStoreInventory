@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">

@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <section class="panel">
                    <header class="panel-heading panel-border">
                        {{ $title }}
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <form action="{{ route('counting.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="exampleInputEmail1">Name</label>
                                <input type="text" value="{{ old('name',$counting->name) }}" required  placeholder="Name" class="form-control" name="name"/>
                            </div>

                            <div class="form-group">
                                <label>Select Store</label>
                                <select required id="store" name="warehousestore_id" class="form-control select2">
                                    <option value="">Select Store</option>
                                    @foreach($stores as $store)
                                        <option  {{ $counting->warehousestore_id == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="text"  required class="form-control datepicker js-datepicker" value="{{ old('name',($counting->date ? $counting->date : date('Y-m-d',strtotime(date('Y-m-d'))))) }}" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"   name="date" placeholder="Date"/>
                            </div>


                            <button type="submit" class="btn btn-primary text-center"><i class="fa fa-save"></i> Save</button>

                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>

    <script  src="{{ asset('assets/js/init-datepicker.js') }}"></script>
@endpush
