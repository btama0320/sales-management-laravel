// =====================================
// グローバル変数
// =====================================
let isDraftMode = false;

// =====================================
// totalbarの計算
// =====================================
function updateTotals() {
  let totalCount = 0;
  let totalAmount = 0;
  let tax8 = 0;
  let tax10 = 0;

  document.querySelectorAll('tr.detail-row').forEach(row => {
    const qty = parseFloat(row.querySelector('input[name*="[quantity]"]')?.value) || 0;
    const amount = parseFloat(row.querySelector('input[name*="[amount]"]')?.value) || 0;

    totalCount += qty;
    totalAmount += amount;

    const taxMark = row.querySelector('.tax-mark');
    if (taxMark && taxMark.style.display !== 'none') {
      tax8 += Math.floor(amount * 0.08);
    } else {
      tax10 += Math.floor(amount * 0.10);
    }
  });

  const grandTotal = totalAmount + tax8 + tax10;
  const groups = document.querySelectorAll(".totals-bar .total-group input");
  groups[0].value = totalCount;
  groups[1].value = "¥" + totalAmount.toLocaleString();
  groups[2].value = "¥" + (tax8 + tax10).toLocaleString();
  groups[3].value = "¥" + grandTotal.toLocaleString();
}

// =====================================
// 高さ調整
// =====================================
function adjustDetailScrollHeight() {
  const logoutHeight   = document.querySelector('.logout-area')?.offsetHeight || 0;
  const hintHeight     = document.querySelector('.hint')?.offsetHeight || 0;

  const header = document.querySelector('.nav-buttons');
  let headerHeight = header?.offsetHeight || 0;

  // nav-buttons がまだ描画されていない → 再計算
  if (headerHeight === 0) {
    setTimeout(adjustDetailScrollHeight, 50);
    return;
  }

  const formHeight     = document.querySelector('.form-table')?.offsetHeight || 0;
  const totalsHeight   = document.querySelector('.totals-bar')?.offsetHeight || 0;

  const reservedHeight = logoutHeight + hintHeight + headerHeight + formHeight + totalsHeight + 40;
  const availableHeight = Math.max(100, window.innerHeight - reservedHeight);

  const scrollContainer = document.querySelector('.detail-scroll-container');
  if (scrollContainer) {
    scrollContainer.style.height = `${availableHeight}px`;
  }
}

// =====================================
// 行番号振り直し
// =====================================
function renumberRows() {
  document.querySelectorAll('.detail-row').forEach((row, i) => {
    const span = row.querySelector('.row-index');
    if (span) span.textContent = i + 1;

    // 行の挿入・削除後も、送信する配列の番号を行順にそろえる
    row.querySelectorAll('[name]').forEach(input => {
      input.name = input.name.replace(/details\[\d+\]/, `details[${i}]`);
    });
  });
}

// =====================================
// 空行生成
// =====================================
function createEmptyRow(rowIndex = 0) {
  const row = document.createElement('tr');
  row.classList.add('detail-row');
  row.innerHTML = `
    <td class="label-cell">
      <div class="label-box"></div>
      <div class="label-popup">
        <div class="color-option" data-color=""></div>
        <div class="color-option" data-color="red"></div>
        <div class="color-option" data-color="blue"></div>
        <div class="color-option" data-color="green"></div>
      </div>
      <span class="row-index">${rowIndex + 1}</span>
    </td>

    <td class="td-item">
      <div class="code-name-wrap">
        <div class="code-input-wrapper">
          <select name="details[${rowIndex}][item_code]" class="code-select">
            <option></option>
          </select>
          <span class="tax-mark" style="display:none;">※</span>
        </div>
        <input type="text" name="details[${rowIndex}][item_name]" class="name-input" placeholder="商品名" value="">
      </div>
    </td>
    <td><input type="text" name="details[${rowIndex}][package]"></td>
    <td><input type="text" name="details[${rowIndex}][unit]"></td>
    <td><input type="text" name="details[${rowIndex}][grade]"></td>
    <td><input type="text" name="details[${rowIndex}][class]"></td>
    <td><input type="number" name="details[${rowIndex}][quantity]" value=""></td>
    <td><input type="number" name="details[${rowIndex}][unit_price]" value=""></td>
    <td><input type="number" name="details[${rowIndex}][amount]" value=""></td>
    <td><input type="text" name="details[${rowIndex}][remarks]"></td>
  `;
  
  setupRowCalcEvents(row);
  setupRowSelectEvents(row);
  setupLabelEvents(row);
  setupRowProductSelect(row);

  
  return row;
}

// =====================================
// 下の明細に対するイベント
// =====================================
function setupRowCalcEvents(row) {
  const codeInput = row.querySelector('.code-input');
  const qty = row.querySelector('input[name*="[quantity]"]');
  const price = row.querySelector('input[name*="[unit_price]"]');
  const amount = row.querySelector('input[name*="[amount]"]');

  // 数量・単価 → 金額計算
  function recalc() {
    const q = parseFloat(qty.value) || 0;
    const p = parseFloat(price.value) || 0;
    amount.value = q * p;
    if (typeof updateTotals === 'function') updateTotals();
  }
  qty.addEventListener('input', recalc);
  price.addEventListener('input', recalc);

  updateTotals();
}

// =====================================
// 明細に対するイベント
// =====================================
function setupRowProductSelect(row) {
  const sel = row.querySelector('.code-select');
  if (!sel) return;
  if (row.dataset.productSelectBound) return;   // 二重登録の防止
  row.dataset.productSelectBound = '1';

  sel.addEventListener('focus', function () {
    if ($(sel).hasClass('select2-hidden-accessible')) return;   // 起動済みなら何もしない

    //initSelect2（初期化）の呼び出し
    initSelect2(sel, {
      url: '/api/products/search',
      placeholder: 'コード',
      width: '100%',
      // ヘッダの品目IDを追加
      extraData: () => ({ item_type_id: $('#item_code_header').val() }), 
      onSelect: data => {
        const d = data.raw;
        // 各欄に値を入れる
        row.querySelector('input[name*="[item_name]"]').value = d.product_name;
        row.querySelector('input[name*="[package]"]').value = d.package;
        row.querySelector('input[name*="[unit]"]').value = d.unit;
        row.querySelector('input[name*="[grade]"]').value = d.grade;
        row.querySelector('input[name*="[class]"]').value = d.class;
        // 数量欄にフォーカス
        const qtyInput = row.querySelector('input[name*="[quantity]"]');
        if (qtyInput) qtyInput.focus();
      }
    });
    $(sel).select2('open');
  });
}

// =====================================
// 行選択イベント
// =====================================
function setupRowSelectEvents(row) {
  row.addEventListener('click', () => {
    document.querySelectorAll('.detail-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
  });
}

// =====================================
// 付箋ラベル制御
// =====================================
function setupLabelEvents(row) {
  const box   = row.querySelector('.label-box');
  const popup = row.querySelector('.label-popup');

  const applyLabelColor = color => {
    row.dataset.labelColor = color || '';
    box.style.backgroundColor =
      color === 'red' ? '#f44336' :
      color === 'blue' ? '#2196f3' :
      color === 'green' ? '#4caf50' :
      '#fff';

    popup.querySelectorAll('.color-option').forEach(option => {
      option.classList.toggle('selected-color', option.dataset.color === (color || ''));
    });
  };

  // 編集画面ではDBに保存した色を付箋へ復元する
  applyLabelColor(row.dataset.labelColor || '');

  box.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();

    document.querySelectorAll('.label-popup').forEach(p => p.classList.remove('show'));
    popup.classList.toggle('show');
  });

  popup.querySelectorAll('.color-option').forEach(opt => {
    opt.addEventListener('click', (e) => {
      e.stopPropagation();

      const color = opt.dataset.color;
      applyLabelColor(color);

      popup.classList.remove('show');
    });
  });

  popup.addEventListener('click', () => {
    popup.classList.remove('show');
  });
}

// =====================================
// 伝票スタイル更新（本伝票/仮伝票）
// =====================================
function updateSlipStyle() {
  const container = document.querySelector('.container');
  const saveBtnLabel = document.querySelector('#btn_save .label');
  let draftMark = document.querySelector('.draft-mark');

  if (isDraftMode) {
    container.classList.add('draft-slip');
    if (saveBtnLabel) saveBtnLabel.textContent = '仮保存';

    if (!draftMark) {
      draftMark = document.createElement('span');
      draftMark.className = 'draft-mark';
      draftMark.textContent = '仮';
      document.querySelector('.nav-buttons')?.appendChild(draftMark);
    }
  } else {
    container.classList.remove('draft-slip');
    if (saveBtnLabel) saveBtnLabel.textContent = '保存';
    if (draftMark) draftMark.remove();
  }
}

// =====================================
// 伝票機能ポップアップ
// =====================================
function setupFinalPopup() {
  const trigger = document.getElementById('btn_final');
  const popup = document.getElementById('finalPopup');
  
  if (!trigger || !popup) return;

  trigger.addEventListener('click', (e) => {
    const rect = trigger.getBoundingClientRect();
    popup.style.top = `${rect.bottom + window.scrollY}px`;
    popup.style.left = `${rect.left + window.scrollX}px`;
    popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
  });

  document.addEventListener('click', (e) => {
    if (popup.style.display === 'block' && !popup.contains(e.target) && !trigger.contains(e.target)) {
      popup.style.display = 'none';
    }
  });

  document.querySelectorAll('.final-option').forEach(button => {
    button.addEventListener('click', () => {
      const action = button.dataset.action;

      switch (action) {
        case 'real':
          isDraftMode = false;
          break;
        case 'temp':
          isDraftMode = true;
          break;
        case 'copy':
          alert('伝票を複製します');
          break;
        case 'delete':
          alert('伝票を削除します');
          break;
      }

      updateSlipStyle();
      popup.style.display = 'none';
    });
  });
}

// =====================================
// 伝票日付連動
// =====================================　
function parseSlipDate(value) {
  const match = String(value || '').trim().match(/^(\d{1,4})[/.\-](\d{1,2})(?:[/.\-](\d{1,4}))?$/);
  if (!match) return null;
  let year, month, day;
  if (!match[3]) {
    year = new Date().getFullYear(); month = Number(match[1]); day = Number(match[2]);
  } else if (match[1].length === 4) {
    year = Number(match[1]); month = Number(match[2]); day = Number(match[3]);
  } else {
    month = Number(match[1]); day = Number(match[2]); year = Number(match[3]);
    if (year < 100) year += 2000;
  }
  const date = new Date(year, month - 1, day);
  if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) return null;
  return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}
function setupDateSync() {
  const slipDateInput = document.getElementById('slip_date');
  const datePicker = document.getElementById('slip_date_picker');
  if (!slipDateInput) return;

  const syncSalesDate = isoDate => {
    if (!isoDate) return;
    const date = new Date(`${isoDate}T00:00:00`);
    date.setDate(date.getDate() + 1);
    const salesDateInput = document.getElementById('sales_date');
    if (salesDateInput) {
      salesDateInput.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }
  };

  const applyDate = isoDate => {
    slipDateInput.value = isoDate.replaceAll('-', '/');
    if (datePicker) datePicker.value = isoDate;
    syncSalesDate(isoDate);
  };

  const initialDate = parseSlipDate(slipDateInput.value);
  if (initialDate) {
    slipDateInput.value = initialDate.replaceAll('-', '/');
    if (datePicker) datePicker.value = initialDate;
  }

  slipDateInput.addEventListener('blur', function () {
    const value = this.value.trim();
    if (!value) return;
    const isoDate = parseSlipDate(value);
    if (isoDate) applyDate(isoDate);
  });

  datePicker?.addEventListener('change', function () {
    if (this.value) applyDate(this.value);
  });

  slipDateInput.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    e.stopPropagation();
    const isoDate = parseSlipDate(this.value);
    if (this.value.trim() && !isoDate) {
      alert('日付を 10/6 または 2026/10/06 の形式で入力してください');
      return;
    }
    if (isoDate) applyDate(isoDate);
    $('#customer_code').select2('open');
  });
  // 新規伝票では日付から連続入力を始める
  if (!document.getElementById('receivable_id')?.value) {
    setTimeout(() => slipDateInput.focus(), 0);
  }
}



// =====================================
// 共通Select2初期化関数
// =====================================
function initSelect2(selector, options = {}) {
  const $el = $(selector);
  //検索の設定
  $el.select2({
    ajax: {
      url: options.url,
      dataType: 'json',
      delay: 250,
      data: params => ({
        q: params.term || '',
        ...(options.extraData ? options.extraData() : {})
      }),
      processResults: data => {
        if (data && Array.isArray(data.results)) {
          return { results: data.results };
        }
        return { results: [] };
      },
      cache: true
    },
    placeholder: options.placeholder || '選択してください',
    minimumInputLength: options.minLength || 0,
    width: options.width || '100%',
    dropdownAutoWidth: true,
    allowClear: true,
    // ドロップダウンの表示
    templateResult: (item) => {
      if (!item.id) return item.text;
      return item.text;
    },
    // 選択後の表示（textの最初の部分 = コード）
    templateSelection: item => {
      if (!item.id) return item.text;
      // "123 - 会社名" から "123" だけを抽出
      const parts = item.text.split(' - ');
      return parts[0] || item.text;
    }
  });

  // フォーカス時にドロップダウンを開く
  $el.on('focus', () => $el.select2('open'));

  // ドロップダウンが開いたら検索ボックスにフォーカス
  $el.on('select2:open', () => {
    setTimeout(() => {
      document.querySelector('.select2-search__field')?.focus();
    }, 50);
  });

  // 選択時のコールバック
  if (options.onSelect) {
    $el.on('select2:select', e => {
      const data = e.params.data;
      // textから code と name を抽出
      const parts = data.text.split(' - ');
      // コールバックに渡すオブジェクトを作成
      options.onSelect({
        id: data.id,
        code: parts[0],
        name: parts[1] || '',
        text: data.text,
        raw: data
      });
    });
  }

  return $el;
}

// =====================================
// 初期化処理
// =====================================
document.addEventListener('DOMContentLoaded', () => {
  adjustDetailScrollHeight();
  window.addEventListener('resize', adjustDetailScrollHeight);

  const tbody = document.querySelector('.detail-scroll-container tbody');
  if (tbody) {
    const existingRows = tbody.querySelectorAll('.detail-row').length;
    for (let i = existingRows; i < 100; i++) {
      tbody.appendChild(createEmptyRow(i));
    }
    renumberRows();
  }

  document.querySelectorAll('.detail-row').forEach(row => {
    setupRowCalcEvents(row);
    setupRowSelectEvents(row);
    setupLabelEvents(row);
    setupRowProductSelect(row); 
  });
  updateTotals();

  const first = document.querySelector('.detail-row');
  if (first) first.classList.add('selected');

  setupFinalPopup();
  setupDateSync();

  // =====================================
  // 荷主Select2
  // =====================================
  initSelect2('#shipper_code', {
    url: '/api/customers/search',
    placeholder: 'コード',
    width: 'element',
    onSelect: data => {
      $('#shipper_name').val(data.name);
    }
  });

  // =====================================
  // 得意先Select2
  // =====================================
  const $customerCode = initSelect2('#customer_code', {
    url: '/api/customers/search',
    placeholder: 'コード',
    width: 'element',
    onSelect: data => {
      console.log('得意先選択:', data);
      
      // 得意先の名称欄に入力
      $('#customer_name').val(data.name);

      // 請求先にも同じ値を設定（selectタグなので正常に動作する）
      const $billing = $('#billing_code');
      const newOption = new Option(data.text, data.id, true, true);
      $billing.empty().append(newOption).trigger('change');
      
      console.log('請求先に設定した値:', $billing.val());
      
      // 請求先の名称も手動で設定
      $('#billing_name').val(data.name);

      // 担当部署にフォーカス（請求先ではない）
      setTimeout(() => {
        const departmentInput = document.getElementById('department');
        if (departmentInput) {
          departmentInput.focus();
        }
      }, 100);
    }
  });

  // 得意先のEnter直接入力
  let pendingCustomerSearch = null;

  $customerCode.on('keydown', function(e) {
    if (e.key === 'Enter') {
      const currentValue = $(this).val();
      if (currentValue) {
        pendingCustomerSearch = currentValue;
        $customerCode.select2('close');
      }
    }
  });

  $customerCode.on('select2:close', function() {
    if (!pendingCustomerSearch) return;

    const searchValue = pendingCustomerSearch;
    pendingCustomerSearch = null;

    $.ajax({
      url: '/api/customers/search',
      data: { q: searchValue },
      dataType: 'json'
    }).done(function(response) {
      if (!response.results || response.results.length === 0) return;

      const match = response.results.find(item => {
        const code = item.text.split(' - ')[0];
        return String(code).trim() === String(searchValue).trim();
      });

      const selected = match || response.results[0];
      if (!selected) return;

      const parts = selected.text.split(' - ');
      const code = parts[0];
      const name = parts[1] || '';

      // 得意先に設定
      const customerOption = new Option(selected.text, selected.id, true, true);
      $customerCode.empty().append(customerOption).trigger('change');
      $('#customer_name').val(name);

      // 請求先にも設定
      const billingOption = new Option(selected.text, selected.id, true, true);
      $('#billing_code').empty().append(billingOption).trigger('change');
      $('#billing_name').val(name);

      setTimeout(() => $('#item_code_header')?.focus(), 100);
    });
  });

  // =====================================
  // 担当部署（Enter後 → 品目コードへ）
  // =====================================
  document.getElementById('department')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      $('#item_code_header').select2('open'); 
    }
  });

  // =====================================
  // 請求先Select2
  // =====================================
  initSelect2('#billing_code', {
    url: '/api/customers/search',
    placeholder: 'コード',
    disableAutoOpen: true,
    width: 'element',
    onSelect: data => {
      console.log('請求先選択:', data);
      $('#billing_name').val(data.name);
    }
  });

  // =====================================
  // 品目Select2 + Enter直接入力
  // =====================================
  console.log('品目Select2を初期化します');
  
  const $itemCodeHeader = initSelect2('#item_code_header', {
    url: '/api/item-types/search',
    placeholder: 'コード',
    width: 'element',
    onSelect: data => {
      console.log('品目選択:', data);
      $('#item_name_header').val(data.name);
      setTimeout(() => $('#carrier_code')?.focus(), 100);
    }
  });

  console.log('品目Select2初期化完了');

  // 品目のEnter直接入力
  let pendingItemSearch = null;

  $itemCodeHeader.on('keydown', function(e) {
    console.log('品目でキー押下:', e.key, '値:', $(this).val());
    
    if (e.key === 'Enter') {
      e.preventDefault();
      e.stopPropagation();
      
      const currentValue = $(this).val();
      console.log('品目でEnter検出、値:', currentValue);
      
      if (currentValue) {
        pendingItemSearch = currentValue;
        $itemCodeHeader.select2('close');
      }
    }
  });

  $itemCodeHeader.on('select2:close', function() {
    console.log('品目Select2クローズ、pending:', pendingItemSearch);
    
    if (!pendingItemSearch) return;

    const searchValue = pendingItemSearch;
    pendingItemSearch = null;

    console.log('品目Enter検索:', searchValue);

    $.ajax({
      url: '/api/item-types/search',
      data: { q: searchValue },
      dataType: 'json'
    }).done(function(response) {
      console.log('品目検索結果:', response);
      
      if (!response.results || response.results.length === 0) {
        console.log('品目が見つかりませんでした');
        return;
      }

      // IDが完全一致するものを優先
      const match = response.results.find(item => {
        const itemId = item.text.split(' - ')[0];
        return String(itemId).trim() === String(searchValue).trim();
      });

      const selected = match || response.results[0];
      console.log('品目選択:', selected);

      const parts = selected.text.split(' - ');
      const itemName = parts[1] || '';

      const newOption = new Option(selected.text, selected.id, true, true);
      $itemCodeHeader.empty().append(newOption).trigger('change');
      $('#item_name_header').val(itemName);

      setTimeout(() => $('#carrier_code')?.focus(), 100);
    }).fail(function(error) {
      console.error('品目検索エラー:', error);
    });
  });

  // =====================================
  // 運送会社Select2 + Enter直接入力
  // =====================================
  console.log('運送会社Select2を初期化します');
  
  const $carrierCode = initSelect2('#carrier_code', {
    url: '/api/carriers/search',
    placeholder: 'コード',
    width: 'element',
    onSelect: data => {
      console.log('運送会社選択:', data);
      $('#carrier_name').val(data.name);
      setTimeout(() => $('#summary').focus(), 100);
    }
  });

  console.log('運送会社Select2初期化完了');

  // 運送会社のEnter直接入力
  let pendingCarrierSearch = null;

  $carrierCode.on('keydown', function(e) {
    console.log('運送会社でキー押下:', e.key, '値:', $(this).val());
    
    if (e.key === 'Enter') {
      e.preventDefault();
      e.stopPropagation();
      
      const currentValue = $(this).val();
      console.log('運送会社でEnter検出、値:', currentValue);
      
      if (currentValue) {
        pendingCarrierSearch = currentValue;
        $carrierCode.select2('close');
      }
    }
  });

  $carrierCode.on('select2:close', function() {
    console.log('運送会社Select2クローズ、pending:', pendingCarrierSearch);
    
    if (!pendingCarrierSearch) return;

    const searchValue = pendingCarrierSearch;
    pendingCarrierSearch = null;

    console.log('運送会社Enter検索:', searchValue);

    $.ajax({
      url: '/api/carriers/search',
      data: { q: searchValue },
      dataType: 'json'
    }).done(function(response) {
      console.log('運送会社検索結果:', response);
      
      if (!response.results || response.results.length === 0) {
        console.log('運送会社が見つかりませんでした');
        return;
      }

      // 運送会社コードが完全一致する候補を優先
      const match = response.results.find(carrier => {
        return String(carrier.code).trim() === String(searchValue).trim();
      });

      const selected = match || response.results[0];
      console.log('運送会社選択:', selected);

      const parts = selected.text.split(' - ');
      const itemName = parts[1] || '';

      const newOption = new Option(selected.text, selected.id, true, true);
      $carrierCode.empty().append(newOption).trigger('change');
      $('#carrier_name').val(selected.name);

      setTimeout(() => $('#summary').focus(), 100);
    }).fail(function(error) {
      console.error('運送会社検索エラー:', error);
    });
  });

  // =====================================
  // キーボード操作
  // =====================================
  document.addEventListener('keydown', (e) => {
    // Select2が開いている時は何もしない
    if ($('.select2-container--open').length > 0) {
      return;
    }

    // Select2の候補選択に使ったEnterを、通常の次項目移動に再利用しない
    if (e.target.classList.contains('select2-search__field')) {
      return;
    }
    if (e.key === 'Enter') {
      // Enterキーで次の入力欄に移動
      e.preventDefault();
      // 現在フォーカスされている要素を取得
      const activeElement = document.activeElement;
      
      let currentInput = activeElement;
      if (activeElement.classList.contains('select2-search__field')) {
        
        const $select2Container = $(activeElement).closest('.select2-container');
        
        const selectId = $select2Container
        .prev('.select2-hidden-accessible')
        .attr('id');
        
        currentInput = document.getElementById(selectId);
      }

      // 全入力要素を取得
      const inputs = Array.from(
        document.querySelectorAll('input:not([readonly]), select, textarea')
      )
        .filter(el => {
          // 荷主関連の要素を除外
          if (el.id === 'shipper_code' || el.id === 'shipper_name') {
            return false;
          }
          // 非表示要素を除外
          return el.offsetParent !== null && !el.classList.contains('select2-search__field');
        });

      const index = inputs.indexOf(currentInput);
      
      if (index > -1 && index < inputs.length - 1) {
        const nextInput = inputs[index + 1];
        
        // 次の要素がSelect2の場合
        if ($(nextInput).hasClass('select2-hidden-accessible')) {
          $(nextInput).select2('open');
        } else {
          nextInput.focus();
        }
      }
    }

    if (e.key === 'F2') {
      e.preventDefault();
      document.getElementById('btn_new')?.click();
    }

    if (e.key === 'F7') {
      e.preventDefault();
      document.getElementById('deleteRowBtn')?.click();
    }

    if (e.key === 'F8') {
      e.preventDefault();
      document.getElementById('insertRowBtn')?.click();
    }


    if (e.key === 'F9') {
      e.preventDefault();
      document.getElementById('btn_list')?.click();
    }

    if (e.key === 'F11') {
      e.preventDefault();
      const trigger = document.getElementById('btn_final');
      const popup = document.getElementById('finalPopup');

      if (!trigger || !popup) return;

      const rect = trigger.getBoundingClientRect();
      popup.style.top = `${rect.bottom + window.scrollY}px`;
      popup.style.left = `${rect.left + window.scrollX}px`;
      popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
    }

    if (e.key === 'F12') {
      e.preventDefault();
      document.getElementById('btn_save')?.click();
    }

    if (e.key === 'Escape') {
      e.preventDefault();
      
      if ($('.select2-container--open').length > 0) {
        $('.select2-hidden-accessible').select2('close');
        return;
      }
      
      document.getElementById('btn_home')?.click();
    }
  });

  document.getElementById('btn_new')?.addEventListener('click', () => {
    window.location.href = newSlipUrl;
  });

  document.getElementById('btn_list')?.addEventListener('click', () => {
    window.location.href = '/receivables';
  });

  document.getElementById('btn_save')?.addEventListener('click', saveSlip);

  document.addEventListener('click', () => {
    document.querySelectorAll('.label-popup').forEach(p => p.classList.remove('show'));
  });

  document.getElementById('deleteRowBtn')?.addEventListener('click', () => {
    const selected = document.querySelector('.detail-row.selected');
    if (selected) {
      selected.remove();
      renumberRows();
      updateTotals();
    }
  });

  document.getElementById('insertRowBtn')?.addEventListener('click', () => {
    const selected = document.querySelector('.detail-row.selected');
    if (selected) {
      const newRow = createEmptyRow();
      selected.before(newRow);
      renumberRows();
    }
  });

  document.getElementById('btn_home')?.addEventListener('click', () => {
    window.location.href = menuUrl;
  });

  // 伝票番号欄でEnterを押したら検索
document.getElementById('slip_no')?.addEventListener('keydown', function (e) {
    console.log('伝票番号欄のキー:', e.key);
    if (e.key !== 'Enter') return;

    e.preventDefault();
    e.stopPropagation(); // 下にある共通Enter処理へ伝わらないようにする

    const slipNo = this.value.trim();
    if (!slipNo) return;

    $.ajax({
      url: '/receivables/find-by-slip-no',
      method: 'GET',
      data: { slip_no: slipNo },
      dataType: 'json'
    }).done(response => {
      if (!response.success) {
        alert(response.message);
        this.focus();
        this.select();
        return;
      }

      // 伝票と明細を読み込む編集画面へ移動
      window.location.href = response.redirect_url;
    }).fail(xhr => {
      console.error('伝票番号検索エラー:', xhr);
      alert('伝票番号の検索に失敗しました。');
    });
  });
});

// =====================================
// 伝票ヘッダデータ収集
// ===================================== 
function collectHeaderData() {
  return {
    slip_date: parseSlipDate(document.getElementById('slip_date').value) || '',
    slip_no: document.getElementById('slip_no').value,
    shipper_code: document.getElementById('shipper_code').value,
    shipper_name: document.getElementById('shipper_name').value,
    customer_code: $('#customer_code').val(),
    customer_name: document.getElementById('customer_name').value,
    department: document.getElementById('department').value,
    honorific: document.getElementById('honorific').value,
    billing_code: $('#billing_code').val(),
    billing_name: document.getElementById('billing_name').value,
    item_code_header: $('#item_code_header').val(),
    item_name_header: document.getElementById('item_name_header').value,
    carrier_code: document.getElementById('carrier_code').value,
    carrier_name: document.getElementById('carrier_name').value,
    summary: document.getElementById('summary').value,
    sales_date: document.getElementById('sales_date').value,
    is_draft: isDraftMode
  };
}

// =====================================
// 明細データ収集
// =====================================  
function collectDetails() {
  const details = [];

  document.querySelectorAll('.detail-row').forEach(row => {
    const itemName = row.querySelector('input[name*="[item_name]"]').value;
    // 商品名が空の行は、送らない
    if (!itemName) return;   

    details.push({
      item_code: row.querySelector('select[name*="[item_code]"]').value,
      item_name: itemName,
      package: row.querySelector('input[name*="[package]"]').value,
      unit: row.querySelector('input[name*="[unit]"]').value,
      grade: row.querySelector('input[name*="[grade]"]').value,
      class: row.querySelector('input[name*="[class]"]').value,
      quantity: row.querySelector('input[name*="[quantity]"]').value,
      unit_price: row.querySelector('input[name*="[unit_price]"]').value,
      amount: row.querySelector('input[name*="[amount]"]').value,
      remarks: row.querySelector('input[name*="[remarks]"]').value,
      label_color: row.dataset.labelColor || ''
    });
  });

  return details;
}

// =====================================
// 伝票入力チェック
// ===================================== 
function validateSlip(details) {
  // 保存対象の明細が1件もない場合は、メッセージを表示して保存を止める
  if (details.length === 0) {
    alert('明細がありません');
    return false;
  }

  // チェックをすべて通過した場合だけ保存へ進む
  return true;
}

// =====================================
// 伝票保存
// ===================================== 
function saveSlip() {
  const details = collectDetails();
  const slipDateValue = document.getElementById('slip_date').value.trim();
  if (slipDateValue && !parseSlipDate(slipDateValue)) {
    alert('伝票日付を 10/6 または 2026/10/06 の形式で入力してください');
    document.getElementById('slip_date').focus();
    return;
  }
  // 入力チェックに失敗したら、ここで保存処理を終了する
  if (!validateSlip(details)) return;

  const payload = collectHeaderData();
  payload.details = details;
  // 画面上の hidden 項目や変数から伝票IDを取得
  const receivableId = $('#receivable_id').val(); 

  // ID があれば更新 (PUT /receivables/{id})、なければ新規作成 (POST /receivables)
  const url = receivableId ? `/receivables/${receivableId}` : '/receivables';
  const method = receivableId ? 'PUT' : 'POST';

  $.ajax({
    url: url,
    method: method,
    dataType: 'json',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: payload
  }).done(function (response) {
    console.log('保存成功:', response);
    document.getElementById('slip_no').value = response.slip_no;
    $('#receivable_id').val(response.id); 
    alert('保存しました');
    window.location.href = newSlipUrl;
  }).fail(function (xhr) {
    console.error('保存エラー:', xhr);
    alert('保存に失敗しました');
  });
}
