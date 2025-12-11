<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ItemType;
use App\Http\Controllers\ItemTypeController;
use App\Models\Customer;
use App\Http\Controllers\CustomerController;

Route::get('item-types/search', function (Request $request) {
    $q = $request->input('q');

    $query = ItemType::query();

    // 数値の場合はIDで検索
    if (is_numeric($q)) {
        $query->where('id', $q);
    } else {
        // 文字列の場合は検索キーで検索
        $query->where('search_key_romaji', 'LIKE', "%{$q}%")
            ->orWhere('search_key_hiragana', 'LIKE', "%{$q}%")
            ->orWhere('search_key_katakana', 'LIKE', "%{$q}%");
    }

    $items = $query->get(['id', 'name']);

    $results = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'text' => $item->id . ' - ' . $item->name,
        ];
    });

    return response()->json(['results' => $results]);
});

Route::get('customers/search', function (Request $request) {
    $q = $request->input('q');

    $items = Customer::where('company_name', 'LIKE', $q.'%')
        ->orWhere('code', 'LIKE', $q.'%') // ← idよりcodeの方が自然かも
        ->get(['id', 'code', 'company_name']);

    $results = $items->map(function ($item) {
        return [
            'id' => $item->id, // Select2内部用
            'code' => $item->code, // 実際の顧客コード
            'company_name' => $item->company_name,
            'text' => $item->code . ' - ' . $item->company_name,
        ];
    });

    return response()->json(['results' => $results]);
});