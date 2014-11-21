<br>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" {{($page=='home')?'class="active"':''}}><a href="{{URL::to('admin')}}"><i class="glyphicon glyphicon-home"></i> Home</a></li>
            <li role="presentation" {{($page=='kompetensi')?'class="active"':''}}><a href="{{URL::to('admin/kompetensi')}}"><i class="glyphicon glyphicon-list-alt"></i> Master Kompetensi</a></li>
            <li role="presentation" {{($page=='jabatan')?'class="active"':''}}><a href="{{URL::to('admin/jabatan')}}"><i class="glyphicon glyphicon-list-alt"></i> Master Jabatan</a></li>
            <li role="presentation" {{($page=='peninjau')?'class="active"':''}}><a href="{{URL::to('admin/peninjau')}}"><i class="glyphicon glyphicon-cog"></i> Atasan, Peninjau, Jabatan</a></li>
            <li role="presentation" {{($page=='kompetensiJab')?'class="active"':''}}><a href="{{URL::to('admin/kompetensiJab')}}"><i class="glyphicon glyphicon-cog"></i> Kompetensi Jabatan</a></li>
            <li role="presentation" {{($page=='report')?'class="active"':''}}><a href="{{URL::to('admin/report')}}"><i class="glyphicon glyphicon-list-alt"></i> Report</a></li>
        </ul>
    </div>
</div>