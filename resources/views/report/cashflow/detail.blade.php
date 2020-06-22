
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small">Tanggal: {{$period}}</small>
    <table>
        <thead>
            <tr>
                <th>Nomor Bukti</th>
                <th>Tanggal</th>
                <th colspan="2">Akun</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td colspan="4" class="font-bold">Saldo Awal ({{$start_balance_date}})</td>
        <td colspan="2" class="text-right">{{format_number($balance)}}</td>
        </tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr><td colspan="6" class="font-bold">Penerimaan</td></tr>
        @php 
        $sum1=0;
        $sum2=0;
        @endphp
        @if(count($income)==0)
        <tr><td colspan="6">--Tidak ada transaksi--</td></tr>
        @endif
        @foreach($income as $i => $dt)
        @php $sum1 = $sum1+$dt->credit; @endphp
            <tr>
                <td>{{$dt->trans_no}}</td>
                <td>{{fdate($dt->trans_date)}}</td>
                <td>{{$dt->account_no}}</td>
                <td>{{$dt->account_name}}</td>
                <td>{{$dt->description}}</td>
                <td class="text-right">{{format_number($dt->credit)}}</td>
            </tr>
        @endforeach    
        <tr>
            <td colspan="4" class="font-bold bt-2 bb-1">Total Penerimaan</td>
            <td colspan="2" class="text-right bt-2 bb-1">{{format_number($sum1)}}</td>
        </tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr><td colspan="6" class="font-bold">Pengeluaran</td></tr>
        @if(count($expense)==0)
        <tr><td colspan="6">--Tidak ada transaksi--</td></tr>
        @endif
        @foreach($expense as $i => $dt)
            @php $sum2 = $sum2+$dt->debit; @endphp
            <tr>
                <td>{{$dt->trans_no}}</td>
                <td>{{fdate($dt->trans_date)}}</td>
                <td>{{$dt->account_no}}</td>
                <td>{{$dt->account_name}}</td>
                <td>{{$dt->description}}</td>
                <td class="text-right">{{format_number($dt->debit)}}</td>
            </tr>
        @endforeach    
        <tr>
            <td colspan="4" class="font-bold bt-2 bb-1">Total Pengeluaran</td>
            <td colspan="2" class="text-right bt-2 bb-1">{{format_number($sum2)}}</td>
        </tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr>
        <td colspan="4" class="font-bold">Saldo Akhir ({{$end_balance_date}})</td>
        <td colspan="2" class="text-right">{{format_number($balance+$sum1-$sum2)}}</td>
        </tr>
        </tbody>
    </table>