<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class hrdEmployee extends Eloquent {
    var $table = 'hrdEmployee';
    
    public function scopeEmployee($query)
    {
        return $query->where('username', '<>', 'administrator');
    }
    
    public  function scopeLike($query, $field, $value){
        return $query->where($field, 'LIKE', "%$value%");
    }
    
    public function scopeDetail($query){
        return $query->leftJoin('hrdEmployeeLevel as b','b.idEmployee','=','hrdEmployee.id')
                ->leftJoin('');
    }
}


