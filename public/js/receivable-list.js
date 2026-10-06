document.addEventListener('DOMContentLoaded', () => {
  const select2Options = (url, placeholder) => ({
    ajax: {
      url,
      dataType: 'json',
      delay: 250,
      data: params => ({ q: params.term || '' }),
      processResults: response => ({ results: response.results || [] }),
      cache: true
    },
    placeholder,
    minimumInputLength: 0,
    width: '100%',
    allowClear: true
  });

  $('#customer_code').select2(
    select2Options('/api/customers/search', '得意先を検索')
  );

  $('#item_type_id').select2(
    select2Options('/api/item-types/search', '品目を検索')
  );
});
