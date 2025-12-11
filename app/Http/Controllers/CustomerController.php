<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
   // app/Http/Controllers/CustomerController.php
    public function search(Request $request)
    {
        $q = $request->input('q');

        $query = Customer::query();

        if (is_numeric($q)) {
            $query->where('code', $q);
        }

        $query->orWhere('company_name', 'LIKE', "%{$q}%")
            ->orWhere('search_key_romaji', 'LIKE', "%{$q}%")
            ->orWhere('search_key_hiragana', 'LIKE', "%{$q}%")
            ->orWhere('search_key_katakana', 'LIKE', "%{$q}%");

        $results = $query->get(['code', 'company_name'])->map(function ($c) {
            return [
                'id' => $c->code,
                'text' => "{$c->code} - {$c->company_name}",
                'code' => $c->code,
                'name' => $c->company_name
            ];
        });

        return response()->json(['results' => $results]);
    }
}
