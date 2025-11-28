@extends('layouts.app')

@section('content')
<h1>ユーザー作成</h1>
<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <input type="text" name="user_id" placeholder="社員番号">
    <input type="text" name="role" placeholder="役割">
    <input type="password" name="password" placeholder="パスワード">
    <button type="submit">登録</button>
</form>
@endsection
