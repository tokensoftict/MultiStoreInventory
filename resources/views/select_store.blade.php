<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ getStoreSettings()->name }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" data-turbolinks-track="reload">
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/weather-icons/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/themify-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/main.css') }}">
    <link href="{{ asset('assets/js/bootstrap-submenu/css/bootstrap-submenu.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('bower_components/rickshaw/rickshaw.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/jquery-easy-pie-chart/easypiechart.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/horizontal-timeline/css/style.css') }}">
    <style>
        .select2-container, .selection, .select2-selection {
            height: 30px;
            width: 100% !important;
            font-size: 10px;
        }
        input {
            margin-bottom: 0 !important;
        }
    </style>
    @stack('css')

    <script src="{{ asset('assets/js/modernizr-custom.js') }}"></script>
    <!-- <script  src="{{ asset('js/app.js') }}"   ></script>-->
    <script>let productfindurl = ""; window.validating_modal_show = false;</script>
</head>

<body>

<div id="ui" class="ui ui-aside-none">
    @include('layouts.header')
    <div id="content" class="ui-content ui-content-aside-overlay">
        <div class="ui-content-body">
            <div class="container">
                <div class="row w-states">
                    @foreach($stores as $store)
                        <div class="col-md-3 col-sm-6">
                            <div class="panel text-center">
                                <a href="{{ route('selected-store', $store->warehousestore_id) }}">
                                    <div class="state-title">
                                        <span class="value" style="font-size: 22px; font-weight: bolder">{{ $store->warehousestore->name }}</span>
                                        <span class="info">Click to Select</span>
                                    </div>
                                    <div class="progress-bar-danger">
                                        <div class="progress mbot-0">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success">
                                                <span></span>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</div>

<script    src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script   src="{{ asset('bower_components/jquery/dist/jquery-ui.min.js') }}"></script>
<script    src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script    src="{{ asset('bower_components/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
<script    src="{{ asset('bower_components/autosize/dist/autosize.min.js') }}"></script>
<script    src="{{ asset('assets/js/bootstrap-submenu/js/bootstrap-submenu.js') }}"></script>
<script    src="{{ asset('assets/js/bootstrap-hover-dropdown.js') }}"></script>

</body>
</html>