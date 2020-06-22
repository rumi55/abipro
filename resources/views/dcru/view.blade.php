@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="row">
  <div class="col-lg-12">
    @component('components.card')
      @slot('title') 
        <a href="{{asset(route('dcru.index', ['name'=>$name],false))}}"><i class="fas fa-chevron-left"></i></a> {{$section_title}} 
      @endslot
      @slot('tools')
        <button type="button" class="btn btn-tool" data-toggle="dropdown">
          <i class="fas fa-th"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right" role="menu">
          <a href="{{asset(route('dcru.edit',['name'=>$name, 'id'=>$data->id],false))}}" class="dropdown-item"  ><i class="fas fa-edit"></i> Edit</a>
          <a href="{{asset(route('dcru.create.duplicate',['name'=>$name, 'id'=>$data->id],false))}}" class="dropdown-item"  ><i class="fas fa-copy"></i> Gandakan</a>
          <a href="#" class="dropdown-item"  data-toggle="modal" data-target="#modal-delete"><i class="fas fa-trash"></i> Hapus</a>
        </div>
      @endslot
      <div class="table-responsive">
      <table class="table" style="width:100%">
      @foreach($fields as $field)
        <tr>
          <td class="font-weight-bold" style="border-top:0;width:200px;">{{$field['label']}}</td>
          <td style="width:30px;border-top:0;">:</td>
          <td style="border-top:0;">
          @php $nm = $field['name'] @endphp
          @empty($data->$nm)
          -
          @else
          @switch($field['type'])
              @case('image')
                  <a href="{{asset(url_file($data->$nm))}}" class="img-link"><img src="{{asset(url_file($data->$nm))}}" class="img-thumbnail" width="20%"/></a>
                  @break
              @case('boolean')
                  @if($data->$nm==1) <i class="fas fa-check"></i> @endif
                  @break
              @case('date')
                  {{ fdate($data->$nm) }}
                  @break
              @case('hdate')
                  <span title="{{ fdatetime($data->$nm) }}">{{ hdate($data->$nm) }}</span>
                  @break
              @case('datetime')
                  {{ fdatetime($data->$nm) }}
                  @break
              @case('file')
                  <div class="btn-group text-center" role="group" aria-label="file-action">
                        <form action="{{asset(route('dcru.download', [], false))}}" method="post">
                        <a href="{{asset(url_file($data->$nm))}}" title="Lihat Berkas" target="_blank" class="btn btn-xs btn-secondary"><i class="fas fa-eye"></i></a>
                        {{csrf_field()}}
                        <input type="hidden" name="file" value="{{$data->$nm}}"/>
                        <button title="Unduh Berkas" type="submit" class="btn btn-xs btn-secondary"><i class="fas fa-download"></i></button>
                        </form>
                        </div>
                  @break
              @default
                  {!! $data->$nm !!}
          @endswitch
          @endempty
          </td>
        <tr>
      @endforeach
      </table>
      </div>
    @endcomponent
  </div>
</div>

@component('components.modal', [
'id'=>'modal-preview', 'title'=>'Pratinjau'
])
  Contoh
@endcomponent
@component('components.modal_form', [
'id'=>'modal-delete', 'title'=>'Hapus', 'btn_label'=>'Hapus','bg'=>'bg-danger',
'method'=>'delete', 'action'=>asset(route('dcru.delete', ['name'=>$name, 'id'=>$data->id],false))
])
  Apakah Anda yakin akan menghapus data ini?
@endcomponent
@endsection

@push('css')
@endpush

@push('js')
<script type="text/javascript">
$(document).ready(function () {
  $('.img-preview').click(function(e){
    e.preventDefault();
    var src = $(this).attr('data-file');
    $('#file').val(file);
    $('#modal-preview').modal('show');
  });
});
</script>@endpush