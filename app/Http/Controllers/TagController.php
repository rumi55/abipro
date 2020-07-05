<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Tag;
use App\JournalDetail;
use Auth;
use DB;

class TagController extends Controller
{
    // public function getAll(){
    //     $company_id = Auth::user()->activeCompany()->id;
    //     $tags = JournalDetail::where('company_id', $company_id)
    //     ->select('tags')
    //     ->get();    
    //     return response()->json(['data'=>$tags]);
    // }
    public function getAll(){
        $company_id = Auth::user()->activeCompany()->id;
        $tags = DB::table('journal_details')
        ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
        ->where('company_id', $company_id)
        ->whereNotNull('journal_details.tags')
        ->where('journal_details.tags', '<>', '')
        ->select('journal_details.tags')
        ->distinct()
        ->get();
        $data = array();
        foreach($tags as $tag){
            $ex = explode(',',$tag->tags);
            $data = array_merge($data, $ex);
        }
        $data = array_unique($data);
        $tags = [];
        foreach($data as $i=>$dt){
            $tags[] = $dt;
        }
        return response()->json(['data'=>$tags]);
    }
    public function getAll2(){
        $company_id = Auth::user()->activeCompany()->id;
        $tags = Tag::where('company_id', $company_id)->get();
        $data = array();
        foreach($tags as $tag){
            $data[$tag->group]['group'] = $tag->group;
            $data[$tag->group]['tags'][] = new TagResource($tag);
        }
        $tags = array();
        foreach($data as $dt){
            $tags[] = $dt;
        }

        return response()->json(['data'=>$tags]);
    }
    public function get(Request $request, $id){
        $id = decode($id);
        $company_id = Auth::user()->activeCompany()->id;
        $tag = Tag::where('company_id', $company_id)->where('id', $id)->first();
        return new TagResource($tag);
    }

    public function index(){
        $data = dcru_dt('sortirs', 'dtables');
        return view('company.tag.index', $data);
    }

    public function create(){
        $model = new Tag;
        $mode = 'create';
        return view('company.tag.form', compact('model', 'mode'));
    }
    public function duplicate($id){
        $model = Tag::findOrFail($id);
        $mode = 'create';
        return view('company.tag.form', compact('model', 'mode'));
    }
    public function edit($id){
        $model = Tag::findOrFail($id);
        $mode = 'edit';
        return view('company.tag.form', compact('model', 'mode'));
    }
    public function save(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        
        $tag = Tag::updateOrCreate([
            'company_id'=>$company_id,
            'group'=>$request->group,
            'tag'=>$request->item_name,
            'item_name'=>$request->item_name,
            'item_id'=>$request->item_id,
        ],
        [ 
            'company_id'=>$company_id,
            'group'=>$request->group,
            'tag'=>$request->item_name,
            'item_name'=>$request->item_name,
            'item_id'=>$request->item_id,
            'created_by'=>$user->id
        ]);
        add_log('tags', 'create', '');
        return redirect()->route('tags.index')->with('success', 'Sortir baru berhasil ditambahkan.');
    }
    public function update(Request $request, $id){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        
        $tag = Tag::findOrFail($id);
        $tag->tag=$request->item_name;
        $tag->item_id=$request->item_id;
        $tag->item_name=$request->item_name;
        $tag->updated_by=$user->id;
        $tag->save();
        add_log('tags', 'update', '');
        return redirect()->route('tags.index')->with('success', 'Perubahan sortir telah disimpan.');
    }
    public function updateGroup(Request $request){
        $user = Auth::user();
        $company_id = $user->activeCompany()->id;
        validate($request->all(), [
            'group_new'=>'required|max:64',
            'group_old'=>'required|max:64',
        ]);
        $group_old = $request->group_old;
        $group_new = $request->group_new;
        $tags = Tag::where('group', $group_old);
        $tags->update(['group'=>$group_new]);
        $tags = $tags->get();

        return TagResource::collection($tags);
    }
    public function delete(Request $request, $id){
        $tag = Tag::findOrFail($id);
        $tag->delete();
        add_log('tags', 'delete', '');
        return redirect()->route('tags.index')->with('success', 'Sortir telah dihapus.');
    }

    public function groups(){
        $company_id = Auth::user()->activeCompany()->id;
        return DB::table('tags')->where('company_id', $company_id)
        ->select('group')->distinct()->orderBy('group')->pluck('group');
    }
}
