<div id="columns-{{$dtname}}" class="row mb-3 collapse">
    <div class="col-md-12 col-sm-12">
      @foreach($columns as $i=> $column)
        @if(!($column['type']=='menu' || $column['type']=='checkbox'))
        <div class="icheck-success d-inline">
            <input id="check-{{$column['data']}}" value="{{$i}}" class="toggle-vis" type="checkbox" @isset($column['visible']) {{$column['visible']==true?'checked':''}} @else checked @endisset>
            <label for="check-{{$column['data']}}">
            {{__($column['title'])}}
            </label>
        </div>
        @endif
      @endforeach
    </div>
</div>