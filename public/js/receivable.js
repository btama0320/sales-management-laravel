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
  document.querySelectorAll('.detail-row .row-index').forEach((span, i) => {
    span.textContent = i + 1;
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
          <input type="text" name="details[${rowIndex}][item_code]" class="code-input" placeholder="コード" value="">
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
      popup.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected-color'));
      opt.classList.add('selected-color');

      box.style.backgroundColor =
        color === 'red' ? '#f44336' :
        color === 'blue' ? '#2196f3' :
        color === 'green' ? '#4caf50' :
        '#fff';

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
function setupDateSync() {
  const slipDateInput = document.getElementById('slip_date');
  if (!slipDateInput) return;

  slipDateInput.addEventListener('change', function () {
    const slipDate = new Date(this.value);
    if (!isNaN(slipDate)) {
      const salesDate = new Date(slipDate);
      salesDate.setDate(slipDate.getDate() + 1);

      const yyyy = salesDate.getFullYear();
      const mm = String(salesDate.getMonth() + 1).padStart(2, '0');
      const dd = String(salesDate.getDate()).padStart(2, '0');
      const formatted = `${yyyy}-${mm}-${dd}`;

      const salesDateInput = document.getElementById('sales_date');
      if (salesDateInput) salesDateInput.value = formatted;
    }
  });
}



// =====================================
// 共通Select2初期化関数（修正版）
// =====================================
function initSelect2(selector, options = {}) {
  const $el = $(selector);

  $el.select2({
    ajax: {
      url: options.url,
      dataType: 'json',
      delay: 250,
      data: params => ({ q: params.term || '' }),
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
    width: '30%',  // ← widthを100%に変更
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

  $el.on('focus', () => $el.select2('open'));

  $el.on('select2:open', () => {
    setTimeout(() => {
      document.querySelector('.select2-search__field')?.focus();
    }, 50);
  });

  if (options.onSelect) {
    $el.on('select2:select', e => {
      const data = e.params.data;
      // textから code と name を抽出
      const parts = data.text.split(' - ');
      options.onSelect({
        id: data.id,
        code: parts[0],
        name: parts[1] || '',
        text: data.text
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
    for (let i = 0; i < 99; i++) {
      tbody.appendChild(createEmptyRow(i));
    }
    renumberRows();
  }

  document.querySelectorAll('.detail-row').forEach(row => {
    setupRowCalcEvents(row);
    setupRowSelectEvents(row);
    setupLabelEvents(row);
  });

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
    onSelect: data => {
      // console.log('得意先選択:', data);
      
      // 得意先の名称欄に入力
      $('#customer_name').val(data.name);

      // 請求先にも同じ値を設定（selectタグなので正常に動作する）
      const $billing = $('#billing_code');
      const newOption = new Option(data.text, data.id, true, true);
      $billing.empty().append(newOption).trigger('change');
      
      // console.log('請求先に設定した値:', $billing.val());
      
      // 請求先の名称も手動で設定
      $('#billing_name').val(data.name);

      setTimeout(() => $('#billing_code')?.focus(), 100);
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
  // 請求先Select2
  // =====================================
  initSelect2('#billing_code', {
    url: '/api/customers/search',
    placeholder: 'コード',
    onSelect: data => {
      // console.log('請求先選択:', data);
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
    onSelect: data => {
      // console.log('品目選択:', data);
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
  // キーボード操作
  // =====================================
  document.addEventListener('keydown', (e) => {
    // Select2が開いている時はスキップ
    if ($('.select2-container--open').length > 0) {
      return;
    }

    if (e.key === 'Enter') {
      e.preventDefault();

      const activeElement = document.activeElement;
      const inputs = Array.from(document.querySelectorAll('input:not([readonly]), select, textarea'))
        .filter(el => el.offsetParent !== null);

      const index = inputs.indexOf(activeElement);
      if (index > -1 && index < inputs.length - 1) {
        const nextInput = inputs[index + 1];
        
        if ($(nextInput).hasClass('select2-hidden-accessible')) {
          $(nextInput).select2('open');
        } else {
          nextInput.focus();
        }
      }
    }

    if (e.key === 'F7') {
      e.preventDefault();
      document.getElementById('deleteRowBtn')?.click();
    }

    if (e.key === 'F8') {
      e.preventDefault();
      document.getElementById('insertRowBtn')?.click();
    }

    if (e.key === 'Escape') {
      e.preventDefault();
      
      if ($('.select2-container--open').length > 0) {
        $('.select2-hidden-accessible').select2('close');
        return;
      }
      
      document.getElementById('btn_home')?.click();
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
  });

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
});