<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
	<style type="text/css">
/* @media print { */
    @page {
		margin: 1.5cm;
	}
	/* target the first page only */
	@page :first {
		margin-top: 1cm;
	}
	body {
		margin: 0;
		color: #000;
		background-color: #fff;
		font: 12pt Georgia, "Sans-Serif";
		line-height: 1.3;
		/* background:rgb(204,204,204) */
	}
	page{
		background:#fff;
		display:block;
		margin:0 auto;
		margin-bottom: 0.5cm;
		box-shadow: 0 0 0 0.5cm rgba(0,0,0,0.5)
	}
	page[size="A4"]{
		width: 21cm;
		height: 29.7cm;
	}
	
	header, aside, nav, form, iframe, .menu, .hero, .adslot {
		display: none;
	}
	footer {
        position: fixed; 
        bottom: 0px; 
        left: 0px; 
        right: 0px;
        line-height: 1.0;
        font: 8pt Georgia, "Sans-Serif";
    }
    h1 {
		font-size: 24pt;
	}
	h2, h3, h4 {
		font-size: 14pt;
		margin-top: 25px;
	}
    .page-break {
        page-break-after: always;
    }
	.border-top{
		border-top: 1px solid #000;
	}
	.border-top-double{
		border-top: 2px double #000;
	}
	.border-bottom{
		border-bottom: 1px solid #000;
	}
	.border-bottom-double{
		border-bottom: 2px double #000;
	}
	table {
		/* page-break-inside: avoid; */
		break-inside: avoid;
		font-size: 9pt;	
		border-collapse: collapse !important;
		width: 100%;
	}
	table td,
	table th {
		background-color: #fff !important;
		padding: 5px;
		vertical-align: top;
	}
	table td{
		word-wrap: break-word;
	}
	.table-bordered th,
	.table-bordered td {
		border: 1px solid #000 !important;
	}
	.text-justify {
	text-align: justify !important;
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
	.font-bold{
		font-weight: bold;
	}
	.font-small{
		font-size: 8pt;
	}
	.font-medium{
		font-size: 10pt;
	}
    .nobreak{ page-break-inside:avoid;}
/* } */
    </style>

</head>
<body>

@yield('content')
</body>
</html>
