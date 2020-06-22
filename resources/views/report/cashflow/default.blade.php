<h4 class="text-center">{{$title}}</h4>
<b>Tanggal:</b> {{$period}}
    <table class="table-report">
        <thead>
            <tr>
                <th colspan="2">Akun</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td colspan="2" class="font-bold">Saldo Awal ({{fdate($start_balance_date, 'd M Y')}})</td>
        <td class="text-right">{{format_number($balance)}}</td>
        </tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr><td colspan="6" class="font-bold"></td></tr>
        <tr><td colspan="6" class="font-bold">Penerimaan</td></tr>
        @php 
        $sum1=0;
        $sum2=0;
        @endphp
        @if(count($income)==0)
        <tr><td colspan="3">--Tidak ada transaksi--</td></tr>
        @endif
        @foreach($income as $i => $dt)
        @php $sum1 = $sum1+$dt->total; @endphp
            <tr>
                <td>{{$dt->account_no}}</td>
                <td>{{$dt->account_name}}</td>
                <td class="text-right">{{format_number($dt->total)}}</td>
            </tr>
        @endforeach    
        <tr>
            <td colspan="2" class="bt-2 bb-1">Total Penerimaan</td>
            <td  class="text-right bt-2 bb-1">{{format_number($sum1)}}</td>
        </tr>
        <tr><td colspan="3" class="font-bold"></td></tr>
        <tr><td colspan="3" class="font-bold">Pengeluaran</td></tr>
        @if(count($expense)==0)
        <tr><td colspan="3">--Tidak ada transaksi--</td></tr>
        @endif
        @foreach($expense as $i => $dt)
            @php $sum2 = $sum2+$dt->total; @endphp
            <tr>
                <td>{{$dt->account_no}}</td>
                <td>{{$dt->account_name}}</td>
                <td class="text-right">{{format_number($dt->total)}}</td>
            </tr>
        @endforeach    
        <tr>
            <td colspan="2" class="bt-2 bb-1">Total Pengeluaran</td>
            <td class="text-right  bt-2 bb-1">{{format_number($sum2)}}</td>
        </tr>
        <tr><td colspan="3" class="font-bold"></td></tr>
        <tr>
        <td colspan="2" class="font-bold">Saldo Akhir ({{fdate($end_balance_date, 'd M Y')}})</td>
        <td class="text-right">{{format_number($balance+$sum1-$sum2)}}</td>
        </tr>
        </tbody>
    </table>