  // =====================================
  // 運送会社Select2 + Enter直接入力
  // =====================================
  console.log('運送会社Select2を初期化します');
  
  const $carrierCode = initSelect2('#carrier_code', {
    url: '/api/carriers/search',
    placeholder: 'コード',
    onSelect: data => {
      console.log('運送会社選択:', data);
      $('#carrier_name').val(data.name);
      setTimeout(() => $('#carrier_code')?.focus(), 100);
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

      // IDが完全一致するものを優先
      const match = response.results.find(item => {
        const itemId = item.text.split(' - ')[0];
        return String(itemId).trim() === String(searchValue).trim();
        
      });

      const selected = match || response.results[0];
      console.log('運送会社選択:', selected);

      const parts = selected.text.split(' - ');
      const itemName = parts[1] || '';

      const newOption = new Option(selected.text, selected.id, true, true);
      $carrierCode.empty().append(newOption).trigger('change');
      $('#carrier_name').val(itemName);

      setTimeout(() => $('#carrier_code')?.focus(), 100);
    }).fail(function(error) {
      console.error('運送会社検索エラー:', error);
    });
  });