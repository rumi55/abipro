<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use DB;
use Str;
use Storage;

class DcruController extends Controller
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
    
    public function index($name){
        $data = dcru_dt($name, 'dtables');
        $config = dcru_config($name);
        $data['title'] = isset($config['title'])?$config['title']:'';
        
        $data['actions'] = isset($config['actions'])?$config['actions']:[];
        $view= 'dcru.index';
        return view($view, $data);
    }
    public function grid($name){
        $title = dcru_config($name, 'title');
        $dtables = dcru_config($name, 'dtables');
        $section_title = $dtables['title'];
        $columns = $dtables['columns'];
        $dtcolumns = [];
        foreach($columns as $column){
            $dt['data'] = $column['data'];
            $dt['name'] = $column['name'];
            $dt['title'] = $column['title'];
            $dt['type'] = $column['type'];
            $dt['searchable'] = isset($column['searchable'])?$column['searchable']:true;
            $dt['orderable'] = isset($column['orderable'])?$column['orderable']:true;
            $dt['visible'] = isset($column['visible'])?$column['visible']:true;
            $dtcolumns[]=$dt;
        }
        $view= 'dcru.grid';
        return view($view, compact('title', 'section_title', 'columns','name', 'dtcolumns', 'group'));
    }
    public function dt(Request $request, $name){
        $columns  = $request->query('columns');
        $order  = $request->query('order');

        $search  = $request->query('search');
        $dtname = $request->query('dtname', 'dtables');
        $dtables = dcru_config($name, $dtname);
        $dtcolumns = $dtables['columns'];

        // dd($request->all());

        $draw = $request->query('draw', '0');
        $length = $request->query('length','10');
        $start = $request->query('start', '0');
        $search = $request->query('search');
        $filter = $request->query('filter',[]);
        $table_name = $dtables['query']['table'];
        // DB::enableQueryLog();
        $query = dcru_query($dtables['query']);

        if(count($filter)>0){
            $query = $query->where(function($q)use($filter){
                foreach($filter as $fil){
                    
                    if(isset($fil['value']['start']) && isset($fil['value']['end'])){
                        $start = $fil['value']['start'];
                        $end = $fil['value']['end'];
                        if(strpos($start, '.')>-1){
                            $start = parse_number($start);
                            $end = parse_number($end);
                        }else
                        if(strpos($start, '-')>-1){
                            $start = fdate($start, 'Y-m-d');
                            $end = fdate($end, 'Y-m-d');
                        }
                        if(!empty($start)){
                            $q = $q->where($fil['name'], '>=', $start);
                        }
                        if(!empty($end)){
                            $q = $q->where($fil['name'], '<=', $end);
                        }
                    }else if(is_array($fil['value'])){
                        if(count($fil['value'])>0){
                            $q = $q->whereIn($fil['name'], $fil['value']);
                        }
                    }else{
                        if(!empty($fil['value'])){
                            $q = $q->where($fil['name'], 'like', "%".$fil['value']."%");
                        }
                    }
                }
            });
            // $query->dd();
        }
        
        $order_flag = 0;
        // $query = $query->where('company_id', user('company_id'));
        $total = $query->count();
        //filter or order
        $query = $query->where(function($q)use($columns, $search){
            foreach($columns as $i =>$column){
                $column_name = $column['name'];
                if(empty($column_name)){
                    continue;
                }
            
                
                if($column['searchable']==true){
                   if(!empty($search['value'])){
                       $q->orWhere($column_name,'like', '%'.$search['value'].'%');
                   }  
                   if(!empty($column['search']['value'])){
                       $q->orWhere($column_name,'like', '%'.$column['search']['value'].'%');
                    }  
                }
            }
        });

        foreach($columns as $i =>$column){
            $column_name = $column['name'];
            if(empty($column_name)){
                continue;
            }
            
            if($column['orderable']==true){
                foreach($order as $ord){
                    if($ord['column']==$i){
                        $query = $query->orderBy($column_name, $ord['dir']);
                        $order_flag++;
                   }
               }
            }
        }
        if($order_flag==0){
            // $query = $query->orderByRaw("$table_name.updated_at - $table_name.created_at DESC");
            $query = $query->orderByRaw("$table_name.created_at DESC");
        }
        
        $total_filtered = $query->count();
        if($length>-1){
            $query = $query->offset($start)->limit($length);
        }
        // dd(DB::getQueryLog());
        $data = $query->get();
        $dt['draw'] = $draw;
        $dt['recordsTotal'] = $total;
        $dt['recordsFiltered'] = $total_filtered;
        $rows = array();
        foreach($data as $dat){
            $d = clone $dat;
            foreach($dtcolumns as $column){
                $colname = $column['data'];
                if($column['type']=='hdate'){
                    $d->$colname = '<span title="'.fdate($d->$colname).'">'.hdate($d->$colname).'</span>';
                }
                if($column['type']=='hdatetime'){
                    $d->$colname = '<span title="'.fdatetime($d->$colname).'">'.hdate($d->$colname).'</span>';
                }
                if($column['type']=='date'){
                    $d->$colname = fdate($d->$colname);
                }
                if($column['type']=='datetime'){
                    $d->$colname = fdatetime($d->$colname);
                }
                if($column['type']=='currency'){
                    $d->$colname = fcurrency($d->$colname);
                }
                if($column['type']=='detail'){
                    $did = $column['detail_id'];
                    if(array_key_exists('detail_id', $column)&&array_key_exists('detail_name', $column)){
                        $d->$colname = '<a href="'.asset(route('dcru.view', ['name'=>$column['detail_name'], 'id'=>$d->$did], false)).'">'.$d->$colname.'</a>';
                    }
                }
                if($column['type']=='boolean'){
                    $d->$colname = $d->$colname==1?'<i class="fas fa-check"></i>':'-';
                }
                if($column['type']=='file'){
                    if(!empty($d->$colname)){
                        $d->$colname = '<div class="btn-group text-center" role="group" aria-label="file-action">
                        <form action="'.asset(route('dcru.download'), [], false).'" method="post">
                        <a href="'.asset(url_file($d->$colname)).'" title="Lihat Berkas" target="_blank" class="btn btn-xs btn-secondary"><i class="fas fa-eye"></i></a>
                        '.csrf_field().'
                        <input type="hidden" name="file" value="'.$d->$colname.'"/>
                        <button title="Unduh Berkas" type="submit" class="btn btn-xs btn-secondary"><i class="fas fa-download"></i></button>
                        </form>
                        </div>';
                    }
                }
                if($column['type']=='image'){
                    $d->$colname = '<img style="width:150px" src="'.asset(empty($d->$colname)?'img/noimage.png':url_file($d->$colname)).'" class="img-thumbnail"/>';
                }
                if($column['type']=='badge'){
                    $badge = $column['badge'];
                    if(isset($badge[$d->$colname])){
                        $color = $badge[$d->$colname]['color'];
                        $text = $badge[$d->$colname]['text'];
                        $d->$colname = '<span class="badge badge-'.$color.'">'.$text.'</span>';
                    }
                }
                if($column['type']=='icon'){
                    $badge = $column['values'];
                    if(isset($badge[$d->$colname])){
                        $color = isset($badge[$d->$colname]['color'])?$badge[$d->$colname]['color']:'';
                        $icon = $badge[$d->$colname]['icon']??'';
                        $text = $badge[$d->$colname]['text']??'';
                        $d->$colname = '<i class="'.$icon.' '.($color!=''?'text-'.$color:'').'" title="'.$text.'"></i>';
                    }
                }
                if($column['type']=='route'){
                    if(isset($column['route']) && isset($column['route']['name'])){
                        $params = isset($column['route']['params'])?$column['route']['params']:[];
                        $rparams = [];
                        foreach($params as $key =>$param){
                            $rparams[$key] = $dat->$param;
                        }
                        $d->$colname = '<a href="'.asset(route($column['route']['name'], $rparams, false)).'">'.$d->$colname.'</a>';
                    }
                }
                if($column['type']=='menu'){
                    //default actions
                    $view = '<a href="'.asset(route('dcru.view', ['name'=>$name, 'id'=>$d->id], false)).'" class="dropdown-item"><i class="fas fa-search"></i> '.trans('Detail').'</a>';
                    $edit = '<a href="'.asset(route('dcru.edit', ['name'=>$name, 'id'=>$d->id], false)).'" class="dropdown-item"><i class="fas fa-edit"></i> '.trans('Edit').'</a>';
                    $duplicate = '<a href="'.asset(route('dcru.create.duplicate', ['name'=>$name, 'id'=>$d->id], false)).'" class="dropdown-item"><i class="fas fa-copy"></i> '.trans('Duplicate').'</a>';
                    $delete = '<a data-toggle="modal" data-target="#modal-delete"  href="'.asset(route('dcru.delete', ['name'=>$name, 'id'=>$d->id], false)).'" class="dropdown-item btn-delete text-danger"><i class="fas fa-trash"></i> '.trans('Delete').'</a>';
                    $delete = '
                    <form method="POST" action="'.asset(route('dcru.delete', ['name'=>$name, 'id'=>$d->id], false)).'">
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <button type="button" class="dropdown-item btn-delete text-danger"><i class="fas fa-trash"></i> '.trans('Delete').'</button>
                    </form>
                    ';
                    $menu_items = '';
                    $count_items = 0;
                    $visible = true;
                    if(array_key_exists('items', $column)){
                        $items = $column['items'];
                        foreach($items as $item){
                            $visible = true;
                            if(isset($item['visible'])){
                                $vis = explode('==', $item['visible']);
                                if(count($vis)==2){
                                    $o = $vis[0];
                                    $p = $vis[1];
                                    $visible = $dat->$o==$p;
                                }
                            }
                            if(array_key_exists('type', $item)){
                                if($item['type']=='view'){
                                    $menu_items.=has_action($name,'view')&&$visible?$view:'';                        
                                    $count_items+=has_action($name,'view')&&$visible?1:0;                        
                                }else if($item['type']=='edit'){
                                    $menu_items.=has_action($name,'edit')&&$visible?$edit:'';                        
                                    $count_items+=has_action($name,'edit')&&$visible?1:0;                        
                                }else if($item['type']=='duplicate'){
                                    $menu_items.=has_action($name,'create')&&$visible?$duplicate:'';                        
                                    $count_items+=has_action($name,'create')&&$visible?1:0;                        
                                }else if($item['type']=='delete'){
                                    $menu_items.=has_action($name, 'delete')&&$visible?$delete:'';                        
                                    $count_items+=has_action($name,'delete')&&$visible?1:0;                        
                                }
                            }else{
                                $label = trans($item['label']);
                                $route = $item['route'];
                                $params = ['id'=>$d->id];
                                $icon = '';
                                if(array_key_exists('icon', $item)){
                                    $icon = '<i class="'.$item['icon'].'"></i>';
                                }
                                if(array_key_exists('params', $route)){
                                    $params = array_merge($params, $route['params']);
                                }
                                $routes = explode('.',$route['name']);
                                if(array_key_exists('method', $route)){
                                    if($route['method']=='POST'){
                                        $menu_items .= has_action($routes[0], $routes[1]) && $visible?'
                                        <form method="POST" action="'.asset(route($route['name'], $params, false)).'">
                                        '.csrf_field().'
                                        <button type="submit" class="dropdown-item">'.$icon.' '.$label.'</button>
                                        </form>
                                        ':'';     
                                    }
                                }else{
                                    $menu_items .= has_action($routes[0], $routes[1]) && $visible?'<a href="'.asset(route($route['name'], $params, false)).'" class="dropdown-item">'.$icon.' '.$label.'</a>':'';            
                                }
                            }    
                        }
                    }else{//if no items, add all default actions
                        $menu_items.=has_action($name,'view')&&$visible?$view:'';                        
                        $menu_items.=has_action($name, 'edit')&&$visible?$edit:'';                        
                        $menu_items.=has_action($name, 'create')&&$visible?$duplicate:'';                        
                        $menu_items.=has_action($name, 'delete')&&$visible?$delete:'';                        
                        $count_items+=has_action($name, 'view')&&$visible?1:0;                        
                        $count_items+=has_action($name, 'edit')&&$visible?1:0;                        
                        $count_items+=has_action($name, 'view')&&$visible?1:0;                        
                        $count_items+=has_action($name, 'view')&&$visible?1:0;                        
                        
                    }
                    $menu = '';
                    if($count_items==0){
                        $menu = $menu_items;
                    }else{
                        $menu = '
                        <button type="button" class="btn btn-tool" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i></button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">';    
                        $menu .=$menu_items;
                        $menu .='</div>';
                    }
                    $d->$colname = $menu;
                }
                if($column['type']=='checkbox'){
                    $d->$colname = '<input type="checkbox" class="check-row" value="'.$d->id.'" >';
                }
                $d->$colname=empty($d->$colname)&&$column['type']!='menu'?'-':$d->$colname;
            }
            $rows[] = $d;
        }
        $dt['data'] = $rows;
        return $dt;
    }

    public function view($name, $id){
        $title = dcru_config($name, 'title');
        $view = dcru_config($name, 'view');
        $section_title =  $view['title'];
        $fields = $view['fields'];
        $table = $view['query']['table'];
        $data = dcru_query($view['query'])->where("$table.id", $id)->first();
        if(empty($data)){
            abort(404);
        }
        return view('dcru.view', compact('name', 'title', 'section_title', 'fields','data'));
    }

    public function create($name){
        $title = dcru_config($name, 'title');
        $form = dcru_config($name, 'form');
        $section_title =  $form['title'];
        $fields = $form['fields'];
        $mode = 'create';
        $view = isset($form['view'])?$form['view']:'dcru.create';
        return view($view, compact('title', 'section_title', 'fields',  'name', 'mode'));
    }
    public function edit($name, $id){
        $title = dcru_config($name, 'title');
        $form = dcru_config($name, 'form');
        $section_title =  $form['title'];
        $fields = $form['fields'];
        $table = $form['table'];
        $data = DB::table($table)->find($id);
        // dd($data);
        if($data==null){
            abort(404);
        }
        $mode = 'edit';
        $view = isset($form['view'])?$form['view']:'dcru.create';
        return view($view, compact('title', 'section_title', 'fields','data',  'name', 'mode'));
    }
    public function duplicate($name, $id){
        $title = dcru_config($name, 'title');
        $form = dcru_config($name, 'form');
        $section_title =  $form['title'];
        $fields = $form['fields'];
        $table = $form['table'];
        $data = DB::table($table)->find($id);
        // dd($data);
        if($data==null){
            abort(404);
        }
        $mode = 'create';
        $view = isset($form['view'])?$form['view']:'dcru.create';
        return view($view, compact('title', 'section_title', 'fields','data', 'name', 'mode', 'group'));
    }

    public function save(Request $request, $name){
        $mode = $request->input('_mode');
        $id=$request->input('id');

        $form = dcru_config($name, 'form');
        $view = dcru_config($name, 'view');
        
        
        $title = $form['title'];
        $fields = $form['fields'];
        $table = $form['table'];
        $rules = [];
        $attr = [];
        $values = [];    
        $files = [];//to store uploaded filename
        $generate = [];
        $now = date('Y-m-d H:i:s');
        
        foreach($fields as $field){
            $field_name = $field['name'];
            $field_type = $field['type'];
            $attr[$field_name] = $field['label'];
            $value = $request->input($field_name);

            if(isset($field['edit']) && $field['edit']==false && $mode=='edit'){
                continue;
            }
            if($request->input('_'.$field_name.'_')!=null){
                continue;
            }
            
            if($mode=='edit' && !empty($field['uvalidation'])){
                $rules[$field_name] = dcru_rules($field['uvalidation'], $id);
            }else if(!empty($field['uvalidation'])){
                $rules[$field_name] = $field['svalidation'];
            }
            
            if($field_type=='password'){
                if(!strpos($field_name, '_confirmation')){
                    $values[$field_name] = Hash::make($value);
                }
            }else if($field_type=='file'){
                $filename = Str::slug($field_name.' '.date('Y m d H i s').' '.time(),'-');
                $filename = upload_file($field_name, $filename, 'public/files/'.$name);
                $files[] = $filename;
                $values[$field_name] = $filename;
            }else if($field_type=='image'){
                $filename = Str::slug($field_name.' '.date('Y m d H i s').' '.time(),'-');
                $filename = upload_file($field_name, $filename, 'public/images/'.$name);
                $files[] = $filename;
                $values[$field_name] = $filename;
            }else if($field_type=='boolean'){
                $values[$field_name] = empty($value)?0:1;
            }else if($field_type=='date'){
                $values[$field_name] = fdate($value, 'Y-m-d');
            }else if($field_type=='datetime'){
                $values[$field_name] = fdatetime($value, 'Y-m-d H:i:s');
            }elseif($field['type']=='generate'){
                $generate[] = $field;
            }elseif($field['type']=='multiselect'){
                $values[$field_name] = implode(',', $value);
            }else{
                $values[$field_name] = $value;
            }
        }

        $validator = Validator::make($request->all(), $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            foreach($files as $file){
                Storage::delete($file);
            }
            $params = ['name'=>$name];
            if($mode!='create'){
                $params['id'] = $request->input('id');
            }
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        if($mode=='create'){
            $values['created_at'] = $now;
            $values['created_by'] = user('id');
            $id = DB::table($table)->insertGetId($values);
            foreach($generate as $i =>$gen){
                $val = dcru_gencode($id, $gen['pattern']);
                DB::table($table)->where('id', $id)->update([$gen['name']=> $val]);
            }
            // add_log("Menambahkan $title dengan id $id");
            return redirect(asset(route('dcru.index', ['name'=>$name], false)))->with('success', $title.' baru berhasil ditambahkan.');
            if(empty($view)){
            }else{
                return redirect(asset(route('dcru.view', ['name'=>$name, 'id'=>$id], false)))->with('success', $title.' baru berhasil ditambahkan.');
            }
        }else if($mode=='edit'){
            $values['updated_at'] = $now;
            $values['updated_by'] = user('id');
            DB::table($table)->where('id', $id)->update($values);
            // add_log("Mengubah $title dengan id $id");
            if(empty($view)){
                return redirect(asset(route('dcru.index', ['name'=>$name], false)))->with('success', 'Perubahan '.$title.' berhasil disimpan.');
            }else{
                return redirect(asset(route('dcru.view', ['name'=>$name, 'id'=>$id], false)))->with('success', 'Perubahan '.$title.' berhasil disimpan.');
            }
        }else{
            abort(404);
        }
    }
    public function deleteFile(Request $request, $name){
        $form = dcru_config($name, 'form');
        $fields = $form['fields'];
        $table = $form['table'];

        $id = $request->input('id');
        $file = $request->input('file');
        $data = DB::table($table)->find($id);
        if($data==null){
            abort(404);
        }
        Storage::delete($data->$file);
        DB::table($table)->where('id', $id)
        ->update([$file => null]);
        add_log("Menghapus file");
        return redirect(route('dcru.edit', ['name'=>$name, 'id'=>$id], false))->with('success', ' File berhasil dihapus.');        
    }
    public function delete(Request $request, $name, $id){
        $form = dcru_config($name, 'form');
        $fields = $form['fields'];
        $table = $form['table'];
        $data = DB::table($table)->find($id);
        if($data==null){
            abort(404);
        }
        DB::table($table)->where('id', $id)
        ->update(['deleted_by' => user('id'), 'deleted_at'=>date('Y-m-d H:i:s')]);
        // add_log($name, 'delete',"Menghapus data $name dengan id $id");
        return redirect(route('dcru.index', ['name'=>$name],false))->with('success', 'Data berhasil dihapus.');
    }
    public function deletePermanent(Request $request, $name, $id){
        $form = dcru_config($name, 'form');
        $fields = $form['fields'];
        $table = $form['table'];
        $data = DB::table($table)->find($id);
        if($data==null){
            abort(404);
        }
        $files=[];
        foreach($fields as $field){
            $field_name = $field['name'];
            if($field['type']=='file' || $field['type']=='image'){
                $files[] = $data->$field_name;
            }
        }
        DB::table($table)->where('id', $id)->delete();
        foreach($files as $file){
            Storage::delete($file);
        }
        add_log("Menghapus $name");
        return redirect()->route('dcru.index', ['name'=>$name], false)->with('success', 'Data berhasil dihapus permanen.');
    }
    public function deleteAll(Request $request, $name){
        $form = dcru_config($name, 'form');
        $fields = $form['fields'];
        $table = $form['table'];
        $ids = $request->input('id');
        foreach($ids as $id){
            DB::table($table)->where('id', $id)->delete();
        }
        // add_log("Menghapus masal data $name");
        return redirect()->route('dcru.index', ['name'=>$name], false)->with('success', 'Data berhasil dihapus.');        
    }

    public function download(Request $request){
        $file_name = $request->input('file');
        if(empty($file_name)){
            abort(404);
        }
        return Storage::download($file_name);
    }
    
    public function testing(){
        // $name = ['Ahmad', 'Andi', 'Arif', 'Iman', 'Ian', 'Bani', 'Eko', 'Budi',
        // 'Ali', 'Fitri', 'Wulan', 'Diah', 'Imam', 'Yuli', 'Andri', 'Firman', 'Toni', 'Umar',
        // 'Faruq', 'Ruli', 'Endah', 'Eka', 'Muhammad', 'Usman', 'Fatih', 'Ikra', 'Fulan', 'Wirman',
        // 'Ramzi', 'Romzi', 'Rara', 'Alya', 'Putra', 'Putri', 'Indah', 'Mustofa', 'Fadil', 'Wira',
        // 'Hanif', 'Farid', 'Luqman', 'Imran', 'Hasan', 'Husain'
        // ];
        
        // foreach($name as $nm){
        //     $user['name'] = $nm;
        //     $user['email'] = strtolower($nm).'@sample.id';
        //     $user['password'] = Hash::make('12345678');
        //     $user['created_at'] = date('Y-m-d H:i:s');
        //     $user['created_by'] = user('id');
        //     $data[] = $user;
        // }
        // dd($data);
        echo base_path();
        // DB::table('users')->insert($data);
    }

}
