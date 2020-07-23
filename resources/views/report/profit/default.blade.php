
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
        $gross_profit = array();
        $operation_profit = array();
        foreach($columns as $i=>$p){
            $sum_0[$i]=0;//total penjualan
            $sum_1[$i]=0;//total income
            $sum_2[$i]=0;//total cogs
            $sum_3[$i]=0;//total expense
            $sum_4[$i]=0;//total other income
            $sum_5[$i]=0;//total other expense
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
                        @php $total = 'total_'.$j; @endphp
                        @php
                            if($dt->tree_level==0){
                                if($dt->account_type_id==10){
                                    $sum_0[$j]=$dt->$total+$sum_0[$j];
                                }
                                if($dt->account_type_id==12){
                                    $sum_1[$j]=$dt->$total+$sum_1[$j];
                                }
                                if($dt->account_type_id==13){
                                    $sum_2[$j]=$dt->$total+$sum_2[$j];
                                }
                                if($dt->account_type_id==15){
                                    $sum_3[$j]=$dt->$total+$sum_3[$j];
                                }
                                if($dt->account_type_id==13){
                                    $sum_4[$j]=$dt->$total+$sum_4[$j];
                                }
                                if($dt->account_type_id==16){
                                    $sum_5[$j]=$dt->$total+$sum_5[$j];
                                }
                            }
                        @endphp
                    <td class="text-right">
                        <a href="{{route('accounts.view', $dt->id)}}?ft_dtables_trans_date_start={{fdate($p['start_date'])}}&ft_dtables_trans_date_end={{fdate($p['end_date'])}}&filter=show">
                            {{currency($dt->$total)}}
                        </a>
                    </td>
                    @endforeach
                </tr>
                @endif
                @if($i+1==$cdata && ($type==13 || $type==15))
                    <tr>
                        <td  class="bt-2 bb-1">
                            @if($type==13)
                            Barang Tersedia untuk Dijual
                            @elseif($type==15)
                            Jumlah Harga Pokok
                            @endif
                        </td>
                        @foreach($columns as $j=> $p)
                            <td class="bt-2 bb-1 text-right">
                                @if($type==13)
                                {{currency($sum_1[$j]+$sum_2[$j])}}
                                @elseif($type==15)
                                {{currency($sum_1[$j]+$sum_2[$j]+$sum_3[$j])}}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @elseif($type==13 || $type==15)
                    @if($type!=($income[$i+1])->account_type_id)
                    <tr>
                        <td  class="bt-2 bb-1">
                            @if($type==13)
                            Barang Tersedia untuk Dijual
                            @elseif($type==15)
                            Jumlah Harga Pokok
                            @endif
                        </td>
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">
                            @if($type==13)
                            {{currency($sum_1[$j]+$sum_2[$j])}}
                            @elseif($type==15)
                            {{currency($sum_1[$j]+$sum_2[$j]+$sum_3[$j])}}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @if($type==15||$type==16)
                    <tr>
                        <td  class="bb-2 ">{{$type==15?'Laba Kotor':'Laba Operasi'}}</td>
                        @foreach($columns as $j=> $p)
                            <td class="bb-2  text-right">
                            @if($type==15)
                            {{currency($sum_1[$j]-$sum_2[$j])}}
                            </td>
                            @elseif($type==16)
                            {{currency($sum_1[$j]-$sum_2[$j]-$sum_3[$j])}}
                            @endif
                        @endforeach
                    </tr>
                    @endif
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>
