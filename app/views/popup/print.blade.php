@extends('layout.simple')

@section('head')
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable({{$kompetensiJson}});
        var options = {
          title: 'Company Performance'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
@stop

@section('main')
<div class="container">
    <h3>{{$selectedSection}}</h3>
    <br>
    <table class="table-striped table-bordered" style="width:100%">
        <tr>
            <th colspan="4" style="text-align: center">&nbsp; </th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Jabatan</th>
            <th>Nama</th>
            <th style="text-align: center">AK Score</th>
        </tr>
        <?php $i = 0; ?>
        @foreach($employees as $employee)
        <?php $i++; ?>
        <tr>
            <td>{{$i}}</td>
            <td>{{$employee->categoryName}}</td>
            <td>{{$employee->employeeName}}</td>
            <td style="text-align: center">{{$employee->score}}</td>
        <input type="hidden" id="employee_{{$employee->id}}" value="{{$employee->employeeNote}}">
        <input type="hidden" id="atasan_{{$employee->id}}" value="{{$employee->atasanNote}}">
        <input type="hidden" id="peninjau_{{$employee->id}}" value="{{$employee->peninjauNote}}">
        <input type="hidden" id="name_{{$employee->id}}" value="{{ucwords(strtolower($employee->employeeName))}}">
        </tr>
        @endforeach
    </table>

    <br>
    <table class='table-bordered' style='width:100%' >
        <tbody>
            <tr style='background:#eee'><th>Pencapaian</th><th style='text-align:center' colspan='2'>Total</th></tr>
            <tr class='danger'><td>Tidak Dapat Diterima</td><td style='text-align:center'>{{$kompetensi[1][1]}}</td></tr>
            <tr class='danger'><td>Tidak Kompeten</td><td style='text-align:center'>{{$kompetensi[2][1]}}</td></tr>
            <tr class='warning'><td>Cukup Kompeten</td><td style='text-align:center'>{{$kompetensi[3][1]}}</td></tr>
            <tr class='warning'><td>Kompeten</td><td style='text-align:center'>{{$kompetensi[4][1]}}</td></tr>
            <tr class='warning'><td>Lebih Kompeten</td><td style='text-align:center'>{{$kompetensi[5][1]}}</td></tr>
            <tr class='warning'><td>Sangat Kompeten</td><td style='text-align:center'>{{$kompetensi[6][1]}}</td></tr>
        </tbody>
    </table>
    <div id="chart_div" style="width: 800px; height: 500px;"></div>
</div>
@stop
