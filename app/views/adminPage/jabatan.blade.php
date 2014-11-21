@extends('layout.master')

@section('mainContent')
    @include('layout.upperMenu')
    <br>
    
    <div class="row">
        <div class="col-lg-6 box-6">
            {{Form::open(array('route'=>'act.jabatan'))}}
            <table class="table-striped" style="width:100%;padding:10px;">
                <tr>
                    <td width="200">Nama Jabatan</td>
                    <td>:</td>
                    <td>
                        <input type="text" name="nama" class="input-250">
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200">Keterangan</td>
                    <td valign="top">:</td>
                    <td>
                        <textarea name="keterangan"></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="200"><input type="submit" name="submit" value="Simpan" class="btn btn-primary"> <input type="reset" value="Reset" class="btn btn-default"></td>
                    <td valign="top"></td>
                    <td></td>
                </tr>
            </table>
            {{Form::close()}}
        </div>
        <div class="col-lg-12">
            <p class="title">List Jabatan</p>
            <p class="title-liner"></p>
            {{ Datatable::table()
    ->addColumn('id','Kode Jabatan','Nama Jabatan','Action')       // these are the column headings to be shown
    ->setUrl(route('api.jabatan'))   // this is the route where data will be retrieved
    ->render() }}
        </div>
    </div>
@stop