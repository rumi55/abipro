<?php
if (! function_exists('dcru_config')) {
    function dcru_config($name, $type=null) {
        $path = base_path("resources/dcru/$name.json");
        if(!file_exists($path)){
            return abort(404);
        }
        $jsonString = file_get_contents($path);
        $config = json_decode($jsonString, true);
        if($type==null){
            return $config;
        }else{
            if(!array_key_exists($type, $config)){
                return abort(404);
            }
            return $config[$type];
        }
    }
}
if (! function_exists('dcru_object')) {
    function dcru_object($config, $field) {
        if(empty($config[$field])){
            abort(404);
        }
        if(is_array($config[$field])){
            return (object) $config[$field];
        }else{
            return $config[$field];
        }
    }
}
if (! function_exists('dcru_query')) {
    function dcru_query($query) {
        if(empty($query['table'])){
            return null;
        }
        
        $db = DB::table($query['table']);
        if(!empty($query['joins'])){
            foreach($query['joins'] as $join){
                if(!(empty($join['join']) && empty($join['table']) && empty($query['on']))){
                    list($t1, $t2) = explode('=', $join['on']);
                    if($join['join']=='left'){
                        $db = $db->leftJoin($join['table'],$t1, '=', $t2);
                    }else if($join['join']=='right'){
                        $db = $db->rightJoin($join['table'],$t1, '=', $t2);
                    }else if($join['join']=='cross'){
                        $db = $db->crossJoin($join['table'],$t1, '=', $t2);
                    }else{
                        $db = $db->join($join['table'],$t1, '=', $t2);
                    }
                }
            }
        }
        if(!empty($query['select'])){
            if(is_array($query['select'])){
                foreach($query['select'] as $i => $select){
                    if($i==0){
                        $db = $db->select($select);
                    }else{
                        $db = $db->addSelect($select);
                    }
                }
            }else{
                $db = $db->selectRaw($query['select']);
            }
            
        }
        if(!empty($query['where'])){
            $condition = $query['where']['condition'];
            if(!empty($query['where']['params'])){
                $params =  $query['where']['params'];
                foreach($params as $param){
                    $replaceWith = $param;
                    $strex = explode(':',$param);
                    if(count($strex)==2){
                        if($strex[0]=='user'){
                            $replaceWith = user($strex[1]);
                        }else if($strex[0]=='params'){
                            $route = Route::current();
                            $parameters = $route->parameters();
                            $replaceWith = $parameters[$strex[1]];
                        }else if($strex[0]=='request'){
                            $replaceWith = request($strex[1]);
                        }
                    }
                    $findStr = '?';
                    $pos = strpos($condition, $findStr);
                    if ($pos !== false) {
                        $condition = substr_replace($condition, $replaceWith, $pos, strlen($findStr));
                    }
                }
            }
            $db = $db->whereRaw($condition);
            
        }
        if(!empty($query['group'])){
            $db = $db->groupBy($query['group']);
        }
        if(!empty($query['order'])){
            $order = $query['order'];
            if(is_array($order)){
                foreach($order as $or){
                    $or = explode(' ', $or);
                    $col = $or[0];
                    $sort = count($or)==2?$or[1]:'asc';
                    $db = $db->orderBy($col, $sort);
                }
            }else{
                $db = $db->orderByRaw($order);
            }
        }
        if(!empty($query['having'])){
            $db = $db->having($query['order']);
        }
        
        return $db;
    }
}
/**
 * Modify unique rule 
 */
if (! function_exists('dcru_rules')) {
    function dcru_rules($rules, $id=null){
        $rules = explode('|', $rules);
        $r = '';
        foreach($rules as $i=> $rule){
            if(strpos($rule, 'unique:')===false){
                $r.=$rule;
            }else{
                $r.=$rule.(empty($id)?'':','.$id);
            }
            if($i<count($rules)-1){
                $r.='|';
            }
        }
        return $r;
    }
}

if (!function_exists('dcru_digit')) {
    function dcru_digit($id, $len)
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
if (!function_exists('dcru_gencode')) {
    function dcru_gencode($id, $pattern)
    {
        if(strpos($pattern, '{Y}')){
            $pattern = str_replace('{Y}',date('Y'), $pattern);
        }
        if(strpos($pattern, '{y}')){
            $pattern = str_replace('{y}',date('y'), $pattern);
        }
        if(strpos($pattern, '{M}')){
            $pattern = str_replace('{M}',date('M'), $pattern);
        }
        //romawi bulan
        if(strpos($pattern, '{mr}')){
            $rum = ['I','II','III','IV', 'V', 'VI','VII', 'VIII', 'IX', 'X','XI','XII'];
            $pattern = str_replace('{mr}',$rum[date('m')-1], $pattern);
        }
        if(strpos($pattern, '{m}')){
            $pattern = str_replace('{m}',date('m'), $pattern);
        }
        if(strpos($pattern, '{d}')){
            $pattern = str_replace('{d}',date('d'), $pattern);
        }
        if(strpos($pattern, '{id}')){
            $pattern = str_replace('{id}',dcru_digit($id, 4), $pattern);
        }
        return $pattern;
    }
}

if (!function_exists('dcru_dt')) {
    function dcru_dt($name, $dtname){
        $dtables = dcru_config($name, $dtname);
        $dt_title = $dtables['title'];
        $dtname = isset($dtables['dtname'])?$dtables['dtname']:$dtname;
        $columns = $dtables['columns'];
        $filter = isset($dtables['filter'])?$dtables['filter']:[];
        $bulk_actions = isset($dtables['bulk_actions'])?$dtables['bulk_actions']:array();
        $dtcolumns = [];
        foreach($columns as $column){
            $dt['data']     = isset($column['data'])?$column['data']:'';
            $dt['name']     = isset($column['name'])?$column['name']:'';
            $dt['title']    = isset($column['title'])?$column['title']:'';
            $dt['type'] = $column['type'];
            if(isset($column['class'])){
                $dt['class'] = $column['class'];
            }
            $dt['searchable'] = isset($column['searchable'])?$column['searchable']:true;
            $dt['orderable'] = isset($column['orderable'])?$column['orderable']:true;
            $dt['visible'] = isset($column['visible'])?$column['visible']:true;
            $dtcolumns[]=$dt;
        }
        return [
            'dt_title'=>$dt_title, 
            'columns'=>$columns,
            'dtname'=>$dtname, 
            'name'=>$name, 
            'dtcolumns'=>$dtcolumns, 
            'bulk_actions'=>$bulk_actions,
            'filter'=>$filter
        ];
    }
}
if (!function_exists('dcru_hiddenval')) {
    function dcru_hiddenval($val){
        if(strpos($val,'session:')==0){
            $session = substr($val, 8);
            $session = explode('|', $session);
            if(count($session)==2 && $session[0]=='user'){
                return user($session[1]);
            }
        }
        return $val;
    }
}