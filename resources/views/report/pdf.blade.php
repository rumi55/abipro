<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    <style type="text/css">
    @charset "utf-8";
    @font-face {
    font-family: 'Source Sans Pro', sans-serif;
    src: url({{ storage_path('fonts/SourceSansPro-Regular.ttf') }});
    font-weight: 400;
    font-style: normal;
    }
    @font-face {
    font-family: 'Source Sans Pro', sans-serif;
    src:url({{ storage_path('fonts/SourceSansPro-ExtraLight.ttf') }});
    font-weight: 300;
    font-style: normal;
    }
    *,
    *::before,
    *::after {
    box-sizing: border-box;
    }

    html {
    font-family: sans-serif;
    line-height: 1.15;
    -webkit-text-size-adjust: 100%;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }

    article, aside, figcaption, figure, footer, header, hgroup, main, nav, section {
    display: block;
    }

    body {
    margin: 0;
    font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    font-size: 11pt;
    font-weight: 400;
    line-height: 1;
    color: #212529;
    text-align: left;
    background-color: #ffffff;
    }
        
    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    b,
    strong {
    font-weight: bolder;
    }

    small {
    font-size: 80%;
    }

    table {
        border-collapse: collapse;
    }
    </style>
    <link rel="stylesheet" href="{{asset('css/report.css')}}">
</head>
<body>
    @include('report._header_pdf')
    @include($view)            
</body>
</html>
