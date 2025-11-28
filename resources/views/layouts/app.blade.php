<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>販売管理システム</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @yield('styles')  {{-- 子Bladeから追加CSSを受け取る --}}
</head>
<body class="@yield('body-class')">

  
  {{-- ログイン画面以外で、ログイン中のときだけ表示 --}}
  @if(View::getSection('bsody-class') !== 'login-body' && Auth::check())
    <header class="logout-area">
        <div class="admin-user">
          @if(Auth::user()->role === 'admin')
            管理者さん
          @else
            {{ Auth::user()->user_id }} さん
          @endif

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">ログアウト</button>
          </form>
        </div>
    </header>
  @endif


  <main>
    @yield('content')
  </main>

  <footer>
    <p>&copy; 2025 販売管理システム</p>
  </footer>
  @yield('scripts')
</body>
</html>