    <h4 class="text-center">{{$title}}</h4>
    <b>Tanggal:</b> {{$period}}
    <table class="table-report">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Akun</th>
                <th class="text-right">Saldo Awal</th>
                <th class="text-right">Debet</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $dt)
                <tr>
                    <td>{{$dt->account_no}}</td>
                    <td>{{$dt->account_name}}</td>
                    <td class="text-right">{{format_number($dt->op_balance)}}</td>
                    <td class="text-right">{{format_number($dt->debit)}}</td>
                    <td class="text-right">{{format_number($dt->credit)}}</td>
                    <td class="text-right">{{format_number($dt->total_balance)}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8" class="bt-2"></td>
            </tr>
        </tbody>
    </table>
