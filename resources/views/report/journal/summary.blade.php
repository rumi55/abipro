<h4  class="text-center" >{{$title}}<br/><span style="font-size:0.7em">Tanggal Laporan: {{date('d-m-Y H:i:s')}}</span></h4>
    <span><b>Tanggal:</b> {{$period}}</span>
    <table class="table-report table-report-noborder">
        <thead>
            <tr>
                <th class="text-left">No. Bukti</th>
                <th class="text-left">Tanggal</th>
                @if($columns['department'])
                <th class="text-left">Dept.</th>
                @endif
                <th class="text-left">Kode Akun</th>
                <th class="text-left">Nama Akun</th>
                @if($columns['description'])
                <th class="text-left">Keterangan</th>
                @endif
                <th class=" text-right">Debet</th>
                <th class=" text-right">Kredit</th>
                @if($columns['created_by'])
                    <th class=" text-right">Dibuat oleh</th>
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
                    @if($journal_id!=$journal->journal_id)
                        @php $journal_id=$journal->journal_id; @endphp
                        <td>{{$journal->trans_no}}</td>
                        <td>{{fdate($journal->trans_date)}}</td>
                    @else
                        <td></td>
                        <td>{{fdate($journal->trans_date)}}</td>
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
            @endforeach
        </tbody>
    </table>
