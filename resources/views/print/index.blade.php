<!DOCTYPE html>
<html>

<head>
	<title>Html2Pdf</title>
	<style>
		table {
			margin-bottom: 1em;
		}

		table td {
			padding: 3px;
		}

		.table1 {
			border: 1px solid red;
		}

		.table2,
		.table2 td {
			border: 1px solid silver;
			border-collapse: collapse;
		}

		.table2 td:first-child {
			background-color: lightblue;
		}

		.CSSTableGenerator {
			margin: 0px;
			padding: 0px;
			width: 100%;
			box-shadow: 10px 10px 5px #888888;
			border: 1px solid #000000;
			-moz-border-radius-bottomleft: 0px;
			-webkit-border-bottom-left-radius: 0px;
			border-bottom-left-radius: 0px;
			-moz-border-radius-bottomright: 0px;
			-webkit-border-bottom-right-radius: 0px;
			border-bottom-right-radius: 0px;
			-moz-border-radius-topright: 0px;
			-webkit-border-top-right-radius: 0px;
			border-top-right-radius: 0px;
			-moz-border-radius-topleft: 0px;
			-webkit-border-top-left-radius: 0px;
			border-top-left-radius: 0px;
		}

		.CSSTableGenerator table {
			border-collapse: collapse;
			border-spacing: 0;
			width: 100%;
			height: 100%;
			margin: 0px;
			padding: 0px;
		}

		.CSSTableGenerator tr:last-child td:last-child {
			-moz-border-radius-bottomright: 0px;
			-webkit-border-bottom-right-radius: 0px;
			border-bottom-right-radius: 0px;
		}

		.CSSTableGenerator table tr:first-child td:first-child {
			-moz-border-radius-topleft: 0px;
			-webkit-border-top-left-radius: 0px;
			border-top-left-radius: 0px;
		}

		.CSSTableGenerator table tr:first-child td:last-child {
			-moz-border-radius-topright: 0px;
			-webkit-border-top-right-radius: 0px;
			border-top-right-radius: 0px;
		}

		.CSSTableGenerator tr:last-child td:first-child {
			-moz-border-radius-bottomleft: 0px;
			-webkit-border-bottom-left-radius: 0px;
			border-bottom-left-radius: 0px;
		}

		.CSSTableGenerator tr:nth-child(odd) {
			background-color: #ffaa56;
		}

		.CSSTableGenerator tr:nth-child(even) {
			background-color: #ffffff;
		}

		.CSSTableGenerator td {
			vertical-align: middle;
			border: 1px solid #000000;
			border-width: 0px 1px 1px 0px;
			text-align: left;
			padding: 7px;
			font-size: 10px;
			font-family: Arial;
			font-weight: normal;
			color: #000000;
		}

		.CSSTableGenerator tr:last-child td {
			border-width: 0px 1px 0px 0px;
		}

		.CSSTableGenerator tr td:last-child {
			border-width: 0px 0px 1px 0px;
		}

		.CSSTableGenerator tr:last-child td:last-child {
			border-width: 0px 0px 0px 0px;
		}

		.CSSTableGenerator tr:first-child td {
			background: -o-linear-gradient(bottom, #ff7f00 5%, #bf5f00 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ff7f00), color-stop(1, #bf5f00));
			background: -moz-linear-gradient(center top, #ff7f00 5%, #bf5f00 100%);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff7f00", endColorstr="#bf5f00");
			background: -o-linear-gradient(top, #ff7f00, bf5f00);
			background-color: #ff7f00;
			border: 0px solid #000000;
			text-align: center;
			border-width: 0px 0px 1px 1px;
			font-size: 14px;
			font-family: Arial;
			font-weight: bold;
			color: #ffffff;
		}

		.CSSTableGenerator tr:first-child:hover td {
			background: -o-linear-gradient(bottom, #ff7f00 5%, #bf5f00 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ff7f00), color-stop(1, #bf5f00));
			background: -moz-linear-gradient(center top, #ff7f00 5%, #bf5f00 100%);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ff7f00", endColorstr="#bf5f00");
			background: -o-linear-gradient(top, #ff7f00, bf5f00);
			background-color: #ff7f00;
		}

		.CSSTableGenerator tr:first-child td:first-child {
			border-width: 0px 0px 1px 0px;
		}

		.CSSTableGenerator tr:first-child td:last-child {
			border-width: 0px 0px 1px 1px;
		}
	</style>
</head>

<body>
	<div id="html" style='position: absolute'>
		<h1>Tables</h1>
		<table class='table1'>
			<tr>
				<td>Item</td>
				<td>Cost</td>
				<td>Description</td>
				<td>Available</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
		</table>
		<table class='table2'>
			<tr>
				<td>Item</td>
				<td>Cost</td>
				<td>Description</td>
				<td>Available</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
		</table>

		<table class='CSSTableGenerator'>
			<tr>
				<td>Item</td>
				<td>Cost</td>
				<td>Description</td>
				<td>Available</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
			<tr>
				<td>Milk</td>
				<td>$1.00</td>
				<td>Hello PDF World</td>
				<td>Out Of Stock</td>
			</tr>
		</table>
	</div>

	<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/0.9.0rc1/jspdf.min.js"></script>
	<script>

		var pdf = new jsPDF('p', 'pt', 'letter');
		pdf.html(document.body, {
			callback: function (pdf) {
				var iframe = document.createElement('iframe');
				iframe.setAttribute('style', 'position:absolute;right:0; top:0; bottom:0; height:100%; width:500px');
				document.body.appendChild(iframe);
				iframe.src = pdf.output('datauristring');
			}
		});
	</script>
</body>

</html>
