<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class PrintController extends Controller
{

    public function index(){
        return view('print.index');
    }

}
