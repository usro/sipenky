<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return App::make('HomeController')->home();
});
Route::post('/users/login','UserController@postLogin');
Route::get('/users/list','UserController@userList');
Route::get('main',array('before'=>'auth','uses'=>'MainController@index'));
Route::get('main/atasan',array('before'=>'auth','uses'=>'MainController@atasan'));
Route::get('main/peninjau',array('before'=>'auth','uses'=>'MainController@peninjau'));
Route::get('users/logout','UserController@logout');
Route::post('main/saveAsesmen','MainController@postSaveAsesment');
Route::post('act/kompetensi', array('as'=>'act.kompetensi', 'uses'=>'AdminController@kompetensiSubmit'));
Route::post('act/jabatan', array('as'=>'act.jabatan', 'uses'=>'AdminController@jabatanSubmit'));
Route::post('act/kompetensiJab', array('as'=>'act.kompetensiJab', 'uses'=>'AdminController@kompetensiJabSubmit'));
Route::get('act/kompetensiRemove', array('as'=>'act.kompetensiRemove', 'uses'=>'AdminController@kompetensiRemove'));
Route::get('act/jabatanRemove', array('as'=>'act.jabatanRemove', 'uses'=>'AdminController@jabatanRemove'));
Route::get('admin',array('before'=>'administrator','uses'=>'AdminController@index'));
Route::get('admin/peninjau',array('before'=>'administrator','uses'=>'AdminController@peninjau'));
Route::get('admin/report',array('before'=>'administrator','uses'=>'AdminController@report'));
Route::get('admin/kompetensi',array('before'=>'administrator','uses'=>'AdminController@kompetensi'));
Route::get('admin/jabatan',array('before'=>'administrator','uses'=>'AdminController@jabatan'));
Route::get('admin/kompetensiJab',array('before'=>'administrator','uses'=>'AdminController@kompetensiJab'));
Route::get('admin/employee/{eid}',array('before'=>'administrator',function($ied = null){
    return App::make('MainController')->adminView($ied);
}));
Route::get('print/section',array('before'=>'administrator','uses'=>'AdminController@printSection'));
Route::get('api/users', array('as'=>'api.users', 'uses'=>'AdminController@employeeDt'));
Route::get('api/kompetensi', array('as'=>'api.kompetensi', 'uses'=>'AdminController@kompetensiDt'));
Route::get('api/kompetensiJab', array('as'=>'api.kompetensiJab', 'uses'=>'AdminController@kompetensiJabDt'));
Route::get('api/jabatan', array('as'=>'api.jabatan', 'uses'=>'AdminController@jabatanDt'));
Route::get('autocomplete/users', array('as'=>'autocomplete.users', 'uses'=>'AdminController@ACUsers'));
Route::get('autocomplete/category', array('as'=>'autocomplete.users', 'uses'=>'AdminController@ACCategory'));
Route::get('autocomplete/categoryDesc', array('as'=>'autocomplete.usersDesc', 'uses'=>'AdminController@ACCategoryDesc'));
Route::get('autocomplete/saveAtasan', array('as'=>'autocomplete.saveAtasan', 'uses'=>'AdminController@ACSaveAtasan'));
Route::get('autocomplete/savePeninjau', array('as'=>'autocomplete.savePeninjau', 'uses'=>'AdminController@ACSavePeninjau'));
Route::get('autocomplete/saveCategory', array('as'=>'autocomplete.saveCategory', 'uses'=>'AdminController@ACSaveCategory'));
Route::get('autocomplete/removeAtasan', array('as'=>'autocomplete.removeAtasan', 'uses'=>'AdminController@ACRemoveAtasan'));
Route::get('autocomplete/removePeninjau', array('as'=>'autocomplete.removePeninjau', 'uses'=>'AdminController@ACRemovePeninjau'));
Route::get('ajax/updateJabKompetensi', array('before'=>'administrator', 'uses'=>'AdminController@ajaxUpdateJabKompetensi'));
Route::get('ajax/updateComDesc', array('before'=>'administrator', 'uses'=>'AdminController@ajaxUpdateComDesc'));
