<html>

<head>
    <style type="text/css">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
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

    </style>
</head>

<body>
    @include('report._header_pdf')
    @yield('content')
</body>

</html>
