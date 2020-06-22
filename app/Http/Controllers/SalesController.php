<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\SalesResource;
use App\Http\Resources\JournalResource;
use App\Exceptions\ApiValidationException;
use App\Counter;
use App\Journal;
use App\Sales;
use App\SalesDetail;
use App\JournalDetail;
use App\Numbering;
use App\SalesType;
use Auth;
use Validator;
use PDF;

class SalesController extends Controller
{
    public function getAllQuotes(Request $request){
        return $this->getAll($request, 'quote');
    }
    public function getAllOrders(Request $request){
        return $this->getAll($request, 'order');
    }
    public function getAllInvoices(Request $request){
        return $this->getAll($request, 'invoice');
    }
    public function getAll(Request $request, $type){
        $company_id = Auth::user()->activeCompany()->id;
        $sales = Sales::where('company_id', '=', $company_id);
        $sales = $sales->where('is_'.$type, true);
        $page_size = $request->query('page_size', $sales->count());
        $sort_key = $request->query('sort_key');
        $sort_order = $request->query('sort_order', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter');
        if(isset($filter)){
            foreach($filter as $column => $value){
                $sales = $sales->where($column,'=', "$value");
            }
        }
        if(!empty($search)){
            $sales = $sales->where(function ($query) use($search){
                $query->where($type.'_no','like', "%$search%")
                ->orWhere($type.'_date','like', "%$search%");
            });
        }

        $sort = $request->query('sort');
        if(!empty($sort)){
            $sort = explode('-',$sort);
            $sort_key=$sort[0];
            $sort_order=count($sort)==2?(substr($sort[1], 0, 3)=='asc'?'asc':'desc'):'asc';
            $sales = $sales->orderBy($sort_key, $sort_order);
        }    
        
        if(!empty($sort_key)){
            $sales = $sales->orderBy($sort_key, $sort_order);
        }
        if(empty($sort_key) && empty($sort)){
            $sales = $sales->orderBy($type.'_date', 'desc');
            $sales = $sales->orderBy($type.'_no', 'desc');
        }
        $sales = $sales->paginate($page_size)->appends($request->query());
        return SalesResource::collection($sales);
    }

    public function getQuote($id){
        return $this->get($id, 'quote');
    }
    public function getOrder($id){
        return $this->get($id, 'order');
    }
    public function getInvoice($id){
        return $this->get($id, 'invoice');
    }
    public function get($id, $type){
        $id = decode($id);
        $trans = Sales::findOrFail($id);
        $prev=Sales::where('company_id', $trans->company_id)
        ->where('is_'.$type, true)
        ->where('id','<', $trans->id)
        ->orderBy('id', 'desc')->first();
        $next=Sales::where('company_id', $trans->company_id)
        ->where('is_'.$type, true)
        ->where('id','>', $trans->id)->orderBy('id', 'asc')->first();
        return (new SalesResource($trans))
        ->additional(['meta' => [
            'previous' => $prev!=null?encode($prev->id):'',
            'next' => $next!=null?encode($next->id):'',
        ]]);
    }
    public function print($id){
        $company = Auth::user()->activeCompany();
        $id = decode($id);
        $sales = Sales::findOrFail($id);
        $data = [
            'company'=>$company,
            'title'=>'Penawaran Penjualan',
            'sales'=>$sales
        ];
        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.sales_quote.quote', $data);
        return $pdf->stream('sales_quote.pdf');
    }
    
    public function createQuote(Request $request){
        validate($request->all(), [
            'quote_no' => 'required',
            'quote_date' => 'required',
            'customer_id' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $quote_date = fdate($request->quote_date, 'Y-m-d');
        $quote_due_date = fdate($request->quote_due_date, 'Y-m-d');
        try{
            \DB::beginTransaction();
            $numbering = Numbering::findOrFail(decode($request->quote_no));
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
                ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company_id],
                ['counter'=>$numbering->counter_start-1]
            );        
            $check = true;
            do{
                $counter->getNumber();
                $quote_no = $counter->last_number;
                $c = Sales::where('quote_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                if($c==0){
                    $sales = Sales::create([
                        'is_quote'=>1,
                        'quote_no'=>$quote_no,
                        'quote_date'=>$quote_date,
                        'quote_due_date'=>$quote_due_date,
                        'status_quote'=>0,
                        'quote_disc'=>$request->disc,
                        'quote_tax'=>$request->tax,
                        'quote_subtotal'=>$request->subtotal,
                        'quote_total'=>$request->total,
                        'quote_total_disc'=>$request->total_disc,
                        'quote_term_id'=>decode($request->term_id),
                        'customer_id'=>decode($request->customer_id),
                        'salesman_id'=>decode($request->salesman_id),
                        'created_by'=>$user->id,
                        'company_id'=>$company_id
                    ]);
                    $counter->save();
                    $check = false;
                }
            }while($check);                
            
            foreach($request->details as $detail){
                SalesDetail::create([
                    'sequence'=>$detail['sequence'],
                    'is_quote'=>1,
                    'product_id'=>decode($detail['product_id']),
                    'quantity'=>$detail['quantity'],
                    'unit_price'=>$detail['unit_price'],
                    'tax_id'=>decode($detail['tax_id']),
                    'disc'=>$detail['disc'],
                    'total_price'=>$detail['total_price'],
                    'amount'=>$detail['amount'],
                    'sales_id'=>$sales->id,
                    'created_by'=>$user->id
                ]);    
            }
            // $this->addToJournal($sales);
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

        return (new SalesResource($sales))
                ->response()
                ->setStatusCode(201);
    }
    public function createOrder(Request $request){
        validate($request->all(), [
            'order_no' => 'required',
            'order_date' => 'required',
            'customer_id' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $order_date = fdate($request->order_date, 'Y-m-d');
        $order_due_date = fdate($request->order_due_date, 'Y-m-d');
        try{
            \DB::beginTransaction();
            $numbering = Numbering::findOrFail(decode($request->order_no));
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
                ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company_id],
                ['counter'=>$numbering->counter_start-1]
            );        
            $check = true;
            do{
                $counter->getNumber();
                $order_no = $counter->last_number;
                $c = Sales::where('order_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                if($c==0){
                    $sales = Sales::create([
                        'is_order'=>1,
                        'order_no'=>$order_no,
                        'order_date'=>$order_date,
                        'due_order_date'=>$due_order_date,
                        'status_order'=>0,
                        'order_disc'=>$request->disc,
                        'order_tax'=>$request->tax,
                        'order_subtotal'=>$request->subtotal,
                        'order_total'=>$request->total,
                        'order_total_disc'=>$request->total_disc,
                        'order_term_id'=>decode($request->term_id),
                        'customer_id'=>decode($request->customer_id),
                        'salesman_id'=>decode($request->salesman_id),
                        'created_by'=>$user->id,
                        'company_id'=>$company_id
                    ]);
                    $counter->save();
                    $check = false;
                }
            }while($check);                
            
            foreach($request->details as $detail){
                SalesDetail::create([
                    'sequence'=>$detail['sequence'],
                    'is_order'=>1,
                    'product_id'=>decode($detail['product_id']),
                    'quantity'=>$detail['quantity'],
                    'unit_price'=>$detail['unit_price'],
                    'tax_id'=>decode($detail['tax_id']),
                    'disc'=>$detail['disc'],
                    'total_price'=>$detail['total_price'],
                    'amount'=>$detail['amount'],
                    'sales_id'=>$sales->id,
                    'created_by'=>$user->id
                ]);    
            }
            // $this->addToJournal($sales);
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

        return (new SalesResource($sales))
                ->response()
                ->setStatusCode(201);
    }
    public function createInvoice(Request $request){
        validate($request->all(), [
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'customer_id' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $invoice_date = fdate($request->invoice_date, 'Y-m-d');
        $invoice_due_date = fdate($request->invoice_due_date, 'Y-m-d');
        try{
            \DB::beginTransaction();
            $numbering = Numbering::findOrFail(decode($request->invoice_no));
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
                ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company_id],
                ['counter'=>$numbering->counter_start-1]
            );        
            $check = true;
            do{
                $counter->getNumber();
                $invoice_no = $counter->last_number;
                $c = Sales::where('invoice_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                if($c==0){
                    $sales = Sales::create([
                        'is_invoice'=>1,
                        'invoice_no'=>$invoice_no,
                        'invoice_date'=>$invoice_date,
                        'due_invoice_date'=>$invoice_due_date,
                        'status_invoice'=>0,
                        'invoice_disc'=>$request->disc,
                        'invoice_tax'=>$request->tax,
                        'invoice_subtotal'=>$request->subtotal,
                        'invoice_total'=>$request->total,
                        'invoice_total_disc'=>$request->total_disc,
                        'invoice_term_id'=>decode($request->term_id),
                        'customer_id'=>decode($request->customer_id),
                        'salesman_id'=>decode($request->salesman_id),
                        'created_by'=>$user->id,
                        'company_id'=>$company_id
                    ]);
                    $counter->save();
                    $check = false;
                }
            }while($check);                
            
            foreach($request->details as $detail){
                SalesDetail::create([
                    'sequence'=>$detail['sequence'],
                    'is_invoice'=>1,
                    'product_id'=>decode($detail['product_id']),
                    'quantity'=>$detail['quantity'],
                    'unit_price'=>$detail['unit_price'],
                    'tax_id'=>decode($detail['tax_id']),
                    'disc'=>$detail['disc'],
                    'total_price'=>$detail['total_price'],
                    'amount'=>$detail['amount'],
                    'sales_id'=>$sales->id,
                    'created_by'=>$user->id
                ]);    
            }
            // $this->addToJournal($sales);
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }

        return (new SalesResource($sales))
                ->response()
                ->setStatusCode(201);
    }
    public function updateQuote(Request $request, $id){
        $id = decode($id);
        $sales = Sales::findOrFail($id);
        validate($request->all(), [
            'quote_no' => 'required',
            'quote_date' => 'required',
            'customer_id' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $quote_date = fdate($request->quote_date, 'Y-m-d');
        $quote_due_date = fdate($request->quote_due_date, 'Y-m-d');
        // dd($request->details);

        try{
            \DB::beginTransaction();
            $sales->quote_date = $quote_date;
            $sales->quote_due_date = $quote_due_date;
            $sales->customer_id = decode($request->customer_id);
            $sales->salesman_id = decode($request->salesman_id);
            $sales->quote_term_id = decode($request->term_id);
            $sales->quote_disc = $request->disc;
            $sales->quote_tax = $request->tax;
            $sales->quote_subtotal = $request->subtotal;
            $sales->quote_total = $request->total;
            $sales->quote_total_disc = $request->total_disc;
            $sales->updated_by = $user->id;
            $sales->update();
            //cek id 
            $old_details = array();
            foreach($sales->details as $detail){
                $old_details[$detail->id] = $detail;
            }
            
            $sales_details=array();
            foreach($request->details as $detail){
                if($detail['id']==null){
                    $sdetail= SalesDetail::updateOrcreate([
                        'sequence'=>$detail['sequence'],
                        'product_id'=>decode($detail['product_id']),
                        'quantity'=>$detail['quantity'],
                        'unit_price'=>$detail['unit_price'],
                        'tax_id'=>decode($detail['tax_id']),
                        'disc'=>$detail['disc'],
                        'total_price'=>$detail['total_price'],
                        'amount'=>$detail['amount'],
                        'is_quote'=>1,
                        'sales_id'=>$sales->id,
                        'updated_by'=>$user->id
                    ]);        
                }else{
                    $jid = decode($detail['id']);
                    $sdetail = SalesDetail::findOrFail($jid);
                    $old_sq = $sdetail->sequence; 
                    $sdetail->sequence=$detail['sequence'];
                    $sdetail->product_id=decode($detail['product_id']);
                    $sdetail->quantity=$detail['quantity'];
                    $sdetail->unit_price=$detail['unit_price'];
                    $sdetail->tax_id=decode($detail['tax_id']);
                    $sdetail->disc=$detail['disc'];
                    $sdetail->total_price=$detail['total_price'];
                    $sdetail->amount=$detail['amount'];
                    $sdetail->sales_id=$sales->id;
                    $sdetail->is_quote = 1;
                    $sdetail->updated_by=$user->id;
                    $sdetail->update();
                    if(array_key_exists($jid, $old_details)){
                       unset($old_details[$jid]); 
                    }
                }
                $sales_details[] = $sdetail;
            }
            // dd($old_details);
            foreach($old_details as $detail){
                $detail->delete();
            }
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }        
        return new SalesResource($sales);
    }
    public function updateOrder(Request $request, $id){
        $id = decode($id);
        $sales = Sales::findOrFail($id);
        validate($request->all(), [
            'order_no' => 'required',
            'order_date' => 'required',
            'customer_id' => 'required',
        ]);
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        $order_date = fdate($request->order_date, 'Y-m-d');
        // dd($request->details);

        try{
            \DB::beginTransaction();
            $sales->order_date = $order_date;
            $sales->customer_id = decode($request->customer_id);
            $sales->salesman_id = decode($request->salesman_id);
            $sales->quote_term_id = decode($request->term_id);
            $sales->quote_disc = $request->disc;
            $sales->quote_tax = $request->tax;
            $sales->quote_subtotal = $request->subtotal;
            $sales->quote_total = $request->total;
            $sales->quote_total_disc = $request->total_disc;
            $sales->updated_by = $user->id;
            if($sales->is_quote==1 && $sales->is_order==0){
                $sales->status_quote = 1;
                $sales->status_order = 0;
                $sales->is_order = 1;
                $numbering = Numbering::findOrFail(decode($request->order_no));
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
                    ['period'=>$period, 'numbering_id'=>$numbering->id, 'company_id'=>$company_id],
                    ['counter'=>$numbering->counter_start-1]
                );        
                $check = true;
                do{
                    $counter->getNumber();
                    $order_no = $counter->last_number;
                    $c = Sales::where('order_no', $counter->last_number)->where('company_id', $company_id)->count(); 
                    if($c==0){
                        $sales->order_no = $order_no;
                        $counter->save();
                        $check = false;
                    }
                }while($check);
            }
            $sales->update();
            //cek id 
            $old_details = array();
            foreach($sales->details as $detail){
                $old_details[$detail->id] = $detail;
            }
            
            $sales_details=array();
            foreach($request->details as $detail){
                if($detail['id']==null){
                    $sdetail= SalesDetail::updateOrcreate([
                        'sequence'=>$detail['sequence'],
                        'is_order'=>1,
                        'product_id'=>decode($detail['product_id']),
                        'quantity'=>$detail['quantity'],
                        'unit_price'=>$detail['unit_price'],
                        'tax_id'=>decode($detail['tax_id']),
                        'disc'=>$detail['disc'],
                        'total_price'=>$detail['total_price'],
                        'amount'=>$detail['amount'],
                        'sales_id'=>$sales->id,
                        'updated_by'=>$user->id
                    ]);        
                }else{
                    $jid = decode($detail['id']);
                    $sdetail = SalesDetail::findOrFail($jid);
                    $old_sq = $sdetail->sequence; 
                    $sdetail->is_order = 1;
                    $sdetail->sequence=$detail['sequence'];
                    $sdetail->product_id=decode($detail['product_id']);
                    $sdetail->quantity=$detail['quantity'];
                    $sdetail->unit_price=$detail['unit_price'];
                    $sdetail->tax_id=decode($detail['tax_id']);
                    $sdetail->disc=$detail['disc'];
                    $sdetail->total_price=$detail['total_price'];
                    $sdetail->amount=$detail['amount'];
                    $sdetail->sales_id=$sales->id;
                    $sdetail->updated_by=$user->id;
                    $sdetail->update();
                    if(array_key_exists($jid, $old_details)){
                       unset($old_details[$jid]); 
                    }
                }
                $sales_details[] = $sdetail;
            }
            foreach($old_details as $detail){
                // $detail->delete();
            }
            // $this->updateJournal($sales, $sales_details);    
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }        
        return new SalesResource($sales);
    }

    public function delete($id)
    {
        $id = decode($id);
        $sales = Sales::findOrFail($id);
        $sales->delete();
        return response()->json(null, 204);
    }
    public function batchDelete(Request $request){
        $id = $request->id;
        $ids = array();
        foreach($id as $i){
            $ids[] = decode($i);
        }
        Sales::destroy($ids);
        return response()->json(null, 204);
    }
    
}
