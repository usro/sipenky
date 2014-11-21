<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class hrdCategory extends Eloquent {
    
    protected $table = "hrdCategory";
    protected $primaryKey = "categoryCode";
    
    public function competence(){
        return $this->belongsToMany('hrdCompetence','hrdCompetanceDetail','categoryCode','competanceCode');
    }
}