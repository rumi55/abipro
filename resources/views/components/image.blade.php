<div class="text-center mb-4">
    <img id="{{$fieldname}}-img" class="profile-user-img img-fluid {{ $holder??'img-circle' }}" src="{{url_image($image_path)}}" alt="{{ $fieldname }}">
</div>
<button type="button" class="btn btn-browse btn-default btn-sm btn-block"><i class="fas fa-image"></i> {{$btn_label ?? __('Choose Image')}}</button>
<small class="text-muted">@isset($helper)<i class="fas fa-info-circle"></i> {{ $helper ?? '' }} @endisset </small>
<input type='file' id="{{ $fieldname }}" name="{{ $fieldname }}" style="display:none" />
@push('js')
<script>
$(function(){
$("#{{$fieldname}}").change(function() {
    if (this.files && this.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#{{$fieldname}}-img').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(this.files[0]); // convert to base64 string
  }
});
$('.btn-browse').click(function(){$('#{{$fieldname}}').trigger('click')})
})
</script>
@endpush