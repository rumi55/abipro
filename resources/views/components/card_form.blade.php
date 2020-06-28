<div class="card {{ $card_type ?? 'card-primary card-outline' }}">
    <form action="{{$action ?? '' }}" id="{{ $id ?? ''}}" method="{{isset($method)&&$method=='GET'?'GET':'POST'}}" enctype="multipart/form-data" autocomplete="off" class="{{ $form_type ?? '' }}">
    @isset($method)
        @if($method!='GET')
        @csrf
        @endif
        @if($method=='PUT')
            @method($method)
        @endif
    @endisset    
    @isset($title)
        <div class="card-header">
            <h5 class="card-title">{{$title ?? ''}}</h5>
            <div class="card-tools">
                {{$tools ?? ''}}
            </div>
        </div>
    @endisset
        <div class="card-body">
        {{$slot}}
        </div>
        <div class="card-footer">
            <button id="{{$btn_id??$id.'-btn'}}" type="submit" class="btn btn-primary">{{ $btn_label ?? trans('Save')}}</button>
        </div>
    </form>    
</div>