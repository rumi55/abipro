<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Numbering;
use App\Counter;
use App\SalesInvoice;
use App\SalesInvoiceDetail;

class SalesInvoiceController extends Controller
{
    public function create(Request $request){
        $company_id = company('id');
        $transaction = new SalesInvoice;
        $mode = 'create';
        return view('sales.invoice.form', compact('transaction', 'mode'));
    }
    public function createFromQuote(Request $request, $quote_id){
        $company_id = company('id');
        $quote = \App\SalesQuote::findOrFail($quote_id);
        $transaction = new SalesInvoice;
        $transaction->customer_id = $quote->customer_id;
        $transaction->salesman_id = $quote->salesman_id;
        $transaction->term_id = $quote->term_id;
        $transaction->due_date = $quote->due_date;
        $transaction->description = $quote->description;
        $transaction->sales_quote_id = $quote->id;
        $transaction->subtotal = $quote->subtotal;
        $transaction->total = $quote->total;
        $transaction->tax = $quote->tax;
        $details = array();
        foreach($quote->details as $detail){
            $order = new SalesInvoiceDetail;
            $order->product_id = $detail->product_id;
            $order->description = $detail->description;
            $order->unit_id = $detail->unit_id;
            $order->quantity = $detail->quantity;
            $order->unit_price = $detail->unit_price;
            $order->discount = $detail->discount;
            $order->discount_percent = $detail->discount_percent;
            $order->tax = $detail->tax;
            $order->tax_id = $detail->tax_id;
            $order->amount = $detail->amount;
            $details[] = $order;
        }
        $transaction->details = $details;
        $mode = 'create';
        return view('sales.invoice.form', compact('transaction', 'mode'));
    }
    public function duplicate(Request $request, $id){
        $company_id = company('id');
        $transaction = SalesInvoice::findOrFail($id);
        $mode = 'create';
        return view('sales.form', compact('transaction', 'mode'));
    }
    public function edit($id){
        $company_id = company('id');
        $transaction = SalesInvoice::findOrFail($id);
        $mode = 'edit';
        return view('sales.invoice.form', compact('transaction', 'mode'));
    }
    public function view($id){
        $company_id = company('id');
        $transaction = SalesInvoice::findOrFail($id);
        $prev=SalesInvoice::where('company_id', $transaction->company_id)
        ->where('id','<', $transaction->id)->orderBy('id', 'desc')->first();
        $next=SalesInvoice::where('company_id', $transaction->company_id)
        ->where('id','>', $transaction->id)->orderBy('id', 'asc')->first();
        $prev_id = $prev!=null?encode($prev->id):'';
        $next_id = $next!=null?encode($next->id):'';
        return view('sales.invoice.view', compact('transaction', 'next_id', 'prev_id'));
    }
    
    public function save(Request $request){
        $transaction =$request->transaction;
        
        $company_id = company('id');
        $rules = [
            'transaction.customer_id'=>'required|integer',
            'transaction.salesman_id'=>'nullable|integer',
            'transaction.numbering_id'=>'nullable|integer',
            'transaction.term_id'=>'nullable|integer',
            'transaction.trans_date'=>'required|date_format:d-m-Y',
            'transaction.detail.*.product_id'=>'required|integer|min:1',
            'transaction.detail.*.description'=>'nullable|max:255',
            'transaction.detail.*.quantity'=>'required|integer|min:1|max:10000',
            'transaction.detail.*.unit_id'=>'required|integer|min:1',
            'transaction.detail.*.unit_price'=>'required',
            'transaction.detail.*.discount'=>'nullable',
            'transaction.detail.*.amount'=>'required',
        ];
        if(empty($transaction['numbering_id'])){
            $rules['trans_no'] = 'required|max:16|unique:sales_invoices,trans_no,NULL,id,company_id,'.$company_id;                
        }else{
            $transaction['trans_no']=null;
        }
        $attr = [
            'transaction.customer_id'=>trans('Customer'),
            'transaction.salesman_id'=>trans('Salesman'),
            'transaction.numbering_id'=>trans('Numbering Format'),
            'transaction.term_id'=>trans('Term'),
            'transaction.trans_date'=>trans('Transaction Date'),
            'transaction.due_date'=>trans('Due Date'),
            'transaction.detail.*.product_id'=>trans('Product'),
            'transaction.detail.*.description'=>trans('Description'),
            'transaction.detail.*.quantity'=>trans('Quantity'),
            'transaction.detail.*.unit_id'=>trans('Unit'),
            'transaction.detail.*.unit_price'=>trans('Unit Price'),
            'transaction.detail.*.discount'=>trans('Discount'),
            'transaction.detail.*.amount'=>trans('Amount'),
        ];
        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{
            \DB::beginTransaction();
            if(!empty($transaction['numbering_id'])){
                $numbering = Numbering::findOrFail($transaction['numbering_id']);
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
                    $trans_no = $counter->last_number;
                    $exists = SalesInvoice::where('trans_no', $trans_no)->where('company_id', $company_id)->exists(); 
                    if(!$exists){
                        $counter->save();
                        $transaction['trans_no']=$trans_no;
                        $check = false;
                    }
                }while($check);
            }
            $transaction['trans_date'] = fdate($transaction['trans_date'], 'Y-m-d');
            $transaction['due_date'] = fdate($transaction['due_date'], 'Y-m-d');
            $transaction['subtotal'] = parse_number($transaction['subtotal']);
            $transaction['total'] = parse_number($transaction['total']);
            $transaction['tax'] = parse_number($transaction['tax']);
            $transaction['company_id'] = $company_id;
            $transaction['created_by'] = user('id');
            $sales = SalesInvoice::create($transaction);
            foreach($transaction['detail'] as $i => $detail){
                $detail['sales_invoice_id'] = $sales->id;
                $detail['sequence'] = $i;
                $detail['amount'] = parse_number($detail['amount']);
                $detail['unit_price'] = parse_number($detail['unit_price']);
                $detail['discount'] = parse_number($detail['discount']);
                $detail['discount_percent'] = parse_number($detail['discount_percent']);
                $detail['tax'] = parse_number($detail['tax']);
                SalesInvoiceDetail::create($detail);
            }
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }
        add_log('sales_invoices', 'create', '');
        return redirect()->route('sales_invoices.view', $sales->id)->with('success', trans('New :attr has been created.', ['attr'=>strtolower(trans('Sales invoice'))]));
    }
    public function update(Request $request, $id){
        $sales = SalesInvoice::findOrFail($id); 
        $transaction =$request->transaction;
        $company_id = company('id');
        $rules = [
            'transaction.customer_id'=>'required|integer',
            'transaction.salesman_id'=>'nullable|integer',
            'transaction.numbering_id'=>'nullable|integer',
            'transaction.term_id'=>'nullable|integer',
            'transaction.trans_date'=>'required|date_format:d-m-Y',
            'transaction.detail.*.product_id'=>'required|integer|min:1',
            'transaction.detail.*.description'=>'nullable|max:255',
            'transaction.detail.*.quantity'=>'required|integer|min:1|max:10000',
            'transaction.detail.*.unit_id'=>'required|integer|min:1',
            'transaction.detail.*.unit_price'=>'required',
            'transaction.detail.*.discount'=>'nullable',
            'transaction.detail.*.amount'=>'required',
        ];
        
        
        $attr = [
            'transaction.customer_id'=>trans('Customer'),
            'transaction.salesman_id'=>trans('Salesman'),
            'transaction.numbering_id'=>trans('Numbering Format'),
            'transaction.term_id'=>trans('Term'),
            'transaction.trans_date'=>trans('Transaction Date'),
            'transaction.due_date'=>trans('Due Date'),
            'transaction.detail.*.product_id'=>trans('Product'),
            'transaction.detail.*.description'=>trans('Description'),
            'transaction.detail.*.quantity'=>trans('Quantity'),
            'transaction.detail.*.unit_id'=>trans('Unit'),
            'transaction.detail.*.unit_price'=>trans('Unit Price'),
            'transaction.detail.*.discount'=>trans('Discount'),
            'transaction.detail.*.amount'=>trans('Amount'),
        ];
        $validator = \Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try{
            \DB::beginTransaction();
            
            $sales->trans_date = fdate($transaction['trans_date'], 'Y-m-d');
            $sales->due_date = fdate($transaction['due_date'], 'Y-m-d');
            $sales->subtotal = parse_number($transaction['subtotal']);
            $sales->total = parse_number($transaction['total']);
            $sales->tax = parse_number($transaction['tax']);
            $sales->updated_by = user('id');

            $sales->update();
            foreach($transaction['detail'] as $i => $detail){
                $detail['sales_invoice_id'] = $sales->id;
                $detail['sequence'] = $i;
                $detail['amount'] = parse_number($detail['amount']);
                $detail['unit_price'] = parse_number($detail['unit_price']);
                $detail['discount'] = parse_number($detail['discount']);
                $detail['discount_percent'] = parse_number($detail['discount_percent']);
                $detail['tax'] = parse_number($detail['tax']);
                SalesInvoiceDetail::updateOrCreate(
                    ['sequence'=>$i, 'sales_invoice_id'=>$sales->id],
                $detail);
            }
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }
        add_log('sales_invoices', 'update', '');
        return redirect()->route('sales_invoices.view', $sales->id)->with('success', trans('Changes have been saved.'));
    }
    
    public function delete($id){
        $transaction = SalesInvoice::findOrFail($id);
        $transaction->delete();
        add_log('sales_invoices', 'delete', '');
        return redirect()->route('dcru.index', 'sales_invoices')->with('success', trans(':attr deleted.', ['attr'=>strtolower(trans('Sales invoice'))]));
    }

}
