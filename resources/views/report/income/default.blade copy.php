@extends('report.layout')
@section('title', $title)
@section('content')
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small"></small>
    <table>
        <tr>
            <td style="width:60px"></td>
            <td style="width:40px" class="text-right">{{fdate($period_end, 'd M Y')}}</td>
        </tr>
        @php $total_0 = 0; @endphp
        @foreach($income as $in)
        <tr>
            <td style="width:60px">{{$in->account_parent_name}}</td>
            <td style="width:40px" class="text-right">{{format_number($in->total)}}</td>
        </tr>
        @php $total_0 += $in->total; @endphp
        @endforeach
        <tr><td colspan="2">&nbsp;</td></tr>
        @php $total_1=0; @endphp
        @foreach($iinventory as $in)
        <tr>
            <td style="width:60px">{{$in->account_parent_name}}</td>
            <td style="width:40px" class="text-right">{{format_number($in->total)}}</td>
        </tr>
        @php $total_1+=$in->total; @endphp
        @endforeach
        @foreach($purchase as $in)
        <tr>
            <td style="width:60px">{{$in->account_parent_name}}</td>
            <td style="width:40px" class="text-right">{{format_number($in->total)}}</td>
        </tr>
        @php $total_1+=$in->total; @endphp
        @endforeach
        <tr><td colspan="2" class="border-bottom"></td></tr>
        <tr>
            <td style="width:60px">Barang Tersedia untuk Dijual</td>
            <td style="width:40px" class="text-right">{{format_number($total_1)}}</td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        @php $total_2=0; @endphp
        @foreach($finventory as $in)
        <tr>
            <td style="width:60px">{{$in->account_parent_name}}</td>
            <td style="width:40px" class="text-right">{{format_number($in->total)}}</td>
        </tr>
        @php $total_2+=$in->total; @endphp
        @endforeach
        <tr><td colspan="2" class="border-bottom"></td></tr>
        <tr>
            <td style="width:60px">Harga Pokok Penjualan</td>
            <td style="width:40px" class="text-right">{{format_number($total_1+$total_2)}}</td>
        </tr>
        @php $revenue=$total_0-($total_1+$total_2); @endphp
        <tr>
            <td style="width:60px" class="font-bold">Laba Kotor</td>
            <td style="width:40px" class="font-bold text-right">{{format_number($revenue)}}</td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        @php $total_3=0; @endphp
        @foreach($expense as $in)
        <tr>
            <td style="width:60px">{{$in->account_parent_name}}</td>
            <td style="width:40px" class="text-right">{{format_number($in->total)}}</td>
        </tr>
        @php $total_3+=$in->total; @endphp
        @endforeach
        <tr><td colspan="2" class="border-bottom"></td></tr>
        <tr>
            <td style="width:60px">Jumlah Biaya</td>
            <td style="width:40px" class="text-right">{{format_number($total_3)}}</td>
        </tr>
        
        @php $net_revenue=$revenue-$total_3; @endphp    
        <tr>
            <td style="width:60px" class="font-bold">Laba Operasi</td>
            <td style="width:40px" class="font-bold text-right">{{format_number($net_revenue)}}</td>
        </tr>

        <tr><td colspan="2">&nbsp;</td></tr>

        <tr>
            <td style="width:60px">Pendapatan Diluar Usaha</td>
            <td style="width:40px" class="text-right">{{format_number($other_income->income)}}</td>
        </tr>
        <tr>
            <td style="width:60px">Biaya Diluar Usaha</td>
            <td style="width:40px" class="text-right">{{format_number($other_income->expense)}}</td>
        </tr>
        <tr><td colspan="2" class="border-bottom"></td></tr>
        @php $oincome = $other_income->income-$other_income->expense; @endphp    
        <tr>
            <td style="width:60px">Jumlah Biaya/Pendapatan di Luar Usaha</td>
            <td style="width:40px" class="text-right">{{format_number($oincome)}}</td>
        </tr>
        <tr>
            <td style="width:60px" class="font-bold">Laba Sebelum Pajak</td>
            <td style="width:40px" class="font-bold text-right">{{format_number($net_revenue+$oincome)}}</td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        @php $tx = $tax!=null?$tax->total:0; @endphp
        <tr>
            <td style="width:60px">Pajak Pendapatan</td>
            <td style="width:40px" class="text-right">{{format_number($tx)}}</td>
        </tr>
        <tr><td colspan="2" class="border-bottom"></td></tr>
        <tr>
            <td style="width:60px" class="font-bold">Laba Setelah Pajak Pajak</td>
            <td style="width:40px" class="font-bold text-right">{{format_number($net_revenue+$oincome-$tx)}}</td>
        </tr>
    </table>
@endsection