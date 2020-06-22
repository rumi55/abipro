<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = ['counter', 'period', 'last_number', 'numbering_id', 'company_id'];

    public function numbering(){
        return $this->belongsTo('App\Numbering', 'numbering_id', 'id');
    }

    public function getNumber(){
        $format = $this->numbering->format;
        $counter_digit = $this->numbering->counter_digit;
        $counter_reset = $this->numbering->counter_reset;

        if(strpos($format, '[Y]')!==false){
            $format = str_replace('[Y]',date('Y'), $format);
        }
        if(strpos($format, '[y]')!==false){
            $format = str_replace('[y]',date('y'), $format);
        }
        if(strpos($format, '[M]')!==false){
            $format = str_replace('[M]',date('M'), $format);
        }
        //romawi bulan
        if(strpos($format, '[mr]')!==false){
            $rum = ['I','II','III','IV', 'V', 'VI','VII', 'VIII', 'IX', 'X','XI','XII'];
            $format = str_replace('[mr]',$rum[date('m')-1], $format);
        }
        if(strpos($format, '[m]')!==false){
            $format = str_replace('[m]',date('m'), $format);
        }
        if(strpos($format, '[d]')!==false){
            $format = str_replace('[d]',date('d'), $format);
        }
        
        if(strpos($format, '[c]')!==false){
            $format = str_replace('[c]', ndigit(++$this->counter, $counter_digit), $format);
        }
        $this->last_number = $format;
    }
}
