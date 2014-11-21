<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
        var $data = array();
	function __construct() {
            
        }
        
        public function home(){
            $this->data['title']='Sistem Penilaian Karyawan PT. Bakrie Building Industries';
            return View::make('pages.home')->with($this->data);
        }
}
