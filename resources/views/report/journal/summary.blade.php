<div  class="text-center" style="margin-bottom: 10px;" >
    <h3  style="margin-bottom:0" >{{$title}}</h3>
    {{__('Report Date')}}: {{date('d-m-Y H:i')}}
</div>

<span><b>{{__('Date')}}:</b> {{$period}}</span>
<table class="table-report table-report-noborder">
    <thead>
        <tr>
            <th class="text-left">{{__('Transaction No.')}}</th>
            <th class="text-left">{{__('Date')}}</th>
            @if($columns['department'])
            <th class="text-left">Dept.</th>
            @endif
            <th class="text-left">{{__('Account')}}</th>
            <th class="text-left">{{__('Account Name')}}</th>
            @if($columns['description'])
            <th class="text-left">{{__('Description')}}</th>
            @endif
            <th class=" text-right">{{__('Debit')}}</th>
            <th class=" text-right">{{__('credit')}}</th>
            @if($columns['created_by'])
                <th class=" text-right">{{__('Created by')}}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $journal_id=0;
            $cdata = count($journals);
            $colspan = 4;
            if($columns['department']){
                $colspan+=1;
            }
            if($columns['description']){
                $colspan+=1;
            }
        @endphp
        @foreach($journals as $i=> $journal)
            <tr>
                @if($journal_id!=$journal->journal_id)
                    @php $journal_id=$journal->journal_id; @endphp
                    <td>{{$journal->trans_no}}</td>
                    <td>{{fdate($journal->trans_date)}}</td>
                @else
                    <td></td>
                    <td>{{fdate($journal->trans_date)}}</td>
                @endif
                @if($columns['department'])
                <td>{{$journal->department_name}}</td>
                @endif
                <td>{{$journal->account_no}}</td>
                <td>{{$journal->account_name}}</td>
                @if($columns['description'])
                <td>{{$journal->description}}
                @if($columns['tags'])
                <br/><b>{{__('Tags')}}:</b> {{$journal->tags}}
                @endif
                </td>
                @endif
                <td class="text-right">{{format_number($journal->debit)}}</td>
                <td class="text-right">{{format_number($journal->credit)}}</td>
                @if($columns['created_by'])
                <td>{{$journal->created_by}}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
