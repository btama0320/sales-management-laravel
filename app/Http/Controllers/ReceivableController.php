<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receivable;

class ReceivableController extends Controller
{
    // 一覧表示
    public function index()
    {
        $receivables = Receivable::with('details')->paginate(20);
        return view('invoice.index', compact('receivables'));
    }

    // 新規作成フォーム
    public function create()
    {
        $receivable = new Receivable();
        $isDraft = false; // 初期は本伝票
        return view('invoice.receivable', compact('receivable', 'isDraft'));
    }

    // 編集フォーム
    public function edit($id)
    {
        $receivable = Receivable::with('details')->findOrFail($id);
        $isDraft = $receivable->is_draft ?? false;
        return view('invoice.receivable', compact('receivable', 'isDraft'));
    }

    // 保存処理
    public function store(Request $request)
{
    $receivable = new Receivable();
    $receivable->fill($request->all());
    $receivable->is_draft = $request->input('is_draft', false);
    $receivable->save();

    // 明細保存
    $details = $request->input('details', []);
    $normalizedDetails = [];

    foreach ($details as $detail) {
        $normalizedDetails[] = [
            'item_code'  => $detail['item_code'] ?? '',
            'item_name'  => $detail['item_name'] ?? '',
            'package'    => $detail['package'] ?? '',
            'unit'       => $detail['unit'] ?? '',
            'grade'      => $detail['grade'] ?? '',
            'class'      => $detail['class'] ?? '',
            'quantity'   => $detail['quantity'] ?? 0,
            'unit_price' => $detail['unit_price'] ?? 0,
            'amount'     => $detail['amount'] ?? 0,
            'remarks'    => $detail['remarks'] ?? '',
        ];
    }

    if (!empty($normalizedDetails)) {
        $receivable->details()->createMany($normalizedDetails);
    }

    return redirect()->route('receivables.edit', $receivable->id)
                     ->with('success', '伝票を保存しました');
}


    // 更新処理
    public function update(Request $request, $id)
{
    $receivable = Receivable::findOrFail($id);
    $receivable->fill($request->all());
    $receivable->is_draft = $request->input('is_draft', false);
    $receivable->save();

    // 明細更新
    $receivable->details()->delete();

    $details = $request->input('details', []);
    $normalizedDetails = [];

    foreach ($details as $detail) {
        $normalizedDetails[] = [
            'item_code'  => $detail['item_code'] ?? '',
            'item_name'  => $detail['item_name'] ?? '',
            'package'    => $detail['package'] ?? '',
            'unit'       => $detail['unit'] ?? '',
            'grade'      => $detail['grade'] ?? '',
            'class'      => $detail['class'] ?? '',
            'quantity'   => $detail['quantity'] ?? 0,
            'unit_price' => $detail['unit_price'] ?? 0,
            'amount'     => $detail['amount'] ?? 0,
            'remarks'    => $detail['remarks'] ?? '',
        ];
    }

    if (!empty($normalizedDetails)) {
        $receivable->details()->createMany($normalizedDetails);
    }

    return redirect()->route('receivables.edit', $receivable->id)
                     ->with('success', '伝票を更新しました');
}

}
