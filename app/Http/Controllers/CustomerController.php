<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');

        $items = Customer::where('code', 'LIKE', "%{$q}%")
            ->orWhere('company_name', 'LIKE', "%{$q}%")
            ->orWhere('search_key_romaji', 'LIKE', "%{$q}%")
            ->orWhere('search_key_hiragana', 'LIKE', "%{$q}%")
            ->orWhere('search_key_katakana', 'LIKE', "%{$q}%")
            ->get(['id', 'code', 'company_name']);

        return response()->json(['results' => $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->code . ' - ' . $item->company_name,
            ];
        })]);
    }
}
