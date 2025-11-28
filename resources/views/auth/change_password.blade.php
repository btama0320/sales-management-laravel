@extends('layouts.app') {{-- 親テンプレートを継承 --}}

@section('body-class', 'password-change-body') {{-- bodyに専用クラスを渡す --}}

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection

@section('content')
<div class="password-container">
    <div class="login-header">
        <h2>初回パスワード変更画面</h2>
    </div>
    <p class="description">
        ※初期パスワードは簡易的なものです。<br>
        セキュリティ確保のため、必ず8文字以上の新しいパスワードに変更してください。
    </p>

    {{-- パスワード変更フォーム --}}
    <form method="POST" action="{{ route('password.change') }}">
        @csrf

        <div class="form-field">
            <label for="current_password">現在のパスワード</label>
            <input type="password" id="current_password" name="current_password" value="{{ old('current_password') }}">
            @error('current_password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="new_password">新しいパスワード</label>
            <input type="password" id="new_password" name="new_password" value="{{ old('new_password') }}" placeholder="半角英数字8文字以上">
            @error('new_password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="confirm_password">確認用パスワード</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="確認用">
            @error('confirm_password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ボタン群（横並び） --}}
        <div class="button-group">
            {{-- パスワード変更 --}}
            <form method="POST" action="{{ route('password.change') }}">
                @csrf
                <button type="submit" class="btn-primary">パスワード変更</button>
            </form>

            {{-- 次回変更する（ログアウトフォーム） --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-secondary">次回変更する</button>
            </form>
        </div>
    </form>
</div>
@endsection