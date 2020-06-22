<h4 class="text-center">{{$title}}</h4>
<b>Tanggal:</b> {{$period}}
    <table class="table-report">
        <thead>
            <tr>
                @if($columns['department'])
                <th>Dept.</th>                
                @endif
                <th>Tanggal</th>
                <th>No. Bukti</th>
                <th>Keterangan</th>
                <th class="text-right">Debet</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Saldo</th>
                @if($columns['created_by'])
                    <th class="text-right">Dibuat oleh</th>
                @endif
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
            $colspan = 3;
            if($columns['department']){
                $colspan+=1;
            }
            if($columns['created_by']){
                $colspan+=1;
            }
            @endphp
            @foreach($ledgers as $i=> $ledger)
                @if($account_id!==$ledger->account_id)
                    @php 
                    $account_id=$ledger->account_id; 
                    $balance = $ledger->opening_balance;
                    @endphp
                    <tr>
                        <td colspan="{{$colspan+4}}" class="">{{$ledger->account_no}}&nbsp;&nbsp;&nbsp;{{$ledger->account_name}}</td>
                    </tr>        
                    <tr>
                        <td colspan="{{$colspan}}" class="">Saldo Awal {{$balance_date}}</td>
                        <td colspan="3" class=" text-right">{{format_number($ledger->opening_balance)}}</td>
                        @if($columns['created_by'])
                        <td></td>
                        @endif
                    </tr>        
                @endif
                @php 
                $balance+=($ledger->debit_sign+$ledger->credit_sign); 
                $debit+=$ledger->debit;
                $credit+=$ledger->credit; 
                @endphp
                <tr>
                    @if($columns['department'])
                    <td>{{$ledger->department_name}}</td>
                    @endif
                    <td>{{fdate($ledger->trans_date)}}</td>
                    <td>{{$ledger->trans_no}}</td>
                    <td>
                    {{$ledger->description}}
                    @if($columns['tags'] && !empty($ledger->tags))
                    <br/><b>Tags:</b> {{$ledger->tags}}
                    @endif
                    </td>
                    <td class="text-right">{{format_number($ledger->debit)}}</td>
                    <td class="text-right">{{format_number($ledger->credit)}}</td>
                    <td class="text-right">{{format_number($balance)}}</td>
                    @if($columns['created_by'])
                    <td>{{$ledger->created_by}}</td>
                    @endif
                </tr>        
                @if($i+1==$cdata)
                    <tr>
                        <th class="bt-2 bb-1 text-right" colspan="{{$colspan}}" >Jumlah</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($debit)}}</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($credit)}}</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($balance)}}</th>
                        @if($columns['created_by'])
                        <th class="bt-2 bb-1"></th>
                        @endif
                    </tr>        
                @else
                    @if($account_id!=($ledgers[$i+1])->account_id)
                    <tr>
                        <th class="bt-2 bb-1 text-right" colspan="{{$colspan}}" >Jumlah</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($debit)}}</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($credit)}}</th>
                        <th class="bt-2 bb-1 text-right">{{format_number($balance)}}</th>
                        @if($columns['created_by'])
                        <th class="bt-2 bb-1"></th>
                        @endif
                    </tr>        
                    @php $debit=0;$credit=0;$balance=0; @endphp
                    @endif
                @endif
            @endforeach  
        </tbody>
    </table>
    