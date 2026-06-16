@props(['recipes' => [], 'ai_error' => null])

<section class="space-y-6">
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kurenaido&display=swap" rel="stylesheet">
    <style>
        .font-maro { font-family: 'Zen Kurenaido', sans-serif; }
    </style>

    <h2 class="text-xl font-bold dark:text-white">献立提案</h2>
    <div class="flex items-center gap-3 p-4 bg-orange-50 dark:bg-gray-800 rounded-xl border border-orange-200 dark:border-gray-700 overflow-hidden">
        <svg class="w-8 h-8 text-yellow-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <p class="font-maro text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap overflow-hidden text-ellipsis">
            慈江美仁麻呂が献立提案を無料枠内にて授けるぞよ
        </p>
    </div>

    {{-- ★ここでタブ切り替えを意識せず、献立タブ表示時のみエラーを表示する制御にします --}}
    <div x-show="activeTab === 'recipe' && (@json($errors->suggestion->any()) || @json(session()->has('ai_error')))">
        <div class="p-4 bg-red-600 text-white rounded-xl font-bold shadow-lg">
            @if(session('ai_error'))
                <p>{{ session('ai_error') }}</p>
            @endif
            @foreach ($errors->suggestion->all() as $error)
                <p>・{{ $error }}</p>
            @endforeach
        </div>
    </div>

    {{-- 入力エリア --}}
    <div x-data="{ isSubmitting: false }">
        <form action="{{ route('shopping.suggest') }}" 
            method="POST" 
            @submit.prevent="isSubmitting = true; $el.submit()"
            :aria-busy="isSubmitting">
            @csrf
            <textarea name="ingredients"
                x-bind:disabled="isSubmitting"
                class="w-full h-32 rounded-xl border-gray-300 dark:border-gray-700 bg-transparent dark:text-white"
                placeholder="食材を改行して入力するでおじゃる">{{ old('ingredients') }}</textarea>

            <button type="submit" 
                    x-bind:class="isSubmitting ? 'opacity-50 pointer-events-none' : ''"
                    class="w-full mt-4 py-3 bg-orange-500 text-white rounded-xl font-bold transition-opacity"
                    :disabled="isSubmitting">
                <span x-show="!isSubmitting">雅な献立を求める</span>
                <span x-show="isSubmitting">思考中でおじゃる…</span>
            </button>
        </form>
    </div>

    {{-- 提案結果エリア --}}
    @foreach($recipes as $recipe)
        <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $recipe['name'] }}</h3>

            {{-- 不足材料の表示エリア --}}
            @if(!empty($recipe['missing_ingredients']))
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm font-bold text-yellow-800 dark:text-yellow-400">不足している材料</p>
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 list-disc list-inside">
                        @foreach($recipe['missing_ingredients'] as $missing)
                            <li>{{ $missing }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 材料の分量表示（もしエージェントが分量付き文字列を返していればそのまま表示） --}}
            <div class="mt-2">
                <p class="font-bold text-sm text-gray-700 dark:text-gray-300">材料</p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                    @foreach($recipe['ingredients'] as $ingredient)
                        <li>{{ $ingredient }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- 全体時間と難易度 --}}
            @if(isset($recipe['metadata']))
                <div class="flex gap-4 mt-2 text-xs text-orange-600 font-bold">
                    <span>難易度: {{ $recipe['metadata']['difficulty'] }}</span>
                    <span>全体時間: {{ $recipe['metadata']['total_time'] }}</span>
                </div>
            @endif

            {{-- 手順と工程ごとの時間 --}}
            @if(!empty($recipe['steps']))
                <div class="mt-3 text-sm">
                    <p class="font-bold border-b border-gray-300 dark:border-gray-600 pb-1 mb-2 text-gray-900 dark:text-white">作り方</p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-800 dark:text-gray-200">
                        @foreach($recipe['steps'] as $step)
                            <li>
                                {{ $step['description'] }}
                                @if($step['duration'])
                                    <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded ml-1 text-gray-700 dark:text-gray-300">
                                        ({{ $step['duration'] }})
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    @endforeach
</section>