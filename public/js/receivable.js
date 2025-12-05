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
  const headerHeight   = document.querySelector('.nav-buttons')?.offsetHeight || 0;
  const formHeight     = document.querySelector('.form-table')?.offsetHeight || 0;
  const totalsHeight   = document.querySelector('.totals-bar')?.offsetHeight || 0;

  const reservedHeight = logoutHeight + hintHeight + headerHeight + formHeight + totalsHeight + 40;
  const availableHeight = window.innerHeight - reservedHeight;

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
// 初期化
// =====================================
document.addEventListener('DOMContentLoaded', () => {
  adjustDetailScrollHeight();
  window.addEventListener('resize', adjustDetailScrollHeight);

  const tbody = document.querySelector('.detail-scroll-container tbody');

  if (tbody) {
    for (let i = 0; i < 100; i++) {
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
  // ✅ Select2初期化（品目コード欄）
  // =====================================
  const $itemCodeHeader = $('#item_code_header');
  $itemCodeHeader.select2({
    ajax: {
      url: '/sales-management/public/api/item-types/search',
      dataType: 'json',
      delay: 250,
      data: function(params) {
        return { q: params.term || '' };
      },
      processResults: function(data) {
        console.log('API response:', data);
        
        if (data && Array.isArray(data.results)) {
          console.log('結果を返します:', data.results.length, '件');
          
          if (data.results.length > 0) {
            console.log('最初のアイテム:', data.results[0]);
          }
          
          // id を必ず文字列に変換
          const formattedResults = data.results.map(item => {
            return {
              id: String(item.id || ''),
              text: item.text || ''
            };
          });
          
          console.log('整形後の結果:', formattedResults);
          
          return { 
            results: formattedResults
          };
        }
        
        console.error('データが不正です:', data);
        return { results: [] };
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        console.error('Status:', status);
        console.error('Response:', xhr.responseText);
      },
      cache: true
    },
    placeholder: 'コード or 検索ワード',
    minimumInputLength: 0,
    width: '200px',
    dropdownAutoWidth: true,
    allowClear: true,
    templateResult: function(item) {
      if (!item.id) return item.text;
      return item.text;
    },
    templateSelection: function(item) {
      if (!item.id) return item.text;
      const parts = item.text.split(' - ');
      return parts[0];
    }
  });
  
  // フォーカス時に自動で開く
  $itemCodeHeader.on('focus', function () {
    $(this).select2('open');
  });

  // 開いたら検索フィールドにフォーカス
  $itemCodeHeader.on('select2:open', function () {
    setTimeout(() => {
      const searchField = document.querySelector('.select2-search__field');
      if (searchField) {
        searchField.focus();
      }
    }, 50);
  });

  // 選択時の処理
  $itemCodeHeader.on('select2:select', function (e) {
    const data = e.params.data;
    const parts = data.text.split(' - ');
    const itemCode = parts[0];
    const itemName = parts[1];

    console.log('選択:', itemCode, itemName);
    
    document.getElementById('item_name_header').value = itemName;

    setTimeout(() => {
      document.getElementById('carrier_code')?.focus();
    }, 100);
  });

  // =====================================
  // ✅ コード直接入力の処理
  // =====================================
  let pendingSearchValue = null;
  
  // パターン1: Select2の検索フィールド内でEnter
  $(document).on('keydown', '.select2-search__field', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      e.stopPropagation();
      
      pendingSearchValue = $(this).val();
      console.log('検索フィールドでEnter、値を保存:', pendingSearchValue);
      
      $itemCodeHeader.select2('close');
    }
  });
  
  // パターン2: Select2のコンテナでEnter
  $itemCodeHeader.on('keydown', function(e) {
    if (e.key === 'Enter') {
      const currentValue = $(this).val();
      console.log('Select2コンテナでEnter、現在の値:', currentValue);
      
      if (currentValue) {
        pendingSearchValue = currentValue;
        $itemCodeHeader.select2('close');
      }
    }
  });

  // ドロップダウンが閉じた後に検索を実行
  $itemCodeHeader.on('select2:close', function() {
    console.log('select2:close イベント発火、pendingSearchValue:', pendingSearchValue);
    
    if (pendingSearchValue) {
      const searchValue = pendingSearchValue;
      pendingSearchValue = null;
      
      console.log('ドロップダウンが閉じたので検索開始:', searchValue);
      
      $.ajax({
        url: '/sales-management/public/api/item-types/search',
        data: { q: searchValue },
        dataType: 'json'
      }).done(function(response) {
        console.log('Enterでの検索結果:', response);
        
        if (response.results && response.results.length > 0) {
          const match = response.results.find(item => {
            const parts = item.text.split(' - ');
            const itemCode = String(parts[0]).trim();
            const searchCode = String(searchValue).trim();
            console.log('比較:', itemCode, '===', searchCode, '→', itemCode === searchCode);
            return itemCode === searchCode;
          });
          
          const selectedItem = match || response.results[0];
          
          if (selectedItem) {
            const parts = selectedItem.text.split(' - ');
            const itemCode = parts[0];
            const itemName = parts[1];
            
            console.log('選択:', itemCode, itemName);
            
            const newOption = new Option(itemCode, selectedItem.id, true, true);
            $itemCodeHeader.append(newOption).trigger('change');
            
            document.getElementById('item_name_header').value = itemName;
            console.log('設定完了 - コード:', itemCode, '品目名:', itemName);
            
            setTimeout(() => {
              document.getElementById('carrier_code')?.focus();
            }, 100);
          }
        }
      }).fail(function(xhr, status, error) {
        console.error('検索エラー:', error);
      });
    }
  });

  // =====================================
  // キーボード操作
  // =====================================
  document.addEventListener('keydown', (e) => {
    if ($('.select2-container--open').length > 0) {
      if (e.key === 'Enter') {
        return;
      }
    }

    if (e.key === 'Enter') {
      e.preventDefault();

      const activeElement = document.activeElement;

      if (activeElement.classList.contains('select2-search__field')) {
        return;
      }

      const inputs = Array.from(document.querySelectorAll('input:not([readonly]), select, textarea'))
        .filter(el => {
          return !el.classList.contains('select2-search__field') && 
                 el.offsetParent !== null;
        });

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