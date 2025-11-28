<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // ログイン画面表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(Request $request)
    {
        $credentials = $request->only('user_id', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 管理者は管理者ダッシュボードへ
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // 初回ログインならパスワード変更へ
            if ($user->must_change_password) {
                return redirect()->route('password.change');
            }

            // 一般ユーザーは通常ダッシュボードへ
            return redirect()->route('dashboard');
        }

        // 認証失敗時
        return back()->withErrors([
            'login' => 'ログインに失敗しました',
        ]);
    }
}