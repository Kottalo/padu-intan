@php
  $tabs = App\Http\Controllers\RouteController::getRoutes();

  $url = array_reverse(explode('/', URL::current()));
@endphp

<!doctype html>
<html lang="zh">
  <head>
    <meta charset="UTF-8">
    <meta prefix="viewport" content="width=device-width,initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Padu Intan</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
  </head>

  <body>
    @isset($user)
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm justify-content-center py-1">
      <div class="container-fluid">
        <div class="col-md-2 h1">
          Padu Intan
        </div>

        <div class="col-md-8">
          <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
            @foreach ($tabs as $tab)
            <li class="nav-item" role="presentation">
              <a class="nav-link @if ($url[1] == $tab['prefix']) active @endif" id="{{ $tab['prefix'] }}-tab" data-toggle="tab" href="#{{ $tab['prefix'] }}" role="tab" aria-controls="{{ $tab['prefix'] }}" aria-selected="true">{{ $tab['title'] }}</a>
            </li>
            @endforeach
          </ul>

          <div class="tab-content pt-2" id="myTabContent">
            @foreach ($tabs as $tab)
            <div class="tab-pane fade @if ($url[1] == $tab['prefix']) show active @endif" id="{{ $tab['prefix'] }}" role="tabpanel" aria-labelledby="{{ $tab['prefix'] }}-tab">
              <ul class="nav nav-pills justify-content-center">
                @foreach ($tab['links'] as $link)
                <li class="nav-item">
                  <a class="nav-link @if ($url[0] == $link['url']) active @endif" href="{{ '/' . $tab['prefix'] . '/' . $link['url'] }}">{{ $link['title'] }}</a>
                </li>
                @endforeach
              </ul>
            </div>
            @endforeach
          </div>
        </div>

        <div class="col-md-1 offset-md-1">
          <div class="h5 text-center">
            {{ $user->username }}
          </div>

          <a class="btn btn-danger my-2 my-sm-0 btn-block" href="{{ route('logout') }}"
             onclick="event.preventDefault();document.getElementById('logout-form').submit();"
          >
            登出
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </div>
      </div>
    </nav>
    @endisset

    <div class="p-3 container-fluid text-center">
      @isset($user)
      <div class="m-3 h1">{{ $title }}</div>
      @endisset

      <div class="">
        @yield('content')
      </div>
    </div>
  </body>
</html>
