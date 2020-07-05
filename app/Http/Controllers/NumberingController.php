<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Numbering;
use App\TransactionType;
use Auth;
use Validator;
use Cache;

class NumberingController extends Controller
{
    public function index(){
        $data = dcru_dt('numberings', 'dtables');
        return view('setting.numbering.index', $data);
    }
        public function create(){
        $model = new Numbering;
        $model->counter_reset = 'y';
        $model->counter_digit = 4;
        $model->counter_start = 1;
        $mode = 'create';
        if (Cache::has(TransactionType::KEY)) {
            $types = Cache::get(TransactionType::KEY);
        }else{
            $types = TransactionType::all();
            Cache::put(TransactionType::KEY, $types);
        }
        return view('setting.numbering.form', compact('model', 'mode', 'types'));
    }

    public function duplicate($id){
        $model = Numbering::findOrFail($id);
        $mode = 'create';
        if (Cache::has(TransactionType::KEY)) {
            $types = Cache::get(TransactionType::KEY);
        }else{
            $types = TransactionType::all();
            Cache::put(TransactionType::KEY, $types);
        }
        return view('setting.numbering.form', compact('model', 'mode', 'types'));
    }
    public function edit($id){
        $model = Numbering::findOrFail($id);
        $mode = 'edit';
        if (Cache::has(TransactionType::KEY)) {
            $types = Cache::get(TransactionType::KEY);
        }else{
            $types = TransactionType::all();
            Cache::put(TransactionType::KEY, $types);
        }
        return view('setting.numbering.form', compact('model', 'mode', 'types'));
    }
    public function view($id){
        $model = Numbering::findOrFail($id);
        return view('setting.numbering.view', compact('model'));
    }
    public function save(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'name' => 'required|max:64|unique:numberings,name,NULL,id,company_id,'.$company->id,
            'format' => 'required|max:64|unique:numberings,format,NULL,id,company_id,'.$company->id,
            'transaction_type_id'=>'required',
            'counter_start'=>'required',
            'counter_digit'=>'required|integer|min:2',
            'counter_start'=>'required|integer|min:0',
        ];
        $attr = [
            'transaction_type_id' => trans('Numbering Type'),
            'name' => trans('Numbering Name'),
            'format' => trans('Numbering Format'),
            'counter_start'=>trans('Counter Reset'),
            'counter_digit'=>trans('Counter Digit'),
            'counter_start'=>trans('Counter Start'),
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = array_merge($request->all(), ['company_id'=>$company->id]);
        $model = Numbering::create($data);
        add_log('numberings', 'create', json_encode(['id'=>$model->id, 'name'=>$model->name]));
        return redirect()->route('numberings.index')->with('success', trans('New :attr has been created.', ['attr'=>strtolower(trans('Numbering Format'))]));
    }
    public function update(Request $request, $id){
        $model = Numbering::findOrFail($id);
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'name' => 'required|max:64|unique:numberings,name,'.$id.',id,company_id,'.$company->id,
            'format' => 'required|max:64|unique:numberings,format,'.$id.',id,company_id,'.$company->id,
            'counter_start'=>'required|',
            'counter_digit'=>'required|integer|min:2',
            'counter_start'=>'required|integer|min:0',
        ];
        $attr = [
            'name' => 'Jenis Transaksi',
            'format' => 'Format Penomoran',
            'counter_start'=>'Counter Reset',
            'counter_digit'=>'Counter Digit',
            'counter_start'=>'Counter Start',
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $model->name = $request->name;
        $model->format = $request->format;
        $model->counter_reset = $request->counter_reset;
        $model->counter_digit = $request->counter_digit;
        $model->counter_start = $request->counter_start;
        $model->save();
        add_log('numberings', 'edit', json_encode(['id'=>$model->id, 'name'=>$model->name]));
        return redirect()->route('numberings.index')->with('success', trans('Changes have been saved.'));
    }
    public function delete($id)
    {
        $model = Numbering::findOrFail($id);
        $name = $model->name;
        $model->delete();
        add_log('numberings', 'delete', json_encode(['name'=>$name]));
        return redirect()->route('numberings.index')->with('success', 'Jenis jurnal '.$name.' telah dihapus');
    }

}