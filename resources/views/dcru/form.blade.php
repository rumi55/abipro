@component('components.card_form',[ 'id'=>'create-form', 
'form_type'=>'form-horizontal', 
'method'=>'POST',
'action'=>asset(route('dcru.create.save', ['name'=>$name],false))])
      @slot('title')
      <a href="{{asset(route('dcru.index', ['name'=>$name], false))}}"><i class="fas fa-chevron-left"></i></a> {{($mode=='edit'?'Edit ':'Tambah ').$section_title }}
      @endslot
      <input type="hidden" name="_mode" id="_mode" value="{{$mode}}" />
      @if($mode=='edit')
      <input type="hidden" name="id" id="id" value="{{$data->id}}" />
      @endif
      @include('dcru._form')
    @endcomponent

    @if($mode=='edit')
@component('components.modal_form', [
'id'=>'modal-delete', 'title'=>'Hapus', 'btn_label'=>'Hapus','bg'=>'bg-danger',
'method'=>'delete', 'action'=>asset(route('dcru.delete.file', ['name'=>$name],false))
])
  Apakah Anda yakin akan menghapus gambar tersebut?
  <input type="hidden" value="{{$data->id}}" name="id" id="file_id" />
  <input type="hidden" value="" name="file" id="file" />
  
@endcomponent
@endif