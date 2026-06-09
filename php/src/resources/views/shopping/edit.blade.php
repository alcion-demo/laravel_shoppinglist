<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            アイテムの編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl p-6 border dark:border-gray-700">
                
                <form action="{{ route('shopping.update', $cart->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT') {{-- ResourceのupdateなのでPUTを指定 --}}

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">商品名</label>
                        <input type="text" name="name" value="{{ old('name', $cart->item->name) }}"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">店舗</label>
                        <select name="shop_type"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                            @foreach(\App\Enums\ShopType::cases() as $type)
                                <option value="{{ $type->value }}" {{ $cart->shop_type == $type ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">単価 (¥)</label>
                            <input type="number" name="price" min="0" value="{{ old('price', $cart->price) }}"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        </div>

                        <div class="w-32">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">個数・数量</label>
                            <input type="text" name="quantity" value="{{ old('quantity', $cart->quantity) }}" inputmode="numeric"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                placeholder="例: 2本、1P">
                        </div>
                    </div>

                    <x-message />

                    <div class="flex gap-2 pt-2">
                        <a href="{{ route('shopping.index') }}" 
                           class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-center py-3 rounded-xl font-bold text-sm">
                            キャンセル
                        </a>
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold text-sm shadow-sm">
                            変更を保存
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>