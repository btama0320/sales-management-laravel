@extends('layouts.app')

@section('body-class', 'receivable-body') {{-- 伝票入力画面用 --}}
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/receivable.css') }}">
@endsection

@section('content')
<div class="container">
  <p class="hint">Enterで次項目へ移動／Escで閉じる／Fキーで各機能</p>

  {{-- ナビゲーションボタン --}}
  <div class="nav-buttons">
    <button id="btn_home">
        <span class="label">閉じる</span><br>
        <span class="shortcut">Esc</span>
    </button>
    <script>
      const menuUrl = "{{ route('menu') }}";
      const newSlipUrl = "{{ route('invoice.receivable') }}";
    </script>
    <button id="btn_new">
        <span class="label">新規</span><br>
        <span class="shortcut">F2</span>
    </button>
    <button id="btn_prev">
        <span class="label">前伝票</span><br>
        <span class="shortcut">F3</span>
    </button>
    <button id="btn_next">
        <span class="label">次伝票</span><br>
        <span class="shortcut">F4</span>
    </button>
    <button id="btn_purchase">
        <span class="label">仕入伝票</span><br>
        <span class="shortcut">F6</span>
    </button>
    <button id="deleteRowBtn">
        <span class="label">行削除</span><br>
        <span class="shortcut">F7</span>
    </button>
    <button id="insertRowBtn">
        <span class="label">行挿入</span><br>
        <span class="shortcut">F8</span>
    </button>
    <button id="btn_list">
        <span class="label">一覧</span><br>
        <span class="shortcut">F9</span>
    </button>
    <!-- 機能選択ポップアップ -->
    <div class="label-popup" id="finalPopup" style="display: none;">
    <button class="final-option" data-action="real">本伝票</button>
    <button class="final-option" data-action="temp">仮伝票</button>
    <button class="final-option" data-action="copy">複製</button>
    <button class="final-option" data-action="delete">削除</button>
    </div>

    <!-- トリガーボタン -->
    <button id="btn_final">
    <span class="label">伝票機能</span><br>
    <span class="shortcut">F11</span>
    </button>
    <button id="btn_save">
        <span class="label">保存</span><br>
        <span class="shortcut">F12</span>
    </button>
  </div>

  {{-- 伝票フォーム --}}
  <form id="slipForm">
    <table class="form-table">
              <tr>
          <td class="td-short">
            <label for="slip_date"class="td-left">伝票日付：</label>
            <div class="slip-date-control">
              <input
                type="text"
                id="slip_date"
                class="slip-date-text"
                inputmode="numeric"
                placeholder="M/D"
                autocomplete="off"
                value="{{ old('slip_date', $receivable->slip_date ?? '') }}">
              <span class="slip-date-calendar-icon" aria-hidden="true">📅</span>
              <input
                type="date"
                id="slip_date_picker"
                class="slip-date-picker"
                aria-label="カレンダーから伝票日付を選択"
                title="カレンダーから日付を選択">
            </div>
          </td>
          <td class="td-short">
            <label for="slip_no">伝票番号：</label>
            <input
                type="text"
                id="slip_no"
                value="{{ old('slip_no', $receivable->slip_no ?? '') }}"
            >
            <input type="hidden" id="receivable_id" name="id" value="{{ $receivable->id ?? '' }}">
          </td>
          <td></td>
          <td class="td-right">
              <label for="shipper_code">荷　主：</label>
              <input 
                type="text" 
                id="shipper_code" 
                class="code-input-small"
                value="{{ $receivable->shipper_code ?? $company->code ?? '' }}">
              <input 
                type="text" 
                id="shipper_name" 
                class="name-input-small"
                value="{{ $receivable->shipper_name ?? $company->name }}" 
                placeholder="荷主名">
          </td>

        </tr>
        <tr>
          <td class="td-medium">
            <label for="customer_code" class="td-left">得&nbsp;意&nbsp;先&nbsp;：</label>
            <select id="customer_code" class="code-input-small">
              <option></option>
              @if(!empty($selectedCustomer))
                <option value="{{ $selectedCustomer->id }}" selected>
                  {{ $selectedCustomer->code }} - {{ $selectedCustomer->company_name }}
                </option>
              @endif
            </select>
            <input 
              type="text" 
              id="customer_name" 
              class="name-input-small" 
              value="{{ old('customer_name', $receivable->customer_name ?? '') }}"
              placeholder="得意先名" 
              readonly>
          </td>
          <td class="td-medium">
            <label for="department">担当部署：</label>
            <input 
              type="text" 
              id="department"
              value="{{ old('department', $receivable->department ?? '') }}">
          </td>
          <td>
            <label for="honorific">敬&#x3000;&#x3000;称：</label>
            <select id="honorific">
              <option>御中</option>
              <option>様</option>
              <option>殿</option>
            </select>
          </td>
          <td></td>
        </tr>
        <tr>
          <td class="td-medium">
            <label for="billing_code" class="td-left">請&nbsp;求&nbsp;先&nbsp;：</label>
            <!-- inputからselectに変更 -->
            <select id="billing_code" class="code-input-small">
              <option></option>
              @if(!empty($selectedBilling))
                <option value="{{ $selectedBilling->id }}" selected>
                  {{ $selectedBilling->code }} - {{ $selectedBilling->company_name }}
                </option>
              @endif
            </select>
            <input 
              type="text" 
              id="billing_name" 
              class="name-input-small" 
              value="{{ old('billing_name', $receivable->billing_name ?? '') }}"
              placeholder="請求先名" 
              readonly>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td class="td-short">
            <label for="item_code_header" class="td-left">品　目：</label>
            <select id="item_code_header" class="code-input-small">
              <option></option>
              @if(!empty($selectedItem))
                <option value="{{ $selectedItem->id }}" selected>
                  {{ $selectedItem->id }} - {{ $selectedItem->name }}
                </option>
              @endif
            </select>
            <input 
              type="text" 
              id="item_name_header" 
              class="name-input-small" 
              value="{{ old('item_name_header', $receivable->item_name_header ?? '') }}"
              placeholder="品目名" 
              readonly>
          </td>
          <td class="td-medium">
            <label for="carrier_code">運送会社：</label>
            <select id="carrier_code" class="code-input-small">
              <option></option>
              @if(!empty($selectedCarrier))
                <option value="{{ $selectedCarrier->code }}" selected>
                  {{ $selectedCarrier->code }} - {{ $selectedCarrier->name }}
                </option>
              @endif
            </select>
            <input 
              type="text" 
              id="carrier_name" 
              class="name-input-small" 
              value="{{ old('carrier_name', $receivable->carrier_name ?? '') }}"
              placeholder="運送会社名" >
          </td>
          <td class="td-medium"></td>
          <td></td>
        </tr>
        <tr>
          <td class="td-xlong">
            <label for="summary" class="td-left">摘&#x3000;&#x3000;要：</label>
            <input 
              type="text" 
              id="summary" 
              class="name-input-small" 
              value="{{ old('summary', $receivable->summary ?? '') }}"
              placeholder="摘要">
          </td>
          <td class="td-short">
            <label for="sales_date">販&nbsp;売&nbsp;日：</label>
            <input 
              type="date" 
              id="sales_date" 
              class="date-input-small" 
              value="{{ old('sales_date', $receivable->sales_date ?? '') }}"> 
          </td>
          <td class="td-medium"></td>
          <td></td>
        </tr>
    </table>
  </form>

  {{-- 明細テーブル --}}
    <div class="scroll-and-totals">
        <div class="detail-scroll-container">
            <table class="detailBlock">
                <thead class="itemField">
                    <tr>
                    <th>行</th>
                    <th>商品名</th>
                    <th>荷姿</th>
                    <th>量目</th>
                    <th>等級</th>
                    <th>階級</th>
                    <th>数量</th>
                    <th>単価</th>
                    <th>金額</th>
                    <th>備考</th>
                    </tr>
                </thead>
              <tbody class="salesDate" id="detailRows">
                @php
                    // 入力エラー時は入力値を優先し、通常はDBの明細を表示する
                    $detailRows = old('details', $receivable->details->toArray());
                    if (count($detailRows) === 0) {
                        $detailRows = [[]];
                    }
                @endphp
                @foreach ($detailRows as $i => $detailRow)
                  @php
                      $itemCode = data_get($detailRow, 'item_code', '');
                      $itemName = data_get($detailRow, 'item_name', '');
                      $taxRate = data_get($detailRow, 'tax_rate', '10');
                      $labelColor = data_get($detailRow, 'label_color', '');
                  @endphp
                  <tr class="detail-row" data-label-color="{{ $labelColor }}">
                      <td class="label-cell">
                          <div class="label-box"></div>
                          <div class="label-popup">
                          <div class="color-option" data-color=""></div>
                          <div class="color-option" data-color="red"></div>
                          <div class="color-option" data-color="blue"></div>
                          <div class="color-option" data-color="green"></div>
                          </div>
                          <span class="row-index">{{ $i + 1 }}</span>
                      </td>

                      <td class="td-item">
                          <div class="code-name-wrap">
                              <div class="code-input-wrapper">
                                  <select name="details[{{ $i }}][item_code]" class="code-select">
                                      <option></option>
                                      @if($itemCode !== '')
                                          <option value="{{ $itemCode }}" selected>{{ $itemCode }}</option>
                                      @endif
                                  </select>
                                <span class="tax-mark" style="{{ (string) $taxRate === '8' ? '' : 'display:none;' }}">※</span>
                              </div>
                          <input type="text" name="details[{{ $i }}][item_name]" class="name-input" placeholder="商品名"
                                    value="{{ old('details.'.$i.'.item_name', $itemName) }}">
                          </div>
                      </td>

                      <td>
                          <input type="text" name="details[{{ $i }}][package]" value="{{ old('details.'.$i.'.package', data_get($detailRow, 'package', '')) }}">
                      </td>

                      <td>
                          <input type="text" name="details[{{ $i }}][unit]" value="{{ old('details.'.$i.'.unit', data_get($detailRow, 'unit', '')) }}">
                      </td>

                      <td>
                          <input type="text" name="details[{{ $i }}][grade]" value="{{ old('details.'.$i.'.grade', data_get($detailRow, 'grade', '')) }}">
                      </td>

                      <td>
                          <input type="text" name="details[{{ $i }}][class]" value="{{ old('details.'.$i.'.class', data_get($detailRow, 'class', '')) }}">
                      </td>

                      <td>
                          <input type="number" name="details[{{ $i }}][quantity]" value="{{ old('details.'.$i.'.quantity', data_get($detailRow, 'quantity', '')) }}">
                      </td>

                      <td>
                          <input type="number" name="details[{{ $i }}][unit_price]" value="{{ old('details.'.$i.'.unit_price', data_get($detailRow, 'unit_price', '')) }}">
                      </td>

                      <td>
                          <input type="number" name="details[{{ $i }}][amount]" value="{{ old('details.'.$i.'.amount', data_get($detailRow, 'amount', '')) }}">
                      </td>

                      <td>
                          <input type="text" name="details[{{ $i }}][remarks]" value="{{ old('details.'.$i.'.remarks', data_get($detailRow, 'remarks', '')) }}">
                      </td>
                  </tr>
                @endforeach


                  {{-- JSで行追加 --}}
              </tbody>
            </table>
        </div>

        {{-- 集計欄 --}}
        <div class="totals-bar">
            <div class="totals-area">
                <div class="total-group">
                    <label>合計数：</label>
                    <input type="text" value="0">
                </div>
                <div class="total-group">
                    <label>金額計：</label>
                    <input type="text" value="¥0">
                </div>
                <div class="total-group">
                    <label>消費税：</label>
                    <input type="text" value="¥0">
                </div>
                <div class="total-group">
                    <label>合計：</label>
                    <input type="text" value="¥0">
                </div>
            </div>
        </div>    
    </div>
</div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="{{ asset('js/receivable.js') }}"></script>
@endsection
