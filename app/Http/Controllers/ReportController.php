<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // レポート一覧
        return view('reports.index');
    }

    public function show($id)
    {
        // レポート詳細
        return view('reports.show', compact('id'));
    }
}
