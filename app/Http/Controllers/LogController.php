<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(){
        // $company = \Auth::user()->activeCompany();
        $data = dcru_dt('logs', 'dtables');
        return view('log.index', $data);
    }
    public function view($id){
        $log = \App\Log::findOrFail($id);
        return view('log.view', compact('log'));
    }
    public function delete($id){
        $log = \App\Log::findOrFail($id);
        $log->delete();
        return redirect()->route('logs.index')->with('success', 'Aktivitas pengguna telah dihapus.');
    }
    public function deleteBatch(Request $request){
        $ids = $request->input('id');
        foreach($ids as $id){
            \DB::table('logs')->where('id', $id)->delete();
        }
        return redirect()->route('logs.index')->with('success', 'Aktivitas pengguna telah dihapus.');
    }
}
