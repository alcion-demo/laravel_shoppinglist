@props(['cart'])

<div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700/50">

    <div class="flex-1 min-w-0 mr-4">
        <div class="text-gray-900 dark:text-gray-100 font-bold text-sm truncate mb-1">
            {{ $cart->item->name }}

            @php
                $recentPurchasedAt = \App\Models\PurchaseLog::getRecentPurchaseIn3Days(auth()->id(), $cart->shopping_item_id);
            @endphp
            @if($recentPurchasedAt)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 whitespace-nowrap">
                    {{ $recentPurchasedAt->format('m/d') }}済
                </span>
            @endif

        </div>

        <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
            <span class="truncate max-w-[100px]">{{ $cart->shop_type?->label() }}</span>
            <span class="text-gray-300 dark:text-gray-600">|</span>

            @if($cart->price > 0)
                <span class="font-medium text-gray-600 dark:text-gray-300 mr-1">¥{{ number_format($cart->price) }}</span>
            @endif

            @if(!empty($cart->quantity))
                <span class="flex items-center gap-1">
                    @if($cart->price > 0)
                        <span class="text-[9px] text-gray-400 dark:text-gray-600">×</span>
                    @endif
                    <span>{{ $cart->quantity }}</span>
                </span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2">
        
        <form action="{{ route('shopping.purchase', $cart->id) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="text-green-500 hover:text-green-600 w-8 h-8 bg-green-500/10 dark:bg-green-900/20 rounded-xl transition-colors font-black text-xs flex items-center justify-center leading-none" 
                    title="購入完了">
                済
            </button>
        </form>

        <a href="{{ route('shopping.edit', $cart->id) }}" 
           class="text-blue-500 hover:text-blue-600 p-1.5 bg-blue-500/10 dark:bg-blue-900/20 rounded-xl transition-colors" 
           title="編集">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </a>

        <form action="{{ route('shopping.destroy', $cart->id) }}"
              method="POST"
              onsubmit="return confirm('リストから削除しますか？')">
            @csrf
            @method('DELETE')

            <button type="submit" class="text-red-500 hover:text-red-600 p-1.5 bg-red-500/10 dark:bg-red-900/20 rounded-xl transition-colors" title="削除">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </form>

    </div>

</div>