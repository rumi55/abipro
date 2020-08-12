<table style="width:100%;margin-bottom:50px;">
	@if(!empty(company('logo')))
	<tr>
		<td class="text-center">
			<img height="100px" src="{{public_path().url_file(company('logo'))}}" >
		</td>
	</tr>
	@endif
	<tr>
		<td class="text-center">
		<h3>{{company('name')}}</h3>
		</td>
	</tr>
	</table>
