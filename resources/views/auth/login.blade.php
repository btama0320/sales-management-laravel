@extends('layouts.app')
@section('body-class', 'login-body')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection

@section('content')
<div class="login-container">
  <h2>ログイン画面</h2>
  <p>※IDとパスワードを入力してください</p>

{{-- ログアウト後のメッセージ表示 --}}
@if (session('status'))
  <div class="alert-info">
    {{ session('status') }}
  </div>
@endif

{{-- ログイン失敗時のエラーメッセージ --}}
@if ($errors->has('login'))
  <div class="alert-error">
    {{ $errors->first('login') }}
  </div>
@endif


  <form method="POST" action="{{ route('login.submit') }}" onsubmit="return validateForm()">
    @csrf
    <label for="user_id">社員ID</label>
    <input type="text" id="user_id" name="user_id" required pattern="[a-zA-Z0-9]+" placeholder="半角英数字のみ">

    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" required minlength="6" placeholder="半角英数字8文字以上">

    <button type="submit">ログイン</button>
  </form>
</div>

<script>
  function validateForm() {
    const user_id = document.getElementById('user_id');
    const password = document.getElementById('password');

    if (!user_id.value.match(/^[a-zA-Z0-9]+$/)) {
      alert("IDは半角英数字のみで入力してください。");
      user_id.focus();
      return false;
    }

    if (password.value.length < 6) {
      alert("パスワードは6文字以上で入力してください。");
      password.focus();
      return false;
    }

    return true;
  }
</script>
@endsection