<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ItemType;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Carrier;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemTypeController;

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

Route::get('products/search', function (Request $request) {
    $q = $request->input('q');
    $itemTypeId = $request->input('item_type_id');

    $query = Product::query();

    // 品目で絞り込む(指定があるときだけ)
    if ($itemTypeId) {
        $query->where('item_type_id', $itemTypeId);
    }

    // コード or 商品名。orWhere は () でまとめる
    $items = $query->where(function ($sub) use ($q) {
            $sub->where('product_code', 'LIKE', $q.'%')
                ->orWhere('product_name', 'LIKE', '%'.$q.'%');
        })
        ->get(['product_code', 'product_name', 'package','unit', 'grade', 'class', 'item_type_id']);

    $results = $items->map(function ($item) {
        return [
            'product_code' => $item->product_code, // 商品コード
            'product_name' => $item->product_name, // 商品名
            'package' => $item->package,
            'unit' => $item->unit,
            'grade' => $item->grade,
            'class' => $item->class,
            'item_type_id' => $item->item_type_id,
            'id'   => $item->product_code,
            'text' => $item->product_code . ' - ' . $item->product_name,
        ];
    });

    return response()->json(['results' => $results]);
});

Route::get('carriers/search', function (Request $request) {
    $q = $request->input('q', '');

    $items = Carrier::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', '%' . $q . '%')
                ->orWhere('code', 'LIKE', $q . '%')
                ->orWhere('search_key_romaji', 'LIKE', '%' . $q . '%')
                ->orWhere('search_key_hiragana', 'LIKE', '%' . $q . '%')
                ->orWhere('search_key_katakana', 'LIKE', '%' . $q . '%');
        })
        ->get(['id', 'code', 'name']);

    $results = $items->map(function ($carrier) {
        return [
            'id' => $carrier->code,
            'code' => $carrier->code,
            'name' => $carrier->name,
            'text' => $carrier->code . ' - ' . $carrier->name,
        ];
    });

    return response()->json(['results' => $results]);
});
