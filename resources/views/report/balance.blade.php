
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
                <th class="border-top-double border-bottom-double">Kode Akun</th>
                <th class="border-top-double border-bottom-double">Nama Akun</th>
                @foreach($columns as $i =>$p)
                    <th class="border-top-double border-bottom-double text-center">
                    @if($compare=='department')
                    {{$p->name}}                    
                    @else
                    {{$p['label']}}
                    @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @php 
        $type = null; 
        $group = null; 
        $cdata = count($accounts);
        $sum_1 = array();
        $sum_2 = array();
        foreach($columns as $i=>$p){
            $sum_1[$i]=0;
            $sum_2[$i]=0;
        }
        $colspan = count($columns)+2;
        @endphp
            @foreach($accounts as $i => $dt)
            @if($group!==$dt->account_group && $dt->account_group!='equity')
                @php $group=$dt->account_group @endphp
                <tr>
                    <td colspan="{{$colspan}}" class="font-bold">{{$dt->account_group=='asset'?'Aset':'Kewajiban & Ekuitas'}}</td>
                </tr>        
            @endif
                @if($type!==$dt->account_type_id)
                @php $type=$dt->account_type_id @endphp
                <tr>
                    <td colspan="{{$colspan}}" class="font-bold">
                    <span style="padding-left:10px;">&nbsp;</span>
                    {{$dt->account_type}}
                    </td>
                </tr>        
                @endif
                @if($dt->tree_level==0 || ($subaccount==true && $dt->tree_level<=$level))
                <tr>
                    <td>
                        <span style="padding-left:{{$dt->tree_level*10+20}}px;">&nbsp;</span>
                        {{$dt->account_no}}
                    </td>
                    <td>
                        <span style="padding-left:{{$dt->tree_level*10+20}}px;">&nbsp;</span>
                        {{$dt->account_name}}
                    </td>
                    @foreach($columns as $j=> $p)
                        @php $total = 'total_'.$j; @endphp 
                        @php 
                            if($dt->tree_level==0){
                                $sum_1[$j]=$dt->$total+$sum_1[$j]; 
                                $sum_2[$j]=$dt->$total+$sum_2[$j]; 
                            }
                        @endphp
                    <td class="text-right">{{format_number($dt->$total)}}</td>
                    @endforeach
                </tr>        
                @endif
                @if($i+1==$cdata)
                    <tr>
                        <td colspan="2" class="border-top">{{$type==4?'Total Aset Lancar':($type==6?'Total Aset Tetap':($type==7?'Total Aset Lainnya':($type==9?'Total Kewajiban Lancar':($type==10?'Total Kewajiban Tidak Lancar':'Total Modal'))))}}</td>                        
                        @foreach($columns as $j=> $p)
                            <td class="border-top text-right">{{format_number($sum_1[$j])}}</td>
                            @php $sum_1[$j]=0 @endphp
                        @endforeach
                    </tr>        
                @else
                    @if($type!=($accounts[$i+1])->account_type_id && ($type==4 || $type==6 || $type==7 || $type==9 || $type==10 || $type==11))
                    <tr>
                        <td colspan="2" class="border-top">{{$type==4?'Total Aset Lancar':($type==6?'Total Aset Tetap':($type==7?'Total Aset Lainnya':($type==9?'Total Kewajiban Lancar':($type==10?'Total Kewajiban Tidak Lancar':'Total Modal'))))}}</td>
                        @foreach($columns as $j=> $p)
                            <td class="border-top text-right">{{format_number($sum_1[$j])}}</td>
                            @php $sum_1[$j]=0 @endphp
                        @endforeach
                    </tr>        
                    @if($type==7||$type==10)
                    <tr>
                        <td colspan="2" class="border-top">{{$type==7?'Total Aset':'Total Kewajiban'}}</td>
                        @foreach($columns as $j=> $p)
                            <td class="border-top text-right">{{format_number($sum_2[$j])}}</td>
                            @php $sum_2[$j]=0 @endphp
                        @endforeach
                    </tr>       
                    @endif 
                    @endif
                @endif
            @endforeach    
        </tbody>
    </table>