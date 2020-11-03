@php

$details = '
<table class="table-report">
    <thead>
        <tr>
            <th colspan="2">Akun</th>
            <th>Keterangan</th>
            <th class="text-right">Debet</th>
            <th class="text-right">Kredit</th>
        </tr>
    </thead>
    <tbody>';
    $no=1;$total=0;
    foreach($data->details as $detail){
            $details.='<tr>
            <td>'.$detail->account->account_no.'</td>
            <td>'.$detail->account->account_name.'</td>
            <td>'.$detail->description.'</td>
            <td class="text-right">'.format_number($detail->debit) .'</td>
            <td class="text-right">'.format_number($detail->credit).'</td>
        </tr>';
    }
    $details.='</tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-left">Total</th>
            <th class="text-right">'.format_number($data->total).'</th>
            <th class="text-right">'.format_number($data->total).'</th>
        </tr>
    </tfoot>
    </table>';

$template = report_template('voucher');
$variable = [
        '{header}'=> report_template('header'),
        '{trans_date}'=> fdate($data->trans_date),
        '{trans_no}'=> $data->trans_no,
        '{payer}'=> $data->contact->name,
        '{description}'=> $data->description,
        '{detail}'=> $details,
        '{approved_by}'=> $data->approvedBy!=null?$data->approvedBy->name:'',
        '{checked_by}'=> $data->approvedBy!=null?$data->approvedBy->name:'',
        '{total}'=>format_number($data->total),
        '{total_inword}'=>ucwords(trim(inword($data->total))),
    ];
    foreach ($variable as $key =>$value) {
        $template = str_replace($key, $value, $template);
    }
@endphp
@empty($template)
<h3 class="text-center">Voucher</h3>
<table>
    <tr>
        <td style="width:150px">Tanggal</td>
        <td style="width:10px">:</td>
        <td>{{ fdate($data->trans_date) }}</td>
    </tr>
    <tr>
        <td>No. Bukti</td>
        <td>:</td>
        <td>{{ $data->trans_no }}</td>
    </tr>
    <tr>
        <td>Keterangan</td>
        <td>:</td>
        <td>{{ $data->description }}</td>
    </tr>
    <tr>
        <td>Terbilang</td>
        <td>:</td>
        <td>{{ucwords(trim(inword($data->total)))}} Rupiah</td>
    </tr>
</table>
<br>
<table class="table-report">
    <thead>
        <tr>
            <th colspan="2">Akun</th>
            <th>Keterangan</th>
            <th class="text-right">Debet</th>
            <th class="text-right">Kredit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->details as $detail)
            <tr>
                <td>{{ $detail->account->account_no }}</td>
                <td>{{ $detail->account->account_name }}</td>
                <td>{{ $detail->description }}</td>
                <td class="text-right">{{ format_number($detail->debit) }}</td>
                <td class="text-right">{{ format_number($detail->credit) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-left">Total</th>
            <th class="text-right">{{ format_number($data->total) }}</th>
            <th class="text-right">{{ format_number($data->total) }}</th>
        </tr>
    </tfoot>
</table>
<br>
<table class="table-report table-report-bordered">
    <tr>
        <td class="bb-1 br-1">Dibuat oleh:</td>
        <td class="bb-1 br-1">Diperiksa oleh:</td>
        <td class="bb-1 br-1">Disetujui oleh:</td>
        <td class="bb-1 br-1">Diterima oleh:</td>
    </tr>
    <tr>
        <td class="br-1" style="height:80px"></td>
        <td class="br-1"></td>
        <td class="br-1"></td>
        <td class="br-1"></td>
    </tr>
    <tr>
    <td class="br-1">{{$data->createdBy->name}}</td>
        <td class="br-1">{{$data->approvedBy->name}}</td>
        <td class="br-1">{{$data->approvedBy->name}}</td>
        <td class="br-1">{{$data->contact->name}}</td>
    </tr>
</table>
@else
{!! $template !!}
@endempty
