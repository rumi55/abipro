<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\ContactResource;
use App\Exceptions\ApiValidationException;
use App\Contact;
use App\Counter;
use App\Numbering;
use Auth;
use Validator;
use PDF;

class ContactController extends Controller
{
    public function index(){
        $dtcustomer = dcru_dt('contacts', 'dtcustomer');
        $dtsupplier = dcru_dt('contacts', 'dtsupplier');
        $dtemployee = dcru_dt('contacts', 'dtemployee');
        $dtothers = dcru_dt('contacts', 'dtothers');
        return view('company.contact.index', ['dtcustomer'=>$dtcustomer, 
        'dtsupplier'=>$dtsupplier,
        'dtemployee'=>$dtemployee,
        'dtothers'=>$dtothers,
        ]);
    }

    public function create(){
        $company_id = company('id');
        $model = new Contact;
        $mode = 'create';
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', \App\TransactionType::CONTACT)->get();
        return view('company.contact.form', compact('model', 'mode', 'numberings'));
    }
    public function view($id){
        $company_id = company('id');
        $contact = Contact::findOrFail($id);
        if($contact->company_id!=$company_id){
            abort(404);
        }
        $prev=Contact::where('company_id', $contact->company_id)
        ->where('id','<', $contact->id)->orderBy('id', 'desc')->first();
        $next=Contact::where('company_id', $contact->company_id)
        ->where('id','>', $contact->id)->orderBy('id', 'asc')->first();
        $prev_id = $prev!=null?$prev->id:'';
        $next_id = $next!=null?$next->id:'';
        return view('company.contact.view', compact('contact', 'next_id', 'prev_id'));
    }
    public function edit($id){
        $model = Contact::findOrFail($id);
        $mode = 'edit';
        return view('company.contact.form', compact('model', 'mode'));
    }
    
    public function save(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        
        $rules = [
            'custom_id' => 'nullable|max:16|unique:contacts,custom_id,NULL,id,company_id,'.$company->id,
            'name' => 'required|max:128',
            'email' => 'nullable|max:64|unique:contacts,email,NULL,id,company_id,'.$company->id,
            'phone' => 'nullable|max:16|unique:contacts,phone,NULL,id,company_id,'.$company->id,
            'mobile' => 'nullable|max:16|unique:contacts,mobile,NULL,id,company_id,'.$company->id,
            'address' => 'max:128'
        ];
        if(empty($data['numbering_id'])){
            $rules['custom_id'] = 'required|max:16|unique:contacts,custom_id,NULL,id,company_id,'.$company->id;                
        }else{
            $data['custom_id']=null;
        }
        $attr = [
            'custom_id' => trans('ID'),
            'name' => trans('Name'),
            'email' => trans('Email'),
            'phone' => trans('Phone'),
            'mobile' => trans('Mobile Phone'),
            'address' => trans('Address')
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data['is_others']=empty($request->is_customer.$request->is_supplier.$request->is_employee)?true:$request->is_others;
        if(!empty($data['numbering_id'])){
            $numbering = Numbering::findOrFail($data['numbering_id']);
            if($numbering->counter_reset=='y'){
                $period = date('Y');
            }else if($numbering->counter_reset=='m'){
                $period = date('Y-m');
            }else if($numbering->counter_reset=='d'){
                $period = date('Y-m-d');
            }else{
                $period  = null;
            }
            $counter = Counter::firstOrCreate(
                ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company->id],
                ['counter'=>$numbering->counter_start-1]
            );        
                
            $check = true;
            do{
                $counter->getNumber();
                $custom_id = $counter->last_number;
                $exists = Contact::where('custom_id', $custom_id)->where('company_id', $company->id)->exists(); 
                if(!$exists){
                    $counter->save();
                    $data['custom_id']=$custom_id;
                    $check = false;
                }
            }while($check);
        }
        $data = array_merge($data,['company_id'=>$company->id]);    
        $contact = Contact::create($data);
        add_log('contacts', 'create', '');
        return redirect()->route('contacts.index')->with('success', trans('New :attr has been created.', ['attr'=>strtolower(trans('Contact'))]));
    }
    public function update(Request $request, $id){
        $user = Auth::user();
        $company = $user->activeCompany();

        $data = $request->all();
        $rules = [
            'custom_id' => 'nullable|max:16|unique:contacts,custom_id,'.$id.',id,company_id,'.$company->id,
            'name' => 'required|max:128',
            'email' => 'nullable|max:64|unique:contacts,email,'.$id.',id,company_id,'.$company->id,
            'phone' => 'nullable|max:64|unique:contacts,phone,'.$id.',id,company_id,'.$company->id,
            'mobile' => 'nullable|max:16|unique:contacts,mobile,'.$id.',id,company_id,'.$company->id,
            'address' => 'max:128'
        ];
        $attr = [
            'custom_id' => trans('ID'),
            'name' => trans('Name'),
            'email' => trans('Email'),
            'phone' => trans('Phone'),
            'mobile' => trans('Mobile Phone'),
            'address' => trans('Address')
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }    
        $contact = Contact::findOrFail($id);
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->mobile = $request->mobile;
        $contact->phone = $request->phone;
        $contact->is_customer = $request->is_customer??false;
        $contact->is_supplier = $request->is_supplier??false;
        $contact->is_employee = $request->is_employee??false;
        $contact->is_others = $request->is_others??false;
        $contact->is_others = empty($request->is_customer.$request->is_supplier.$request->is_employee)?true:$contact->is_others;
        
        $contact->address = $request->address;
        $contact->save();
        add_log('contacts', 'edit', '');
        return redirect()->route('contacts.index')->with('success', trans('Changes have been saved.'));
    }
    
    public function delete($id)
    {
        $id = decode($id);
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return response()->json(null, 204);
    }
}
