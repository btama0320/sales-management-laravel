{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

{{-- body-class を admin-body に指定 --}}
@section('body-class', 'admin-body')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
@endsection

{{-- content 部分に管理者画面を差し込む --}}
@section('content')
<main class="user-container">
  <p class="user-description">
    必要な操作を選択してください。
  </p>

  <div class="user-menu">
    <a href="{{ route('invoice.receivable') }}" class="menu-button">🧾 売掛伝票入力</a>
    <a href="#" class="menu-button disabled">💰 入金伝票入力<span class="badge">準備中</span></a>
    <a href="#" class="menu-button disabled">📦 買掛伝票入力 <span class="badge">準備中</span></a>
    <a href="#" class="menu-button disabled">💸 支払伝票入力 <span class="badge">準備中</span></a>
    <a href="#" class="menu-button disabled">📁 マスタ管理 <span class="badge">準備中</span></a>
  </div>
</main>

@endsection