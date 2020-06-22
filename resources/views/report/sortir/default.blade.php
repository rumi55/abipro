

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
            @if($columns['department'])
            <th>Dept.</th>                
            @endif
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Keterangan</th>
            <th class="text-right">Debet</th>
            <th class="text-right">Kredit</th>
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
        $tag_item = null;
        $tag_group = null;
        $item_id = null;
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
                @endphp
                <tr>
                    <td colspan="{{$colspan+4}}" class="font-bold">{{$ledger->item_name}}</td>
                </tr>        
                <tr>
                    <td colspan="{{$colspan+4}}" class="">{{$ledger->account_no}}&nbsp;&nbsp;&nbsp;{{$ledger->account_name}}</td>
                </tr>        
            @endif
            
            @php 
            $debit+=$ledger->debit;
            $credit+=$ledger->credit; 
            @endphp
            <tr>
                @if($columns['department'])
                <td>{{$ledger->department_name}}</td>
                @endif
                <td>{{fdate($ledger->trans_date)}}</td>
                <td>{{$ledger->trans_no}}</td>
                <td class="text-wrap">
                {{$ledger->description}}
                </td>
                <td class="text-right">{{format_number($ledger->debit)}}</td>
                <td class="text-right">{{format_number($ledger->credit)}}</td>
                @if($columns['created_by'])
                <td>{{$ledger->created_by}}</td>
                @endif
            </tr>        
            @if($i+1==$cdata)
                <tr>
                    <th class="bt-2 bb-1 " colspan="{{$colspan}}" >Jumlah</th>
                    <th class="bt-2 bb-1 text-right">{{format_number($debit)}}</th>
                    <th class="bt-2 bb-1 text-right">{{format_number($credit)}}</th>
                    @if($columns['created_by'])
                    <th class="bt-2 bb-1"></th>
                    @endif
                </tr>        
            @else
                @if($account_id!=($ledgers[$i+1])->account_id)
                <tr>
                    <td class="bt-2 bb-1 font-bold" colspan="{{$colspan}}" >Jumlah {{$ledger->account_no.' '.$ledger->account_name}} </td>
                    <td class="bt-2 bb-1 font-bold text-right">{{format_number($debit)}}</td>
                    <td class="bt-2 bb-1 font-bold text-right">{{format_number($credit)}}</td>
                    @if($columns['created_by'])
                    <td class="bt-2 bb-1"></td>
                    @endif
                </tr>        
                @php $debit=0;$credit=0;$balance=0; @endphp
                @endif
            @endif
        @endforeach  
    </tbody>
</table>