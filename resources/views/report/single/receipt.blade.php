@php

$details = '
<table class="table-report">
    <thead>
    <tr>
        <th class="text-left" style="width:30px">No.</th>
        <th class="text-left">Uraian</th>
        <th class="text-right">Jumlah</th>
    </tr>
    </thead>
    <tbody>';
    $no=1;$total=0;
    if($type=='transaction'){
        foreach($data->details as $detail){
            $details.='<tr>
                        <td>'.$no++.'</td>
                        <td>'.$detail->description.'</td>
                        <td class="text-right">'.format_number($detail->amount).'</td>
                    </tr>';
        }
    }else{
        foreach($data->details as $detail){
            if($detail->credit>0 && $detail->debit==0){
                $details.='<tr>
                    <td>'.$no++.'</td>
                    <td>'.$detail->description.'</td>
                    <td class="text-right">'.format_number($detail->credit).'</td>
                </tr>';
            }
        }
    }
    $details.='</tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>Jumlah</th>
                <th class="text-right">'.format_number($type=='voucher'?$data->total:$data->amount).'</th>
            </tr>
        </tfoot>
    </table>';

$template = report_template('receipt');
$variable = [
        '{header}'=> report_template('header'),
        '{trans_date}'=> fdate($data->trans_date),
        '{trans_no}'=> $data->trans_no,
        '{payer}'=> $data->contact->name,
        '{description}'=> $data->description,
        '{detail}'=> $details,
        '{total}'=>format_number($type=='voucher'?$data->total:$data->amount),
        '{total_inword}'=>ucwords(trim(inword($type=='voucher'?$data->total:$data->amount))),
    ];
    foreach ($variable as $key =>$value) {
        $template = str_replace($key, $value, $template);
    }
@endphp
@empty($template)
<h2 class="text-center">Kuitansi</h2>
<table>
    <tr>
        <td style="width: 150px">Tanggal</td>
        <td style="width: 20px">:</td>
        <td>{{fdate($data->trans_date)}}</td>
    </tr>
    <tr>
        <td>Nomor</td>
        <td>:</td>
        <td>{{$data->trans_no}}</td>
    </tr>
    <tr>
        <td>Dibayarkan kepada</td>
        <td>:</td>
        <td>{{$data->contact->name}}</td>
    </tr>
</table>
<table class="table-report">
    <thead>
    <tr>
        <th class="text-left">No.</th>
        <th class="text-left">Uraian</th>
        <th class="text-right">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    @php $no=1;$total=0; @endphp
    @if($type=='transaction')
    @foreach($data->details as $detail)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->amount)}}</td>
            </tr>
    @endforeach
    @else
    @foreach($data->details as $detail)
        @if($detail->credit>0 && $detail->debit==0)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$detail->description}}</td>
                <td class="text-right">{{format_number($detail->credit)}}</td>
            </tr>
        @endif
    @endforeach
    @endif
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>Jumlah</th>
            <th class="text-right">{{format_number($data->total)}}</th>
        </tr>
        <tr>
            <td colspan="3">Terbilang: <b>{{ucwords(trim(inword($data->total)))}} Rupiah</b></td>
        </tr>
    </tfoot>
</table>
@else
{!! $template !!}
@endempty
