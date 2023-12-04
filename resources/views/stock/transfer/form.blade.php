@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')

    <div class="ui-container">

        <div class="row">
            @if(!isset($transfer->id))
                <div class="col-md-8">
                    <section class="panel">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif

                        <div class="panel-body">
                            <form action="" method="post">
                                {{ csrf_field() }}
                                <br/>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Transfer From</label>
                                        <select required id="store" name="from" class="form-control select2">
                                            @foreach($stores as $store)
                                                <option {{ $from == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Transfer To</label>
                                        <select required id="store" name="to" class="form-control select2">
                                            @foreach($stores as $store)
                                                <option  {{ $to == $store->id ? "selected" : "" }}  value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Select Product Type</label>
                                        <select required id="type" name="type" class="form-control select2">
                                            @foreach(config('stock_type_name.'.config('app.store')) as $key=>$_type)
                                                <option {{ $_type == $type ? "selected" : ""  }}  value="{{ $_type }}">{{ $key }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-sm btn-primary"  style="margin-top: 25px;" type="submit">Go</button>
                                    </div>
                                </div>
                                <br/>
                            </form>
                        </div>

                    </section>
                </div>
            @endif
            @php
                if(isset($from) && isset($to) && isset($type)){
            @endphp
            <div class="col-md-12" >
                <section class="panel">
                    <div class="panel-heading">
                        {{ $title }}
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <form action="">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <label>Select Stock</label>
                                        <select class="form-control" name="stock" id="products" placeholder="Search for Product"></select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Available Quantity</label>
                                        <span type="text"  class="form-control" id="av_qty"></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Quantity</label>
                                        <input type="number" max="0" class="form-control" id="qty"/>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-sm btn-primary" onclick="return add_item();" style="margin-top: 25px;" type="button">Add Stock</button>
                                    </div>
                                </div>
                                <br/>
                            </form>
                            <br/>
                            @if(isset($transfer->id))
                                <form onsubmit="return checkform()" action="{{ route('stocktransfer.update',$transfer->id) }}" enctype="multipart/form-data" method="post">
                                    {{ method_field('PUT') }}
                            @else
                                <form onsubmit="return checkform()" action="" enctype="multipart/form-data" method="post">
                             @endif
                                {{ csrf_field() }}
                                <input type="hidden" name="from" value="{{ $from }}"/>
                                <input type="hidden" name="to" id="to" value="{{ $to }}"/>
                                <input type="hidden" name="type" id="type" value="{{ $type }}"/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-3 col-lg-offset-9">
                                            <label>Transfer Date</label>
                                            <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd"   name="transfer_date" value="{{ date('Y-m-d',strtotime($transfer->transfer_date)) }}"/>
                                            @if ($errors->has('date_created'))
                                                <label for="name-error" class="error"
                                                       style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <br/>  <br/>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-striped table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="text-left">Name</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-right">Selling Price</th>
                                                    <th class="text-right">Total</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id="appender">
                                                @foreach($transfer->stock_transfer_items()->get() as $item)
                                                    <tr>
                                                        <input type="hidden" name="stock_id[]" value="{{ $item->stock_id }}"/>
                                                        <input type="hidden" name="qty[]" value="{{ $item->quantity }}"/>
                                                        <td>{{ $item->stock->name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ $item->cost_price }}</td>
                                                        <td>{{ number_format( ($item->cost_price * $item->quantity),2) }}</td>
                                                        <td><button class="btn btn-sm btn-danger" onclick="remove_item(this)">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <th class="text-right">Total</th>
                                                    <th class="text-right" id="total_transfer">{{ number_format($transfer->total_price,2) }}</th>
                                                    <td></td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            @if(isset($transfer->id))
                                                <button class="btn btn-primary btn-sm" type="submit" name="status" value="DRAFT"><i class="fa fa-arrow-right"></i>Update</button>
                                            @else
                                                <button class="btn btn-primary btn-sm" type="submit" name="status" value="DRAFT"><i class="fa fa-arrow-right"></i>Draft Transfer Stock</button>
                                                <button class="btn btn-success btn-sm" type="submit" name="status" value="COMPLETE"><i class="fa fa-arrow-right"></i>Complete Transfer Stock</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
            @php
                }
            @endphp

        </div>

    </div>
@endsection


@push('js')
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false" src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script data-turbolinks-eval="false" data-turbo-eval="false"  src="{{ asset('assets/js/init-datepicker.js') }}"></script>

    <script>
        $(document).ready(function(e){
            var path = "{{ route('findselectstock') }}?select2=yes&type={{ $type }}&store={{ $from }}";
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
                if(data) {
                    $('#av_qty').html(data['available_quantity']);
                    $('#qty').attr('max', data['available_quantity']);

                    $('#qty').attr('data-cost-price', data['cost_price'])
                    $('#qty').attr('data-selling-price', data['cost_price'])
                }
            });

        });

        function add_item(){
            if(document.getElementById($("#products option:selected").val()+"-row")){
                $("#qty").val("");
                $('#av_qty').html('');
                $("#cost_price").val("");
                alert("Item already exits, Please check and try again");
                return false;
            }

            if(($("#products option:selected").val() ==="") || ($("#qty").val()==="")){
                return false;
            }

            var html= "<tr id='"+$("#products option:selected").val()+"-row'>";
            html+='<input type="hidden" name="stock_id[]" value="'+$("#products option:selected").val()+'"/>';
            html+='<input type="hidden" name="qty[]" value="'+$("#qty").val()+'"/>';
            html+="<td>"+$("#products option:selected").text()+"</td>";
            html += "<td class='text-center'>" + $("#qty").val() + "</td>";
            html+="<td class='text-right'>"+formatMoney($('#qty').attr('data-cost-price'))+"</td>";
            html+="<td class='total_trans text-right' value='"+(parseFloat($('#qty').attr('data-cost-price')) * parseFloat($("#qty").val()))+"' >"+formatMoney(($('#qty').attr('data-cost-price') * parseFloat($("#qty").val())))+"</td>";
            html+='<td><button class="btn btn-sm btn-danger" onclick="remove_item(this)">Remove</button></td>';
            html+="</tr>";

            $("#products").val('').trigger('change')
            $("#qty").val("");
            $('#av_qty').html('');
            $("#cost_price").val("");
            $("#appender").append(html);
            total();
            return false;
        }

        function remove_item(btn){
            $(btn).parent().parent().remove();
            total();
        }


        function total(){
            var total =0;
            $('.total_trans').each(function(id,elem){
                total +=parseFloat($(elem).attr('value'));
            })

            $('#total_transfer').html(formatMoney(total));
        }

        function checkform(){
            if($('#appender tr').length === 0){
                alert("Please add at least one item to to continue");
                return false;
            }else{
                $('#save_and_create').attr('disabled','disabled')
            }

            return true
        }
    </script>
@endpush
