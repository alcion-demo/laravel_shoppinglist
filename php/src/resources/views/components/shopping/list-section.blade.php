@props(['items'])

@php
    use App\Enums\ShopType;
@endphp

<section x-show="activeTab === 'list'" class="space-y-6">

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

                <input type="number" name="price" min="0" value="0"
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