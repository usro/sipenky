@extends('layout.master')

@section('mainContent')

<div class="row" style="margin-top:18%">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
        <div class="box-1" style="padding:20px;">
            {{Form::open(array('action'=>'UserController@postLogin'))}}
            <input type="hidden" name="_method" value="POST">
            <table class="table-responsive" style="width:100%;text-align:left;">
                <tr>
                    <td>No. Karyawan</td>
                    <td> : &nbsp; </td>
                    <td><input autofocus="true" type="text" name="nokar"></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td>Password</td>
                    <td> : &nbsp; </td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td><input type="submit" name="submit" value="LogIn" class="btn btn-primary"></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            {{Form::close()}}
        </div>
    </div>
    <div class="col-lg-4"></div>
</div>
@stop