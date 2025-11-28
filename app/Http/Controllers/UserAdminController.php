<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    /**
     * ユーザー一覧表示
     */
    public function index()
    {
        // 仮のデータ（本来はDBから取得）
        $users = [
            ['id' => 1, 'name' => '山田太郎', 'role' => 'admin'],
            ['id' => 2, 'name' => '佐藤花子', 'role' => 'user'],
        ];

        return view('admin.users.index', compact('users'));
    }

    /**
     * ユーザー追加フォーム表示
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * ユーザー登録処理
     */
    public function store(Request $request)
    {
        // バリデーション例
        $request->validate([
            'user_id' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
        ]);

        // 本来はDBに保存する処理を書く
        // User::create([...]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'ユーザーを登録しました');
    }
}
