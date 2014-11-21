@extends('layout.master')

@section('mainContent')
@include('layout.upperMenu')
<br>
<div class="row">
    <div class="col-lg-12">
            {{ Datatable::table()
    ->addColumn('id','Name','Section', 'Atasan','Peninjau Langsung','Job Title / Position')       // these are the column headings to be shown
    ->setUrl(route('api.users'))   // this is the route where data will be retrieved
    ->render() }}
    </div>
</div>

@stop

@section('script')
<script>
    var checkMembershipRequest = "{{URL::to('autocomplete/users')}}";
    var cat = "{{URL::to('autocomplete/category')}}";

$.ui.autocomplete.prototype._renderItem = function(table, item) {
  return $( "<tr></tr>" )
    .data( "item.autocomplete", item )
    .append( "<td>"+item.label+"</td>"+"<td>&nbsp;|&nbsp;</td>"+"<td>"+item.value+"</td>")
    .appendTo( table );
};
  function atasan(x){
      $(x).autocomplete({
          autofocus:true,
          source:checkMembershipRequest,
          select:function(event,ui){
              var str = x.id;
              var split = str.split("_");
              var id = split[1];
              $.ajax({
                  url: "{{URL::to('autocomplete/saveAtasan')}}"+'?atasan='+ui.item.id+'&id='+id,
              })
          },
      });
                    
    
  }
  
  function peninjau(x){
      $(x).autocomplete({
          autofocus:true,
          source:checkMembershipRequest,
          select:function(event,ui){
              var str = x.id;
              var split = str.split("_");
              var id = split[1];
              $.ajax({
                  url: "{{URL::to('autocomplete/savePeninjau')}}"+'?peninjau='+ui.item.id+'&id='+id,
              })
          }
      });
  }
  function category(x){
      $(x).autocomplete({
          autofocus:true,
          source:cat,
          select:function(event,ui){
              var str = x.id;
              var split = str.split("_");
              var id = split[1];
              $.ajax({
                  url: "{{URL::to('autocomplete/saveCategory')}}"+'?categoryCode='+ui.item.id+'&id='+id,
              })
          }
      });
  }
  
  function atasanRemove(x){
       var str = x.id;
        var split = str.split("_");
        var id = split[1];
      if(x.value==''){
        $.ajax({
          url: "{{URL::to('autocomplete/removeAtasan')}}"+'?id='+id,
        })
      }
  }
  
  function peninjauRemove(x){
       var str = x.id;
        var split = str.split("_");
        var id = split[1];
      if(x.value==''){
        $.ajax({
          url: "{{URL::to('autocomplete/removePeninjau')}}"+'?id='+id,
        })
      }
  }

  
  
    
</script>
@stop