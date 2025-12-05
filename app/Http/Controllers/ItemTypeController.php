<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemType; // ← モデルがある場合

class ItemTypeController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');

        $items = ItemType::where('search_key_romaji', 'LIKE', "%{$q}%")
            ->orWhere('search_key_hiragana', 'LIKE', "%{$q}%")
            ->orWhere('search_key_katakana', 'LIKE', "%{$q}%")
            ->orWhere('name', 'LIKE', "%{$q}%")
            ->get(['id', 'name']);

        // Select2形式で返す
        return response()->json(['results' => $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->id . ' - ' . $item->name,
            ];
        })]);
    }
}