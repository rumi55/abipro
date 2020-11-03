<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        html {
            font-family: sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        p{
            line-height: 1;
            margin-bottom: 5;
            margin-top: 5;
        }
        h1{
            font-size: 12pt;
            font-weight: bold;
        }
        h2{
            font-size: 11pt;
            font-weight: bold;
        }
        h3{
            font-size: 11pt;
            font-weight: bold;
        }
        h4{
            font-size: 10pt;
            font-weight: bold;
        }
        .text-bold{
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }

        .table-report {
            border-collapse: collapse !important;
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            background-color: transparent;
            /* max-width: 970px; */
        }

        .table-report th {
            padding: 0.3rem;
            vertical-align: top;
            word-wrap: break-word;
            max-width: 200px;
        }

        .table-report td {
            padding: 0.2rem;
            vertical-align: top;
            word-wrap: break-word;
            max-width: 200px;
        }

        .table-report thead th {
            vertical-align: bottom;
            background-color: #d6d8db !important;
        }

        .table-report tfoot th {
            vertical-align: bottom;
            border-top: 2px solid #d6d8db;
            border-bottom: 1px solid #d6d8db;
        }

        .table-report tbody td {
            border: none;
        }

        .table-report tbody+tbody {
            border-top: 2px solid #d6d8db;
        }


        .table-report-sm th {
            padding: 0.2rem;
        }

        .table-report-sm td {
            padding: 0.1rem;
        }


        .table-report-sm th,
        .table-report-sm td {
            padding: 0.3rem;
        }

        .table-report-bordered {
            border: 1px solid #d6d8db;
        }

        .table-report-bordered th,
        .table-report-bordered td {
            border: 1px solid #d6d8db;
        }

        .table-report-bordered thead th,
        .table-report-bordered thead td {
            border-bottom-width: 2px;
        }

        .table-report td a {
            color: #212529;
            text-decoration: none;
        }

        .table-report td a:hover {
            color: #007bff;
        }

        .text-justify {
            text-align: justify !important;
        }

        .text-wrap {
            white-space: normal !important;
        }

        .text-nowrap {
            white-space: nowrap !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .font-bold {
            font-weight: bold;
        }

        .nob {
            border: none;
        }

        .table-report tbody td.bt-1 {
            border-top: 1px solid #d6d8db;
        }

        .table-report tbody td.bt-2 {
            border-top: 2px solid #d6d8db;
        }

        .table-report tbody td.bb-1 {
            border-bottom: 1px solid #d6d8db;
        }

        .table-report tbody td.bb-2 {
            border-bottom: 2px solid #d6d8db;
        }

        .table-report th.bt-1 {
            border-top: 1px solid #d6d8db;
        }

        .table-report th.bt-2 {
            border-top: 2px solid #d6d8db;
        }

        .table-report th.bb-1 {
            border-bottom: 1px solid #d6d8db;
        }

        .table-report th.bb-2 {
            border-bottom: 2px solid #d6d8db;
        }
        figure.table{
            margin-top:0;
                margin-left: 0;
                margin-right: 0;
                margin-bottom:0;
        }
        header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;

                text-align: center;
                line-height: 1.5cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: -0.5cm;
                left: 0cm;
                right: 0cm;
                height: 1cm;

                /** Extra personal styles **/
                text-align: center;
                line-height: 0.5cm;
            }
            .pagenum:before {
                content: counter(page);
            }
    </style>
</head>
<body>
    @include($view)
</body>
</html>
