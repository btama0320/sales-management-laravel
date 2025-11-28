@extends('layouts.app')

@section('body-class', 'settings-body') {{-- 管理者用のbodyクラス --}}

@section('content')
<div class="settings-container">
    <div class="settings-header">
        <h2>システム設定</h2>
        <p class="settings-description">
            管理者専用の設定画面です。ユーザー権限やシステム全体の動作を調整できます。
        </p>
    </div>

    <div class="settings-menu">
        <ul>
            <li><a href="#">ユーザー権限設定</a></li>
            <li><a href="#">パスワードポリシー</a></li>
            <li><a href="#">通知設定</a></li>
            <li><a href="#">バックアップ管理</a></li>
        </ul>
    </div>
</div>
@endsection
