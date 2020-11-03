<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Journal;
use App\Account;
use Auth;
use PDF;
use DB;

class ReportController extends Controller
{
    public function index(){
        $reports = array(
            [
                'group'=>'finance',
                'label'=>'Finance',
                'reports'=>[
                    ['name'=>'profit','title'=>'Profit & Loss', 'description'=>'Menampilkan laporan laba - rugi', 'route'=>route('reports.view', 'profit')],
                    ['name'=>'balance','title'=>'Balance Sheet', 'description'=>'Menampilkan laporan neraca', 'route'=>route('reports.view', 'balance')],
                    ['name'=>'cashflow','title'=>'Cashflow', 'description'=>'Menampilkan arus kas', 'route'=>route('reports.view', 'cashflow')],
                    ['name'=>'hpp','title'=>'Harga Pokok Produksi', 'description'=>'Menampilkan laporan harga pokok produksi', 'route'=>route('reports.view', 'hpp')],
                ]
            ],
            [
                'group'=>'ledger',
                'label'=>'General Ledger',
                'reports'=>[
                    ['name'=>'vouchers','title'=>'Voucher', 'description'=>'Menampilkan laporan voucher', 'route'=>route('reports.view', 'vouchers')],
                    ['name'=>'journals','title'=>'Journal', 'description'=>'Menampilkan laporan jurnal', 'route'=>route('reports.view', 'journals')],
                    ['name'=>'ledgers','title'=>'General Ledger', 'description'=>'Menampilkan laporan buku besar', 'route'=>route('reports.view', 'ledgers')],
                    ['name'=>'trial_balance','title'=>'Trial Balance', 'description'=>'Menampilkan laporan neraca saldo', 'route'=>route('reports.view', 'trial_balance')],
                    ['name'=>'sortirs','title'=>'Sortir', 'description'=>'Menampilkan laporan transaksi berdasarkan sortir', 'route'=>route('reports.view', 'sortirs')]
                ]
            ],
            // [
            //     'group'=>'sales',
            //     'label'=>'Penjualan',
            //     'reports'=>[
            //         ['title'=>'Daftar Penjualan', 'description'=>'Menunjukkan daftar kronologis dari semua faktur, pemesanan, penawaran, dan pembayaran Anda untuk rentang tanggal yang dipilih.', 'route'=>'#'],
            //         ['title'=>'Piutang Pelanggan', 'description'=>'Menampilkan tagihan yang belum dibayar untuk setiap pelanggan, termasuk nomor & tanggal faktur, tanggal jatuh tempo, jumlah nilai, dan sisa tagihan yang terhutang pada Anda.', 'route'=>'#'],
            //     ]
            // ]
        );
        return view('report.index', compact('reports'));
    }
    public function view($name){
        return view('report.view', [
            'report'=>$name,
            'title'=>ucwords($this->name[$name])
        ]);
    }
    public function print($group,$id, $name){
        $file = asset(route("$group.$name", ['id'=>$id], false));
        return view('report.print', [
            'id'=>$id,
            'group'=>$group,
            'report'=>$name,
            'file'=>$file,
            'title'=>ucwords($name)
        ]);
    }

    protected $name = [
        'profit'=>'Profit and Loss',
        'balance'=>'Balance Sheet',
        'hpp'=>'Harga Pokok Produksi',
        'cashflow'=>'Cashflow',
        'vouchers'=>'Voucher',
        'journals'=>'Journal',
        'ledgers'=>'General Ledger',
        'trial_balance'=>'Trial Balance',
        'sortirs'=>'Sortir'

    ];
}
