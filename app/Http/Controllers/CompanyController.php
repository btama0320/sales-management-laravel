<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function create()
    {
        // 自社データを取得
        $company = Company::where('is_default', true)->first();

        // 伝票入力画面に渡す
        return view('invoice.receivable', compact('company'));
    }
}