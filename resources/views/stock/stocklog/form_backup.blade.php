@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <section class="panel">
                    @if(session('success'))
                        {!! alert_success(session('success')) !!}
                    @elseif(session('error'))
                        {!! alert_error(session('error')) !!}
                    @endif
                    <div class="panel-heading">
                        <form action="" onsubmit="return add_item();">
                            <br/>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label>Select Stock</label>
                                    <select id="products" class="form-control select2" id="product">
                                        <option value="">-Select One-</option>
                                    </select>
                                </div>


                                <div class="col-sm-2">
                                    <label style="white-space: nowrap;">Quantity</label>
                                    <input type="number" class="form-control" id="qty"/>
                                </div>

                                <div class="col-sm-2">
                                    <button class="btn btn-sm btn-primary" onclick="add_item()" style="margin-top: 25px;" type="button">Add Stock</button>
                                </div>
                            </div>
                            <br/>
                        </form>
                    </div>
                    <div class="panel-body">
                        <form onsubmit="return checkform()" action="{{ route('stocklog.add_log') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Select Usage Type</label>
                                        <select required id="usage_type" name="usage_type" class="form-control">
                                            <option value="">-Select One-</option>
                                            <option value="DAMAGES">DAMAGES</option>
                                        </select>
                                        @if ($errors->has('usage_type'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('supplier') }}</label>
                                        @endif
                                    </div>

                                        <div class="col-md-3">
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
                                    <div class="col-md-3">
                                        <label>Select Store</label>
                                        <select required id="store" name="store" class="form-control select2">
                                            @foreach($stores as $store)
                                                <option  value="{{ $store->packed_column }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('store'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('store') }}</label>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label>Date</label>
                                        <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  name="date_created" value="{{ \Carbon\Carbon::today()->toDateString() }}"/>
                                        @if ($errors->has('date_created'))
                                            <label for="name-error" class="error"
                                                   style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <br/>
                                <div class="table-responsive">
                                    <table class="table table-striped table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-right">Selling Price</th>
                                            <th class="text-right">Cost Price</th>
                                            <th class="text-right">Total</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody id="appender">
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <th class="text-right">Total</th>
                                            <th class="text-right" id="total_po"></th>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <button class="btn btn-primary btn-lg" type="submit" name="save" value="save_and_create"><i class="fa fa-save"></i> Log Stock</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script   src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script   src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script   src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
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

        function add_item(){
            if(($("#products option:selected").val() ==="") || ($("#qty").val()==="") || ($("#cost_price").val()==="") || ($("#selling_price").val()==="")){
                return false;
            }
            if(document.getElementById('row_'+$("#products option:selected").val())){
                alert('Item already exist in the Purchase Order List');
                return false;
            }
            var html= "<tr id='row_"+$("#products option:selected").val()+"'>";
            html+='<input type="hidden" name="stock_id['+$("#products option:selected").val()+']" value="'+$("#products option:selected").val()+'"/>';
            html+='<input type="hidden" name="qty['+$("#products option:selected").val()+']" value="'+$("#qty").val()+'"/>';
            html+='<input type="hidden" name="cost_price['+$("#products option:selected").val()+']" value="'+$("#cost_price").val()+'"/>';
            html+='<input type="hidden" name="selling_price['+$("#products option:selected").val()+']" value="'+$("#selling_price").val()+'"/>';
            html+="<td>"+$("#products option:selected").text()+"</td>";
            html+="<td class='text-center'>"+$("#qty").val()+"</td>";
            html+="<td class='text-right'>"+formatMoney($("#selling_price").val())+"</td>";
            html+="<td class='text-right'>"+formatMoney($("#cost_price").val())+"</td>";
            html+="<td class='total_attr text-right' data-value='"+(parseFloat($("#cost_price").val()) * parseInt($("#qty").val()))+"'>"+formatMoney(parseFloat($("#cost_price").val()) * parseInt($("#qty").val()))+"</td>";
            html+='<td><button class="btn btn-sm btn-danger" onclick="remove_item(this)">Remove</button></td>';
            html+="</tr>";

            $("#products").select2("val","");
            $("#qty").val("");
            $("#cost_price").val("");
            $("#selling_price").val("");
            $("#appender").append(html);
            total_po();
            return false;
        }

        function total_po(){
            var total = 0;
            $('.total_attr').each(function(id,elem){
                var value = parseFloat($(elem).attr('data-value'));
                total +=value
            })
            $("#total_po").html(formatMoney(total));
        }

        function remove_item(btn){
            $(btn).parent().parent().remove();
        }

        function checkform(){
            if($('#appender tr').length === 0){
                alert("Please add atleast one item to to continue");
                return false;
            }

            return true
        }
    </script>
@endpush
