
<table>
    <tr>
        <td colspan="2"  class="text-center">
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
    @php 
        $total_1 = array();
        $total_2 = array();
        $total_3 = array();
        $total_4 = array();
        $total_5 = array();
    @endphp
    <table class="table-report">
        <thead>
            <tr>
                <th></th>
                @foreach($columns as $i =>$column)
                @php 
                    $total_1[$i] = 0;
                    $total_2[$i] = 0;
                    $total_3[$i] = 0;
                    $total_4[$i] = 0;
                    $total_5[$i] = 0;
                @endphp
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
            @foreach($mappings as $map)
            @php $mapping = $map['mapping'];$accounts = $map['data']; 
            @endphp
                @foreach($accounts as $i=> $account)
                <tr>
                    <td>
                        {{tt($account, 'account_name')}}
                    </td>
                    @foreach($columns as $j=> $p)
                        <td class="text-right">
                        @php $total = 'total_'.$j @endphp
                        {{format_number($account->$total)}}
                        </td>
                        @php 
                        if(in_array($mapping->id,[1,2])){
                            $total_1[$j]=$account->$total+$total_1[$j]; 
                        }
                        if(in_array($mapping->id,[1,2,3])){
                            $total_2[$j]=$account->$total+$total_2[$j]; 
                        }
                        if(in_array($mapping->id,[4,5])){
                            $total_3[$j]=$account->$total+$total_3[$j]; 
                        }
                        if($mapping->id<=5){
                            $total_4[$j]=$account->$total+$total_4[$j]; 
                        }
                        if($mapping->id<=7){
                            $total_5[$j]=$account->$total+$total_5[$j]; 
                        }
                        @endphp
                    @endforeach
                    @if($mapping->id==2)
                    <tr>
                        <td class="bt-2 bb-1">Bahan Tersedia Untuk Dipakai</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">{{format_number($total_1[$j])}}</td>
                        @endforeach
                    </tr> 
                    @endif
                    @if($mapping->id==3)
                    <tr>
                        <td class="bt-1 bb-1 fb">Total Pemakaian Bahan Baku</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">{{format_number($total_2[$j])}}</td>
                        @endforeach
                    </tr> 
                    @endif
                    @if($mapping->id==5)
                    <tr>
                        <td class="bt-2 bb-1">Jumlah Biaya Produksi</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">{{format_number($total_3[$j])}}</td>
                        @endforeach
                    </tr> 
                    <tr>
                        <td class="bt-2 bb-1">Total Pemakaian Bahan Baku</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">{{format_number($total_4[$j])}}</td>
                        @endforeach
                    </tr> 
                    @endif
                    @if($mapping->id==7)
                    <tr>
                        <td class="bt-2 bb-1">Total Harga Pokok Produksi</td>                        
                        @foreach($columns as $j=> $p)
                        <td class="bt-2 bb-1 text-right">{{format_number($total_4[$j])}}</td>
                        @endforeach
                    </tr> 
                    @endif
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>