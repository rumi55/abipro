
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small">Tanggal: {{$period}}</small>
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="border-top border-bottom-double">Kode</th>
                <th rowspan="2" class="border-top border-bottom-double">Akun</th>
                <th colspan="2" class="border-top border-bottom text-center">
                Saldo Awal
                </th>
                <th colspan="2" class="border-top border-bottom text-center">
                Perubahan
                </th>
                <th colspan="2" class="border-top border-bottom text-center">
                Saldo Akhir
                </th>
            </tr>
            <tr>
                <th class="border-top border-bottom-double text-center">Debet</th>
                <th class="border-top border-bottom-double text-center">Kredit</th>
                <th class="border-top border-bottom-double text-center">Debet</th>
                <th class="border-top border-bottom-double text-center">Kredit</th>
                <th class="border-top border-bottom-double text-center">Debet</th>
                <th class="border-top border-bottom-double text-center">Kredit</th>
            </tr>
        </thead>
        <tbody>
        @php 
        $group = null; 
        $total_op_debit = 0;
        $total_op_credit = 0;
        $total_debit = 0;
        $total_credit = 0;
        $total_fin_debit = 0;
        $total_fin_credit = 0;
        @endphp
            @foreach($accounts as $dt)
                @php 
                $total_op_debit += $dt->op_debit;
                $total_op_credit += $dt->op_credit;
                $total_debit += $dt->debit;
                $total_credit += $dt->credit;
                $total_fin_debit += $dt->total_debit;
                $total_fin_credit += $dt->total_credit;
                
                @endphp
                @if($group!==$dt->account_group)
                @php $group=$dt->account_group @endphp
                    <tr><td colspan="8"></td></tr>
                    <tr>
                        <td colspan="7" class="font-bold">{{$dt->account_group=='asset'?'Aset':($dt->account_group=='liability'?'Kewajiban':($dt->account_group=='equity'?'Ekuitas':($dt->account_group=='income'?'Pendapatan':'Beban')))}}</td>
                    </tr>        
                @endif
                <tr>
                    <td>{{$dt->account_no}}</td>
                    <td>{{$dt->account_name}}</td>
                    <td class="text-right">{{format_number($dt->op_debit)}}</td>
                    <td class="text-right">{{format_number($dt->op_credit)}}</td>
                    <td class="text-right">{{format_number($dt->debit)}}</td>
                    <td class="text-right">{{format_number($dt->credit)}}</td>
                    <td class="text-right">{{format_number($dt->total_debit)}}</td>
                    <td class="text-right">{{format_number($dt->total_credit)}}</td>
                </tr>        
            @endforeach    
            <tr>
                <td class="border-top border-bottom" colspan="2">Jumlah</td>
                <td class="border-top border-bottom text-right">{{format_number($total_op_debit)}}</td>
                <td class="border-top border-bottom text-right">{{format_number($total_op_credit)}}</td>
                <td class="border-top border-bottom text-right">{{format_number($total_debit)}}</td>
                <td class="border-top border-bottom text-right">{{format_number($total_credit)}}</td>
                <td class="border-top border-bottom text-right">{{format_number($total_fin_debit)}}</td>
                <td class="border-top border-bottom text-right">{{format_number($total_fin_credit)}}</td>
            </tr>
        </tbody>
    </table>