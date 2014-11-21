@extends('layout.master')

@section('mainContent')
    @include('layout.upperMenu')
    <br>
    <div class="row">
        <div class="col-lg-6 box-6">
            {{Form::open(array('route'=>'act.kompetensiJab'))}}
            <table class="table-striped" style="width:100%;padding:10px;">
                <tr>
                    <td width="200">Jabatan</td>
                    <td>:</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-chevron-down"></i></span>
                            <input type="text" id="jabatanSearch" value="{{$which->categoryName or ""}}" class="form-control" placeholder="Type to search Jabatan">
                            <input type="hidden" id="hiddenJabatan" value="{{$which->categoryCode or ""}}" class="form-control" placeholder="Type to search Jabatan">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200">Keterangan</td>
                    <td valign="top">:</td>
                    <td>
                        <textarea cols="45" rows="5" name="keterangan" id="keterangan">{{$which->categoryDesc or ""}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200"><input type="reset" value="Reset" class="btn btn-default"></td>
                    <td valign="top"></td>
                    <td></td>
                </tr>
            </table>
            {{Form::close()}}
        </div>
        <div class="col-lg-12">
            <p class="title">List Kompetensi</p>
            <p class="title-liner"></p>
            <?php $qString = isset($which->categoryCode)?"?jabatan=".$which->categoryCode:'' ;?>
        {{ Datatable::table()
    ->addColumn('Kode Kompetensi','Nama Kompetensi','Nilai Standar','Include')       // these are the column headings to be shown
    ->setUrl(URL::to('api/kompetensiJab'.$qString))   // this is the route where data will be retrieved
    ->render() }}    
        </div>
    </div>
@stop

@section('script')
<script>
var cat = "{{URL::to('autocomplete/categoryDesc')}}";
$.ui.autocomplete.prototype._renderItem = function(table, item) {
  return $( "<tr></tr>" )
    .data( "item.autocomplete", item )
    .append( "<td>"+item.label+"</td>")
    .appendTo( table );
};

function checkChange(x){
    var which = $('#hiddenJabatan').val();
    var whichs = $('#jabatanSearch').val();
    if(!whichs){
        alert('Pilih Jabatan !');
        $('#jabatanSearch').focus();
        return false;
    }
    if(x.checked){
        //set checked
        var dv = prompt("Masukan nilai standar", 0);
        $.ajax({
                  url: "{{URL::to('ajax/updateJabKompetensi')}}"+'?which='+which+'&what='+x.name+'&how=add&dv='+dv,
              }).done(function(){
                $('#'+x.name).text('selected');
                $('#'+x.name).attr('class','label label-success');
                $('#dv_'+x.name).text(dv);
              });
    }else{
        //set unchecked
        $.ajax({
                  url: "{{URL::to('ajax/updateJabKompetensi')}}"+'?which='+which+'&what='+x.name+'&how=remove',
              }).done(function(){
                $('#'+x.name).text('select');
                $('#'+x.name).attr('class','label label-default');
                $('#dv_'+x.name).text('');
              });
    }
    return true;
}
    
$(document).ready(function(){
    $('#jabatanSearch').autocomplete({
        autofocus:true,
          source:cat,
          select:function(event,ui){
              window.location.href = '{{URL::to("admin/kompetensiJab?which=")}}'+ui.item.id;
          }
          
    });
    
});
</script>
@stop