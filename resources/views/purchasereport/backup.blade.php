@extends('layouts.app')


@section('content')
    <div class="ui-container">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        {{ $title }}
                        @if(userCanView('purchaseorders.create'))
                            <span class="tools pull-right">
                                  <a  href="{{ route('purchaseorders.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Purchase Order</a>
                            </span>
                        @endif
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            {!! alert_success(session('success')) !!}
                        @elseif(session('error'))
                            {!! alert_error(session('error')) !!}
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>


@endsection
