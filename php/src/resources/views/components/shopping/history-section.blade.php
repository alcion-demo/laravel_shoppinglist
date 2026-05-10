@props(['history'])

<section x-show="activeTab === 'history'" class="space-y-4" x-cloak>
    @foreach($history as $date => $logs)
        <div class="bg-white dark:bg-gray-800 rounded-xl border dark:border-gray-700 overflow-hidden">

            <button
                @click="openHistory['{{ $date }}'] = !openHistory['{{ $date }}']"
                class="w-full flex justify-between items-center p-4">

                <span class="font-bold text-gray-900 dark:text-gray-100">
                    {{ $date }}
                </span>

                <span
                    class="text-gray-400"
                    x-text="openHistory['{{ $date }}'] ? '▲' : '▼'">
                </span>
            </button>

            <div
                x-show="openHistory['{{ $date }}']"
                class="border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">

                @foreach($logs as $log)

                    <div class="flex justify-between items-center p-4 border-b last:border-0 dark:border-gray-700">

                        <div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $log->item->name }}
                            </div>

                            <div class="text-xs text-gray-400">
                                {{ $log->shop_type->label() }}
                            </div>
                        </div>

                        <div class="font-mono font-bold text-gray-600 dark:text-gray-300">
                            ¥{{ number_format($log->price) }}
                        </div>

                    </div>

                @endforeach

            </div>
        </div>
    @endforeach
</section>