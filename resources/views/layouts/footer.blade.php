
<footer class="main-footer">
    <div class="row">
        <div class="col">
            <strong>Copyright &copy; {{date('Y')=='2020'?date('Y'):'2020-'.date('Y')}} <a href="{{url('/')}}">{{ config('app.name', 'Webapp') }}</a>.</strong> All rights reserved.
        </div>
        <div class="col text-right">
            @php $lang = App::getLocale(); @endphp
              @if($lang=='en')
              <a href="{{route('users.language', ['id'=>'id'])}}" onclick="event.preventDefault();document.getElementById('lang-form').submit();">Bahasa</a>|<strong>English</strong>
              @else
                <strong>Bahasa</strong>|<a href="{{route('users.language', ['id'=>'en'])}}" onclick="event.preventDefault();document.getElementById('lang-form').submit();">English</a>
              @endif
                <form id="lang-form" action="{{route('users.language', ['id'=>$lang=='id'?'en':'id'])}}" method="POST">@csrf</form>

        </div>

    </div>
</footer>
