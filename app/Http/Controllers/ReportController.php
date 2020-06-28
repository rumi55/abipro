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
                    ['title'=>'Profit & Loss', 'description'=>'Menampilkan laporan laba - rugi', 'route'=>route('reports.profit')],
                    ['title'=>'Balance Sheet', 'description'=>'Menampilkan laporan neraca', 'route'=>route('reports.balance')],
                    ['title'=>'Cashflow', 'description'=>'Menampilkan arus kas', 'route'=>route('reports.cashflow')],
                    ['title'=>'Harga Pokok Produksi', 'description'=>'Menampilkan laporan harga pokok produksi', 'route'=>route('reports.hpp')],
                ]
            ],
            [
                'group'=>'ledger',
                'label'=>'General Ledger',
                'reports'=>[
                    ['title'=>'Voucher', 'description'=>'Menampilkan laporan voucher', 'route'=>route('reports.vouchers')],
                    ['title'=>'Journal', 'description'=>'Menampilkan laporan jurnal', 'route'=>route('reports.journals')],
                    ['title'=>'General Ledger', 'description'=>'Menampilkan laporan buku besar', 'route'=>route('reports.ledgers')],
                    ['title'=>'Trial Balance', 'description'=>'Menampilkan laporan neraca saldo', 'route'=>route('reports.trial_balance')],
                    ['title'=>'Sortir', 'description'=>'Menampilkan laporan transaksi berdasarkan sortir', 'route'=>route('reports.sortirs')]
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
}
