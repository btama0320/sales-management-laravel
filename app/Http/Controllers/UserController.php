<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // ユーザー一覧表示
        return view('users.index');
    }

    public function create()
    {
        // ユーザー作成フォーム
        return view('users.create');
    }

    public function store(Request $request)
    {
        // ユーザー保存処理
    }

    public function show($id)
    {
        // ユーザー詳細
        return view('users.show', compact('id'));
    }

    public function edit($id)
    {
        // ユーザー編集フォーム
        return view('users.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // ユーザー更新処理
    }

    public function destroy($id)
    {
        // ユーザー削除処理
    }
}
