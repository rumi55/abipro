<div  class="text-center" style="margin-bottom: 10px;" >
    <h3  style="margin-bottom:0" >{{$title}}</h3>
    {{__('Report Date')}}: {{date('d-m-Y H:i')}}
</div>

<span><b>{{__('Date')}}:</b> {{$period}}</span>
<table class="table-report table-report-noborder">
    <thead>
        <tr>
            @if($columns['department'])
            <th class="text-left">{{__('Dept.')}}</th>
            @endif
            <th class="text-left">{{__('Account')}}</th>
            <th class="text-left">{{__('Account Name')}}</th>
            @if($columns['description'])
            <th class="text-left">{{__('Description')}}</th>
            @endif
            <th class="text-right">{{__('Debit')}}</th>
            <th class="text-right">{{__('Credit')}}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $journal_id=0;
            $cdata = count($journals);
            $colspan = 2;
            if($columns['department']){
                $colspan+=1;
            }
            if($columns['description']){
                $colspan+=1;
            }
        @endphp
        @if($cdata==0)
        <tr><td class="border-bottom text-center" colspan="6">{{__('No data')}}</td></tr>
        @endif
        @foreach($journals as $i=> $dt)
        @if($journal_id!==$dt->journal_id)
            <tr class="first">
                <td class="nob" colspan="{{$colspan+2}}">
                <b>{{__('Date')}}:</b> {{fdate($dt->trans_date)}}&nbsp;&nbsp;&nbsp;&nbsp;
                <b>{{__('Transaction No.')}}:</b> {{$dt->trans_no}}&nbsp;&nbsp;&nbsp;&nbsp;
                <b>{{__('Description')}}:</b> {{$dt->description}}&nbsp;&nbsp;&nbsp;&nbsp;
                @if($columns['created_by'])
                <b>{{__('Created by')}}:</b> {{$dt->created_by}}
                @endif
                </td>
            </tr>
        @php $journal_id=$dt->journal_id; @endphp
        @endif
            <tr>
                @if($columns['department'])
                <td class="nob">{{$dt->department_custom_id}}</td>
                @endif
                <td class="nob">{{$dt->account_no}}</td>
                <td class="nob">{{$dt->account_name}}</td>
                @if($columns['description'])
                <td class="nob">{{$dt->description}}</td>
                @endif
                <td class="text-right nob">{{format_number($dt->debit)}}</td>
                <td class="text-right nob">{{format_number($dt->credit)}}</td>
            </tr>
        @if($i+1==$cdata)
            <tr>
                    <th class="bt-2 bb-1" colspan="{{$colspan}}">{{__('Total')}}</th>
                    <th class="bt-2 bb-1 text-right">{{format_number(abs($dt->total))}}</th>
                    <th class="bt-2 bb-1 text-right">{{format_number(abs($dt->total))}}</th>
            </tr>
        @else
            @if($journal_id!=($journals[$i+1])->journal_id)
                <tr>
                    <th class="bt-2 bb-1" colspan="{{$colspan}}" class="">{{__('Total')}} {{$dt->trans_no}}</th>
                    <th class="bt-2 bb-1 text-right">{{format_number(abs($dt->total))}}</th>
                    <th class="bt-2 bb-1 font-bold text-right">{{format_number(abs($dt->total))}}</th>
                </tr>
            @endif
        @endif
        @endforeach
    </tbody>
</table>
