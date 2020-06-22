    <h2  class="text-center" >{{$title}}</h2>
    <table>
        <tr>
            <td style="width:150px">Tanggal</td>
            <td style="width:10px">:</td>
            <td>{{fdate($data->trans_date)}}</td>
        </tr>
        <tr>
            <td>No. Bukti</td>
            <td>:</td>
            <td>{{$data->trans_no}}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td>{{$data->description}}</td>
        </tr>
    </table>        
    <br>
<table class="table-report">
        <thead>
            <tr>
                <th colspan="2">Akun</th>
                <th>Keterangan</th>
                <th class="text-right">Debet</th>
                <th class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data->details as $detail)
            <tr>
                <td>{{$detail->account->account_no}}</td>
                <td>{{$detail->account->account_name}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->debit)}}</td>
                <td class="text-right">{{format_number($detail->credit)}}</td>
            </tr>        
        @endforeach   
        </tbody>
    <tfoot>
        <tr>        
            <th colspan="3" class="text-left">Total</th>
            <th class="text-right">{{format_number($data->total)}}</th>
            <th class="text-right">{{format_number($data->total)}}</th>
        </tr>        
    </tfoot>
</table>
<br>
<table class="table-report table-report-bordered">
    <tr><td>Dibuat oleh:</td><td>Diperiksa oleh:</td><td>Disetujui oleh:</td><td>Diterima oleh:</td></tr>
    <tr><td style="height:100px"></td><td></td><td></td><td></td></tr>
</table>