<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MainController extends BaseController{
    var $data = array();
    function __construct() {
        $this->user = Auth::user()->username;
        $this->idUser = Auth::user()->id;
        $this->bawahan = Input::get('low');
        $this->atasanStat = DB::table('hrdEmployeeLevel')->where('idEmployeeAtasan','=',$this->idUser)->get();
        $this->peninjauStat = DB::table('hrdEmployeeLevel')->where('idEmployeePeninjau','=',$this->idUser)->get();
        
    }
    
    function index($eid = null){
        $idUser = empty($eid)?$this->idUser:$eid;
        $this->bawahan = DB::table('hrdEmployeeLevel as a')
                ->leftJoin('hrdEmployee as b','a.idEmployee','=','b.id')
                ->leftJoin('hrdEmployee as c','a.idEmployeeAtasan','=','c.id')
                ->leftJoin('hrdEmployee as d','a.idEmployeePeninjau','=','d.id')
                ->where('a.idEmployee','=',$idUser)
                ->select('b.employeeName as employeeName', 'b.employeeID as employeeID','b.positionName as positionName'
                        ,'b.sectionName as sectionName','b.departmentName as departmentName','c.employeeName as atasan','d.employeeName as peninjau'
                        ,'a.employeeNote','b.joinDate')->first();
//        die(print_r($this->bawahan));
        $asesmen = DB::table('hrdEmployee as a')->where('a.id','=',$idUser)
                ->join('hrdEmployeeLevel as b','a.id','=','b.idEmployee')
                ->join('hrdCategory as c','c.categoryCode','=','b.categoryCode')
                ->join('hrdCompetanceDetail as d','d.categoryCode','=','c.categoryCode')
                ->join('hrdCompetance as e','e.competanceCode','=','d.competanceCode')
                ->leftJoin('hrdEmployeeCompetance as f',function($join)use($idUser){
                    $join->on('f.competanceCode','=','e.competanceCode')
                            ->where('f.idEmployee','=',$idUser);
                })
                
                ->select('confirmKaryawan', 'e.competanceCode as competanceCode','e.competanceName as competanceName','CompetanceDesc','d.default_value as default_value',
                        'level1_value')
                ->orderBy('competanceType')->orderBy('competanceOrder')->get();
        $data = array();
        $submit = array('submitKaryawan'=>false,'confirmAtasan'=>false,'submitBawahan1'=>true,'submitBawahan2'=>true,'submitHcga'=>false);
        foreach($asesmen as $as){
            $group = substr($as->competanceCode,0,strlen($as->competanceCode)-4);
            $data[$group][$as->competanceName]['group']=$group;
            $data[$group][$as->competanceName]['competanceCode']=$as->competanceCode;
            $data[$group][$as->competanceName]['competanceName']=$as->competanceName;
            $data[$group][$as->competanceName]['default_value']=$as->default_value;
            $data[$group][$as->competanceName]['competanceDesc']=$as->CompetanceDesc;
            $data[$group][$as->competanceName]['level1_value']=$as->level1_value;
//            if($as->level1_value<>0){ $submit['submitKaryawan'] = true;}
            if($as->confirmKaryawan=='t'){ $submit['confirmAtasan'] = true;}
        }
        $strHidden = "<input type='hidden' name='level' value='karyawan'><input type='hidden' name='low' value='$idUser'>";
        $strHead = "<th width='75'>Karyawan</th>";
        $strHRow = "";
        $strRow = "";
        foreach($data as $key=>$val){
        $strRow.="<tr style=\"font-weight:bold;background:#eee\">
            <td>&nbsp;</td>
            <td style=\"text-align:left;padding-left:5px;\">$key</td>
            <td></td>
            <td></td>
        </tr>";
        $i=0;
            foreach($data[$key] as $value){
             $i++;
            $strRow.="<tr>
                <td>$i</td>
                <td style=\"text-align:left;padding-left:5px;\"><span data-toggle=\"tooltip\" data-placement=\"left\" title=\"$value[competanceDesc]\" class=\"tip\" style=\"display:block\">$value[competanceName]</span></td>
                <td>$value[default_value]<input type=\"hidden\" id=\"df-$value[competanceCode]\" value=\"$value[default_value]\"</td>
                <td> 
                    <select class=\"custom-select kary-value must-valid\" name=\"kary_value[".$value['competanceCode']."]\" id=\"$value[competanceCode]\">
                        <option ".($value['level1_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level1_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level1_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level1_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level1_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level1_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level1_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>
            </tr>";
            }//end child loop
        }//end loop
        $catatan = "<table class='table-bordered' >"
                . "<tr style='background:#eee'><th>Catatan Karyawan ( Kebutuhan pelatihan, feedback, dan lain-lain )</th></tr><tr><td><textarea cols='53' name='catatan'>".$this->bawahan->employeeNote."</textarea></td></tr>"
                . "</table>";
        $colspan = 1;
        $this->data['btnConfirm'] = '';
        $this->data['karyawanStr'] = '<li role="presentation" class="active"><a href="'.URL::to('main').'"><small>sebagai</small> Karyawan</a></li>';
        $this->data['atasanStr'] = ($this->atasanStat)?'<li id="atasanDesc" role="presentation"><a href="'.URL::to('main/atasan').'"><small>sebagai</small> Atasan</a></li>':"";
        $this->data['peninjauStr'] = ($this->peninjauStat)?'<li id="peninjauDesc" role="presentation"><a href="'.URL::to('main/peninjau').'"><small>sebagai</small> Peninjau</a></li>':"";
        $this->data['level']='Karyawan';
        $this->data['selectedBawahanDetail']=$this->bawahan;
        $this->data['submit']=$submit;
        $this->data['strHRow']=$strHRow;
        $this->data['strRow']=$strRow;
        $this->data['colspan']=$colspan;
        $this->data['strHidden']=$strHidden;
        $this->data['strHead']=$strHead;
        $this->data['title']='Main';
        $this->data['data']=$data;
        $this->data['user']=Auth::user();
        $this->data['bawahan']=0;
        $this->data['catatan']=$catatan;
        return View::make('pages.main')->with($this->data);
    }
    
    function atasan(){
        $low = Input::get('low');
        $lower = ($low)?$low:DB::table('hrdEmployeeLevel')->where('idEmployeeAtasan','=',$this->idUser)->orderBy('idEmployee','asc')->first()->idEmployee;
        $this->bawahan = DB::table('hrdEmployeeLevel as a')
                ->leftJoin('hrdEmployee as b','a.idEmployee','=','b.id')
                ->leftJoin('hrdEmployee as c','a.idEmployeeAtasan','=','c.id')
                ->leftJoin('hrdEmployee as d','a.idEmployeePeninjau','=','d.id')
                ->where('a.idEmployee','=',$lower)
                ->select('b.id as idEmployee','b.employeeName as employeeName', 'b.employeeID as employeeID','b.positionName as positionName'
                        ,'b.sectionName as sectionName','b.departmentName as departmentName','c.employeeName as atasan','d.employeeName as peninjau'
                        ,'a.employeeNote','a.atasanNote','b.joinDate')->first();
//        die(print_r($this->bawahan));
        $bawahanList = DB::table('hrdEmployeeLevel as a')->join('hrdEmployee as b','a.idEmployee','=','b.id')->where('idEmployeeAtasan','=',$this->idUser)->orderBy('idEmployee','asc')->get(array('b.employeeName','b.id'));
        $asesmen = DB::table('hrdEmployee as a')->where('a.id','=',$this->bawahan->idEmployee)
                ->join('hrdEmployeeLevel as b','a.id','=','b.idEmployee')
                ->join('hrdCategory as c','c.categoryCode','=','b.categoryCode')
                ->join('hrdCompetanceDetail as d','d.categoryCode','=','c.categoryCode')
                ->join('hrdCompetance as e','e.competanceCode','=','d.competanceCode')
                ->leftJoin('hrdEmployeeCompetance as f',function($join){
                    $join->on('f.competanceCode','=','e.competanceCode')
                            ->where('f.idEmployee','=',$this->bawahan->idEmployee);
                })
                ->select('confirmAtasan','e.competanceCode as competanceCode','e.competanceName as competanceName','CompetanceDesc','d.default_value as default_value',
                        'level1_value','level2_value')
                ->orderBy('competanceType')->orderBy('competanceOrder')->get();
        $data = array();
        $submit = array('submitKaryawan'=>false,'confirmAtasan'=>false,'submitBawahan1'=>false,'submitBawahan2'=>true,'submitHcga'=>false);
        foreach($asesmen as $as){
            $group = substr($as->competanceCode,0,strlen($as->competanceCode)-4);
            $data[$group][$as->competanceName]['group']=$group;
            $data[$group][$as->competanceName]['competanceCode']=$as->competanceCode;
            $data[$group][$as->competanceName]['competanceName']=$as->competanceName;
            $data[$group][$as->competanceName]['default_value']=$as->default_value;
            $data[$group][$as->competanceName]['competanceDesc']=$as->CompetanceDesc;
            $data[$group][$as->competanceName]['level1_value']=$as->level1_value;
            $data[$group][$as->competanceName]['level2_value']=$as->level2_value;
//            if($as->level2_value<>0){ $submit['submitKaryawan'] = true;}
            if($as->level1_value<>0){ $submit['submitBawahan1'] = true;}
            if($as->confirmAtasan=='t'){ $submit['confirmAtasan'] = true;}
        }
//        die(var_dump($submit));
        $strHidden = "<input type='hidden' name='level' value='atasan'><input type='hidden' name='low' value='$lower'>";
        $strHead = "<th width='75'>Karyawan</th><th width='75'>Atasan Langsung</th>";
        $strHRow = "<td></td>";
        $strRow = "";
        foreach($data as $key=>$val){
        $strRow.="
            <tr style=\"font-weight:bold;background:#eee\">
            <td>&nbsp;</td>
            <td style=\"text-align:left;padding-left:5px;\">$key</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>";
        $i=0;
            foreach($data[$key] as $value){
             $i++;
            $strRow.="<tr>
                <td>$i</td>
                <td style=\"text-align:left;padding-left:5px;\"><span data-toggle=\"tooltip\" data-placement=\"left\" title=\"$value[competanceDesc]\" class=\"tip\" style=\"display:block\">$value[competanceName]</span></td>
                <td>
                    $value[default_value]"
                    . "<input type=\"hidden\" id=\"df-$value[competanceCode]\" value=\"$value[default_value]\">"
                    . "<input type=\"hidden\" id=\"kv-$value[competanceCode]\" value=\"$value[level1_value]\">
                </td>
                <td> 
                
                    <select class=\"custom-select kary-value\" id=\"$value[competanceCode]\" disabled>
                        <option ".($value['level1_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level1_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level1_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level1_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level1_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level1_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level1_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>
                <td>
                    <select class=\"custom-select kary-value must-valid rule-1\" name=\"kary_value[".$value['competanceCode']."]\" id=\"$value[competanceCode]\" >
                        <option ".($value['level2_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level2_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level2_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level2_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level2_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level2_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level2_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>
            </tr>";
            }//end child loop
        }//end loop
        $catatan = "<table class='table-bordered' >"
                . "<tr style='background:#eee'><th>Catatan Karyawan ( Kebutuhan pelatihan, feedback, dan lain-lain )</th></tr><tr><td><textarea cols='53' readonly>".$this->bawahan->employeeNote."</textarea></td></tr>"
                . "<tr style='background:#eee'><th>Catatan Atasan Langsung ( Tinjau ulang, respon, peringatan, dan lain-lain)</th></tr><tr><td><textarea cols='53' name='catatan'>".$this->bawahan->atasanNote."</textarea></td></tr>"
                . "</table>";
        $colspan = 2;
        $this->data['btnConfirm']='<input type="submit" name="submit" onclick="javascript:return selectValid()" value="Confirm" id="confirm" class="btn btn-success">';
        $this->data['level']='Atasan';
        $this->data['selectedBawahan']=$lower;
        $this->data['selectedBawahanDetail']=$this->bawahan;
        $this->data['bawahanList']=$bawahanList;
        $this->data['karyawanStr'] = '<li role="presentation"><a href="'.URL::to('main').'"><small>sebagai</small> Karyawan</a></li>';
        $this->data['atasanStr'] = ($this->atasanStat)?'<li id="atasanDesc" role="presentation" class="active"><a href="'.URL::to('main/atasan').'"><small>sebagai</small> Atasan</a></li>':"";
        $this->data['peninjauStr'] = ($this->peninjauStat)?'<li id="peninjauDesc" role="presentation"><a href="'.URL::to('main/peninjau').'"><small>sebagai</small> Peninjau</a></li>':"";
        $this->data['strHRow']=$strHRow;
        $this->data['strRow']=$strRow;
        $this->data['submit']=$submit;
        $this->data['colspan']=$colspan;
        $this->data['strHead']=$strHead;
        $this->data['strHidden']=$strHidden;
        $this->data['title']='Main';
        $this->data['data']=$data;
        $this->data['bawahan']=3;
        $this->data['user']=Auth::user();
        $this->data['catatan']=$catatan;
        return View::make('pages.main')->with($this->data);
    }
    
    function peninjau(){
        $low = Input::get('low');
        $lower = ($low)?$low:DB::table('hrdEmployeeLevel')->where('idEmployeePeninjau','=',$this->idUser)->orderBy('idEmployee','asc')->first()->idEmployee;
        $this->bawahan = DB::table('hrdEmployeeLevel as a')
                ->leftJoin('hrdEmployee as b','a.idEmployee','=','b.id')
                ->leftJoin('hrdEmployee as c','a.idEmployeeAtasan','=','c.id')
                ->leftJoin('hrdEmployee as d','a.idEmployeePeninjau','=','d.id')
                ->where('a.idEmployee','=',$lower)
                ->select('b.id as idEmployee','b.employeeName as employeeName', 'b.employeeID as employeeID','b.positionName as positionName'
                        ,'b.sectionName as sectionName','b.departmentName as departmentName','c.employeeName as atasan','d.employeeName as peninjau'
                        ,'a.employeeNote','a.atasanNote','a.hcgaNote','a.peninjauNote','b.joinDate')->first();
        $bawahanList = DB::table('hrdEmployeeLevel as a')->join('hrdEmployee as b','a.idEmployee','=','b.id')->where('idEmployeePeninjau','=',$this->idUser)->orderBy('a.idEmployee','asc')->get(array('b.employeeName','b.id'));
        $asesmen = DB::table('hrdEmployee as a')->where('a.id','=',$this->bawahan->idEmployee)
                ->join('hrdEmployeeLevel as b','a.id','=','b.idEmployee')
                ->join('hrdCategory as c','c.categoryCode','=','b.categoryCode')
                ->join('hrdCompetanceDetail as d','d.categoryCode','=','c.categoryCode')
                ->join('hrdCompetance as e','e.competanceCode','=','d.competanceCode')
                ->leftJoin('hrdEmployeeCompetance as f',function($join){
                    $join->on('f.competanceCode','=','e.competanceCode')
                            ->where('f.idEmployee','=',$this->bawahan->idEmployee);
                })
                ->select('e.competanceCode as competanceCode','e.competanceName as competanceName','CompetanceDesc','d.default_value as default_value',
                        'level1_value','level2_value','level3_value')
                ->orderBy('competanceType')->orderBy('competanceOrder')->get();
        $data = array();
        $submit = array('submitKaryawan'=>false,'confirmAtasan'=>true,'submitBawahan1'=>false,'submitBawahan2'=>false,'submitHcga'=>false);
        foreach($asesmen as $as){
            $group = substr($as->competanceCode,0,strlen($as->competanceCode)-4);
            $data[$group][$as->competanceName]['group']=$group;
            $data[$group][$as->competanceName]['competanceCode']=$as->competanceCode;
            $data[$group][$as->competanceName]['competanceName']=$as->competanceName;
            $data[$group][$as->competanceName]['default_value']=$as->default_value;
            $data[$group][$as->competanceName]['competanceDesc']=$as->CompetanceDesc;
            $data[$group][$as->competanceName]['level1_value']=$as->level1_value;
            $data[$group][$as->competanceName]['level2_value']=$as->level2_value;
            $data[$group][$as->competanceName]['level3_value']=$as->level3_value;
            if($as->level3_value<>0){ $submit['submitKaryawan'] = true;}
            if($as->level2_value<>0){ $submit['submitBawahan2'] = true;}
            if($as->level1_value<>0){ $submit['submitBawahan1'] = true;}
        }
        $h="";
        $colspan=2;
        $th="";
        $td="";
        if($this->bawahan->atasan!=''){
            $h = "<th width='75'>Atasan Langsung</th>";
            $colspan=3;
            $th="<th></th>";
            $td="<td></td>";
        }
        $strHidden = "<input type='hidden' name='level' value='peninjau'><input type='hidden' name='low' value='$lower'>";
        $strHead = "<th width='75'>Karyawan</th>$h<th width='75'>Peninjau</th>";
        $strHRow = "<td></td>$td<td></td>";
        $strRow = "";
        $dv_total = array();
        foreach($data as $key=>$val){
        $strRow.="<tr style=\"font-weight:bold;background:#eee\">
            <td>&nbsp;</td>
            <td style=\"text-align:left;padding-left:5px;\">$key</td>
            <td></td>
            <td></td>
            <td></td>
            $td
            <td></td>
        </tr>";
        $i=0;
        $clsp =1;
            foreach($val as $ke=>$value){
             $i++;
            $strRow.="<tr>
                <td>$i</td>
                <td style=\"text-align:left;padding-left:5px;\"><span data-toggle=\"tooltip\" data-placement=\"left\" title=\"$value[competanceDesc]\" class=\"tip\" style=\"display:block\">$value[competanceName]</span></td>
                <td>$value[default_value]<input type=\"hidden\" id=\"df-$value[competanceCode]\" value=\"$value[default_value]\"</td>
                <td> 
                    <select class=\"custom-select kary-value\" disabled>
                        <option ".($value['level1_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level1_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level1_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level1_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level1_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level1_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level1_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>";
                if($this->bawahan->atasan!=''){
                    $strRow .="<td>
                    <select class=\"custom-select kary-value\" disabled>
                        <option ".($value['level2_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level2_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level2_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level2_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level2_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level2_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level2_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>";
                    $clsp=2;
                }
                $strRow.="<td>
                    <select class=\"custom-select  senjangCount must-valid\" name=\"kary_value[".$value['competanceCode']."]\" id=\"$value[competanceCode]\" >
                        <option ".($value['level3_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level3_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level3_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level3_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level3_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level3_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level3_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>
                <td><span id='sv-$value[competanceCode]'>".($value['level3_value']-$value['default_value'])."</span></td>
            </tr>";
                $dv_total['default_value'][] =  $value['default_value'];
                $dv_total['level3_value'][] =  $value['level3_value'];
                $dv_total['senjang_value'][] =  $value['level3_value']-$value['default_value'];
            }//end child loop
        }//end loop
        $strRow .="<tr>"
                . "<td colspan='2'>Total</td>"
                . "<td>".array_sum($dv_total['default_value'])."</td>"
                . "<td colspan='$clsp'></td>"
                . "<td><span id='total_value'>".array_sum($dv_total['level3_value'])."</span></td>"
                . "<td>".array_sum($dv_total['senjang_value'])."</td>"
                . "</tr>";
        $value = (array_sum($dv_total['level3_value'])<>0 and array_sum($dv_total['default_value'])<>0)?(floor((array_sum($dv_total['level3_value'])/array_sum($dv_total['default_value'])*100))/100):0;
        if($value >= 0.90){
            $strSum = "<tr class='warning' style='text-align:center'><td>5</td><td>Sangat Kompeten</td><td colspan='2'>&gt;=0.90</td></tr>";
        }elseif($value>=0.83){
            $strSum = "<tr class='warning' style='text-align:center'><td>4</td><td>Lebih Kompeten</td><td>0.83</td><td>0.91</td></tr>";
        }elseif($value>=0.74){
            $strSum = "<tr class='warning' style='text-align:center'><td>3</td><td>Kompeten</td><td>0.74</td><td>0.82</td></tr>";
        }elseif($value>=0.65){
            $strSum = "<tr class='warning' style='text-align:center'><td>2</td><td>Cukup Kompeten</td><td>0.65</td><td>0.73</td></tr>";
        }elseif($value>=0.56){
            $strSum = "<tr class='danger' style='text-align:center'><td>1</td><td>Tidak Kompeten</td><td>0.56</td><td>0.64</td></tr>";
        }elseif($value<=0.55){
            $strSum = "<tr class='danger' style='text-align:center'><td>0</td><td>Tidak Dapat Diterima</td><td colspan='2'>&lt;=0.55</td></tr>";
        }
        $strRow .="<tr>"
                . "<td colspan='5'></td>"
                . "<td class='warning'><span id='value'>".$value."</span></td>"
                . "<td></td>"
                . "</tr>";
        $summary =($submit['submitKaryawan'])?"<table class='table-bordered' style='width:100%'>$strSum</table>":"";
        $summaryDesc = "<table class='table-bordered' style='width:100%' >"
                . "<tbody>"
                . "<tr style='background:#eee'><td></td><th>Pencapaian</th><th style='text-align:center' colspan='2'>Nilai</th></tr>"
                . "<tr class='danger'><td>0</td><td>Tidak Dapat Diterima</td><td style='text-align:center'>0.47</td><td style='text-align:center'>0.55</td></tr>"
                . "<tr class='danger'><td>1</td><td>Tidak Kompeten</td><td style='text-align:center'>0.56</td><td style='text-align:center'>0.64</td></tr>"
                . "<tr class='warning'><td>2</td><td>Cukup Kompeten</td><td style='text-align:center'>0.65</td><td style='text-align:center'>0.73</td></tr>"
                . "<tr class='warning'><td>3</td><td>Kompeten</td><td style='text-align:center'>0.74</td><td style='text-align:center'>0.82</td></tr>"
                . "<tr class='warning'><td>4</td><td>Lebih Kompeten</td><td style='text-align:center'>0.83</td><td style='text-align:center'>0.91</td></tr>"
                . "<tr class='warning'><td>5</td><td>Sangat Kompeten</td><td style='text-align:center'>0.90</td><td style='text-align:center'>1</td></tr>"
                . "</tbody>"
                . "</table>";
        $catatan = "<table class='table-bordered' >"
                . "<tr style='background:#eee'><th>Catatan Karyawan ( Kebutuhan pelatihan, feedback, dan lain-lain )</th></tr><tr><td><textarea cols='53' readonly>".$this->bawahan->employeeNote."</textarea></td></tr>"
                . "<tr style='background:#eee'><th>Catatan Atasan Langsung ( Tinjau ulang, respon, peringatan, dan lain-lain )</th></tr><tr><td><textarea cols='53' readonly>".$this->bawahan->atasanNote."</textarea></td></tr>"
                . "<tr style='background:#eee'><th>Catatan HC & GA ( Kesimpulan akhir )</th></tr><tr><td><textarea cols='53' name='catatan' readonly>".$this->bawahan->hcgaNote."</textarea></td></tr>"
                . "</table>";
        
        $this->data['level']='Peninjau';
        $this->data['selectedBawahanDetail']=$this->bawahan;
        $this->data['bawahanList']=$bawahanList;
        $this->data['selectedBawahan']=$lower;
        $this->data['karyawanStr'] = '<li role="presentation"><a href="'.URL::to('main').'"><small>sebagai</small> Karyawan</a></li>';
        $this->data['atasanStr'] = ($this->atasanStat)?'<li id="atasanDesc" role="presentation"><a href="'.URL::to('main/atasan').'"><small>sebagai</small> Atasan</a></li>':"";
        $this->data['peninjauStr'] = ($this->peninjauStat)?'<li id="peninjauDesc" class="active" role="presentation"><a href="'.URL::to('main/peninjau').'"><small>sebagai</small> Peninjau</a></li>':"";
        $this->data['strHRow']=$strHRow;
        $this->data['strRow']=$strRow;
        $this->data['strSenjang']="<th width=\"75\" rowspan='2'>Nilai Kesenjangan</th>";
        $this->data['submit']=$submit;
        $this->data['colspan']=$colspan;
        $this->data['strHead']=$strHead;
        $this->data['strHidden']=$strHidden;
        $this->data['title']='Main';
        $this->data['data']=$data;
        $this->data['bawahan']=3;
        $this->data['summaryDesc']=$summaryDesc;
        $this->data['summary']=$summary;
        $this->data['catatan']=$catatan;
        $this->data['user']=Auth::user();
        return View::make('pages.main')->with($this->data);
    }
    
    public function adminView($eid){
        $this->bawahan = DB::table('hrdEmployeeLevel as a')
                ->leftJoin('hrdEmployee as b','a.idEmployee','=','b.id')
                ->leftJoin('hrdEmployee as c','a.idEmployeeAtasan','=','c.id')
                ->leftJoin('hrdEmployee as d','a.idEmployeePeninjau','=','d.id')
                ->where('a.idEmployee','=',$eid)
                ->select('b.id as idEmployee','b.employeeName as employeeName', 'b.employeeID as employeeID','b.positionName as positionName'
                        ,'b.sectionName as sectionName','b.departmentName as departmentName','c.employeeName as atasan','d.employeeName as peninjau'
                        ,'a.employeeNote','a.atasanNote','a.hcgaNote','a.peninjauNote','b.joinDate')->first();
        $asesmen = DB::table('hrdEmployee as a')->where('a.id','=',$eid)
                ->join('hrdEmployeeLevel as b','a.id','=','b.idEmployee')
                ->join('hrdCategory as c','c.categoryCode','=','b.categoryCode')
                ->join('hrdCompetanceDetail as d','d.categoryCode','=','c.categoryCode')
                ->join('hrdCompetance as e','e.competanceCode','=','d.competanceCode')
                ->leftJoin('hrdEmployeeCompetance as f',function($join)use($eid){
                    $join->on('f.competanceCode','=','e.competanceCode')
                            ->where('f.idEmployee','=',$eid);
                })
                ->select('e.competanceCode as competanceCode','e.competanceName as competanceName','CompetanceDesc','d.default_value as default_value',
                        'level1_value','level2_value','level3_value')
                ->orderBy('competanceType')->orderBy('competanceOrder')->get();
        $data = array();
        $submit = array('submitKaryawan'=>false,'confirmAtasan'=>true,'submitBawahan1'=>true,'submitBawahan2'=>true,'submitHcga'=>false);
        foreach($asesmen as $as){
            $group = substr($as->competanceCode,0,strlen($as->competanceCode)-4);
            $data[$group][$as->competanceName]['group']=$group;
            $data[$group][$as->competanceName]['competanceCode']=$as->competanceCode;
            $data[$group][$as->competanceName]['competanceName']=$as->competanceName;
            $data[$group][$as->competanceName]['default_value']=$as->default_value;
            $data[$group][$as->competanceName]['competanceDesc']=$as->CompetanceDesc;
            $data[$group][$as->competanceName]['level1_value']=$as->level1_value;
            $data[$group][$as->competanceName]['level2_value']=$as->level2_value;
            $data[$group][$as->competanceName]['level3_value']=$as->level3_value;
        }
        $h="";
        $colspan=2;
        $th="";
        $td="";
        if($this->bawahan->atasan!=''){
            $h = "<th width='75'>Atasan Langsung</th>";
            $colspan=3;
            $th="<th></th>";
            $td="<td></td>";
        }
        $strHidden = "<input type='hidden' name='level' value='hcga'><input type='hidden' name='low' value='$eid'>";
        $strHead = "<th width='75'>Karyawan</th>$h<th width='75'>Peninjau</th>";
        $strHRow = "<td></td>$td<td></td>";
        $strRow = "";
        $dv_total = array();
        foreach($data as $key=>$val){
        $strRow.="<tr style=\"font-weight:bold;background:#eee\">
            <td>&nbsp;</td>
            <td style=\"text-align:left;padding-left:5px;\">$key</td>
            <td></td>
            <td></td>
            <td></td>
            $td
            <td></td>
        </tr>";
        $i=0;
        $clsp =1;
            foreach($val as $ke=>$value){
             $i++;
            $strRow.="<tr>
                <td>$i</td>
                <td style=\"text-align:left;padding-left:5px;\"><span data-toggle=\"tooltip\" data-placement=\"left\" title=\"$value[competanceDesc]\" class=\"tip\" style=\"display:block\">$value[competanceName]</span></td>
                <td>$value[default_value]<input type=\"hidden\" id=\"df-$value[competanceCode]\" value=\"$value[default_value]\"</td>
                <td> 
                    <select class=\"custom-select kary-value\" disabled>
                        <option ".($value['level1_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level1_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level1_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level1_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level1_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level1_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level1_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>";
                if($this->bawahan->atasan!=''){
                    $strRow .="<td>
                    <select class=\"custom-select kary-value\" disabled>
                        <option ".($value['level2_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level2_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level2_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level2_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level2_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level2_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level2_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>";
                    $clsp=2;
                }
                $strRow.="<td>
                    <select class=\"custom-select senjangCount\" disabled name=\"kary_value[".$value['competanceCode']."]\" id=\"$value[competanceCode]\" >
                        <option ".($value['level3_value']==0?'selected':'')." value=\"0\">-</option>
                        <option ".($value['level3_value']==1?'selected':'')." value=\"1\">1</option>
                        <option ".($value['level3_value']==2?'selected':'')." value=\"2\">2</option>
                        <option ".($value['level3_value']==3?'selected':'')." value=\"3\">3</option>
                        <option ".($value['level3_value']==4?'selected':'')." value=\"4\">4</option>
                        <option ".($value['level3_value']==5?'selected':'')." value=\"5\">5</option>
                        <option ".($value['level3_value']==6?'selected':'')." value=\"6\">6</option>
                    </select>
                </td>
                <td><span id='sv-$value[competanceCode]'>".($value['level3_value']-$value['default_value'])."</span></td>
            </tr>";
                $dv_total['default_value'][] =  $value['default_value'];
                $dv_total['level3_value'][] =  $value['level3_value'];
                $dv_total['senjang_value'][] =  $value['level3_value']-$value['default_value'];
            }//end child loop
        }//end loop
        $strRow .="<tr>"
                . "<td colspan='2'>Total</td>"
                . "<td>".array_sum($dv_total['default_value'])."</td>"
                . "<td colspan='$clsp'></td>"
                . "<td><span id='total_value'>".array_sum($dv_total['level3_value'])."</span></td>"
                . "<td>".array_sum($dv_total['senjang_value'])."</td>"
                . "</tr>";
        $value = (array_sum($dv_total['level3_value'])<>0 and array_sum($dv_total['default_value'])<>0)?(floor((array_sum($dv_total['level3_value'])/array_sum($dv_total['default_value'])*100))/100):0;
        if($value >= 0.90){
            $strSum = "<tr class='warning' style='text-align:center'><td>5</td><td>Sangat Kompeten</td><td colspan='2'>&gt;=0.90</td></tr>";
        }elseif($value>=0.83){
            $strSum = "<tr class='warning' style='text-align:center'><td>4</td><td>Lebih Kompeten</td><td>0.83</td><td>0.91</td></tr>";
        }elseif($value>=0.74){
            $strSum = "<tr class='warning' style='text-align:center'><td>3</td><td>Kompeten</td><td>0.74</td><td>0.82</td></tr>";
        }elseif($value>=0.65){
            $strSum = "<tr class='warning' style='text-align:center'><td>2</td><td>Cukup Kompeten</td><td>0.65</td><td>0.73</td></tr>";
        }elseif($value>=0.56){
            $strSum = "<tr class='danger' style='text-align:center'><td>1</td><td>Tidak Kompeten</td><td>0.56</td><td>0.64</td></tr>";
        }elseif($value<=0.55){
            $strSum = "<tr class='danger' style='text-align:center'><td>0</td><td>Tidak Dapat Diterima</td><td colspan='2'>&lt;=0.55</td></tr>";
        }
        $strRow .="<tr>"
                . "<td colspan='5'></td>"
                . "<td class='warning'><span id='value'>".$value."</span></td>"
                . "<td></td>"
                . "</tr>";
        $summary =($submit['submitKaryawan'])?"<table class='table-bordered' style='width:100%'>$strSum</table>":"";
        $summaryDesc = "<table class='table-bordered' style='width:100%' >"
                . "<tbody>"
                . "<tr style='background:#eee'><td></td><th>Pencapaian</th><th style='text-align:center' colspan='2'>Nilai</th></tr>"
                . "<tr class='danger'><td>0</td><td>Tidak Dapat Diterima</td><td style='text-align:center'>0.47</td><td style='text-align:center'>0.55</td></tr>"
                . "<tr class='danger'><td>1</td><td>Tidak Kompeten</td><td style='text-align:center'>0.56</td><td style='text-align:center'>0.64</td></tr>"
                . "<tr class='warning'><td>2</td><td>Cukup Kompeten</td><td style='text-align:center'>0.65</td><td style='text-align:center'>0.73</td></tr>"
                . "<tr class='warning'><td>3</td><td>Kompeten</td><td style='text-align:center'>0.74</td><td style='text-align:center'>0.82</td></tr>"
                . "<tr class='warning'><td>4</td><td>Lebih Kompeten</td><td style='text-align:center'>0.83</td><td style='text-align:center'>0.91</td></tr>"
                . "<tr class='warning'><td>5</td><td>Sangat Kompeten</td><td style='text-align:center'>0.90</td><td style='text-align:center'>1</td></tr>"
                . "</tbody>"
                . "</table>";
        $catatan = "<table class='table-bordered' >"
                . "<tr style='background:#eee'><th>Catatan Karyawan ( Kebutuhan pelatihan, feedback, dan lain-lain )</th></tr><tr><td><textarea cols='53' readonly>".$this->bawahan->employeeNote."</textarea></td></tr>"
                . "<tr style='background:#eee'><th>Catatan Atasan Langsung ( Tinjau ulang, respon, peringatan, dan lain-lain )</th></tr><tr><td><textarea cols='53' readonly>".$this->bawahan->atasanNote."</textarea></td></tr>"
                . "<tr style='background:#eee'><th>Catatan HC & GA ( Kesimpulan akhir )</th></tr><tr><td><textarea cols='53' name='catatan'>".$this->bawahan->hcgaNote."</textarea></td></tr>"
                . "</table>";
        $this->data['level']='hcga';
        $this->data['strHRow']=$strHRow;
        $this->data['strRow']=$strRow;
        $this->data['strSenjang']="<th width=\"75\" rowspan='2'>Nilai Kesenjangan</th>";
        $this->data['submit']=$submit;
        $this->data['colspan']=$colspan;
        $this->data['strHead']=$strHead;
        $this->data['strHidden']=$strHidden;
        $this->data['title']='Main';
        $this->data['selectedBawahanDetail']=$this->bawahan;
        $this->data['data']=$data;
        $this->data['karyawanStr']='';
        $this->data['atasanStr']='';
        $this->data['peninjauStr']='';
        $this->data['bawahan']=0;
        $this->data['summaryDesc']=$summaryDesc;
        $this->data['summary']=$summary;
        $this->data['catatan']=$catatan;
        $this->data['user']=Auth::user();
        return View::make('pages.main')->with($this->data);
    }
    
    function postSaveAsesment(){
        $confirmAtasan = Input::get('submit')=='Confirm'?true:false;
        $karyValue=Input::get('kary_value');
        $level=Input::get('level');
        $low=Input::get('low');
        $catatan=Input::get('catatan');
        $score=Input::get('score');
        $redirect = "";
        $controller = "main";
        if($level=='karyawan'){
            foreach($karyValue as $key=>$val){
               $rowExist = DB::table('hrdEmployeeCompetance')->where('idEmployee','=',$this->idUser)->where('competanceCode','=',$key)->count();
               if($rowExist==1){
                   DB::table('hrdEmployeeCompetance')
                    ->where('idEmployee', $low)
                    ->where('competanceCode', $key)
                    ->update(array('level1_value' => $val));
               }else{
                   DB::table('hrdEmployeeCompetance')->insert(array('idEmployee'=>$this->idUser,'competanceCode'=>$key,'level1_value'=>$val));
               }
            }
            DB::table('hrdEmployeeLevel')->where('idEmployee',$this->idUser)->update(array('employeeNote'=>$catatan,'confirmKaryawan'=>true));
        }elseif($level=='atasan'){
            foreach($karyValue as $key=>$val){
               DB::table('hrdEmployeeCompetance')
                ->where('idEmployee', $low)
                ->where('competanceCode', $key)
                ->update(array('level2_value' => $val));
            }
            DB::table('hrdEmployeeLevel')->where('idEmployee',$low)->update(array('atasanNote'=>$catatan,'confirmAtasan'=>$confirmAtasan,'confirmKaryawan'=>$confirmAtasan));
            $redirect = '/atasan?low='.$low;
        }elseif($level=='peninjau'){
            foreach($karyValue as $key=>$val){
               DB::table('hrdEmployeeCompetance')
                ->where('idEmployee', $low)
                ->where('competanceCode', $key)
                ->update(array('level3_value' => $val));
            }
            DB::table('hrdEmployeeLevel')->where('idEmployee',$low)->update(array('peninjauNote'=>$catatan,'score'=>$score));
            $redirect = '/peninjau?low='.$low;
        }elseif($level=='hcga'){
            DB::table('hrdEmployeeLevel')->where('idEmployee',$low)->update(array('hcgaNote'=>$catatan));
            $redirect = '/employee/'.$low;
            $controller="admin";
        }
//        echo "<pre>";
//        print_r($dataSet);
//        echo "</pre>";
//        echo $catatan;
        return Redirect::to($controller.$redirect)->with(array('message'=>'Terimakasih, Anda telah berhasil melakukan pengisian formulir'));
    }
}