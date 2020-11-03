<?php

if (!function_exists('validate')) {
    function validate($data, $rules, $attr=array()){
        $validator = \Validator::make($data, $rules)->setAttributeNames($attr);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
}

if (!function_exists('encode')) {
    function encode($id){
        return $id;
        // $hashids = new \Hashids\Hashids(env('APP_KEY'));
        // return $hashids->encode($id);
    }
}
if (!function_exists('decode')) {
    function decode($id){
        return $id;
        // $hashids = new \Hashids\Hashids(env('APP_KEY'));
        // $decode_id = $hashids->decode($id);
        // return count($decode_id)>0?$decode_id[0]:$id;
    }
}

if (!function_exists('currency')) {
    function currency($value, $locale='id_ID')
    {
        // $fmt = numfmt_create( $locale, NumberFormatter::CURRENCY );
        // return numfmt_format_currency($fmt, $value, "IDR");
        if($value<0){
            return '('.number_format(abs($value), 2, ',','.').')';
        }
        return number_format($value, 2, ',','.');
    }
}
if (!function_exists('format_number')) {
    function format_number($value, $locale='id_ID')
    {
        // $fmt = numfmt_create( $locale, NumberFormatter::DECIMAL );
        // return numfmt_format($fmt, $value);
        return number_format($value, 2, ',','.');
    }
}


if (!function_exists('setting')) {
    function setting($name){
        return DB::table('settings')->where('name', $name)->value('value');
    }
}

if (!function_exists('company')) {
    function company($attribute){
        $company = Auth::user()->activeCompany();
        return empty($company->$attribute) && $attribute!='logo'?'-':$company->$attribute;
    }
}
//depend on company function
if (!function_exists('company_setting')) {
    function company_setting($key){
        return DB::table('company_settings')->where('key', $key)->where('company_id', company('id'))->value('value');
    }
}
if (!function_exists('user')) {
    function user($attribute){
        $user = Auth::user();
        if($attribute=='group.display_name'){
            return ($user->userGroup()!=null?$user->userGroup()->userGroup->display_name:($user->is_owner?'Owner':''));
        }

        if($attribute=='group.name'){
            return $user->userGroup()!=null?$user->userGroup()->userGroup->name:($user->is_owner?'owner':'');
        }

        if($attribute=='company_id'){
            return $user->activeCompany()!==null?$user->activeCompany()->id:null;
        }

        return $user->$attribute;
    }
}

if (!function_exists('inword_b')) {
    function inword_b($value){
        $word = array('nol', 'satu', 'dua','tiga', 'empat','lima','enam','tujuh','delapan','sembilan');
        $v = '';
        $len = strlen($value);
        for($i=0;$i<$len;$i++){
            $a = substr($value,$i,1);
            $v.= $word[$a].' ';
        }
        return $v;
    }
}

if (!function_exists('inword_a')) {
    function inword_a($value){
        $value = abs($value);
        $word = array('', 'satu', 'dua','tiga', 'empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas');
        $result = '';
        if($value<12){
            $result = ' '.$word[$value];
        } else if ($value <20) {
			$result = inword_a($value - 10). " belas";
		} else if ($value < 100) {
			$result = inword_a($value/10)." puluh". inword_a($value % 10);
		} else if ($value < 200) {
			$result = " seratus" . inword_a($value - 100);
		} else if ($value < 1000) {
			$result = inword_a($value/100) . " ratus" . inword_a($value % 100);
		} else if ($value < 2000) {
			$result = " seribu" . inword_a($value - 1000);
		} else if ($value < 1000000) {
			$result = inword_a($value/1000) . " ribu" . inword_a($value % 1000);
		} else if ($value < 1000000000) {
			$result = inword_a($value/1000000) . " juta" . inword_a($value % 1000000);
		} else if ($value < 1000000000000) {
			$result = inword_a($value/1000000000) . " milyar" . inword_a(fmod($value,1000000000));
		} else if ($value < 1000000000000000) {
			$result = inword_a($value/1000000000000) . " trilyun" . inword_a(fmod($value,1000000000000));
        }
        return $result;
    }
}
if (!function_exists('inword')) {
    function inword($value){
        $v = explode('.', $value);
        $dec = '';
        if(count($v)==2){
            if(intval($v[1])>0){
                $dec = inword_b($v[1]);
            }
        }
        return inword_a($value).($dec==''?'':' koma '.$dec);
    }
}

if (!function_exists('ndigit')) {
    function ndigit($id, $len)
    {
        $zero = '';
        for($i=1;$i<$len;$i++){
            $zero.='0';
        }
        if($id<10){
            return $zero.$id;
        }elseif($id<100 && $id>9){
            return substr($zero, 0, $len-2).$id;
        }elseif($id<1000 && $id>99){
            return substr($zero, 0, $len-3).$id;
        }elseif($id<10000 && $id>999){
            return substr($zero, 0, $len-4).$id;
        }elseif($id<100000 && $id>9999){
            return substr($zero, 0, $len-5).$id;
        }elseif($id<1000000 && $id>99999){
            return substr($zero, 0, $len-6).$id;
        }else{
            return $id;
        }
    }
}

if (!function_exists('upload_file')) {
    function upload_file($name, $filename, $file_path){
        if (Request::hasFile($name)) {
            $file = Request::file($name);
            $ext = $file->getClientOriginalExtension();
            $filesize = $file->getClientSize() / 1024;
            $filename = $filename.'.'.$ext;
            Storage::makeDirectory($file_path);
            if (Storage::putFileAs($file_path, $file, $filename)) {
                return $file_path.'/'.$filename;
            }
        }
        return null;
    }
}
if (!function_exists('save_file')) {
    function save_file($file, $filename, $file_path){
        Storage::makeDirectory($file_path);
        Storage::put($file_path.'/'.$filename, $file);
    }
}
if (!function_exists('url_file')) {
    function url_file($path){
        return Storage::url($path);
    }
}
if (!function_exists('url_image')) {
    function url_image($path){
        return asset(empty($path)?'/img/noimage.png':url_file($path));
    }
}

if (!function_exists('fdatetime')) {
    function fdatetime($date, $format='d-m-Y H:i'){
        $dt=date_create($date);
        return  date_format($dt, $format);
    }
}
if (!function_exists('fdate')) {
    function fdate($date, $format='d-m-Y'){
        if($date==null)return null;
        $date = str_replace('/','-', $date);
        $dt=date_create($date);
        return  date_format($dt, $format);
        // $date = \Carbon\Carbon::create($date);
        // return $date->isoFormat($format);
    }
}
if (!function_exists('hdate')) {
    function hdate($date, $format='d-m-Y'){
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }
}
if (!function_exists('fmonth')) {
    function fmonth($date, $format='d-m-Y'){
        return \Carbon\Carbon::parse($date)->format('F Y');
    }
}
if (!function_exists('fcurrency')) {
    function fcurrency($value){
        if(!(strpos($value, ',')==FALSE)){
            $value = parse_number($value);
        }
        if(empty($value))return '0,00';
        return number_format($value, 2, ',', '.');
    }
}
if (!function_exists('parse_number')) {
    function parse_number($value){
        if($value==null){
            return null;
        }
        $value = str_replace('.','', $value);
        return str_replace(',','.', $value);
    }
}


if (!function_exists('add_log')) {
    function add_log($action_group, $action_name, $description=null)
    {
        $data = [];
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['url'] = Request::url();
        $data['description'] = $description;
        $data['created_at'] = date('Y-m-d H:i:s');
        $user = Auth::user();
        $data['created_by'] = $user->id;
        $data['company_id'] = $user->activeCompany()->id;
        $action = DB::table('actions')->where('group', $action_group)->where('name', $action_name)->first();
        $data['action_id'] = $action!=null?$action->id:null;

        DB::table('logs')->insert($data);
    }
}
if (!function_exists('has_action')) {
    function has_action($group, $action)
    {
        $user = Auth::user();
        if($user==null){
            return false;
        }
        return$user->hasAction($group, $action);
    }
}
if (!function_exists('has_approval')) {
    function has_approval($action)
    {
        $user = Auth::user();
        if($user==null){
            return false;
        }
        return $user->isSuper() || $user->hasAction($action);
    }
}
if (!function_exists('status_approval')) {
    function status_approval($type, $id)
    {
        $last_flow = \App\ApprovalFlow::where('type', $type)->where('trans_id', $id)
        ->orderBy('id', 'desc')->first();
        if($last_flow==null){
            return 'draft';
        }
        //action_id = 1:proposed, 2:reviewed 3:accepted, 4:processed, 9:rejected

        $approval = $last_flow->approval;
        if($last_flow->action_id==1){
            return 'proposed';
        }else if($last_flow->action_id==2){
            return 'reviewed';
        }else if($last_flow->action_id==3){
            return 'accepted';
        }else if($last_flow->action_id==4){
            return 'processed';
        }else if($last_flow->action_id==9){
            return 'rejected';
        }else{
            return 'noaction';
        }
    }
}

if (!function_exists('get_route_prefix')) {
    function get_route_prefix()
    {
        $route = Route::getCurrentRoute();
        if($route!=null){
            $prefix = $route->action['prefix'];
            return str_replace('/','', $prefix);
        }
        return null;
    }
}
if (!function_exists('get_route_param')) {
    function get_route_param($param)
    {
        $params = Route::current()->parameters();
        if(array_key_exists($param, $params)){
            return $params[$param];
        }
        return null;
    }
}
if (!function_exists('is_current_uri')) {
    function is_current_uri($uri)
    {
        $ex_uri = explode('/', $uri);
        if(count($ex_uri)!=2){
            return false;
        }

        $route = Route::current();
        $params = $route->parameters();
        if(array_key_exists('group', $params) && array_key_exists('name', $params)){
            return $params['name']==$ex_uri[1] && $params['group']==$ex_uri[0];
        }
        $curr_uri = $route->uri;
        $ex_curr_uri = explode('/', $curr_uri);
        if(count($ex_curr_uri)>=2){
            return $ex_curr_uri[0]==$ex_uri[0] && $ex_curr_uri[1]==$ex_uri[1];
        }
        return false;
    }
}
if (!function_exists('cek_uri')) {
    function cek_uri($uri)
    {
        $ex_uri = explode('/', $uri);
        if(count($ex_uri)!=2){
            return false;
        }
        $route = Route::current();
        $params = $route->parameters();
        if(array_key_exists('group', $params) && array_key_exists('name', $params)){
            return $ex_uri[1]==$params['name'].'sdasd';
            return ($params['name']==$ex_uri[1]);
        }
    }
}
if (!function_exists('param')) {
    function param($param)
    {
        return request()->has($param)?request()->get($param):null;
    }
}
/** For translating field of database */
if (!function_exists('tt')) {
    function tt($model, $field)
    {
        if($model==null){
            return '-';
        }
        $field_en = $field.'_en';
        $locale = \App::getLocale();
        return $locale=='en' && !empty($model->$field_en)?$model->$field_en:$model->$field;
    }
}
if (!function_exists('notify')) {
    function notify($config)
    {
        $to = $config['url'];
        $users = $config['users'];
        $users = ($users) ?: [user('id')];
        foreach ($users as $id) {
            $a = [];
            $a['created_at'] = date('Y-m-d H:i:s');
            $a['user_id'] = $id;
            $a['message'] = $config['message'];
            $a['message_en'] = $config['message_en'];
            $a['is_read'] = 0;
            $a['url'] = $config['url'];
            DB::table('notifications')->insert($a);
        }
        return true;
    }
}
if (!function_exists('report_template')) {
    function report_template($name)
    {
        $template = DB::table('report_templates')->where('report_name', $name)->where('is_default', true)
        ->where('company_id', company('id'))->first();
        $logo = company('logo');
        $logo = empty($logo)?'/img/noimage.png':url_file(company('logo'));
        $variable = [
            '{company_logo}'=> '<img height="60px" src="'.public_path().$logo.'">',
            '{company_name}'=> company('name'),
            '{company_address}'=> company('address'),
            '{company_phone}'=> company('phone'),
            '{company_fax}'=> company('fax'),
            '{company_website}'=> company('website'),
            '{company_email}'=> company('email'),
            '{pagenum}'=>'<span class="pagenum"></span>',
            '{date}'=>date('d-m-Y'),
            '{datetime}'=>date('d-m-Y H:i'),
        ];
        if($template==null){
            return null;
        }
        $text = $template->template_content;
        foreach ($variable as $key =>$value) {
            $text = str_replace($key, $value, $text);
        }
        return $text;
    }
}

?>
