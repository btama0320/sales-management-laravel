<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ItemType;
use App\Http\Controllers\ItemTypeController;

Route::get('item-types/search', function (Request $request) {
    $q = $request->input('q');

    $items = ItemType::where('search_key_romaji', 'LIKE', $q.'%')
        ->orWhere('search_key_hiragana', 'LIKE', $q.'%')
        ->orWhere('search_key_katakana', 'LIKE', $q.'%')
        ->get(['id', 'name']);

    // Select2が期待する形式に整形
    $results = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'text' => $item->id . ' - ' . $item->name,
        ];
    });

    return response()->json(['results' => $results]);
});