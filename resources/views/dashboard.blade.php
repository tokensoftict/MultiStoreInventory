@php use App\Models\Invoice;use Illuminate\Support\Arr; @endphp
@extends('layouts.app')

@push("js")
    @if(userCanView("reports.dashboard.income_and_expenses_analysis"))
        <script type="text/javascript" src="{{ asset("assets/js/echarts/echarts-all-3.js") }}"></script>
        <script>
            @php
                $report = $dashboard->getYearIncomeChartWithExpense();
                $category = Arr::pluck($report, "name");
                $expenses =  Arr::pluck($report, "totalExpenses");
                $income =  Arr::pluck($report, "totalIncome");
            @endphp
            var dom = document.getElementById("rainfall");
            var rainChart = echarts.init(dom);

            var app = {};
            option = null;
            option = {
                color: ['#11811f', '#bf2455'],
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['Income', 'Expenses']
                },
                calculable: false,
                xAxis: [
                    {
                        type: 'category',
                        data: JSON.parse('{!! json_encode($category) !!}')
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: 'Income',
                        type: 'bar',
                        data: JSON.parse('{!! json_encode($income) !!}')
                    },
                    {
                        name: 'Expenses',
                        type: 'bar',
                        data: JSON.parse('{!! json_encode($expenses) !!}')
                    }
                ]
            };

            if (option && typeof option === "object") {
                rainChart.setOption(option, false);
            }
            /**
             * Resize chart on window resize
             * @return {void}
             */
            window.onresize = function () {
                rainChart.resize();
            };
        </script>
    @endif
@endpush

@section('content')
    <div class="ui-content-body">
        <div class="container">
            @php
                $nearOSItems = $dashboard->getNearOutOfStockItems();
            @endphp
            @if($nearOSItems->count() > 0)
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="alert alert-warning alert-dismissible" role="alert" style="border-left: 5px solid #d9534f; background-color: #fcf8e3; color: #8a6d3b; margin-bottom: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 style="margin-top: 0; font-weight: 600; color: #c9302c; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" data-toggle="collapse" data-target="#reorderStockList" aria-expanded="false" class="collapsed">
                                <span>
                                    <i class="fa fa-exclamation-triangle"></i> Stock Re-order Alert ({{ getActiveStore()->name }})
                                    <span class="label label-danger" style="font-size: 11px; margin-left: 5px; vertical-align: middle;">{{ $nearOSItems->count() }} items need re-order</span>
                                </span>
                                <i class="fa fa-chevron-down toggle-icon" style="font-size: 12px; transition: transform 0.2s ease;"></i>
                            </h4>
                            <div id="reorderStockList" class="collapse">
                                <p style="font-size: 13px; margin-bottom: 10px; margin-top: 10px;">The following stock items have reached or fallen below their reorder level. Please replenish them as soon as possible.</p>
                                <div style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-condensed" style="margin-bottom: 0; font-size: 12px; color: #8a6d3b;">
                                        <thead>
                                            <tr style="border-bottom: 2px solid #f5e79e;">
                                                <th>Product Name</th>
                                                <th class="text-center">Current Quantity</th>
                                                <th class="text-center">Reorder Level</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($nearOSItems as $item)
                                                <tr style="border-bottom: 1px solid #f5e79e;">
                                                    <td style="font-weight: 600;">{{ $item->name }}</td>
                                                    <td class="text-center" style="font-weight: bold; color: #c9302c;">{{ number_format($item->available_quantity) }}</td>
                                                    <td class="text-center">{{ number_format($item->reorder_level) }}</td>
                                                    <td class="text-center">
                                                        <span class="label label-danger">Reorder</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @push('css')
                <style>
                    h4[data-toggle="collapse"] .toggle-icon {
                        transform: rotate(0deg);
                    }
                    h4[data-toggle="collapse"]:not(.collapsed) .toggle-icon {
                        transform: rotate(180deg);
                    }
                </style>
                @endpush
            @endif

            <div class="row">
                @if(userCanView("reports.dashboard.todays_income"))
                    <div class="col-md-3 col-sm-6">
                        <div class="panel short-states">
                            <div class="panel-title">
                                <h4><span class="label label-danger pull-right">Today's Income</span></h4>
                            </div>
                            <div class="panel-body">
                                <h3>N{{ money($dashboard->todaysIncome()) }}</h3>
                                <small>Today's Income</small>
                            </div>
                        </div>
                    </div>
                @endif
                @if(userCanView("reports.dashboard.current_month_income"))
                    <div class="col-md-3 col-sm-6">
                        <div class="panel short-states">
                            <div class="panel-title">
                                <h4><span class="label label-info pull-right">{{  \Carbon\Carbon::now()->format('F') }}'s Income</span>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <h3>N{{ money($dashboard->currentMonthIncome()) }}</h3>
                                <small>{{  \Carbon\Carbon::now()->format('F') }} Income</small>
                            </div>
                        </div>
                    </div>
                @endif
                @if(userCanView("reports.dashboard.todays_expenses"))
                    <div class="col-md-3 col-sm-6">
                        <div class="panel short-states">
                            <div class="panel-title">
                                <h4><span class="label label-warning pull-right">Today's Expenses</span></h4>
                            </div>
                            <div class="panel-body">
                                <h3>N{{ money($dashboard->todaysExpenses()) }}</h3>
                                <small>Today's Expenses</small>
                            </div>
                        </div>
                    </div>
                @endif
                @if(userCanView("reports.dashboard.current_month_expenses"))
                    <div class="col-md-3 col-sm-6">
                        <div class="panel short-states">
                            <div class="panel-title">
                                <h4><span class="label label-success pull-right">{{  \Carbon\Carbon::now()->format('F') }}'s Expenses</span>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <h3>N{{ money($dashboard->currentMonthExpenses()) }}</h3>
                                <small>{{  \Carbon\Carbon::now()->format('F') }}'s Expenses</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row">
                @if(userCanView("reports.dashboard.income_and_expenses_analysis"))
                    <div class="col-md-6">
                        <div class="panel">
                            <header class="panel-heading">
                                Income and Expenses Analysis
                                <span class="tools pull-right">
                                    <a class="close-box fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div id="rainfall" style="height: 390px"></div>
                            </div>
                        </div>
                    </div>
                @endif
                @if(userCanView("reports.dashboard.recent_payment"))
                    <div class="col-md-6">
                        <div class="panel">
                            <header class="panel-heading panel-border">
                                Recent Payment(s)
                                <span class="tools pull-right">
                                    <a class="close-box fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="table-responsive" style="height: 390px">
                                    <table class="table table-bordered table-responsive table convert-data-table table-striped"
                                           id="invoice-list" style="font-size: 12px">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Payment Method</th>
                                            <th>Bank</th>
                                            <th>Total Paid</th>
                                            <th>Payment Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $total=0;
                                        $payments = \App\Models\PaymentMethodTable::query()
                                        ->where('warehousestore_id', getActiveStore()->id)->latest()->limit(15)->get();
                                        @endphp
                                        @forelse($payments as $payment)
                                            @php
                                                $total+=$payment->amount;
                                                $bank = "";
                                                $bankpayment = json_decode($payment->payment_info);
                                                if(isset($bankpayment->bank_id)){
                                                    $bank = \App\Models\BankAccount::find($bankpayment->bank_id);
                                                    $bank = $bank->bank->name."-".$bank->account_number;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ optional($payment->customer)->firstname }} {{ optional($payment->customer)->lastname }}</td>
                                                <td>{{ optional($payment->payment_method)->name }}</td>
                                                <td>{{ $bank }} </td>
                                                <td>{{ money($payment->amount) }}</td>
                                                <td>{{ date("h:i a",strtotime($payment->created_at)) }}</td>
                                            </tr>

                                        @empty

                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if(userCanView("reports.dashboard.recent_invoice"))
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <header class="panel-heading panel-border">
                                Recent Invoice(s)
                                <span class="tools pull-right">
                                    <a class="close-box fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                @php
                                    $recentInvoices =  Invoice::with(['created_user','customer'])->where('warehousestore_id', getActiveStore()->id)->where('invoice_date',date('Y-m-d'))->latest()->limit(15)->get()
                                @endphp
                                <x-invoice-list-component :lists="$recentInvoices" :show-total="false"/>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
