<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // 売掛伝票入力画面を表示
    public function receivable()
    {
        return view('invoice.receivable'); 
        // resources/views/invoice/receivable.blade.php を返す
    }
}
