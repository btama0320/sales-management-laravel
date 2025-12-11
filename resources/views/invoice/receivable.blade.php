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
            <input type="date" id="slip_date">
          </td>
          <td class="td-short">
            <label for="slip_no">伝票番号：</label>
            <input type="text" id="slip_no">
          </td>
          <td></td>
          <td class="td-right">
              <label for="shipper_code">荷　主：</label>
              <input type="text" id="shipper_code" class="code-input-small"
                    value="{{ $company->code }}" placeholder="コード">
              <input type="text" id="shipper_name" class="name-input-small"
                    value="{{ $company->name }}" placeholder="荷主名">
          </td>

        </tr>
        <tr>
          <td class="td-medium">
            <label for="customer_code" class="td-left">得&nbsp;意&nbsp;先&nbsp;：</label>
            <!-- inputからselectに変更 -->
            <select id="customer_code" class="code-input-small">
              <option></option>
            </select>
            <input type="text" id="customer_name" class="name-input-small" placeholder="得意先名" readonly>
          </td>
          <td class="td-medium">
            <label for="department">担当部署：</label>
            <input type="text" id="department">
          </td>
          <td>
            <label for="honorific">敬&#x3000;&#x3000;称：</label>
            <select id="honorific">
              <option>様</option>
              <option>御中</option>
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
            </select>
            <input type="text" id="billing_name" class="name-input-small" placeholder="請求先名" readonly>
          </td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td class="td-short">
            <label for="item_code_header" class="td-left">品　目：</label>
            <select id="item_code_header" class="code-input-small" style="width:200px"></select>
            <input type="text" id="item_name_header" class="name-input-small" placeholder="品目名" readonly>
          </td>
          <td class="td-medium">
            <label for="carrier_code">運送会社：</label>
            <input type="text" id="carrier_code" class="code-input-small" placeholder="コード">
            <input type="text" id="carrier_name" class="name-input-small" placeholder="運送会社名" >
          </td>
          <td class="td-medium"></td>
          <td></td>
        </tr>
        <tr>
          <td class="td-xlong">
            <label for="summary" class="td-left">摘&#x3000;&#x3000;要：</label>
            <input type="text" id="summary">
          </td>
          <td class="td-short">
            <label for="sales_date">販&nbsp;売&nbsp;日：</label>
            <input type="date" id="sales_date">
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
                  <tr class="detail-row">
                      <td class="label-cell">
                          <div class="label-box"></div>
                          <div class="label-popup">
                          <div class="color-option" data-color=""></div>
                          <div class="color-option" data-color="red"></div>
                          <div class="color-option" data-color="blue"></div>
                          <div class="color-option" data-color="green"></div>
                          </div>
                          <span class="row-index">1</span>
                      </td>

                      <td class="td-item">
                          <div class="code-name-wrap">
                              <div class="code-input-wrapper">
                                  <input type="text" name="details[0][item_code]" class="code-input" placeholder="コード"
                                        value="{{ old('details.0.item_code') }}">
                                  <span class="tax-mark" style="{{ old('details.0.tax_rate') == '8' ? '' : 'display:none;' }}">※</span>
                              </div>
                              <input type="text" name="details[0][item_name]" class="name-input" placeholder="商品名"
                                    value="{{ old('details.0.item_name') }}">
                          </div>
                      </td>

                      <td>
                          <input type="text" name="details[0][package]" value="{{ old('details.0.package') }}">
                      </td>

                      <td>
                          <input type="text" name="details[0][unit]" value="{{ old('details.0.unit') }}">
                      </td>

                      <td>
                          <input type="text" name="details[0][grade]" value="{{ old('details.0.grade') }}">
                      </td>

                      <td>
                          <input type="text" name="details[0][class]" value="{{ old('details.0.class') }}">
                      </td>

                      <td>
                          <input type="number" name="details[0][quantity]" value="{{ old('details.0.quantity') }}">
                      </td>

                      <td>
                          <input type="number" name="details[0][unit_price]" value="{{ old('details.0.unit_price') }}">
                      </td>

                      <td>
                          <input type="number" name="details[0][amount]" value="{{ old('details.0.amount') }}">
                      </td>

                      <td>
                          <input type="text" name="details[0][remarks]" value="{{ old('details.0.remarks') }}">
                      </td>
                  </tr>


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
