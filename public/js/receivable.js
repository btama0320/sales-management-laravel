// =====================================
// 商品マスタ（仮データ）
// =====================================
const productMaster = [
  { code: 'A001', name: 'りんご', tax_rate: '8', unit_price: 120 },
  { code: 'B002', name: '水', tax_rate: '10', unit_price: 100 }
];

// =====================================
// グローバル変数
// =====================================
let isDraftMode = false;


// グローバル関数
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
  
  // イベント設定
  setupRowCalcEvents(row);
  setupRowSelectEvents(row);
  setupLabelEvents(row);
  
  return row;
}

// =====================================
// 税率印・計算イベント
// =====================================
function setupRowCalcEvents(row) {
  const codeInput = row.querySelector('.code-input');
  const qty = row.querySelector('input[name*="[quantity]"]');
  const price = row.querySelector('input[name*="[unit_price]"]');
  const amount = row.querySelector('input[name*="[amount]"]');

  // 商品コード入力時
  codeInput.addEventListener('input', () => {
    const code = codeInput.value.trim();
    const product = productMaster.find(p => p.code === code);

    if (product) {
      row.querySelector('.name-input').value = product.name;
      price.value = product.unit_price;
    }

    // ← ここに入れる！
    const taxMark = row.querySelector('.tax-mark');
    if (taxMark) {
      const taxRate = product?.tax_rate || '10';
      taxMark.style.display = (taxRate === '8') ? 'inline' : 'none';
    }

    if (typeof updateTotals === 'function') {
      updateTotals();
    }
  });

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

  // ラベル枠クリック → ポップアップ開閉
  box.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();

    document.querySelectorAll('.label-popup').forEach(p => p.classList.remove('show'));
    popup.classList.toggle('show');
  });

  // 色クリック
  popup.querySelectorAll('.color-option').forEach(opt => {
    opt.addEventListener('click', (e) => {
      e.stopPropagation();

      const color = opt.dataset.color;

      // 選択枠のリセット
      popup.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected-color'));
      opt.classList.add('selected-color');

      // 色反映
      box.style.backgroundColor =
        color === 'red' ? '#f44336' :
        color === 'blue' ? '#2196f3' :
        color === 'green' ? '#4caf50' :
        '#fff';

      popup.classList.remove('show');
    });
  });

  // ポップアップ自体クリック → 閉じる
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

    // 仮印がまだなければ追加
    if (!draftMark) {
      draftMark = document.createElement('span');
      draftMark.className = 'draft-mark';
      draftMark.textContent = '仮';
      document.querySelector('.nav-buttons')?.appendChild(draftMark);
    }
  } else {
    container.classList.remove('draft-slip');
    if (saveBtnLabel) saveBtnLabel.textContent = '保存';

    // 仮印があれば削除
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

  // ボタンクリックでポップアップ表示
  trigger.addEventListener('click', (e) => {
    const rect = trigger.getBoundingClientRect();

    popup.style.top = `${rect.bottom + window.scrollY}px`;
    popup.style.left = `${rect.left + window.scrollX}px`;
    popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
  });

  // 他の場所をクリックしたら閉じる
  document.addEventListener('click', (e) => {
    if (popup.style.display === 'block' && !popup.contains(e.target) && !trigger.contains(e.target)) {
      popup.style.display = 'none';
    }
  });

  // 各オプションの処理
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
      salesDate.setDate(slipDate.getDate() + 1); // ＋1日

      // yyyy-mm-dd 形式に整形
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
  // 高さ調整
  adjustDetailScrollHeight();
  window.addEventListener('resize', adjustDetailScrollHeight);

  const tbody = document.querySelector('.detail-scroll-container tbody');

  // JSで100行追加
  if (tbody) {
    for (let i = 0; i < 100; i++) {
      tbody.appendChild(createEmptyRow(i));
    }
    renumberRows();
  }

  // Bladeで書いた行も含めて全行にイベント仕込み
  document.querySelectorAll('.detail-row').forEach(row => {
    setupRowCalcEvents(row);
    setupRowSelectEvents(row);
    setupLabelEvents(row);
  });

  // 最初の行選択（Blade含む）
  const first = document.querySelector('.detail-row');
  if (first) first.classList.add('selected');

  // 伝票機能セットアップ
  setupFinalPopup();
  
  // 日付連動セットアップ
  setupDateSync();

  // Enter移動
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();

      // 商品コード欄ならダミーデータ呼び出し
      if (document.activeElement.classList.contains('code-input')) {
        const codeInput = document.activeElement;
        const row = codeInput.closest('.detail-row');
        const product = productMaster.find(p => p.code === codeInput.value.trim());

        if (product) {
          row.querySelector('.name-input').value = product.name;
          row.querySelector('input[name*="[unit_price]"]').value = product.unit_price;
        }

        const taxMark = row.querySelector('.tax-mark');
        const taxRate = product?.tax_rate || '10';
        taxMark.style.display = (taxRate === '8') ? 'inline' : 'none';

        if (typeof updateTotals === 'function') updateTotals();
      }

      // 次の入力欄へ移動（ここは1回だけでOK）
      const inputs = Array.from(document.querySelectorAll('input, textarea'));
      const index = inputs.indexOf(document.activeElement);
      if (index > -1 && index < inputs.length - 1) {
        inputs[index + 1].focus();
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
  });

  // 表の外クリック → すべて閉じる
  document.addEventListener('click', () => {
    document.querySelectorAll('.label-popup').forEach(p => p.classList.remove('show'));
  });

  // 行削除
  document.getElementById('deleteRowBtn')?.addEventListener('click', () => {
    const selected = document.querySelector('.detail-row.selected');
    if (selected) {
      selected.remove();
      renumberRows();          // 行番号を振り直す
      updateTotals();          // ← 削除後に合計を再計算
    }
  });

  // 行挿入
  document.getElementById('insertRowBtn')?.addEventListener('click', () => {
    const selected = document.querySelector('.detail-row.selected');
    if (selected) {
      const newRow = createEmptyRow();
      selected.before(newRow);
      renumberRows();
    }
  });
});