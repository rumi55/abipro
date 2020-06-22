
    <table class="table">
    <tr>
        <td colspan="2"  class="text-center" >
          <h2>{{$title}}</h2>
        </td>
      </tr>
      <tr>
        <td><b>Tanggal:</b> {{fdate($end_date, 'd M Y')}}</td>
        <td class="text-right">
            @if(count($departments)>0)
                <b>Departemen: </b>
                @foreach($departments as $i=>$d)
                {{$d->name.($i < count($departments)-1? ', ':' ')}}
                @endforeach
            @endif
          </td>
      </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="2" class="border-top-double border-bottom-double"></th>
                @foreach($columns as $i =>$column)
                    <th class="border-top-double border-bottom-double text-right">
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
                <tr>
                    <td colspan="{{$colspan}}" class="font-bold">
                    {{$dt->account_type}}
                    </td>
                </tr>        
                @endif
                @if($dt->tree_level==0 || ($subaccount==true && $dt->tree_level<=$level))
                <tr>
                    <td>
                        <span style="padding-left:{{$dt->tree_level*20}}px;">&nbsp;</span>
                        {{$dt->account_no}}
                    </td>
                    <td>
                        <span style="padding-left:{{$dt->tree_level*20}}px;">&nbsp;</span>
                        {{$dt->account_name}}
                    </td>
                    @foreach($columns as $j=> $p)
                        @php $total = 'total_'.$j; @endphp 
                        @php 
                            if($dt->tree_level==0){
                                if($dt->account_type_id==12){
                                    $sum_1[$j]=$dt->$total+$sum_1[$j]; 
                                }
                                if($dt->account_type_id==14){
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
                    <td class="text-right">{{format_number($dt->$total)}}</td>
                    @endforeach
                </tr>        
                @endif
                @if($i+1==$cdata)
                    <tr>
                        <td colspan="2" class="border-top">{{$type==12?'Total Pendapatan':($type==14?'Total Harga Pokok Penjualan':($type==15?'Total Biaya':($type==9?'Total Kewajiban Lancar':($type==10?'Total Kewajiban Tidak Lancar':'Total Modal'))))}}</td>                        
                        @foreach($columns as $j=> $p)
                            <td class="border-top text-right">{{format_number($type==12?$sum_1[$j]:($type==14?$sum_2[$j]:$sum_3[$j]))}}</td>
                        @endforeach
                    </tr>        
                @else
                    @if($type!=($income[$i+1])->account_type_id)
                    <tr>
                    <td colspan="2" class="border-top">{{$type==12?'Total Pendapatan':($type==14?'Total Harga Pokok Penjualan':($type==15?'Total Biaya':($type==9?'Total Kewajiban Lancar':($type==10?'Total Kewajiban Tidak Lancar':'Total Modal'))))}}</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="border-top text-right">{{format_number($type==12?$sum_1[$j]:($type==14?$sum_2[$j]:$sum_3[$j]))}}</td>
                        @endforeach
                    </tr>        
                    @if($type==14||$type==15)
                    <tr>
                        <td colspan="2" class="border-top">{{$type==14?'Laba Kotor':'Laba Operasi'}}</td>
                        @foreach($columns as $j=> $p)
                            <td class="border-top text-right">
                            @if($type==14)
                            {{format_number($sum_1[$j]-$sum_2[$j])}}
                            </td>
                            @elseif($type==15)
                            {{format_number($sum_1[$j]-$sum_2[$j]-$sum_3[$j])}}
                            @endif
                        @endforeach
                    </tr>       
                    @endif 
                    @endif
                @endif
            @endforeach    
        </tbody>
    </table>