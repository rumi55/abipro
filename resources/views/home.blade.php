@extends('layouts.topnav')
@section('title', 'Beranda')
@section('content')
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="{{asset($user!=null&&!empty($user->photo)?url_file($user->photo):'img/noimage.png')}}"
                       alt="photo">
                </div>

                <h3 class="profile-username text-center">{{(!empty($pegawai->gelar_depan)?$pegawai->gelar_depan.' ':'').$pegawai->nama.(!empty($pegawai->gelar_belakang)?', '.$pegawai->gelar_belakang:'')}}</h3>

                <p class="text-muted text-center">{{$pegawai->jabatan->nama}}</p>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Data Pegawai</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong>Pangkat/Golongan</strong>
                <p class="text-muted">
                  {{empty($pegawai->pangkat)?'-':$pegawai->pangkat}}
                </p>
                <hr>
                <strong>Unit Organisasi</strong>
                <p class="text-muted">
                  {{$pegawai->unitorg==null?'-':$pegawai->unitorg->nama}}
                </p>
                <hr>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="row">

          <div class="col-md-12">
          <h5>Aplikasi</h5>
            <a href="{{ url('/simadu') }}" title="Klik untuk membuka aplikasi Simadu" >
            <img src="{{asset('img/simadu.jpg')}}" 
                style="width:100px; height:100px;margin:10px;border: solid 2px #007bff;">
                </a>
            <a href="{{ url('/diseminasi') }}" title="Klik untuk membuka aplikasi Diseminasi">
            <img src="{{asset('img/dls.jpg')}}" 
                style="width:100px; height:100px;margin:10px;border: solid 2px #6f42c1;">
                </a>
            @if(user('role.name')=='super-admin' || user('role.name')=='admin')
            
            <a href="{{ url('/adminzone') }}" title="Klik untuk membuka AdminZone">
            <img src="{{asset('img/adminzone.jpg')}}" 
                style="width:100px; height:100px;margin:10px;border: solid 2px #dc3545;">
                </a>
            @endif
            </div>
            </div>
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#agenda" data-toggle="tab">Agenda</a></li>
                  <li class="nav-item"><a class="nav-link" href="#pesan" data-toggle="tab">Pesan</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="agenda">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Rapat/Pertemuan</h3>
                                        <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                        </button>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        @if(count($rapat)>0)
                                        <table class="table">
                                        <tbody>
                                        @foreach($rapat as $i=> $rpt)
                                            <tr>
                                                <td>{{$i+1}}</td>
                                                <td>{{$rpt->nama}}</td>
                                                <td>{{fdatetime($rpt->waktu_mulai)}}</td>
                                                <td>{{$rpt->tempat}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        </table>
                                        @else
                                            <blockquote>
                                                Belum ada agenda rapat/pertemuan beberapa hari ke depan.
                                            </blockquote>
                                        @endif
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <div class="post">
                      <div class="user-block">
                        <img class="img-circle img-bordered-sm" src="{{asset(!empty(user('photo'))?url_file(user('photo')):'')}}" alt="user image">
                        <span class="username">
                          <a href="#">Jonathan Burke Jr.</a>
                          <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                        </span>
                        <span class="description">Shared publicly - 7:30 PM today</span>
                      </div>
                      <!-- /.user-block -->
                      <p>
                        Lorem ipsum represents a long-held tradition for designers,
                        typographers and the like. Some people hate it and argue for
                        its demise, but others ignore the hate as they create awesome
                        tools to help create filler text for everyone from bacon lovers
                        to Charlie Sheen fans.
                      </p>

                      <p>
                        <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                        <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                        <span class="float-right">
                          <a href="#" class="link-black text-sm">
                            <i class="far fa-comments mr-1"></i> Comments (5)
                          </a>
                        </span>
                      </p>

                      <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                    </div>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputName" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputName2" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
@endsection
@push('css')
@endpush
@push('js')
<script type="text/javascript">
    $(function(){});
</script>
@endpush