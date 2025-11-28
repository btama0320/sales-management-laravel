<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * 管理者ダッシュボード表示
     */
    public function index()
    {
        // resources/views/admin/dashboard.blade.php を返す
        return view('admin.dashboard');
    }
}
