<h2  class="text-center" >Kuitansi</h2>
<small>Nomor: {{$data->trans_no}}</small>
<table class="table-report">
    <thead>
    <tr>
        <th>No.</th>
        <th>Uraian</th>
        <th class="text-right">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    @php $no=1;$total=0; @endphp
    @foreach($data->details as $detail)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->amount)}}</td>
            </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>Jumlah</th>
            <th class="text-right">{{format_number($total)}}</th>
        </tr>
        <tr>
            <td colspan="3">Terbilang: {{ucwords(trim(inword($total)))}} Rupiah</td>
        </tr>
    </tfoot>
</table>
