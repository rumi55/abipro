<h4 class="text-center">{{$title}}</h4>
<table>
    <tr>
        <td><b>Tanggal:</b> {{$period}}</td>
        <td class="text-right"><b>Sortir:</b> {{$params['sortir']}}</td>
    </tr>
</table>
<table class="table-report">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Akun</th>
            <th class="text-right">Debet</th>
            <th class="text-right">Kredit</th>
            <th class="text-right">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($accounts as $dt)
        @if($dt->debit>0 || $dt->credit>0)
            <tr>
                <td>{{$dt->account_no}}</td>
                <td>{{$dt->account_name}}</td>
                <td class="text-right">{{format_number($dt->debit)}}</td>
                <td class="text-right">{{format_number($dt->credit)}}</td>
                <td class="text-right">{{format_number($dt->total_balance)}}</td>
            </tr>        
        @endif    
        @endforeach    
        <tr>
            <td colspan="8" class="bt-2"></td>
        </tr>
    </tbody>
</table>