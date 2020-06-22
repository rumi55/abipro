@extends('report.layout')
@section('title', $title)
@section('content')
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small">Tanggal: {{$period}}</small>
    <table>
        <thead>
            <tr>
                <th class="border-top-double border-bottom-double">Tanggal</th>
                <th class="border-top-double border-bottom-double">No. Bukti</th>
                @if($columns['department'])
                <th class="border-top-double border-bottom-double">Dept.</th>
                @endif
                <th class="border-top-double border-bottom-double">Kode Akun</th>
                <th class="border-top-double border-bottom-double">Nama Akun</th>
                @if($columns['description'])
                <th class="border-top-double border-bottom-double">Keterangan</th>
                @endif
                <th class="border-top-double border-bottom-double text-right">Debet</th>
                <th class="border-top-double border-bottom-double text-right">Kredit</th>
                @if($columns['created_by'])
                    <th class="border-top-double border-bottom-double text-right">Dibuat oleh</th>
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
                    
                    @if($journal_id!==$journal->journal_id)
                        @php $journal_id=$journal->journal_id; @endphp
                        <td>{{fdate($journal->trans_date)}}</td>
                        <td>{{$journal->trans_no}}</td>
                    @else
                        <td>{{fdate($journal->trans_date)}}</td>
                        <td>{{$journal->trans_no}}</td>
                    @endif
                    @if($columns['department'])
                    <td>{{$journal->department_name}}</td>
                    @endif
                    <td>{{$journal->account_no}}</td>
                    <td>{{$journal->account_name}}</td>
                    @if($columns['description'])
                    <td>{{$journal->description}}
                    @if($columns['tags'])
                    <br/><b>Tags:</b> {{$journal->tags}}
                    @endif
                    </td>
                    @endif
                    <td class="text-right">{{format_number($journal->debit)}}</td>
                    <td class="text-right">{{format_number($journal->credit)}}</td>
                    @if($columns['created_by'])
                    <td>{{$journal->created_by}}</td>
                    @endif
                </tr>      
                @if($i+1==$cdata)
                <tr>
                        <td  colspan="{{$colspan}}" class="border-top border-bottom font-bold">Jumlah</td>
                        <td class="border-top border-bottom font-bold text-right">{{format_number(abs($journal->total))}}</td>
                        @if($columns['created_by'])
                        <td class="border-top border-bottom font-bold text-right"></td>
                        @endif
                </tr>        
            @else
                @if($journal_id!=($journals[$i+1])->journal_id)
                    <tr>
                        <td  colspan="{{$colspan}}" class="border-top border-bottom font-bold">Jumlah</td>
                        <td class="border-top border-bottom font-bold text-right">{{format_number(abs($journal->total))}}</td>
                        <td class="border-top border-bottom font-bold text-right">{{format_number(abs($journal->total))}}</td>
                        @if($columns['created_by'])
                        <td class="border-top border-bottom font-bold text-right"></td>
                        @endif
                    </tr>        
                @endif
            @endif
            @endforeach    
        </tbody>
    </table>
@endsection