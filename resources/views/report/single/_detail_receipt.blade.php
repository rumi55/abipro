<table class="table-report">
    <thead>
    <tr>
        <th class="text-left">No.</th>
        <th class="text-left">Uraian</th>
        <th class="text-right">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    @php $no=1;$total=0; @endphp
    @if($type=='transaction')
    @foreach($data->details as $detail)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->amount)}}</td>
            </tr>
    @endforeach
    @else
    @foreach($data->details as $detail)
        @if($detail->credit>0 && $detail->debit==0)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->credit)}}</td>
            </tr>
        @endif
    @endforeach
    @endif
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>Jumlah</th>
            <th class="text-right">{{format_number($data->total)}}</th>
        </tr>
        <tr>
            <td colspan="3">Terbilang: <b>{{ucwords(trim(inword($data->total)))}} Rupiah</b></td>
        </tr>
    </tfoot>
</table>
