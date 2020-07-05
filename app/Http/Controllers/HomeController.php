<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $company_id = company('id');
        $vouchers= \App\Transaction::where('company_id',$company_id)->where('status', 'submitted')->limit(5)->get();
        return view('home.index', compact('vouchers'));
    }
    
}
