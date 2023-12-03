@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-4">
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <form   action="{{ route('stocklog.update',$log->id) }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Selected Stock</label>
                                <input type="hidden" name="stock_id" value="{{ $log->stock->id }}"/>
                                <span class="form-control">
                                    {{ $log->stock->name }}
                                </span>
                            </div>

                            <div class="form-group">
                                <label style="white-space: nowrap;">Quantity</label>
                                <input required type="number" value="{{ $log->quantity }}" name="qty" placeholder="Quantity" class="form-control" id="qty"/>
                            </div>

                            <div class="form-group">
                                <label>Select Usage Type</label>
                                <select required id="usage_type" name="usage_type" class="form-control">
                                    @foreach($usages as $usage)
                                        <option {{ $log->usage_type == $usage->id ? "selected" : "" }} value="{{ $usage->id }}">{{ $usage->name }}</option>
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
                                        <option  {{ $log->product_type == $type ? "selected" : "" }}  value="{{ $type }}">{{ $key }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('product_type'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('product_type') }}</label>
                                @endif
                            </div>
                            <input type="hidden" id="department" name="department" value="STORE" />


                            <input type="hidden" id="store" name="store" value="{{ $log->warehousestore_id }}" />

                            <div class="form-group">
                                <label>Select Store</label>
                                <select required id="store" name="store" class="form-control select2">
                                    @foreach($stores as $store)
                                        <option {{ $log->warehousestore_id == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('store'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('store') }}</label>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control datepicker js-datepicker"  data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  name="date_created" value="{{ date('Y-m-d',strtotime($log->log_date))  }}"/>
                                @if ($errors->has('date_created'))
                                    <label for="name-error" class="error"
                                           style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                @endif
                            </div>
                            <button class="btn btn-primary btn-lg" type="submit" name="save" value="save_and_create"><i class="fa fa-save"></i> Update</button>
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
