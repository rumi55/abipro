<h4 class="text-center">{{$title}}</h4>
    @php 
        $tot_income = array();
        $gross_profit = array();
        $net_profit = array();
        $ops_profit = array();
        foreach($period as $i=>$p){
            $gross_profit[$i]=0;
            $net_profit[$i]=0;
            $ops_profit[$i]=0;
        }
        $cperiod=count($period);
        $colspan = $cperiod+1;

        @endphp
    <table>
        <thead>
            <tr>
                <th class="border-top border-bottom-double">Uraian</th>
                @foreach($period as $p)
                <th class="border-top border-bottom-double text-right">{{fdate($p, 'd M Y')}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold" colspan="{{$colspan}}">Pedapatan dari Penjualan</td>
            </tr>
            @foreach($income as $i => $dt)
                @php 
                $td='';
                $isnull=false; 
                @endphp
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $isnull = $dt->$total!=0 || $isnull;
                        $td .= '<td class="text-right">'.($dt->$total<0?'(':'').format_number(abs($dt->$total)).($dt->$total<0?')':'').'</td>';
                    @endphp 
                @endforeach
                @if($isnull)
                <tr>
                    <td>{{$dt->account_no.' '.$dt->account_name}}</td>
                    {!! $td !!}
                </tr>
                @endif
            @endforeach
            <tr>
                <td class="border-top">Total Pendapatan dari Penjualan</td>
                @foreach($period as $j=> $p)
                    @php $total = 'total_'.$j; @endphp 
                    <td class="border-top text-right">{{format_number($total_income->$total)}}</td>
                @endforeach
            </tr>
            <tr>
                <td class="font-bold"  colspan="{{$colspan}}">Harga Pokok Penjualan</td>
            </tr>
            @foreach($cogs as $i => $dt)
            @php 
                $td='';
                $isnull=false; 
                @endphp
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $isnull = $dt->$total!=0 || $isnull;
                        $td .= '<td class="text-right">'.($dt->$total<0?'(':'').format_number(abs($dt->$total)).($dt->$total<0?')':'').'</td>';
                    @endphp 
                @endforeach
                @if($isnull)
                <tr>
                    <td>{{$dt->account_no.' '.$dt->account_name}}</td>
                    {!! $td !!}
                </tr>
                @endif
            @endforeach
            <tr>
                <td class="border-top">Total Harga Pokok Penjualan</td>
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $gross_profit[$j]+=$total_income->$total-$total_cogs->$total;
                    @endphp 
                    <td class="border-top text-right">{{format_number($total_cogs->$total)}}</td>
                @endforeach
            </tr>
            <tr>
                <td class="border-top font-bold">Laba Kotor</td>
                @foreach($period as $j=> $p)
                    <td class="border-top text-right">{{($gross_profit[$j]<0?'(':'').format_number(abs($gross_profit[$j])).($gross_profit[$j]<0?')':'')}}</td>                
                @endforeach
            </tr>
            <tr>
                <td colspan="{{$cperiod+2}}"></td>
            </tr>
            <tr>
                <td class="font-bold"  colspan="{{$colspan}}">Biaya Operasional</td>
            </tr>
            @foreach($expense as $i => $dt)
            @php 
                $td='';
                $isnull=false; 
                @endphp
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $isnull = $dt->$total!=0 || $isnull;
                        $td .= '<td class="text-right">'.($dt->$total<0?'(':'').format_number(abs($dt->$total)).($dt->$total<0?')':'').'</td>';
                    @endphp 
                @endforeach
                @if($isnull)
                <tr>
                    <td>{{$dt->account_no.' '.$dt->account_name}}</td>
                    {!! $td !!}
                </tr>
                @endif
            @endforeach
            <tr>
                <td class="border-top">Total Biaya Operasional</td>
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $ops_profit[$j]+=$gross_profit[$j]-$total_expense->$total;
                    @endphp 
                    <td class="border-top text-right">{{format_number($total_expense->$total)}}</td>
                @endforeach
            </tr>
            <tr>
                <td class="border-top font-bold">Laba Operasi</td>
                @foreach($period as $j=> $p)
                <td class="border-top text-right">{{($ops_profit[$j]<0?'(':'').format_number(abs($ops_profit[$j])).($ops_profit[$j]<0?')':'')}}</td>                
                @endforeach
            </tr>
            <tr>
                <td colspan="{{$cperiod+2}}"></td>
            </tr>
            <tr>
                <td class="font-bold"  colspan="{{$colspan}}">Pendapatan Lainnya</td>
            </tr>
            @foreach($other_income as $i => $dt)
                @php 
                $td='';
                $isnull=false; 
                @endphp
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $isnull = $dt->$total!=0 || $isnull;
                        $td .= '<td class="text-right">'.($dt->$total<0?'(':'').format_number(abs($dt->$total)).($dt->$total<0?')':'').'</td>';
                    @endphp 
                @endforeach
                @if($isnull)
                <tr>
                    <td>{{$dt->account_no.' '.$dt->account_name}}</td>
                    {!! $td !!}
                </tr>
                @endif
            @endforeach
            <tr>
                <td class="border-top">Total Pendapatan Lainnya</td>
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $net_profit[$j]+=$ops_profit[$j]+$total_other_income->$total;
                    @endphp 
                    <td class="border-top text-right">{{format_number($total_other_income->$total)}}</td>
                @endforeach
            </tr>
            <tr>
                <td class="font-bold"  colspan="{{$colspan}}">Biaya Lainnya</td>
            </tr>
            @foreach($other_expense as $i => $dt)
            @php 
                $td='';
                $isnull=false; 
                @endphp
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $isnull = $dt->$total!=0 || $isnull;
                        $td .= '<td class="text-right">'.($dt->$total<0?'(':'').format_number(abs($dt->$total)).($dt->$total<0?')':'').'</td>';
                    @endphp 
                @endforeach
                @if($isnull)
                <tr>
                    <td>{{$dt->account_no.' '.$dt->account_name}}</td>
                    {!! $td !!}
                </tr>
                @endif
            @endforeach
            <tr>
                <td class="border-top">Total Biaya Lainnya</td>
                @foreach($period as $j=> $p)
                    @php 
                        $total = 'total_'.$j; 
                        $net_profit[$j]+=$net_profit[$j]-$total_other_expense->$total;
                    @endphp 
                <td class="border-top text-right">{{format_number($total_other_expense->$total)}}</td>
                @endforeach
            </tr>
            <tr>
                <td class="border-top font-bold">Laba Bersih</td>
                @foreach($period as $j=> $p)
                <td class="border-top text-right">{{($net_profit[$j]<0?'(':'').format_number(abs($net_profit[$j])).($net_profit[$j]<0?')':'')}}</td>
                @endforeach
            </tr>
        </tbody>
    </table>