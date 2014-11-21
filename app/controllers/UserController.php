<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserController extends BaseController{
    
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->beforeFilter('auth', array('only'=>array('/')));
    }
    
    public function postLogin(){
        $username = Input::get('nokar');
        $password = Input::get('password');
        if(Auth::attempt(array("username"=>$username,"password"=>$password))){
            return Redirect::intended('main');
        }else{
            
            return Redirect::to('/')->with(array('message'=>'Login gagal, hubungi departemen HRD'))->withInput();
        }
    }
    
    public function logout(){
        Auth::logout();
        return Redirect::to('/')->with('message', 'Your are now logged out!');
    }
    public function userList(){
//          $user = User::find(9999);
//          $user = new User;
//          $user->id = 9999;
//          $user->employeeName = 'Administrator';
//          $user->username = 'administrator';
//          $user->password = Hash::make('bbisipenky2014');
//          $user->save();
//        foreach($users as $user){
//            $user->password = Hash::make($user->employeeID);
//            $user->save();
//        }
        echo "Ok";
    }
}