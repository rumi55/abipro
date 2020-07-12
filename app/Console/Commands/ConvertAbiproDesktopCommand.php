<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertAbiproDesktopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $src = storage_path("/app/public/dbf/comp_23/$name.dbf");
        $this->info('Importing from '.$src);
        $columns = $this->columnMappings();
        $data = $this->readdbf($src, $columns[$name]);
        $data = $this->$name(23, $data);
        $this->info(json_encode($data));
    }

    public function readdbf($dbf_file,$columns){
        $dbf = dbase_open($dbf_file, 0);
        $column_info = dbase_get_header_info($dbf);
        $num_rec = dbase_numrecords($dbf);
        $num_fields = dbase_numfields($dbf);
        $data = array();
        for($i=1;$i<=$num_rec;$i++){
            $dbf_row = dbase_get_record_with_names($dbf,$i);
            $row = array();
            $empty = '';
            foreach ($dbf_row as $key => $val){
                if ($key == 'deleted'){ continue; }
                if(array_key_exists($key, $columns)){
                    $row[$columns[$key]] = trim($val);
                    $empty.=trim($val);
                }
            }
            if(!(empty($empty))){
                $data[] = $row;
            }
        }
        return $data;
    }
    public function columnMappings(){
        return array(
            'gltype'=>[
                'F_KODE'=>'account_no', 'F_NAMA'=>'account_name'
            ],
            'glnama'=>[
                'KODE'=>'account_no', 'NAMA'=>'account_name', 'TYPE'=>'account_type_no'
            ],
            'glmast'=>[
                'GL_KODE'=>'account_no', 'GL_NAMA'=>'account_name', 'GL_TYPE'=>'account_type_no', 'GL_DEPT'=>'department', 
                'GL_AWAL'=>'opening_balance'
            ],
            'gldept'=>[
                'KODE'=>'custom_id', 'NAMA'=>'name'
            ],
        );
    }

    public function gltype($company_id, $data){
        $newdata = array();
        foreach($data as $dt){
            $dt['company_id']=$company_id;
            $dt['tree_level']=0;
            $dt['has_children']=0;
            $newdata[]=$dt;
        }
        // \DB::table('accounts')->where('company_id', $company_id)->delete();
        \DB::table('accounts')->insert($newdata);
        return $newdata;
    }
}
