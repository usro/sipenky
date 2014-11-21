<!doctype html> 

<html lang="en"> 
    <head> 
            <meta charset="UTF-8"> 
            <title>{{ $title }}</title> 
            {{-- Imports twitter bootstrap and set some styling --}} 
            {{HTML::style('tb/css/bootstrap.min.css')}}
            {{HTML::style('tb/css/custom.css')}}
            {{HTML::style('tb/css/custom-fonts.css')}}
             <!-- Datatables CSS-->
            {{HTML::style('datatables/media/css/jquery.dataTables.css')}}
            <!-- DataTables -->
            {{HTML::script('tb/js/jquery-2.1.1.min.js')}}
            {{HTML::script('datatables/media/js/jquery.dataTables.js')}}

            <!--Autocomplete Jquery UI -->
            {{HTML::script('tb/js/jquery-ui.min.js')}}
            {{HTML::style('tb/css/jquery-ui.min.css')}}
            
            
        <style> 
        /*body { background-image:url('file/images/stripped-background.jpg')}*/
        body{
                font-family: Actor,Arial,Helvetica,sans-serif !important;
            }
        </style> 
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="icon" href="{{ URL::to('favicon.ico') }}" type="image/x-icon">
        @yield('head')
    </head>
    <body>
        <div class="row-fluid" id="topInfo">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <ul class="custom-nav-top noPadding">
                            <li><a style="font-size:11px;">Sistem Penilaian Karyawan - 2014</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                    </div>
                </div>
            </div>
        </div>
        <!--=Header===============================================================================-->
        <div class="row-fluid"  id="header">
            <div class="container">
                <div class="row">
                <div class="col-sm-2">
                    {{HTML::image('file/images/2686759.png','',array('width'=>'130'))}}
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-5" style="padding-top:40px;">
                    <!--=Menu===============================================================================-->
                </div>
                <div class="col-sm-4">
                    <div class="row">
                        <div class="pull-right" style="padding-top:40px;">
                            <a href="{{URL::to('faq')}}"><small class="text text-muted"></small></a> 
                        </div>
                    </div>
                    <div class="row">
                        @if(isset($user))
                        <div class="pull-right">
                            <a href="#"><small><b>{{ucwords(strtolower($user->employeeName))}}</b></small></a>,  <a href="{{URL::to('users/logout')}}"><small> Logout <i class="glyphicon glyphicon-log-out"></i></small> </a>
                        </div>
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row" id="content" >
                <div class="container">
                    
                    @yield('mainContent')
                </div>
            </div>
        </div>

    </body>
    {{HTML::script('tb/js/bootstrap.min.js')}}
    {{HTML::script('tb/js/tooltip.js')}}
    
    @yield('script')
</html>