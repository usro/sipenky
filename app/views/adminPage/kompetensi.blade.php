@extends('layout.master')

@section('mainContent')
    @include('layout.upperMenu')
    <br>
    <div class="row">
        <div class="col-lg-6 box-6">
            {{Form::open(array('route'=>'act.kompetensi'))}}
            <table class="table-striped" style="width:100%;padding:10px;">
                <input type="hidden" name="action" value="save">
                <tr>
                    <td width="200">Kategori Kompetensi</td>
                    <td>:</td>
                    <td>
                        <select name="kategori">
                            <option value="inti">Inti</option>
                            <option value="managerial">Managerial</option>
                            <option value="fungsional">Fungsional</option>
                            <option value="teknikal">Teknikal</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="200">Nama Kompetensi</td>
                    <td>:</td>
                    <td>
                        <input type="text" name="nama" class="input-250">
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200">Keterangan</td>
                    <td valign="top">:</td>
                    <td>
                        <textarea name="keterangan" cols="50" rows="10" ></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200" colspan="2"><input type="submit" name="submit" value="Simpan" class="btn btn-primary"> <input type="reset" value="Reset" class="btn btn-default"></td>
                    <td valign="top"></td>
                </tr>
            </table>
            {{Form::close()}}
        </div>
        <div class="col-lg-12">
            <p class="title">List Kompetensi</p>
            <p class="title-liner"></p>
        {{ Datatable::table()
    ->addColumn('Order','Kode Kompetensi','Nama Kompetensi','Keterangan','Action')       // these are the column headings to be shown
    ->setUrl(route('api.kompetensi'))   // this is the route where data will be retrieved
    ->render() }}    
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    {{Form::open(array('route'=>'act.kompetensi'))}}
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
            <table class="table-striped" style="width:100%;padding:10px;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="idKom" id="idKom">
                <tr>
                    <td width="200">Nama Kompetensi</td>
                    <td>:</td>
                    <td>
                        <input type="text" name="namaKom" autocomplete="off" id="namaKom" class="input-250">
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200">Keterangan</td>
                    <td valign="top">:</td>
                    <td>
                        <textarea name="keteranganKom" cols="50" rows="10" id="keteranganKom"></textarea>
                    </td>
                </tr>
            </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="submit" class="btn btn-primary" value="Simpan">
                </div>
            </div>
        </div>
    {{Form::close()}}
    </div>
@stop

@section('script')
<script>
function editDesc(x){
    var y = prompt('Edit Keterangan');
    var z = $(x).attr('class');
    if(y==null||y==''){
        return false;
    }else{
    $.ajax({
        url:"{{URL::to('ajax/updateComDesc')}}"+'?id='+z+'&val='+y,
    }).done(function(){
        $(x).text(y);
    })
    }
}

function invokeValue(x){
    var name = $('#name').val();
    var id = $('#id').val();
    var desc = $('#description').val();
    $('#namaKom').val(name);
    $('#idKom').val(id);
    $('#keteranganKom').val(desc);
}
</script>
@stop