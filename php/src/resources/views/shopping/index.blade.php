@php
    use App\Enums\ShopType;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="w-8 h-8 flex-shrink-0">
                <x-app-logo class="w-full h-full" />
            </div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('買い物メモ') }}
            </h2>
            <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700">
                <span x-show="!darkMode">🌙</span>
                <span x-show="darkMode">☀️</span>
            </button>
        </div>
    </x-slot>

    <div x-data="{ 
        // URLのtabパラメータを優先し、なければ 'list'
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'list',
        openHistory: {} 
    }" class="max-w-md mx-auto">

        <div class="pb-32 p-4">
            <div x-show="activeTab === 'list'">
                <x-shopping.list-section :items="$items" :frequentItems="$frequentItems" />
            </div>

            <div x-show="activeTab === 'recipe'">
                <x-shopping.recipe-section 
                    :recipes="$recipes" 
                    :ai_error="$ai_error" 
                />
            </div>
            
            <div x-show="activeTab === 'history'">
                <x-shopping.history-section :history="$history" />
            </div>
            
            <div x-show="activeTab === 'setting'">
                <x-shopping.setting-section />
            </div>
            <x-custom-bottom-nav />
        </div>
    </div>
</x-app-layout>