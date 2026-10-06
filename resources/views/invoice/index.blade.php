@extends('layouts.app')

@section('body-class', 'receivable-list-body')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/receivable-list.css') }}">
@endsection

@section('content')
<div class="receivable-list-page">
    <div class="receivable-list-heading">
        <div>
            <p class="receivable-list-eyebrow">SALES MANAGEMENT</p>
            <h1>売掛伝票一覧</h1>
        </div>
        <a class="receivable-list-new" href="{{ route('invoice.receivable') }}">＋ 新規伝票</a>
    </div>

    <form class="receivable-search" method="GET" action="{{ route('receivables.index') }}">
        <input type="hidden" name="searched" value="1">
        <div class="receivable-search-grid">
            <div class="receivable-search-field date-range-field">
                <label for="date_from">伝票日付</label>
                <div class="date-range-inputs">
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    <span>～</span>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="receivable-search-field">
                <label for="slip_no">伝票番号</label>
                <input type="text" id="slip_no" name="slip_no" value="{{ request('slip_no') }}" placeholder="伝票番号で検索">
            </div>

            <div class="receivable-search-field">
                <label for="customer_code">得意先</label>
                <select id="customer_code" name="customer_code" data-placeholder="得意先を検索">
                    <option value="">すべて</option>
                    @if ($selectedCustomer)
                        <option value="{{ $selectedCustomer->id }}" selected>
                            {{ $selectedCustomer->code }} - {{ $selectedCustomer->company_name }}
                        </option>
                    @endif
                </select>
            </div>

            <div class="receivable-search-field">
                <label for="item_type_id">品目</label>
                <select id="item_type_id" name="item_type_id" data-placeholder="品目を検索">
                    <option value="">すべて</option>
                    @if ($selectedItem)
                        <option value="{{ $selectedItem->id }}" selected>
                            {{ $selectedItem->id }} - {{ $selectedItem->name }}
                        </option>
                    @endif
                </select>
            </div>
        </div>

        <div class="receivable-search-actions">
            <a class="receivable-clear" href="{{ route('receivables.index') }}">条件をクリア</a>
            <button class="receivable-search-button" type="submit">検索する</button>
        </div>
    </form>

    <section class="receivable-results">
        @if ($hasSearched)
            <div class="receivable-results-heading">
                <h2>検索結果</h2>
                <span>{{ $receivables->total() }} 件</span>
            </div>

            <div class="receivable-table-wrap">
                <table class="receivable-table">
                    <thead>
                        <tr>
                            <th>伝票日付</th>
                            <th>伝票番号</th>
                            <th>得意先</th>
                            <th>品目</th>
                            <th>伝票区分</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($receivables as $receivable)
                            <tr>
                                <td>{{ $receivable->slip_date }}</td>
                                <td class="slip-number">{{ $receivable->slip_no }}</td>
                                <td>{{ $receivable->customer_name }}</td>
                                <td>{{ $receivable->item_name_header ?: '—' }}</td>
                                <td>
                                    <span class="slip-status {{ $receivable->is_draft ? 'is-draft' : '' }}">
                                        {{ $receivable->is_draft ? '仮伝票' : '本伝票' }}
                                    </span>
                                </td>
                                <td>
                                    <a class="receivable-open" href="{{ route('receivables.edit', $receivable->id) }}">開く</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="receivable-empty" colspan="6">条件に一致する伝票がありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="receivable-pagination">
                {{ $receivables->links() }}
            </div>
        @else
            <p class="receivable-search-prompt">検索条件を選んで「検索する」を押してください。</p>
        @endif
    </section>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/receivable-list.js') }}"></script>
@endsection
