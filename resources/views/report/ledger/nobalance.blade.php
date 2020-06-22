
<h2 class="text-center">{{$title}}</h2>    


<table>
<tr>
<td><b>Tanggal:</b> {{$period}}</td>
<td class="text-right"><small class="font-small"><b>Tags:</b> {{$tags}}</small></td>
</tr>
</table>
    <table class="table-report">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Dept.</th>                
                <th>No. Bukti</th>
                <th>Keterangan</th>
                <th class="text-right">Debet</th>
                <th class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php 
            $balance=0;
            $debit=0;
            $credit=0;
            $account_id = null; 
            $department_id = null; 
            $cdata = count($ledgers);
            @endphp
            @foreach($ledgers as $i=> $ledger)
                
                @if($account_id!==$ledger->account_id)
                    @php 
                    $account_id=$ledger->account_id; 
                    $balance = array_key_exists($ledger->account_id,$accounts)?$accounts[$ledger->account_id]:0;
                    @endphp
                    <tr>
                        <td colspan="6">{{$ledger->account_no}} {{$ledger->account_name}}</td>
                    </tr>        
                @endif
                @php 
                $balance+=($ledger->debit_sign+$ledger->credit_sign); 
                $debit+=$ledger->debit;
                $credit+=$ledger->credit; 
                @endphp
                <tr>
                    <td>{{fdate($ledger->trans_date)}}</td>
                    <td>{{$ledger->department_name}}</td>
                    <td>{{$ledger->trans_no}}</td>
                    <td>
                    {{$ledger->description}}
                    </td>
                    <td class="text-right">{{format_number($ledger->debit)}}</td>
                    <td class="text-right">{{format_number($ledger->credit)}}</td>
                </tr>        
                @if($i+1==$cdata)
                    <tr>
                        <td class="bt-1" colspan="6"></td>
                    </tr>        
                @else
                    @if($account_id!=($ledgers[$i+1])->account_id)
                    <tr>
                        <td class="bt-1" colspan="6"></td>
                    </tr>        
                    @php $debit=0;$credit=0;$balance=0; @endphp
                    @endif
                @endif
            @endforeach  
        </tbody>
    </table>