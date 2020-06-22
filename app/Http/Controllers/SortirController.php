<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sortir;
use App\SortirMeta;
use App\SortirData;
use App\Http\Resources\SortirDataResource;
use App\Http\Resources\SortirMetaResource;
use App\Http\Resources\SortirResource;
use Auth;

class SortirController extends Controller
{
    public function getAll(){
        $company_id = Auth::user()->activeCompany()->id;
        $sortirs = Sortir::where('company_id', $company_id)->get();
        return SortirResource::collection($sortirs);
    }
    public function get($id){
        $id = decode($id);
        $sortir = Sortir::find($id);
        return new SortirResource($sortir);
    }

    public function getData(Request $request, $id){
        $did = decode($id);
        $sortir = Sortir::findOrFail($did);
        $rowCount = SortirData::where('sortir_id', $did)->max('row');
        
        $rows = array();
        for($i=1;$i<=$rowCount;$i++){
            $data = SortirData::where('sortir_id', $did)
            ->where('row', $i)->get();
            $row = array('id'=>$i, 'sortir_id'=>$id);
            foreach($data as $column){
                // dd($column->meta);
                // $row[$column->meta->field_name] = new SortirDataResource($column);
                $row[$column->meta->field_name] = $column->value;
            }
            $rows[] = $row;
        }
        // $response['meta'] = SortirMetaResource::collection($sortir->meta);
        $response['data'] = $rows;
        return $response;
    }
    public function getItemData(Request $request, $id, $row){
        $did = decode($id);
        $sortir = Sortir::findOrFail($did);
        $rowCount = SortirData::where('sortir_id', $did)->max('row');
        
        $data = SortirData::where('sortir_id', $did)
        ->where('row', $row)->get();
        $row = array('id'=>$row, 'sortir_id'=>$id);
        foreach($data as $column){
            $row[$column->meta->field_name] = $column->value;
        }
        $response['data'] = $row;
        return $response;
    }

    public function create(Request $request){
        $company_id = Auth::user()->activeCompany()->id;
        validate($request->all(), [
            'display_name'=>'required|max:64',
            'description'=>'max:64',
        ]);
        $name = \Str::slug($request->display_name, '_');
        try{
            \DB::beginTransaction();
            $sortir = Sortir::updateOrCreate(['name'=>$name, 'company_id'=>$company_id],[
                'name'=>$name, 'company_id'=>$company_id,
                'display_name'=>$request->display_name,
                'description'=>$request->description,
            ]);
            $field_display_name = 'Kode';
            $field_name = \Str::slug($field_display_name, '_');
            $field_type = 'string';
            SortirMeta::updateOrCreate([
                'sortir_id'=>$sortir->id, 'field_name'=>$field_name
            ],[
                'sortir_id'=>$sortir->id, 'field_name'=>$field_name,
                'field_display_name'=>$field_display_name,
                'field_type'=>$field_type, 'is_unique'=>true
            ]);
            $field_display_name = 'Nama';
            $field_name = \Str::slug($field_display_name, '_');
            SortirMeta::updateOrCreate([
                'sortir_id'=>$sortir->id, 'field_name'=>$field_name
            ],[
                'sortir_id'=>$sortir->id, 'field_name'=>$field_name,
                'field_display_name'=>$field_display_name,
                'field_type'=>$field_type, 'is_unique'=>true
            ]);

            \DB::commit();
        
        }catch(Exception $e){
            \DB::rollback();
        }
        return new SortirResource($sortir);
    }
    public function update(Request $request, $id){
        $id = decode($id);
        $sortir = Sortir::findOrFail($id);
        validate($request->all(), [
            'display_name'=>'required|max:64',
            'description'=>'max:64',
        ]);

        $sortir->display_name = $request->display_name;
        $sortir->description = $request->description;
        $sortir->save();
        return new SortirResource($sortir);
    }
    public function updateData(Request $request, $id, $row){
        $id = decode($id);
        $sortir = Sortir::findOrFail($id);
        try{
            \DB::beginTransaction();
            foreach($request->all() as $key => $value){
                $meta = SortirMeta::where('sortir_id', $id)->where('field_name', $key)->first();
                if($meta!=null){
                    SortirData::where('row', $row)->where('sortir_id', $id)->where('sortir_meta_id', $meta->id)->update(['value'=>$value]);
                }
            }    
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }
        return new SortirResource($sortir);
    }

    public function createData(Request $request, $id){
        $company_id = Auth::user()->activeCompany()->id;
        $id = decode($id);
        $sortir = Sortir::findOrFail($id);
        $row = SortirData::where('sortir_id', $id)->max('row')+1;
        
        try{
            \DB::beginTransaction();
            $data = array('id'=>$row, 'sortir_id'=>$sortir->id);
            foreach($request->all() as $key => $value){
                $meta = SortirMeta::where('sortir_id', $id)->where('field_name', $key)->first();
                if($meta!=null){
                    SortirData::create([
                        'row'=>$row, 'sortir_id'=>$id,
                        'sortir_meta_id'=>$meta->id,'value'=>$value
                    ]);
                    $data[$key] = $value;
                }
            }    
            \DB::commit();        
        }catch(Exception $e){
            \DB::rollback();
        }
        return array('data'=>$data);
    }
    public function deleteData(Request $request, $id, $row){
        $id = decode($id);
        $sortir = Sortir::findOrFail($id);
        SortirData::where('row', $row)->where('sortir_id', $id)->delete();
        return response()->json(null, 204);
    }
}
