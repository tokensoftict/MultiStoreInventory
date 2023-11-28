<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} - @yield('title') </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        html, body {
            min-height: 100%;
            padding: 5px;
            margin: 5px 30px 3px 15px;
            /*margin-left: 3px;
            margin-right: 30px;*/
            font-family: 'Open Sans', 'Trebuchet Ms',Calibri, Tahoma,sans-serif;
            font-size: 10px;
            color: #000;
            overflow-x: hidden;
        }

        h2 {font-size: 18px;}
        h3 {font-size: 16px;}
        h2, h3 {margin-block-start: 0.1em;
            margin-block-end: 0.1em;}
        h3, .h3 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        h3 .footer-summary {
            margin-top: 5px;
            font-weight: 400;
        }

        h4 .summary-title {
            margin-top: 5px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: inherit;
            color: #000;
            padding: 0px;
            margin: 0px;
            line-height: 1.1;
        }

        h4, .h4 {
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 400;
        }

        div.title h3 {
            font-family: Verdana, Helvetica, "Gill Sans", sans-serifr;
        }
        .text-center{
            text-align: center;
        }
        .text-left {
            text-align: left;
        }

        .page-break {
            page-break-after: always;
        }

        .print-logo{ width: 70px;}

        /*.table .middle  { vertical-align: middle;}*/

        /*@page { size: 29.7cm 42cm; margin: 5mm}*/
        div.page {page-break-after:always}

        div.clear {
            clear: both;
            margin: 10px 0;
        }

        @media all {
            .page-break {
                display: block;
                page-break-after: always;
            }
        }

        div.pull-left {
            float: left;
            margin-right: 20px;
        }

        .pull-right {
            float: right!important;
        }

        .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
            border-color: #E5E5E5;
            border-width: 1px;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            padding: 4px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        .table-bordered td,.table-bordered th,.table-bordered tr {border:1px solid #ddd!important }
        .table-bordered>tbody>tr>td,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>td,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>thead>tr>th{border:1px solid #ddd}
        .table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}

        .table-bordered>tbody>tr>td,
        .table-bordered>tbody>tr>th{border:1px solid #ddd}
        .table-bordered>tbody>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}

        /*table.no-border tr th, table.no-border tr td {
            border: 0px
        }*/

        th {
            text-align: left;
        }

        tr.total {
            background: rgba(236, 151, 31, 0.25);
            border: 0px;
        }

        .money {
            text-align: right;
        }

        .center{ text-align: center; }

        #header,
        #footer {
            position: fixed;
            left: 0;
            right: 10px;
            color: #f00;
            font-size: 1.2em;
            text-align: right;
        }
        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }
        #footer {
            bottom: 30px;
            /*border-top: 0.1pt solid #aaa;*/
        }
        .page-number:before {
            content: "Page " counter(page);
        }

        #footer .paychoice {
            padding-left:10px;
            /*padding-bottom:70px;*/
            text-align: left !important;
            color:#000;
        }

        .paychoice:before {
            content: "";
        }
    </style>
    @yield('extra_css_files')
</head>
<body class="">
<!-- Main view  -->
@yield('content')

<style>

</style>

@stack('extra_js_scripts_files')

</body>
</html>
