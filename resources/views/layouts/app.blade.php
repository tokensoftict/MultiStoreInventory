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
    @include('layouts.nav')
    <div id="content" class="ui-content ui-content-aside-overlay">
        <div class="ui-content-body">
            @yield('content')
        </div>
    </div>

    @include('layouts.footer')
</div>

<div class="modal fade" id="validateInvoice" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
    <div class="modal-dialog modal-dialog-center modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="mloader"></div>
                <div class="loader-txt" id="loader-txt">
                </div>
            </div>
        </div>
    </div>
</div>


<script    src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script   src="{{ asset('bower_components/jquery/dist/jquery-ui.min.js') }}"></script>
<script    src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script    src="{{ asset('bower_components/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
<script    src="{{ asset('bower_components/autosize/dist/autosize.min.js') }}"></script>
<script    src="{{ asset('assets/js/bootstrap-submenu/js/bootstrap-submenu.js') }}"></script>
<script    src="{{ asset('assets/js/bootstrap-hover-dropdown.js') }}"></script>

@stack('js')

<!-- Common Script   -->
<script    src="{{ asset('dist/js/main.js') }}"></script>
<script>
    $('.confirm_action').on("click",function(e){
        if(confirm($(this).attr('data-msg') )== false){
            e.preventDefault();
        }
    });
    function confirm_action(elem){
        if(confirm($(elem).attr('data-msg')) == true){
            return true;
        }
        return false;
    }

    function open_print_window(elem){
        var href = $(elem).attr('href');
        var win = window.open(href, "MsgWindow", "width=800,height=500");
        win.onload = function(){
            win.print();
        }
        return false;
    }
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    }
</script>
<script>
    function showMask(loadingText){
        if(!loadingText){
            loadingText = "Processing Please wait..";
        }
        //$('#loadingMask').removeAttr('style','display:block;')
        if(window.validating_modal_show !== true) {
            window.validating_modal_show = true;
            $("#validateInvoice").modal({
                backdrop: "static", //remove ability to close modal with click
                keyboard: false, //remove option to close with keyboard
                show: true //Display loader!
            });
        }
        $('#loader-txt').html('<p>'+loadingText+'</p>');
    }

    function hideMask(){
        //$('#loadingMask').attr('style','display:none;');
        window.validating_modal_show = false;
        $("#validateInvoice").modal("hide");
    }


    window.addEventListener('load', function (){
        $(document).ready(function(){
            $('input[type=submit], button[type=submit]').click(function() {
                @php
                    $id = 'magic'.mt_rand();
                @endphp
                $('input[type=submit], button[type=submit]').attr('disabled', 'disabled');
                $(this).parents('form').append('<input type="hidden" id="{{ $id }}" name="'+$(this).attr('name')+'" value="'+$(this).val()+'" />');
                $(this).parents('form').submit();
                setTimeout(function(){
                    $('#{{ $id }}').remove();
                    $('input[type=submit], button[type=submit]').removeAttr('disabled');
                }, 10000)
            });
        })
    });

</script>
</body>
</html>
