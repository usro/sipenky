@extends('layout.master')

@section('mainContent')
<br>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <ul class="nav nav-tabs" role="tablist">
            {{$karyawanStr}}
            {{$atasanStr}}
            {{$peninjauStr}}
</ul>
        <div class="box-5" style="border-top:0px;">
            <span class="pull-right text text-muted"><small>Form No ......./HRD/.......</small></span>
            <div class="page-header">
                <h1><b>Form. Asesmen Kompetensi </b></h1> {{HTML::image('file/images/2686759.png')}}
            </div>
            <table>
                <tr>
                    <td width="75">Nama </td>
                    <td width="10">: </td>
                    <td width="450">
                        @if($bawahan<>0)
                        <select class="custom-select" id="bawahan">
                            @foreach($bawahanList as $bawahan)
                            <option {{$bawahan->id==$selectedBawahan?'selected':''}} value="{{$bawahan->id}}">{{ ucwords(strtolower($bawahan->employeeName))}}</option>
                            @endforeach
                        </select>
                        @else
                        {{ ucwords(strtolower($selectedBawahanDetail->employeeName))}}<input type="hidden" id="bawahan">
                        @endif
                    </td>
                    <td width="50"></td>
                    <td width="150">Departemen</td>
                    <td width="10">: </td>
                    <td width="250">{{ucwords(strtolower($selectedBawahanDetail->departmentName))}} </td>
                </tr>
                <tr>
                    <td width="100">NIK </td>
                    <td width="10">: </td>
                    <td>{{$selectedBawahanDetail->employeeID}}</td>
                    <td width="380"></td>
                    <td width="100">Bergabung</td>
                    <td width="10">: </td>
                    <td width="10"> {{$selectedBawahanDetail->joinDate}} </td>
                </tr>
                <tr>
                    <td width="100">Posisi </td>
                    <td width="10">: </td>
                    <td>{{$selectedBawahanDetail->positionName}}</td>
                    <td width="380"></td>
                    <td width="100">Atasan Langsung</td>
                    <td width="10">: </td>
                    <td width="10"> {{($selectedBawahanDetail->atasan==$user->employeeName)?'<span class"text text-info"> Anda</span>':ucwords(strtolower($selectedBawahanDetail->atasan)) }}
                    
                    </td>
                </tr>
                <tr>
                    <td width="100">Divisi </td>
                    <td width="10">: </td>
                    <td>{{$selectedBawahanDetail->sectionName}}</td>
                    <td width="380"></td>
                    <td width="100">Peninjau Ulang</td>
                    <td width="10">: </td>
                    <td width="10"> {{($selectedBawahanDetail->peninjau==$user->employeeName)?'<span class"text text-info"> Anda</span>':ucwords(strtolower($selectedBawahanDetail->peninjau)) }} </td>
                </tr>
            </table>
            <br>
            @if($submit['submitKaryawan']) 
            <span class="text text-info"><i class="glyphicon glyphicon-info-sign"></i> Terimakasih, Anda telah berhasil melakukan penilaian.</span>
            @elseif(!$submit['submitBawahan1'])
            <span class="text text-warning"><i class="glyphicon glyphicon-info-sign"></i> Tidak dapat melanjutkan. <b>{{ucwords(strtolower($selectedBawahanDetail->employeeName))}}</b> belum melakukan penilaian.</span>
            @elseif(!$submit['submitBawahan2'])
            <span class="text text-warning"><i class="glyphicon glyphicon-info-sign"></i> Tidak dapat melanjutkan. <b>{{ucwords(strtolower($selectedBawahanDetail->atasan))}}</b> belum melakukan penilaian.</span>
            @endif
            {{Form::open(array('action'=>'MainController@postSaveAsesment'))}}
            {{$strHidden}}
            <table class="table-bordered table-hover table-custom" style="width:100%;">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">No</th>
                        <th rowspan="2">Jenis Kompetensi</th>
                        <th width="75" rowspan="2">Level Standar</th>
                        <th colspan="{{$colspan}}" style="text-align:center">Penilaian</th>
                        {{$strSenjang or ""}}
                    </tr>
                    <tr>
                        {{$strHead}}
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:#eee">
                        <td>&nbsp;</td>
                        <td style="text-align:left;padding-left:5px;"></td>
                        <td></td>
                        <td><span style="display:block;cursor:pointer" class="text text-muted tip-1"data-toggle="tooltip" data-placement="left" ><i class="glyphicon glyphicon-question-sign" ></i></span></td>
                        {{$strHRow}}
                    </tr>
                    {{$strRow}}
                </tbody>
            </table><br>
            <div class="row">
                <div class="col-lg-6">
                    {{$summaryDesc or ""}}
                </div>
                <div class="col-lg-6">
                    {{$summary or ""}}
                </div>
            </div>
            <input type="hidden" id="score" name="score">
            <br>
            {{$catatan}}
            {{(!$submit['submitKaryawan'] and !$submit['confirmAtasan'] and $submit['submitBawahan1'] and $submit['submitBawahan2'] and !$submit['submitHcga'])?'<input type="submit" name="submit" onclick="javascript:return selectValid()" value="Submit" class="btn btn-primary">':''}}
            {{(!$submit['confirmAtasan'])?$btnConfirm:''}}
            <br><br>
            {{Form::close()}}

        </div>
    </div>
</div><br>
@stop

@section('script')
<script>
$(document).ready(function(){
$('.tip').tooltip();
$('.tip-1').tooltip({
    html:true,
    title:'<ul style="padding:10px;" class="list-unstyled"><li>0 = Description </li><li>1 = Description </li><li>2 = Description </li></ul>'
});

$('#bawahan').on('change',function(){
    window.location.href = '{{URL::to("main/".strtolower($level)."?low=")}}'+this.value;
});
$('.kary-value').each(function(){
    $(this).on('change',function(){
        var x = $('#df-'+this.id).val();
        if(this.value>x){
            alert('Tidak boleh lebih besar dari nilai standar.');
            this.value = x;
            return false;
        }
    });
});
// nilai senjang counts
$('.senjangCount').each(function(){
    $(this).on('change',function(){
        var x = $('#df-'+this.id).val();
        var z = this.value - x ;
        $('#sv-'+this.id).text(z);
        
        var peninjauTotal = 0;
        var senjangTotal = 0;
        var defaultTotal = 0;
        $('.senjangCount').each(function(){
            defaultTotal += +Number($('#df-'+this.id).val());
            senjangTotal += +Number($('#sv-'+this.id).text());
            peninjauTotal += +Number($(this).val());
        });
        var rata2 = Math.floor((peninjauTotal/defaultTotal)*100)/100;
        $('#total_value').text(peninjauTotal);
        $('#senjang_value').text(senjangTotal)
        $('#value').text(rata2);
        $('#score').val(rata2);
    });
});

});
//rule 1
$('.rule-1').each(function(){
        var kv = $('#kv-'+this.id).val();
        var av = $(this).val();
//            alert(av+' < '+kv);
        if(Number(av)<Number(kv)){
        $('#confirm').attr('disabled','true');
        $(this).attr('style','color:red');
    }
});
$('.rule-1').each(function(){
        $(this).on('change',function(){
        $('#confirm').removeAttr('disabled');
        $('.rule-1').each(function(){
            var kv = $('#kv-'+this.id).val();
            var av = $(this).val();
//            alert(av+' < '+kv);
            if(Number(av)<Number(kv)){
            $('#confirm').attr('disabled','true');
            $(this).attr('style','color:red');
            }else{
                $(this).attr('style','color:black');
            }
        });
    });
});

function selectValid(){
    $('.must-valid').each(function(){
    if(this.value==0){
        x = false;
        alert('Tidak boleh sama dengan 0');
        $(this).focus();
        return false;
    }else{
        x = true;
    }
    });
if(x){
    return true;
}else{
    return false;
}
}
</script>
@stop