
<table>
    <tr>
        <td   class="text-center">
          <h4>{{$title}}</h4>
        </td>
      </tr>
      <tr>
        <td></td>
        <td class="text-right">
            @if(count($departments)>0)
                <b>{{__('Departemen')}}: </b>
                @foreach($departments as $i=>$d)
                {{$d->name.($i < count($departments)-1? ', ':' ')}}
                @endforeach
            @endif
        </td>
      </tr>
    </table>
    <table class="table-report">
        <thead>
            @if($compare=='budget')
            <tr>
                <th rowspan="2" ></th>
                @foreach($columns as $i =>$column)
                    <th colspan="3" class=" text-center">
                    {{$column['label']}}
                    </th>
                @endforeach
            </tr>
            <tr>
                @foreach($columns as $i =>$column)
                    <th class="text-center">
                    Anggaran
                    </th>
                    <th class="text-center">
                    Realisasi
                    </th>
                    <th class="text-center">
                    Selisih
                    </th>
                @endforeach
            </tr>
            @else
            <tr>
                <th ></th>
                @foreach($columns as $i =>$column)
                    <th class=" text-right">
                    @if($compare=='department')
                        {{$column->name}}
                    @else
                        {{$column['label']}}
                    @endif
                    </th>
                @endforeach
            </tr>
            @endif
        </thead>
        <tbody>
        @php
        $type = null;
        $group = null;
        $cdata = count($income);
        $sum_1 = array();
        $sum_2 = array();
        $sum_3 = array();
        $sum_4 = array();
        $sum_5 = array();
        $sum_6 = array();
        $sum_budget_1 = array();
        $sum_budget_2 = array();
        $sum_budget_3 = array();
        $sum_budget_4 = array();
        $sum_budget_5 = array();
        $sum_budget_6 = array();
        $gross_profit = array();
        $operation_profit = array();
        foreach($columns as $i=>$p){
            $sum_0[$i]=0;//total penjualan
            $sum_1[$i]=0;//total income
            $sum_2[$i]=0;//total cogs
            $sum_3[$i]=0;//total expense
            $sum_4[$i]=0;//total expense
            $sum_5[$i]=0;//total other income/expense
            $sum_6[$i]=0;//total tax
            $sum_budget_0[$i]=0;
            $sum_budget_1[$i]=0;
            $sum_budget_2[$i]=0;
            $sum_budget_3[$i]=0;
            $sum_budget_4[$i]=0;
            $sum_budget_5[$i]=0;
            $sum_budget_6[$i]=0;
            $gross_profit[$i]=0;
            $operation_profit[$i]=0;
        }
        $colspan = count($columns)+2;
        @endphp
            @foreach($income as $i => $dt)
                @if($type!==$dt->account_type_id)
                @php $type=$dt->account_type_id @endphp

                @endif
                @if($dt->tree_level==0 || ($subaccount>0 && $dt->tree_level<=$subaccount))
                <tr>
                    <td>
                        <span style="padding-left:{{$dt->tree_level*20}}px;">&nbsp;</span>
                        <a href="{{route('accounts.view', $dt->id)}}">
                        {{$dt->account_name}}
                        </a>
                    </td>
                    @foreach($columns as $j=> $p)
                        @php $total = 'total_'.$j;
                        $budget = 'budget_'.$j;

                            if($dt->tree_level==0){
                                if($dt->account_type_id==20){
                                    $sum_0[$j]=$dt->$total+$sum_0[$j];
                                    if($compare=='budget'){
                                        $sum_budget_0[$j]=$dt->$budget+$sum_budget_0[$j];
                                    }
                                }
                                if($dt->account_type_id==21){
                                    $sum_1[$j]=$dt->$total+$sum_1[$j];
                                    if($compare=='budget'){
                                        $sum_budget_1[$j]=$dt->$budget+$sum_budget_1[$j];
                                    }
                                }
                                if($dt->account_type_id==22){
                                    $sum_2[$j]=$dt->$total+$sum_2[$j];
                                    if($compare=='budget'){
                                        $sum_budget_2[$j]=$dt->$budget+$sum_budget_2[$j];
                                    }
                                }
                                if($dt->account_type_id==23){
                                    $sum_3[$j]=$dt->$total+$sum_3[$j];
                                    if($compare=='budget'){
                                        $sum_budget_3[$j]=$dt->$budget+$sum_budget_3[$j];
                                    }
                                }
                                if($dt->account_type_id==24){
                                    $sum_4[$j]=$dt->$total+$sum_4[$j];
                                    if($compare=='budget'){
                                        $sum_budget_4[$j]=$dt->$budget+$sum_budget_4[$j];
                                    }
                                }
                                if($dt->account_type_id==25){
                                    $sum_5[$j]=$dt->$total+$sum_5[$j];
                                    if($compare=='budget'){
                                        $sum_budget_5[$j]=$dt->$budget+$sum_budget_5[$j];
                                    }
                                }
                            }
                        @endphp
                        @if($compare=='budget')
                            <td class="text-right">
                                {{currency($dt->$budget)}}
                            </td>
                            <td class="text-right">
                                <a href="{{route('accounts.view', $dt->id)}}?ft_dtables_trans_date_start={{fdate($p['start_date'])}}&ft_dtables_trans_date_end={{fdate($p['end_date'])}}&filter=show">
                                    {{currency($dt->$total)}}
                                </a>
                            </td>
                            <td class="text-right">
                                {{currency($dt->$budget-$dt->$total)}}
                            </td>
                        @else
                        <td class="text-right">
                            <a href="{{route('accounts.view', $dt->id)}}?ft_dtables_trans_date_start={{fdate($p['start_date'])}}&ft_dtables_trans_date_end={{fdate($p['end_date'])}}&filter=show">
                                {{currency($dt->$total)}}
                            </a>
                        </td>
                        @endif
                    @endforeach
                </tr>
                @endif
                @if($i+1==$cdata && (in_array($type, [23])))
                    <tr>
                        <td  class="bt-2 bb-1">
                            @if($type==23)
                            Jumlah Biaya Produksi
                            @endif
                        </td>
                        @foreach($columns as $j=> $p)
                            <td class="bt-2 bb-1 text-right">
                                @if($type==23)
                                {{currency($sum_1[$j]+$sum_2[$j]+$sum_3[$j])}}
                                @endif
                            </td>
                            @if($compare=='budget')
                            <td class="bt-2 bb-1 text-right">
                                @if($type==23)
                                {{currency($sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j])}}
                                @endif
                            </td>
                            <td class="bt-2 bb-1 text-right">
                                @if($type==23)
                                {{currency(($sum_1[$j]+$sum_2[$j]+$sum_3[$j])-($sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]))}}
                                @endif
                            </td>
                            @endif
                        @endforeach
                    </tr>
                @elseif(in_array($type, [23]))
                    @if($type!=($income[$i+1])->account_type_id)
                    <tr>
                        <td  class="bt-2 bb-1">
                            @if($type==23)
                            Jumlah Biaya Produksi
                            @endif
                        </td>
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">
                            @if($type==23)
                            {{currency($sum_1[$j]+$sum_2[$j]+$sum_3[$j])}}
                            @endif
                        </td>
                        @if($compare=='budget')
                        <td class="bt-2 bb-1 text-right">
                            @if($type==22)
                            {{currency($sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j])}}
                            @endif
                        </td>
                        <td class="bt-2 bb-1 text-right">
                            @if($type==21)
                            {{currency(($sum_1[$j]+$sum_2[$j])-($sum_budget_1[$j]+$sum_budget_2[$j]))}}
                            @elseif($type==22)
                            {{currency(($sum_1[$j]+$sum_2[$j]+$sum_3[$j])-($sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]))}}
                            @elseif($type==23)
                            {{currency($sum_budget_4[$j])}}
                            @elseif($type==25)
                            {{currency($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]+$sum_budget_5[$j]-$sum_budget_6[$j])}}
                            @endif
                        </td>
                        @endif
                        @endforeach
                    </tr>
                    @elseif(in_array($type, [21, 22, 23, 25]))
                    <tr>
                        <td  class="bb-2 ">
                            @if($type==21)
                            Bahan Tersedia untuk Dipakai
                            @elseif($type==22)
                            Total Pemakaian Bahan Baku
                            @elseif($type==23)
                            Total Pemakaian Bahan Baku dan Biaya
                            @elseif($type==25)
                            Total Harga Pokok Produksi
                            @endif
                        </td>
                        @foreach($columns as $j=> $p)
                            <td class="bb-2  text-right">
                            @if($type==21)
                            {{currency($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j])}}
                            @elseif($type==22)
                            {{currency($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j])}}
                            @elseif($type==23)
                            {{currency($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j]+$sum_5[$j])}}
                            @elseif($type==25)
                            {{currency($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j]+$sum_5[$j]-$sum_6[$j])}}
                            @endif
                            </td>
                            @if($compare=='budget')
                            <td class="bb-2  text-right">
                            @if($type==21)
                            {{currency($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j])}}
                            @elseif($type==22)
                            {{currency($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j])}}
                            @elseif($type==23)
                            {{currency($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]+$sum_budget_5[$j])}}
                            @elseif($type==25)
                            {{currency($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]+$sum_budget_5[$j]-$sum_budget_6[$j])}}
                            @endif
                            </td>
                            <td class="bb-2  text-right">
                            @if($type==21)
                            {{currency(($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j])-($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]))}}
                            @elseif($type==22)
                            {{currency(($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j])-($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]))}}
                            @elseif($type==23)
                            {{currency(($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j]+$sum_5[$j])-($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]+$sum_budget_5[$j]))}}
                            @elseif($type==25)
                            {{currency(($sum_0[$j]+$sum_1[$j]+$sum_2[$j]+$sum_3[$j]-$sum_4[$j]+$sum_5[$j]-$sum_6[$j])-($sum_budget_0[$j]+$sum_budget_1[$j]+$sum_budget_2[$j]+$sum_budget_3[$j]-$sum_budget_4[$j]+$sum_budget_5[$j]-$sum_budget_6[$j]))}}
                            @endif
                            </td>
                            @endif
                        @endforeach
                    </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>
