<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings'); // resources/views/admin/settings.blade.php を返す
    }
}
