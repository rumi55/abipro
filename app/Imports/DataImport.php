<?php

namespace App\Imports;

use App\Account;
use App\Balance;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToCollection, WithHeadingRow
{
    private $company_id;
    private $company;
    private $user_id;
    private $columns;
    private $main_columns;
    private $table;
    public function __construct(int $company_id, int $user_id, $table, $main_columns, $columns) 
    {
        $this->company_id = $company_id;
        $this->company = \App\Company::find($company_id);
        $this->user_id = $user_id;
        $this->columns = $columns;
        $this->main_columns = $main_columns;
        $this->table = $table;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        // try{
        //     \DB::beginTransaction();        
            foreach($rows as $row){
                // dd($row );
                $main = array('company_id'=>$this->company_id); 
                $value = array('created_by'=>$this->user_id); 
                foreach($this->main_columns as $key=>$column){
                    $main[$key] = $row[$column];
                }
                foreach($this->columns as $key=>$column){
                    $value[$key] = $row[$column];
                }
                \DB::table($this->table)->updateOrInsert(
                    $main, $value
                );
            }
        // }catch(Exception $e){
        //     \DB::rollback();
        // }
    }
}