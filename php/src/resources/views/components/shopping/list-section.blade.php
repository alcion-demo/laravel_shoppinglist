@props(['items', 'frequentItems'])

@php
    use App\Enums\ShopType;
@endphp

<section x-show="activeTab === 'list'" class="space-y-6">

    @if($frequentItems->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-xs font-bold text-gray-500 px-2 uppercase tracking-wider mb-2">よく買うもの</h3>
            <div class="grid grid-cols-3 gap-2">
                @foreach($frequentItems as $log)
                    @php
                        $isAlreadyInCart = $items->contains('shopping_item_id', $log->shopping_item_id);
                    @endphp
                    <form action="{{ route('shopping.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="name" value="{{ $log->item->name }}">

                        <button type="submit" 
                            {{ $isAlreadyInCart ? 'disabled' : '' }}
                            class="w-full py-2 px-3 text-[11px] font-bold text-center truncate 
                                {{ $isAlreadyInCart ? 'opacity-30 cursor-not-allowed' : 'opacity-100 hover:bg-blue-500/20' }}
                                bg-gray-800/40 text-gray-200 
                                border border-blue-200/30 rounded-full 
                                transition-all duration-300 transform active:scale-95">
                            ＋ {{ $log->item->name }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border dark:border-gray-700">
        <form action="{{ route('shopping.store') }}" method="POST"
              class="space-y-2 p-2 dark:bg-gray-800/50 rounded-2xl">
            @csrf

            <div class="flex gap-2">
                <input type="text" name="name"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 bg-transparent text-black dark:text-white"
                    placeholder="何を買う？">

                <button type="submit"
                    class="w-12 h-12 flex items-center justify-center bg-blue-600 text-white rounded-xl">
                    ＋
                </button>
            </div>

            <div class="flex gap-2 items-center">
                <select name="shop_type"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white text-sm">
                    @foreach(ShopType::cases() as $type)
                        <option value="{{ $type->value }}">
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="price" min="0" value="1"
                    class="w-28 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white text-sm"
                    placeholder="単価">

                <input type="text" name="quantity"
                    class="w-16 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white text-sm"
                    placeholder="個">
            </div>

            <x-message />
        </form>
    </div>

    <div class="space-y-2">
        <h3 class="text-sm font-bold text-gray-500 px-2 uppercase tracking-wider">
            最近のアイテム
        </h3>

        @foreach ($items as $cart)
            <x-shopping.item-card :cart="$cart" />
        @endforeach

        @if($items->isEmpty())
            <p class="text-center text-sm text-gray-400 py-4">リストは空です</p>
        @endif
    </div>

</section>