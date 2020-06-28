<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 
        'industry', 
        'address', 
        'shipping_address',
        'phone',
        'email',
        'fax',
        'website',
        'tax_no',
        'accounting_period',
        'accounting_start_date',
        'owner_id',
        'company_type_id'
    ];

    public function owner(){
        return $this->belongsTo('App\User', 'owner_id', 'id');
    }

    public function users(){
        return $this->hasManyThrough('App\CompanyUser', 'App\UserGroup');    
    }

    public function getPeriod($year=null, $month=null){
        if($year==null && $month==null){
            $year = fdate($this->created_at, 'Y');
            $month = fdate($this->created_at, 'm');
        }
        $month_start = $this->accounting_period<10?'0'.$this->accounting_period:$this->accounting_period;//bulan
        $month = $month<$month_start?$month_start:$month;
        $year_start = ($month>=$month_start)?$year:($year-1);
        $start_month = $year.'-'.$month_start;//menentukan periode awal akuntansi dan periode akhir

        $start_period = $start_month.'-01';//awal periode akuntansi
        $end_period = \Carbon\Carbon::parse($start_period)->addYears()->subDay()->format('Y-m-d');
        $last_period = \Carbon\Carbon::parse($start_period)->subDay()->format('Y-m-d');
        return array($last_period, $start_period, $end_period);
    }
    public function getAllPeriod($year=null, $format='Y-m'){
        $year = $year??date('Y');
        $period = [];
        $i = $this->accounting_period;
        $cperiod = count($period);
        while($cperiod<12){
            $p = fdate($year.'-'.($i++), $format);
            $period[] = $p;
            if($i==13){
                $i=1;
                $year++;
            }
            $cperiod = count($period);
        }
        return $period;
    }
}