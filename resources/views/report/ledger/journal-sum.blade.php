
    <h2 class="text-center">{{$title}}</h2>
    <small class="font-small">Periode: {{$period}}</small>
    <table>
        <thead>
            <tr>
                <th class="border-top border-bottom-double">No. Bukti</th>
                <th class="border-top border-bottom-double">Tanggal</th>
                <th class="border-top border-bottom-double">Dept.</th>
                <th class="border-top border-bottom-double">Kode Akun</th>
                <th class="border-top border-bottom-double">Nama Akun</th>
                <th class="border-top border-bottom-double">Keterangan</th>
                <th class="border-top border-bottom-double text-right">Debet</th>
                <th class="border-top border-bottom-double text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $journal_id=0; 
                $cdata = count($data);
            @endphp
            @foreach($data as $dt)     
                <tr>
                    @if($journal_id!==$dt->journal_id)
                        @php $journal_id=$dt->journal_id; @endphp
                        <td>{{$dt->trans_no}}</td>
                    @else
                        <td></td>
                    @endif
                    <td>{{fdate($dt->trans_date)}}</td>
                    <td>{{$dt->department_name}}</td>
                    <td>{{$dt->account_no}}</td>
                    <td>{{$dt->account_name}}</td>
                    <td>{{$dt->description}}</td>
                    <td class="text-right">{{format_number($dt->debit)}}</td>
                    <td class="text-right">{{format_number($dt->credit)}}</td>
                </tr>      
            @endforeach    
        </tbody>
    </table>