<!doctype html> 

<html lang="en"> 
    <head> 
            <meta charset="UTF-8"> 
            <title>{{ $title }}</title> 
            {{-- Imports twitter bootstrap and set some styling --}} 
            {{HTML::style('tb/css/bootstrap.min.css')}}
            {{HTML::style('tb/css/custom.css')}}
            {{HTML::style('tb/css/custom-fonts.css')}}
            @yield('head')
    </head>
    <body>
        @yield('main')
    </body>
</html>