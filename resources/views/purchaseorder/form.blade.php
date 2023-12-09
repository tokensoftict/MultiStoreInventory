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
                                    <label>Recent Cost Price</label>
                                    <input  style="background-color: #FFF;color: #000" type="number" placeholder="Recent Cost Price" class="form-control" id="cost_price"/>
                                </div>

                                <div class="col-sm-2">
                                    <label>Selling Price</label>
                                    <input  style="background-color: #FFF;color: #000" type="number" placeholder="Selling Price" class="form-control" id="selling_price"/>
                                </div>

                                <div class="col-sm-2">
                                    <label style="white-space: nowrap;">Quantity</label>
                                    <input type="number" placeholder="Quantity" class="form-control" id="qty"/>
                                </div>

                                <div class="col-sm-2">
                                    <button class="btn btn-sm btn-primary" onclick="add_item()" style="margin-top: 25px;" type="button">Add Stock</button>
                                </div>
                            </div>
                            <br/>
                            <hr/>
                        </form>
                    </div>
                    <div class="panel-body">
                        @if(isset($porder->id))
                            <form onsubmit="return checkform()" action="{{ route('purchaseorders.update',$porder->id) }}" enctype="multipart/form-data" method="post">
                                {{ method_field('PUT') }}
                                @else
                                    <form onsubmit="return checkform()" action="{{ route('purchaseorders.store') }}" enctype="multipart/form-data" method="post">
                                        @endif
                                        {{ csrf_field() }}
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Select Supplier</label>
                                                    <select required id="supplier" name="supplier_id" class="form-control select2">
                                                        <option value="">-Select One-</option>
                                                        @foreach($suppliers as $suppllier)
                                                            <option {{ old('supplier_id',$porder->supplier_id) == $suppllier->id ? "selected" : "" }}  value="{{ $suppllier->id }}">{{ $suppllier->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('supplier'))
                                                        <label for="name-error" class="error"
                                                               style="display: inline-block;">{{ $errors->first('supplier') }}</label>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Select Store</label>
                                                    <select required id="store" name="store" class="form-control select2">
                                                        @foreach($stores as $store)
                                                            <option  {{ old('store',$porder->store) == $store->packed_column ? "selected" : "" }} value="{{ $store->packed_column }}">{{ $store->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('store'))
                                                        <label for="name-error" class="error"
                                                               style="display: inline-block;">{{ $errors->first('store') }}</label>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Date</label>
                                                    <input class="form-control datepicker js-datepicker" data-min-view="2" data-date-format="yyyy-mm-dd" style="background-color: #FFF; color: #000;"  name="date_created" value="{{ old('date_created', \Carbon\Carbon::today()->toDateString()) }}"/>
                                                    @if ($errors->has('date_created'))
                                                        <label for="name-error" class="error"
                                                               style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Purchase Invoice Number</label>
                                                    <input required id="purchase_order_invoice_number" value="{{ old('purchase_order_invoice_number', $porder->purchase_order_invoice_number) }}" placeholder="Purchase Invoice Number" class="form-control" name="purchase_order_invoice_number"/>
                                                    @if ($errors->has('date_created'))
                                                        <label for="name-error" class="error"
                                                               style="display: inline-block;">{{ $errors->first('date_created') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <input type="hidden" value="{{ $type }}" name="type">
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
                                                    @foreach($porder->purchase_order_items as $items)
                                                        <tr id='row_{{ $items->stock->id }}'>";
                                                            <input type="hidden" name="stock_id[]" value="{{ $items->stock->id }}"/>
                                                            <input type="hidden" name="qty[]" value="{{ $items->qty }}"/>
                                                            <input type="hidden" name="cost_price[]" value="{{ $items->cost_price }}"/>
                                                            <input type="hidden" name="selling_price[]" value="{{ $items->selling_price }}"/>
                                                            <td>{{ $items->stock->name }}</td>
                                                            <td class='text-center'>{{ $items->qty }}</td>
                                                            <td class='text-right'>{{ number_format($items->selling_price,2) }}</td>
                                                            <td class='text-right'>{{ number_format($items->cost_price,2) }}</td>
                                                            <td class='total_attr text-right' data-value='{{ $items->cost_price * $items->qty }}'>{{ number_format(($items->cost_price * $items->qty),2) }}</td>
                                                            <td><button class="btn btn-sm btn-danger" onclick="remove_item(this)">Remove</button></td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <th class="text-right">Total</th>
                                                        <th class="text-right" id="total_po">{{ number_format($porder->total,2) }}</th>
                                                        <td></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <button class="btn btn-success btn-sm" type="submit" name="status" value="DRAFT"><i class="fa fa-save"></i> Draft Purchase Order</button>
                                            <button class="btn btn-primary btn-sm" type="submit" name="status" value="COMPLETE"><i class="fa fa-save"></i> Complete Purchase Order</button>
                                        </div>
                                    </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/init-select2.js') }}"></script>
    <script src="{{ asset('assets/js/init-datepicker.js') }}"></script>


    <script>

        $(document).ready(function(e){
            var path = "{{ route('findpurchaseorderstock') }}?select2=yes";
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
                $("#cost_price").val(data['cost_price']);
                $("#selling_price").val(data['selling_price']);
                $("#quantity_qty").val(data['qty']);
            });

            $( "#supplier" ).select2();

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
            html+='<input type="hidden" name="stock_id[]" value="'+$("#products option:selected").val()+'"/>';
            html+='<input type="hidden" name="qty[]" value="'+$("#qty").val()+'"/>';
            html+='<input type="hidden" name="cost_price[]" value="'+$("#cost_price").val()+'"/>';
            html+='<input type="hidden" name="selling_price[]" value="'+$("#selling_price").val()+'"/>';
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
            total_po();
        }

        function checkform(){
            if($('#appender tr').length === 0){
                alert("Please add at least one item to to continue");
                return false;
            }

            if($('#supplier').val() == ""){
                alert("Please select a supplier to continue");
                return false;
            }


            if($('#purchase_order_invoice_number').val() == ""){
                alert("Please enter Purchase Invoice Number");
                return false;
            }

            return true
        }
    </script>
@endpush
