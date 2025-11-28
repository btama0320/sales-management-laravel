{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

{{-- body-class を admin-body に指定 --}}
@section('body-class', 'admin-body')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

{{-- content 部分に管理者画面を差し込む --}}
@section('content')
<main class="admin-container">
  <p class="admin-description">
    管理者専用の操作メニューです。各種設定やデータ管理を行えます。
  </p>

  <div class="admin-menu">
    <a href="{{ route('admin.users.index') }}" class="admin-button">👤 ユーザー管理</a>
    <a href="{{ route('admin.reports.index') }}" class="admin-button">📊 売上レポート</a>
    <a href="{{ route('settings.index') }}" class="admin-button">⚙️ システム設定</a>
    <a href="{{ route('logs.index') }}" class="admin-button">📋 操作ログ</a>
  </div>
</main>

@endsection