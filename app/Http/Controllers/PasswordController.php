<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // パスワード変更フォーム表示
    public function showChangeForm()
    {
        return view('auth.change_password');
    }

    // パスワード更新処理
    public function update(Request $request)
    {
        $user = Auth::user(); // ← 先に取得

        // 現在のパスワードチェック
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => '現在のパスワードが違います']);
        }

        // 新しいパスワードのバリデーション
        $request->validate([
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        // 更新処理
        $user->password = Hash::make($request->new_password);
        $user->must_change_password = 0; // ←フラグ更新
        $user->save();

        return redirect()->route('dashboard')->with('status', 'パスワードを変更しました');
    }
}