@extends('layout.master')

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

@section('mainContent')
    @include('layout.upperMenu')
    <div class="row top-space">
        <div class="col-lg-12"><span id="print" class="btn pull-right" title="print"><i class="glyphicon glyphicon-print"></i></span></div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table class="table-striped" style="width:100%;padding:10px;">
                <tr>
                    <td width="200">Section </td>
                    <td>:</td>
                    <td>
                        <select name="section">
                            <option value="all" onclick="javascript:return redirectSection(this.value)">-- Show All --</option>
                            @foreach($sections as $section)
                            <option value="{{$section->sectionCode}}" {{($section->sectionCode==$selectedSection)?'selected':''}} onclick="javascript:return redirectSection(this.value)">{{$section->sectionName}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            
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
                    <td><a href="{{URL::to('admin/employee/'.$employee->id)}}" target="_blank">{{$employee->employeeName}}</a></td>
                    <td style="text-align: center">{{$employee->score}}</td>
                    <input type="hidden" id="employee_{{$employee->id}}" value="{{$employee->employeeNote}}">
                    <input type="hidden" id="atasan_{{$employee->id}}" value="{{$employee->atasanNote}}">
                    <input type="hidden" id="peninjau_{{$employee->id}}" value="{{$employee->peninjauNote}}">
                    <input type="hidden" id="name_{{$employee->id}}" value="{{ucwords(strtolower($employee->employeeName))}}">
                </tr>
                @endforeach
            </table>
        </div>
        <div class="col-lg-6">
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
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div id="chart_div" style="width: 100%; height: 500px;"></div>
        </div>
    </div>
    
    
    
    
    
    
    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><small>Catatan </small><span id="karyawanName" class="text text-primary"></span></h4>
      </div>
      <div class="modal-body">
          <table style="width:100%">
              <tr>
                  <td>Catatan Karyawan</td>
                  <td><textarea id="textKaryawan"></textarea></td>
              </tr>
              <tr>
                  <td>Catatan Atasan</td>
                  <td><textarea id="textAtasan"></textarea></td>
              </tr>
              <tr>
                  <td>Catatan Peninjau</td>
                  <td><textarea id="textPeninjau"></textarea></td>
              </tr>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@stop

@section('script')
<script>
function redirectSection(x){
    return window.location.href="{{URL::to('admin/report?section=')}}"+x;
}

$('#print').on('click',function(){
    window.open('{{URL::to("print/section?section=".$selectedSection)}}','_blank');
});

function customModal(x){
    $('#textKaryawan').text($('#employee_'+x).val());
    $('#textAtasan').text($('#atasan_'+x).val());
    $('#textPeninjau').text($('#peninjau_'+x).val());
    $('#karyawanName').text($('#name_'+x).val());
}
</script>
@stop