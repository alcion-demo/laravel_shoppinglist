@props(['item'])

<div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm">

    <div class="flex-1 min-w-0 mr-4">

        <div class="text-gray-900 dark:text-gray-100 font-bold text-sm truncate mb-1">
            {{ $item->name }}
        </div>

        @if($latestLog = $item->recentPurchaseLogs->first())
            <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                <span class="truncate max-w-[100px]">{{ $latestLog->shop_type?->label() }}</span>
                <span class="text-gray-300 dark:text-gray-600">|</span>

                @if($latestLog->price > 0)
                    <span class="font-medium text-gray-600 dark:text-gray-300">¥{{ number_format($latestLog->price) }}</span>
                @endif

                @if($latestLog->quantity)
                    <span class="flex items-center gap-1">
                        <span class="text-[9px] text-gray-400 dark:text-gray-600">×</span>
                        <span>{{ $latestLog->quantity }}</span>
                    </span>
                @endif
                <span class="ml-2 text-gray-400">
                    &nbsp;{{ $latestLog->purchased_at->format('m/d') }}購入
                </span>
            </div>
        @endif

    </div>

    <form action="{{ route('shopping.destroy', $item) }}"
          method="POST"
          onsubmit="return confirm('リストから削除しますか？')">
        @csrf
        @method('DELETE')

        <button type="submit" class="text-red-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </form>

</div>