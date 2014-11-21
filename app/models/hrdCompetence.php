<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class hrdCompetence extends Eloquent{
    
    protected $table = "hrdCompetance";
    protected $primaryKey = "competanceCode";
    
    public function category(){
        return $this->belongsToMany('hrdCategory','hrdCompetanceDetail','competanceCode','competanceCode');
    }
    
    public function scopeJabatanCompetence($query){
        return $query->raw('select * from (select b."competanceCode" as "compete" from "hrdCategory" as a inner join "hrdCompetanceDetail" as b on a."categoryCode" = b."categoryCode"
            inner join "hrdCompetance" as c on c."competanceCode" = b."competanceCode" 
            where a."categoryCode" = \'CAT-001\') as table1
            right join "hrdCompetance" as d on d."competanceCode" = table1."compete"
            order by "competanceType" ,"compete" ');
    }
}