@extends('auth.layout')
@section('title', 'Register')  
@section('content')        
<form method="POST" action="{{ route('company.create') }}">
  @csrf  
  <div class="form-group">
    <label for="name">Nama Perusahaan</label>
    <input id="name" name="name" type="text" class="form-control" placeholder="Nama Perusahaan">
  </div>
  <div class="form-group">
    <label for="company_type_id">Jenis Usaha</label>
    <select class="form-control" name="company_type_id" id="company_type_id">
      @foreach($types as $type)
      <option value="{{$type->id}}">{{$type->display_name}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <button type="submit" class="btn btn-primary btn-block">Buat Perusahaan</button>
  </div>
</form>
@endsection