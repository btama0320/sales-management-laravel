<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        return view('admin.logs'); // resources/views/admin/logs.blade.php を返す
    }
}
