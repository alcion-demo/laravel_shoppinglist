@props(['history'])

{{-- history-section.blade.php を修正 --}}
<section x-data="{ search: '', openHistory: {} }" x-show="activeTab === 'history'" class="space-y-4" x-cloak>

    {{-- 検索コンテナ --}}
    <div class="relative group px-4 py-6">
        {{-- 1. inputを一番上に書く --}}
        <input type="text" id="historySearch" x-model="search" placeholder="アイテム名で検索..." 
            class="w-full pl-11 pr-24 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl shadow-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 transition-all">
        
        {{-- 2. アイコン --}}
        <div class="absolute left-8 top-1/2 -translate-y-1/2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- 3. ボタンコンテナ（inputの後に配置） --}}
        <div class="absolute right-8 top-1/2 -translate-y-1/2 flex items-center">
            <template x-if="search !== ''">
                <button @click="search = ''" 
                    class="px-4 py-1.5 bg-slate-200 dark:bg-slate-700 rounded-xl text-[11px] font-bold text-slate-600 dark:text-slate-300 active:scale-95 transition-all">
                    クリア
                </button>
            </template>
            <template x-if="search === ''">
                <button class="px-4 py-1.5 bg-blue-600 text-white text-[11px] font-bold rounded-xl active:scale-95 transition-all shadow-md shadow-blue-500/20">
                    検索
                </button>
            </template>
        </div>
    </div>

    @foreach($history as $date => $logs)
        {{-- 検索フィルタリング：商品名にヒットするものだけ表示 --}}
        <div x-show="search === '' || {{ json_encode($logs->pluck('item.name')) }}.some(n => n.includes(search))"
             class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">

            <button @click="openHistory['{{ $date }}'] = !openHistory['{{ $date }}']" class="w-full p-4 text-left">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $date }}</span>
                    <span class="text-gray-400 text-xs" x-text="openHistory['{{ $date }}'] ? '▲' : '▼'"></span>
                </div>
                <div class="text-[11px] text-gray-500 truncate pr-4">
                    {{ $logs->take(3)->map(fn($log) => $log->item->name)->implode('、') }}
                    @if($logs->count() > 3) ... @endif
                </div>
            </button>

            <div x-show="openHistory['{{ $date }}']" class="border-t dark:border-gray-700 dark:text-white bg-gray-50 dark:bg-gray-900/50">
                @foreach($logs as $log)
                    <div class="flex justify-between items-center p-4 border-b">
                        <span class="text-sm">{{ $log->item->name }}</span>
                        <form action="{{ route('shopping.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="name" value="{{ $log->item->name }}">
                            <input type="hidden" name="price" value="{{ $log->price }}">
                            <input type="hidden" name="quantity" value="{{ $log->quantity }}">
                            <input type="hidden" name="shop_type" value="{{ $log->shop_type->value }}">
                            <button type="submit" class="text-xs text-blue-500 font-bold px-2 py-1 bg-blue-50 rounded">＋再追加</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</section>