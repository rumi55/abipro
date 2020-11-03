<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    protected $table = 'report_templates';
    protected $fillable = [
        'report_name', 'template_name', 'template_content', 'company_id', 'created_by', 'created_at', 'is_default'
    ];

    public static function createDefault($company_id){
        ReportTemplate::create([
            'company_id'=>$company_id,
            'created_by'=>user('id'),
            'report_name'=>'header',
            'template_name'=>'Header',
            'template_content'=>'<figure class="table"><table><tbody><tr><td style="width:80px;">{company_logo}</td><td><h3><strong>{company_name}</strong></h3><p>{company_address}</p><p>Telp.&nbsp;{company_phone}, Email: {company_email}, Website: {company_website}</p></td></tr></tbody></table></figure><hr><p>&nbsp;</p>',
            'is_default'=>1
        ]);
        ReportTemplate::create([
            'company_id'=>$company_id,
            'created_by'=>user('id'),
            'report_name'=>'footer',
            'template_name'=>'Footer',
            'template_content'=>'<p>&nbsp;</p><hr><figure class="table"><table><tbody><tr><td>Tanggal Cetak: {datetime}</td><td><p style="text-align:right;">{pagenum}</p></td></tr></tbody></table></figure>',
            'is_default'=>1
        ]);
        ReportTemplate::create([
            'company_id'=>$company_id,
            'created_by'=>user('id'),
            'report_name'=>'receipt',
            'template_name'=>'Receipt',
            'template_content'=>'<p style="text-align:center;">{header}</p><h3 style="text-align:center;"><strong><u>KWITANSI</u></strong></h3><figure class="table"><table><tbody><tr><td style="width:50%;"><strong>Nomor:</strong> {trans_no}</td><td style="width:50%;"><p style="text-align:right;"><strong>Tanggal:</strong> {trans_date}</p></td></tr></tbody></table></figure><figure class="table"><table><tbody><tr><td style="width:150px;">Telah dibayar dari</td><td style="width:10px;">:</td><td>{payer}</td></tr><tr><td>Terbilang</td><td>:</td><td>{total_inword}</td></tr><tr><td>Untuk Pembayaran</td><td>:</td><td>{description}</td></tr></tbody></table></figure><p>{detail}&nbsp;</p><figure class="table"><table><tbody><tr><td style="width:50%;"><p>&nbsp;</p><p><strong>Jumlah:</strong> <u>Rp {{total}}</u></p></td><td><p style="text-align:center;"><strong>Jakarta, {{date}}</strong></p><p style="text-align:center;">&nbsp;</p><p style="text-align:center;">&nbsp;</p><p style="text-align:center;">(_________________________)</p></td></tr></tbody></table></figure>',
            'is_default'=>1
        ]);
        ReportTemplate::create([
            'company_id'=>$company_id,
            'created_by'=>user('id'),
            'report_name'=>'voucher',
            'template_name'=>'Voucher',
            'template_content'=>'<p style="text-align:center;">{header}</p><h3 style="text-align:center;"><strong><u>VOUCHER</u></strong></h3><figure class="table"><table><tbody><tr><td style="width:50%;"><strong>Nomor:</strong> {trans_no}</td><td style="width:50%;"><p style="text-align:right;"><strong>Tanggal:</strong> {trans_date}</p></td></tr></tbody></table></figure><figure class="table"><table><tbody><tr><td style="width:150px;">Telah dibayar dari</td><td style="width:10px;">:</td><td>{payer}</td></tr><tr><td>Terbilang</td><td>:</td><td>{total_inword}</td></tr><tr><td>Untuk Pembayaran</td><td>:</td><td>{description}</td></tr></tbody></table></figure><p>{detail}&nbsp;</p><figure class="table"><table><tbody><tr><td>Disetujui oleh:</td><td>Diperiksa oleh:</td><td>Dibayar oleh:</td><td>Diterima oleh:</td></tr><tr><td style="height:80px;">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>{approved_by}</td><td>{checked_by}</td><td>{payer}</td><td>{payer}</td></tr></tbody></table></figure><p>{pagenum}</p>',
            'is_default'=>1
        ]);
        ReportTemplate::create([
            'company_id'=>$company_id,
            'created_by'=>user('id'),
            'report_name'=>'journal',
            'template_name'=>'General Journal',
            'template_content'=>'<p style="text-align:center;">{header}</p><h3 style="text-align:center;"><u>Jurnal Umum</u></h3><figure class="table"><table><tbody><tr><td style="width:50%;"><strong>Nomor:</strong> {trans_no}</td><td style="width:50%;"><p style="text-align:right;"><strong>Tanggal:</strong> {trans_date}</p></td></tr></tbody></table></figure><figure class="table"><table><tbody><tr><td style="width:150px;">Telah dibayar dari</td><td style="width:10px;">:</td><td>{payer}</td></tr><tr><td>Terbilang</td><td>:</td><td>{total_inword}</td></tr><tr><td>Untuk Pembayaran</td><td>:</td><td>{description}</td></tr></tbody></table></figure><p>{detail}&nbsp;</p><figure class="table"><table><tbody><tr><td>Disetujui oleh:</td><td>Diperiksa oleh:</td><td>Dibayar oleh:</td><td>Diterima oleh:</td></tr><tr><td style="height:80px;">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>{approved_by}</td><td>{checked_by}</td><td>{payer}</td><td>{payer}</td></tr></tbody></table></figure>',
            'is_default'=>1
        ]);
    }
}
