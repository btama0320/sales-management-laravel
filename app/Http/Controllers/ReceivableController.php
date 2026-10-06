<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Receivable;
use App\Models\SlipNumberCounter;
use App\Models\Customer;
use App\Models\ItemType;
use App\Models\Carrier;

class ReceivableController extends Controller
{
    // 一覧表示
    public function index(Request $request)
    {
        $hasSearched = $request->boolean('searched');
        $query = Receivable::with('details');

        if ($request->filled('date_from')) {
            $query->whereDate('slip_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('slip_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('slip_no')) {
            $query->where('slip_no', 'like', '%' . $request->input('slip_no') . '%');
        }

        if ($request->filled('customer_code')) {
            $query->where('customer_code', $request->input('customer_code'));
        }

        if ($request->filled('item_type_id')) {
            $query->where('item_code_header', $request->input('item_type_id'));
        }

        // 初回表示では全件を出さず、検索フォーム送信後にだけ結果を表示
        if (!$hasSearched) {
            $query->whereRaw('1 = 0');
        }

        $receivables = $query
            ->orderByDesc('slip_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $selectedCustomer = $request->filled('customer_code')
            ? Customer::find($request->input('customer_code'))
            : null;
        $selectedItem = $request->filled('item_type_id')
            ? ItemType::find($request->input('item_type_id'))
            : null;

        return view('invoice.index', compact(
            'receivables',
            'hasSearched',
            'selectedCustomer',
            'selectedItem'
        ));
    }

    // 新規作成フォーム
    public function create()
    {
        $receivable = new Receivable();
        // 初期は本伝票
        $isDraft = false; 
        return view('invoice.receivable', compact('receivable', 'isDraft'));
    }

    // 編集フォーム
    public function edit($id)
    {
        $receivable = Receivable::with('details')->findOrFail($id);
        $isDraft = $receivable->is_draft ?? false;

        // Select2の欄に、保存済みの選択肢を初期表示するためのマスター情報
        $selectedCustomer = Customer::find($receivable->customer_code);
        $selectedBilling = Customer::find($receivable->billing_code);
        $selectedItem = ItemType::find($receivable->item_code_header);
        $selectedCarrier = Carrier::where('code', $receivable->carrier_code)->first();

        // 過去にDBのIDをcarrier_codeへ保存していたデータにも対応
        if (!$selectedCarrier && is_numeric($receivable->carrier_code)) {
            $selectedCarrier = Carrier::find($receivable->carrier_code);
        }

        return view('invoice.receivable', compact(
            'receivable',
            'isDraft',
            'selectedCustomer',
            'selectedBilling',
            'selectedItem',
            'selectedCarrier',
        ));
    }

    // 伝票番号採番
    private function generateSlipNo(): string
    {
        // "2026-09-29" のような文字列
        $today = now()->toDateString();   

        return DB::transaction(function () use ($today) {
            $counter = SlipNumberCounter::where('counter_date', $today)
                // ← 他の人が同時に触れないようにする
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                // 今日初めての伝票なら、行を新しく作る
                $counter = SlipNumberCounter::create([
                    'counter_date' => $today,
                    'current_no'   => 0,
                ]);
            }

            // 1つ進める(DBにも即保存される)
            $counter->increment('current_no');
            // "1" → "0001"
            $no = str_pad($counter->current_no, 4, '0', STR_PAD_LEFT);
            // "2026-09-29" → "20260929"
            $datePart = str_replace('-', '', $today);
            return $datePart . '-' . $no;
        });
    }

    // 保存処理
    public function store(Request $request)
    {
        $receivable = new Receivable();
        $receivable->fill($request->all());
        $receivable->slip_no = $this->generateSlipNo(); 
        
        // 明示的に 0 または 1 の数値に変換して上書きする
        $receivable->is_draft = filter_var($request->input('is_draft'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
        $receivable->save();

        // 明細保存
        $details = $request->input('details', []);
        $normalizedDetails = [];

        foreach ($details as $detail) {
            // 明細データの各項目を整え、保存用の連想配列として追加する
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
                'label_color' => $detail['label_color'] ?? null,
            ];
        }
        // 明細($normalizedDetails)が空でない場合のみ保存
        if (!empty($normalizedDetails)) {
            $receivable->details()->createMany($normalizedDetails);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id'      => $receivable->id,
                'slip_no' => $receivable->slip_no,
                'is_draft' => $receivable->is_draft,
            ]);
        }

        return redirect()->route('receivables.edit', $receivable->id)
                        ->with('success', '伝票を保存しました');
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        $receivable = Receivable::findOrFail($id);
        $receivable->fill($request->all());
        
        $receivable->is_draft = filter_var($request->input('is_draft'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
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
                'label_color' => $detail['label_color'] ?? null,
            ];
        }

        if (!empty($normalizedDetails)) {
            $receivable->details()->createMany($normalizedDetails);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'id'       => $receivable->id,
                'slip_no'  => $receivable->slip_no,
                'is_draft' => $receivable->is_draft,
            ]);
        }

        return redirect()->route('receivables.edit', $receivable->id)
                        ->with('success', '伝票を更新しました');
    }

    public function findBySlipNo(Request $request) 
    { 
        $slipNo = trim((string) $request->input('slip_no', ''));
        // 全角・異体字のハイフンを伝票番号で使う半角ハイフンへ統一
        $slipNo = strtr($slipNo, ['－' => '-', '−' => '-', '–' => '-', '—' => '-']);

        $receivable = Receivable::with('details')
            ->whereRaw('TRIM(slip_no) = ?', [$slipNo])
            ->first();

        if ($receivable) { 
            return response()->json([
                'success' => true,
                'redirect_url' => route('receivables.edit', $receivable->id),
            ]);
        } else { 
            return response()->json([
                'success' => false,
                'message' => '該当する伝票がありません。伝票番号を確認してください。',
            ]);
        } 
    }

}


