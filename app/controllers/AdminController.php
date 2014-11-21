<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AdminController extends HomeController {
    
    var $data = array();
    
    function __construct() {
        $this->data['user']=Auth::user();
    }
    
    public function index(){
        $this->data['page']='home';
        $this->data['title']='Admin Page';
        return View::make('adminPage.adminMain')->with($this->data);
    }
    
    public function peninjau(){
        $this->data['title']='Atasan dan Peninjau - Konfigurasi';
        $this->data['page']='peninjau';
        return View::make('adminPage.peninjau')->with($this->data);
    }
    
    public function report(){
        $section = (Input::get('section'))?Input::get('section'):DB::table(DB::raw('(select distinct("sectionName"),"sectionCode" from "hrdEmployee" where "sectionCode"<>\'\' order by "sectionName" asc limit 1) as a'))->first()->sectionCode;
        $filter = ($section=='all')?'': 'where "sectionCode" = \''.$section.'\'';
        $sections = DB::table(DB::raw('(select distinct("sectionName"),"sectionCode" from "hrdEmployee" where "sectionCode"<>\'\' order by "sectionName" asc) as a'))->get();
        $employees = DB::table(DB::raw('(select a.id,a."employeeID",a."employeeName",a."sectionName",a."sectionCode",score,"categoryName","employeeNote","atasanNote","peninjauNote" from "hrdEmployee" as a
                                    inner join "hrdEmployeeLevel" as b on b."idEmployee" = a.id
                                    inner join "hrdCategory" as c on b."categoryCode" = c."categoryCode"
                                    '.$filter.' ) as f'))->get();
        $kompetensi = array(array('Kompetensi','Jumlah'),array('Tidak Dapat Diterima',0),array('Tidak Kompeten',0),array('Cukup Kompeten',0),array('Kompeten',0),array('Lebih Kompeten',0),array('Sangat Kompeten',0));
        foreach($employees as $value){
        if($value->score >= 0.90){
            $kompetensi[6][1]=$kompetensi[6][1]+1;
        }elseif($value->score >=0.83){
            $kompetensi[5][1]=$kompetensi[5][1]+1;
        }elseif($value->score >=0.74){
            $kompetensi[4][1]=$kompetensi[4][1]+1;
        }elseif($value->score >=0.65){
            $kompetensi[3][1]=$kompetensi[3][1]+1;
        }elseif($value->score >=0.56){
            $kompetensi[2][1]=$kompetensi[2][1]+1;
        }elseif($value->score <=0.55){
            $kompetensi[1][1]=$kompetensi[1][1]+1;
        }
        }
        $this->data['kompetensi']=$kompetensi;
        $this->data['kompetensiJson']=json_encode($kompetensi);
        $this->data['selectedSection']=$section;
        $this->data['sections']=$sections;
        $this->data['employees']=$employees;
        $this->data['title']='Report';
        $this->data['page']='report';
        return View::make('adminPage.report')->with($this->data);
    }
    
    public function kompetensi(){
        $this->data['title']='Master Kompetensi';
        $this->data['page']='kompetensi';
        return View::make('adminPage.kompetensi')->with($this->data);
    }
    
    public function jabatan(){
        $this->data['title']='Master Posisi / Jabatan';
        $this->data['page']='jabatan';
        return View::make('adminPage.jabatan')->with($this->data);
    }
    
    public function kompetensiJab(){
        $which = Input::get('which')?hrdCategory::find(Input::get('which')):'';
        $this->data['which']=$which;
        $this->data['title']='Konfigurasi Kompetensi Jabatans';
        $this->data['page']='kompetensiJab';
        return View::make('adminPage.kompetensiJab')->with($this->data);
    }
    
    public function kompetensiSubmit(){
        if(Input::get('action')=='update'){
            $nama = Input::get('namaKom');
            $keterangan = Input::get('keteranganKom');
            $id = Input::get('idKom');
            $data =  hrdCompetence::where('competanceCode','=',$id)->first();
            $data->competanceName = $nama;
            $data->CompetanceDesc = $keterangan;
            $data->save();
        }elseif(Input::get('action')=='save'){
            $type = array('inti'=>1,'managerial'=>2,'fungsional'=>3,'teknikal'=>4);
            $kategori = Input::get('kategori');
            $lastKompetensi = hrdCompetence::where(DB::raw('lower("competanceCode")'),'like','%'.strtolower($kategori).'%')->orderBy('competanceOrder','desc')->first()->competanceOrder;
            $leadZero = str_pad(($lastKompetensi+1), 3, '0', STR_PAD_LEFT);
            $newCompetanceCode = ucwords($kategori).'-'.$leadZero;
            
            $data = new hrdCompetence;
            $data->competanceCode = $newCompetanceCode;
            $data->competanceName = $nama;
            $data->CompetanceDesc = $keterangan;
            $data->competanceOrder = $lastKompetensi+1;
            $data->competanceType = $type[$kategori];
            $data->save();
        }
            
        return Redirect::to('admin/kompetensi');
    }
    
    public function jabatanSubmit(){
        $nama = Input::get('nama');
        $keterangan = Input::get('keterangan');
        $lastId  = hrdCategory::orderBy('id','desc')->first()->id;
        $catId = 'CAT-'.str_pad($lastId+1,3,'0',STR_PAD_LEFT);
        $jabatan = new hrdCategory;
        $jabatan->categoryCode = $catId;
        $jabatan->categoryName = $nama;
        $jabatan->categoryDesc = $keterangan;
        $jabatan->save();
        return Redirect::to('admin/jabatan');
        
    }
    
    public function kompetensiJabSubmit(){
        
    }
    
    public function jabatanRemove(){
        $which = Input::get('which');
        hrdCategory::destroy($which);
        return Redirect::to('admin/jabatan');
    }
    
    public function kompetensiRemove(){
        $which = Input::get('which');
        hrdCompetence::destroy($which);
        return Redirect::to('admin/kompetensi');
    }
    
    public function employeeDt(){
        $x = hrdEmployee::leftJoin('hrdEmployeeLevel as b','b.idEmployee','=','hrdEmployee.id')
                ->leftJoin('hrdEmployee as c','c.id','=','b.idEmployeeAtasan')
                ->leftJoin('hrdEmployee as d','d.id','=','b.idEmployeePeninjau')
                ->leftJoin('hrdCategory as e','e.categoryCode','=','b.categoryCode')
                ->where('hrdEmployee.username','<>','administrator')
                ->get(array('hrdEmployee.id','hrdEmployee.employeeName','hrdEmployee.sectionCode','hrdEmployee.sectionName','c.employeeName as atasan',
                    'd.employeeName as peninjau','e.categoryName','hrdEmployee.employeeID'));
//        var_dump($x);
        $strInput = "<div class=\"input-group input-group-sm\">
                            <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-chevron-down\"></i></span>";
        return Datatable::collection($x)
                ->showColumns('employeeID', 'employeeName')
                ->addColumn('section', function ($row) {
                    return $row->sectionCode.' - '.$row->sectionName; 
                    })
                ->addColumn('atasan', function ($row) {
                    return "<div class=\"input-group input-group-sm\">
                            <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-chevron-down\"></i></span><input type='text' name='atasan' class='form-control' onkeypress='atasan(this)' onblur='atasanRemove(this)' value='".$row->atasan."' id='atasan_".$row->id."'></div>"; 
                    })
                ->addColumn('pengawas', function ($row) {
                    return "<div class=\"input-group input-group-sm\">
                            <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-chevron-down\"></i></span><input type='text' name='pengawas' class='form-control' onkeypress='peninjau(this)' onblur='peninjauRemove(this)' value='".$row->peninjau."' id='peninjau_".$row->id."'></div>"; 
                    })
                ->addColumn('category', function ($row) {
                    return "<div class=\"input-group input-group-sm\">
                            <span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-chevron-down\"></i></span><input type='text' name='pengawas' class='form-control' onkeypress='category(this)' value='".$row->categoryName."'  id='category_".$row->id."'></div>";
                    })
                ->searchColumns('employeeName','section','atasan','pengawas','category')
                ->orderColumns('employeeID','employeeName','section','atasan','pengawas','category')
                ->make();
    }
    
    public function kompetensiDt(){
        $x = hrdCompetence::orderBy('competanceCode')->get();
        
       return Datatable::collection($x)
               ->showColumns('competanceOrder','competanceCode','competanceName')
               ->addColumn('CompetanceDesc',function($row){
                   return "$row->CompetanceDesc &nbsp;";
               })
               ->addColumn('action',function($row){
                   return "<span class='center'><a onclick='javascript:return confirm(\"Hapus Data ?\")' class='label label-info' href='".URL::to('act/kompetensiRemove?which='.$row->competanceCode)."'><i class='glyphicon glyphicon-remove'></i> Delete</a>"
                           . "<input type='hidden' id='id' value='$row->competanceCode'><input type='hidden' id='name' value='$row->competanceName'><input type='hidden' id='description' value='$row->CompetanceDesc'>"
                           . " | <a onclick='invokeValue(this)' data-toggle=\"modal\" data-target=\"#myModal\" class='label label-success' href='#' ><i class='glyphicon glyphicon-pencil'></i> Edit</a>"
                           . "</span>";
               })
               ->searchColumns('competanceCode','competanceName')
               ->orderColumns('competanceCode','competanceName')
               ->make();
    }
    
    public function kompetensiJabDt(){
        $jabatan = Input::get('jabatan');
        if($jabatan){
            $x = DB::select(DB::raw('select * from (select b."competanceCode" as "compete", default_value as default from "hrdCategory" as a inner join "hrdCompetanceDetail" as b on a."categoryCode" = b."categoryCode"
                                inner join "hrdCompetance" as c on c."competanceCode" = b."competanceCode" 
                                where a."categoryCode" = \''.$jabatan.'\') as table1
                                right join "hrdCompetance" as d on d."competanceCode" = table1."compete"
                                order by "competanceType" ,"compete" '));
        }else{
            $x = DB::select(DB::raw('select *, \'x\' as compete, 0 as default from "hrdCompetance"'));
        }
       return Datatable::collection(new \Illuminate\Support\Collection($x))
               ->showColumns('competanceCode','competanceName')
               ->addColumn('default',function($row){
                   return "<span style='text-align:center' id='dv_$row->competanceCode'>$row->default</span>";
               })
               ->addColumn('action',function($row){
                   if($row->compete==$row->competanceCode){
                       $str = "<input type='checkbox' name='$row->competanceCode' onclick='return checkChange(this)' checked id='checkbox_$row->competanceCode'> <label style='cursor:pointer' id='$row->competanceCode' for='checkbox_$row->competanceCode' class='label label-success'>selected</label>";
                   }else{
                       $str = "<input type='checkbox' name='$row->competanceCode' onclick='return checkChange(this)' id='checkbox_$row->competanceCode'> <label style='cursor:pointer' id='$row->competanceCode' for='checkbox_$row->competanceCode' class='label label-default'>select</label>";
                   }
                   return $str;
               })
               ->searchColumns('competanceCode','competanceName')
               ->orderColumns('competanceType','compete','competanceName')
               ->make();
    }
    
    public function jabatanDt(){
        $x = hrdCategory::all();
        
       return Datatable::collection($x)
               ->showColumns('id','categoryCode','categoryName')
               ->addColumn('action',function($row){
                   return "<span class='center'><a onclick='javascript:return confirm(\"Hapus Data ?\")' class='label label-info' href='".URL::to('act/jabatanRemove?which='.$row->categoryCode)."'><i class='glyphicon glyphicon-remove'></i> Delete</a></span>";
               })
               ->searchColumns('categoryCode','categoryName')
               ->orderColumns('id','categoryCode','categoryName')
               ->make();
    }
    
    public function ACUsers(){
        $term = strtoupper(Input::get('term'));
        $query = hrdEmployee::Employee()->where(DB::raw('"employeeID"::text'),'like','%'.$term.'%')->orWhere(DB::raw('upper("employeeName")'),'like','%'.$term.'%')->orderBy('employeeName','asc')->select('employeeID as label','employeeName as value','id')->get();
        return Response::json($query);
    }
    public function ACCategory(){
        $term = strtoupper(Input::get('term'));
        $query = DB::table('hrdCategory')->where(DB::raw('upper("categoryName")'),'like','%'.$term.'%')->orderBy('categoryName','asc')->select('categoryCode as label','categoryName as value','categoryCode as id')->get();
        return Response::json($query);
    }
    public function ACCategoryDesc(){
        $term = strtoupper(Input::get('term'));
        $query = DB::table('hrdCategory')->where(DB::raw('upper("categoryName")'),'like','%'.$term.'%')->orderBy('categoryName','asc')->select('categoryName as label','categoryDesc as value','categoryCode as id')->get();
        return Response::json($query);
    }
    
    public function ACSaveAtasan(){
        $atasanId = Input::get('atasan');
        $id = Input::get('id');
        $karyawan = hrdEmployeeLevel::where('idEmployee', '=', $id)->first();
        if(!$karyawan){
            /*insert*/
            $nkaryawan = new hrdEmployeeLevel;
            $nkaryawan->idEmployee = $id;
            $nkaryawan->idEmployeeAtasan = $atasanId;
            $nkaryawan->save();
        }else{
            /*update*/
            $karyawan->idEmployeeAtasan = $atasanId;
            $karyawan->save();
        }
    }
    public function ACSavePeninjau(){
        $peninjauId = Input::get('peninjau');
        $id = Input::get('id');
        $karyawan = hrdEmployeeLevel::where('idEmployee', '=', $id)->first();
        if(!$karyawan){
            /*insert*/
            $nkaryawan = new hrdEmployeeLevel;
            $nkaryawan->idEmployee = $id;
            $nkaryawan->idEmployeePeninjau = $peninjauId;
            $nkaryawan->save();
        }else{
            /*update*/
            $karyawan->idEmployeePeninjau = $peninjauId;
            $karyawan->save();
        }
    }
    
    public function ACSaveCategory(){
        $categoryCode = Input::get('categoryCode');
        $id = Input::get('id');
        $karyawan = hrdEmployeeLevel::where('idEmployee', '=', $id)->first();
        if(!$karyawan){
            /*insert*/
            $nkaryawan = new hrdEmployeeLevel;
            $nkaryawan->idEmployee = $id;
            $nkaryawan->categoryCode = $categoryCode;
            $nkaryawan->save();
        }else{
            /*update*/
            $karyawan->categoryCode = $categoryCode;
            $karyawan->save();
        }
    }
    
    public function ACRemoveAtasan(){
        $id = Input::get('id');
        $karyawan = hrdEmployeeLevel::where('idEmployee', '=', $id)->first();
        $karyawan->idEmployeeAtasan=0;
        $karyawan->save();
    }
    public function ACRemovePeninjau(){
        $id = Input::get('id');
        $karyawan = hrdEmployeeLevel::where('idEmployee', '=', $id)->first();
        $karyawan->idEmployeePeninjau=0;
        $karyawan->save();
    }
    
    public function ajaxUpdateJabKompetensi(){
        $jabatan = Input::get('which');
        $kompetensi = Input::get('what');
        $dv = Input::get('dv');
        $how = Input::get('how');
        if($how=='add'){
            DB::table('hrdCompetanceDetail')->insert(
                    array('categoryCode' => $jabatan, 'competanceCode' => $kompetensi, 'default_value'=>$dv)
                );
        }elseif($how=='remove'){
            DB::table('hrdCompetanceDetail')->where('categoryCode', '=', $jabatan)->where('competanceCode','=',$kompetensi)->delete();
        }
    }
    
    public function ajaxUpdateComDesc(){
        $id = Input::get('id');
        $value = Input::get('val');
        $com = hrdCompetence::find($id);
        $com->CompetanceDesc = $value;
        $com->save();
    }
    
    public function printSection(){
        $section = (Input::get('section'))?Input::get('section'):DB::table(DB::raw('(select distinct("sectionName"),"sectionCode","sectionName" from "hrdEmployee" where "sectionCode"<>\'\' order by "sectionName" asc limit 1) as a'))->first()->sectionName;
        $filter = ($section=='all')?'': 'where "sectionCode" = \''.$section.'\'';
        $sections = DB::table(DB::raw('(select distinct("sectionName"),"sectionCode" from "hrdEmployee" where "sectionCode"<>\'\' order by "sectionName" asc) as a'))->get();
        $employees = DB::table(DB::raw('(select a.id,a."employeeID",a."employeeName",a."sectionName",a."sectionCode",score,"categoryName","employeeNote","atasanNote","peninjauNote" from "hrdEmployee" as a
                                    inner join "hrdEmployeeLevel" as b on b."idEmployee" = a.id
                                    inner join "hrdCategory" as c on b."categoryCode" = c."categoryCode"
                                    '.$filter.' ) as f'))->get();
        $kompetensi = array(array('Kompetensi','Jumlah'),array('Tidak Dapat Diterima',0),array('Tidak Kompeten',0),array('Cukup Kompeten',0),array('Kompeten',0),array('Lebih Kompeten',0),array('Sangat Kompeten',0));
        foreach($employees as $value){
        if($value->score >= 0.90){
            $kompetensi[6][1]=$kompetensi[6][1]+1;
        }elseif($value->score >=0.83){
            $kompetensi[5][1]=$kompetensi[5][1]+1;
        }elseif($value->score >=0.74){
            $kompetensi[4][1]=$kompetensi[4][1]+1;
        }elseif($value->score >=0.65){
            $kompetensi[3][1]=$kompetensi[3][1]+1;
        }elseif($value->score >=0.56){
            $kompetensi[2][1]=$kompetensi[2][1]+1;
        }elseif($value->score <=0.55){
            $kompetensi[1][1]=$kompetensi[1][1]+1;
        }
        }
        $this->data['kompetensi']=$kompetensi;
        $this->data['kompetensiJson']=json_encode($kompetensi);
        $this->data['selectedSection']=($section=='all')?'All Section':DB::table(DB::raw('(select distinct("sectionName"),"sectionCode","sectionName" from "hrdEmployee" '.$filter.' order by "sectionName" asc limit 1) as a'))->first()->sectionName;;
        $this->data['sections']=$sections;
        $this->data['employees']=$employees;
        $this->data['title']='Report';
        $this->data['page']='report';
        return View::make('popup.print')->with($this->data);
    }
}