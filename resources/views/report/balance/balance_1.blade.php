@extends('report.layout')
@section('title', $title)
@section('content')
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small">Periode Akuntansi: {{fdate($start_period, 'd M Y').' s.d '.fdate($end_period, 'd M Y')}}</small>
    <table>
        <thead>
            <tr>
                <th class="border-top border-bottom-double">Akun</th>
                <th class="border-top border-bottom-double text-right">{{fdate($period_end, 'd M Y')}}</th>
            </tr>
        </thead>
        <tbody>
        @php 
        $type = null; 
        $group = null; 
        @endphp
            @foreach($data as $i => $dt)
            @if($group!==$dt->group)
            @php $group=$dt->group @endphp
                <tr>
                    <td colspan="2" class="font-bold">{{$dt->group=='asset'?'Aktiva':($dt->group=='liability'?'Kewajiban':'Ekuitas')}}</td>
                </tr>        
            @endif
            @if($type!==$dt->account_type)
            @php $type=$dt->account_type @endphp
                <tr>
                    <td colspan="2" class="font-bold">&nbsp;&nbsp;&nbsp;&nbsp;{{$dt->account_type}}</td>
                </tr>        
            @endif
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{$dt->account_name}}</td>
                    <td class="text-right">{{format_number($dt->total)}}</td>
                </tr>        
            @endforeach    
            <tr>
                <td colspan="6" class="border-bottom"></td>
            </tr>
        </tbody>
    </table>
@endsection