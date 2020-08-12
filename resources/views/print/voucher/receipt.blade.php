
    <h2  class="text-center" >Kuitansi</h2>
    <table>
        <tr>
            <td style="width: 150px">Tanggal</td>
            <td style="width: 20px">:</td>
            <td>{{fdate($data->trans_date)}}</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td>{{$data->trans_no}}</td>
        </tr>
        <tr>
            <td>Dibayarkan kepada</td>
            <td>:</td>
            <td>{{$data->contact->name}}</td>
        </tr>
<br/>
    </table>
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
                <th class="text-right">{{format_number($data->amount)}}</th>
            </tr>
            <tr>
                <td colspan="3">Terbilang: <b>{{ucwords(trim(inword($data->amount)))}} Rupiah</b></td>
            </tr>
        </tfoot>
    </table>

