<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\ProductResource;
use App\Exceptions\ApiValidationException;
use App\Product;
use App\Counter;
use App\Numbering;
use App\ProductCategory;
use App\ProductUnit;
use Str;
use Auth;
use Validator;

class ProductController extends Controller
{
    
    public function view($id){
        $product = Product::findOrFail($id);
        return view('product.view', compact('product'));
    }
    
    public function create(Request $request){
        $company_id = company('id');
        $product = new Product;
        $mode ='create';
        $categories = ProductCategory::where('company_id', $company_id)->get();
        $units = ProductUnit::where('company_id', $company_id)->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', \App\TransactionType::PRODUCT)->get();
        return view('product.form', compact('mode','product', 'categories', 'numberings', 'units'));
    }
    public function duplicate(Request $request, $id){
        $company_id = company('id');
        $product = Product::findOrFail($id);
        $product->image = null;
        $product->custom_id = null;
        $product->numbering_id = null;
        $mode ='create';
        $categories = ProductCategory::where('company_id', $company_id)->get();
        $units = ProductUnit::where('company_id', $company_id)->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', \App\TransactionType::PRODUCT)->get();
        return view('product.form', compact('mode','product', 'categories', 'numberings', 'units'));
    }
    public function edit(Request $request, $id){
        $company_id = company('id');
        $product = Product::findOrFail($id);
        $mode ='edit';
        $categories = ProductCategory::where('company_id', $company_id)->get();
        $units = ProductUnit::where('company_id', $company_id)->get();
        $numberings = \App\Numbering::where('company_id', $company_id)
        ->where('transaction_type_id', \App\TransactionType::PRODUCT)->get();
        return view('product.form', compact('mode','product', 'categories', 'numberings', 'units'));
    }
    public function save(Request $request){
        $user = Auth::user();
        $company = $user->activeCompany();
        $data = $request->all();
        $data['buy_price'] = parse_number($data['buy_price']);
        $data['sale_price'] = parse_number($data['sale_price']);
        $rules = [
            'custom_id' => 'nullable|max:16|unique:products,custom_id,NULL,id,company_id,'.$company->id,
            'name' => 'required|max:128',
            'image' => 'nullable|image|mimes:jpeg,bmp,png,jpg|max:256',
            'description' => 'max:128'
        ];
        if(empty($data['numbering_id'])){
            $rules['custom_id'] = 'required|max:16|unique:products,custom_id,NULL,id,company_id,'.$company->id;                
        }else{
            $data['custom_id']=null;
        }
        $attr = [
            'custom_id' => trans('ID'),
            'name' => trans('Product Name'),
            'description' => trans('Description'),
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $filename = Str::slug('product_'.$request->name.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('image', $filename, 'public/comp_'.$company->id.'/products');
        $data['image'] = $filename;
        $data['company_id'] = $company->id;
        $data['created_by'] = user('id');
        

        try{
            \DB::beginTransaction();
            if(!empty($request->numbering_id)){
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
                    $exists = Product::where('custom_id', $custom_id)->where('company_id', $company->id)->exists(); 
                    
                    if($exists==false){
                        $data['custom_id'] = $custom_id;
                        $counter->save();
                        $check = false;
                    }
                }while($check);                
            }
            $product = Product::create($data);
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }
        add_log('products', 'create', '');
        return redirect()->route('dcru.index', 'products')->with('success', \Str::ucfirst(trans('New :attr has been created.', ['attr'=>trans('Product')])));
    }
    
    public function update(Request $request, $id){
        $product = Product::findOrFail($id);
        $user = Auth::user();
        $company = $user->activeCompany();
        $data = $request->all();
        $rules = [
            'name' => 'nullable|max:16|unique:products,name,'.$id.',id,company_id,'.$company->id,
            'description' => 'nullable|max:128',
            'image' => 'nullable|image|mimes:jpeg,bmp,png,jpg|max:256'
        ];
        $attr = [
            'name' => trans('Product Name'),
            'description' => trans('Description'),
            'image' => trans('Product Image'),
        ];
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $filename = Str::slug('product_'.$request->name.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('image', $filename, 'public/comp_'.$company->id.'/products');
        
        $product->name = $request->name;
        $product->description = $request->description;
        $product->product_category_id = $request->product_category_id;
        $product->unit_id = $request->unit_id;
        $product->custom_id = $request->custom_id;
        $product->buy_price = parse_number($request->buy_price);
        $product->sale_price = parse_number($request->sale_price);
        $product->updated_by = $user->id;
        if(!empty($filename)){
            $old_photo = $product->image;
            $product->image = $filename;    
        }
        $product->save();
        if(!empty($old_photo)){
            \Storage::delete($old_photo);    
        }
        add_log('products', 'edit', '');
        return redirect()->route('dcru.index', 'products')->with('success', \Str::ucfirst(trans('Changes have been saved.')));
    }
    public function upload(Request $request, $id){
        $decoid = decode($id);
        $product = Product::findOrFail($decoid);
        
        $filename = Str::slug('product_'.$request->name.'_'.date('Y m d H i s').' '.time(),'_');
        $filename = upload_file('image', $filename, 'public/products');

        $product->image = $filename;
        $product->save();
        return new ProductResource($product);
    }
    
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        add_log('products', 'delete', '');
        return redirect()->route('dcru.index', 'products')->with('success', \Str::ucfirst(trans('Item has been deleted.')));
    }
    public function deleteImage($id)
    {
        $id = decode($id);
        $product = Product::findOrFail($id);
        $product->image = null;
        $product->save();
        return response()->json(null, 204);
    }
}
