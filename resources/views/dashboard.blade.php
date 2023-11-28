@extends('layouts.app')


@section('content')
<div class="ui-content-body">
    <div class="container">
    <!--
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title"> Dashboard
                    <small>Overview of Things in the System</small>
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel short-states">
                    <div class="panel-title">
                        <h4> <span class="label label-danger pull-right">Today's Sales</span></h4>
                    </div>
                    <div class="panel-body">
                        <h1>{{ number_format(dailySales(),2) }}</h1>
                        <div class="text-info pull-right"> <i class="fa fa-level-up"></i></div>
                        <small>Today's Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel short-states">
                    <div class="panel-title">
                        <h4> <span class="label label-info pull-right">Weekly's Sales</span></h4>
                    </div>
                    <div class="panel-body">
                        <h1>{{ number_format(weeklySales(),2) }}</h1>
                        <div class="text-info pull-right"> <i class="fa fa-level-up"></i></div>
                        <small>Weekly's Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel short-states">
                    <div class="panel-title">
                        <h4> <span class="label label-warning pull-right">Monthly Income</span></h4>
                    </div>
                    <div class="panel-body">
                        <h1>{{ number_format(monthlySales(),2) }}</h1>
                        <div class="text-info pull-right"> <i class="fa fa-level-up"></i></div>
                        <small>Monthly Sales</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel short-states">
                    <div class="panel-title">
                        <h4> <span class="label label-success pull-right">Total Stock</span></h4>
                    </div>
                    <div class="panel-body">
                        <h1>{{ \App\Models\Stock::where('status',1)->count() }}</h1>
                        <div class="text-success pull-right"><i class="fa fa-level-up"></i></div>
                        <small>Total Active Stock</small>
                    </div>
                </div>
            </div>
        </div>
 -->
    </div>
</div>
@endsection
