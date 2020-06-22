<div class="modal fade {{$class ?? ''}}" id="{{$id ?? ''}}">
  <div class="modal-dialog">
    <div class="modal-content {{$bg ?? ''}}">
    <form id="{{ $form_id ?? '' }}" action="{{$action}}" method="POST">
    @csrf
    @isset($method)
      @method($method)
    @endisset
      <div class="modal-header">
        <h4 class="modal-title">{{$title}}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{$slot}}
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn {{empty($bg)?'btn-default':'btn-outline-light'}}" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn {{empty($bg)?'btn-primary':'btn-outline-light'}}">{{$btn_label}}</button>
      </div>
    </form>
    </div>
  </div>
</div>