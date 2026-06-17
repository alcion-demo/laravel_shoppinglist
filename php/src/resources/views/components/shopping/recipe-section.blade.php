@props([
    'recipes' => [],
    'ai_error' => null,
    'job_id' => null,
])
@php
    $recipeJson = $recipes;
    $jobId = $job_id;
@endphp

<section class="space-y-6" x-data="{ 
    jobId: {{ Js::from($jobId) }},
    recipeData: {{ Js::from($recipeJson) }},
    isPolling: {{ $jobId ? 'true' : 'false' }},
    aiError: {{ Js::from($ai_error) }},

    init() {
        if (this.isPolling && this.jobId) {
            this.pollStatus();
        }
    },

    pollStatus() {
        let interval = setInterval(() => {
            fetch('/shopping/recipe-status/' + this.jobId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'completed') {
                        this.recipeData = data.recipes;
                        this.isPolling = false;
                        this.jobId = null;
                        clearInterval(interval);
                    } else if (data.status === 'error') {
                        this.aiError = data.message;
                        this.isPolling = false;
                        this.jobId = null;
                        clearInterval(interval);
                    }
                });
        }, 3000);
    }
}">

    <link href="https://fonts.googleapis.com/css2?family=Zen+Kurenaido&display=swap" rel="stylesheet">
    <style>
        .font-maro { font-family: 'Zen Kurenaido', sans-serif; }
    </style>
    <h2 class="text-xl font-bold dark:text-white">献立提案</h2>
    <div class="flex items-center gap-3 p-4 bg-orange-50 dark:bg-gray-800 rounded-xl border border-orange-200 dark:border-gray-700 overflow-hidden">
        <svg class="w-8 h-8 text-yellow-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <p class="font-maro text-[10px] sm:text-xs text-gray-700 dark:text-gray-300 font-medium whitespace-nowrap overflow-hidden">
            慈江美仁麻呂が献立提案を無料枠内にて授けるぞよ
        </p>
    </div>

    {{-- バリデーションエラーの表示（FormRequestから自動反映） --}}
    @error('ingredients', 'suggestion')
        <div class="p-4 bg-red-100 text-red-700 rounded-xl border border-red-200">
            {{ $message }}
        </div>
    @enderror

    {{-- AIエラーの表示 --}}
    @if($ai_error)
        <div class="p-4 bg-red-100 text-red-700 rounded-xl border border-red-200">
            {{ $ai_error }}
        </div>
    @endif

    <template x-if="aiError">
        <div class="p-4 bg-red-100 text-red-700 rounded-xl border border-red-200">
            <span x-text="aiError"></span>
        </div>
    </template>

    {{-- フォーム：送信時は submitForm を呼び出す --}}
    <form method="POST" action="{{ route('shopping.suggest') }}">
        @csrf
        <textarea name="ingredients" 
                  :disabled="isPolling" 
                  class="w-full h-32 rounded-xl border-gray-300 dark:border-gray-700 bg-transparent dark:text-white" 
                  placeholder="食材を改行して入力">{{ old('ingredients') }}</textarea>
        
        <button type="submit" 
                :disabled="isPolling" 
                class="w-full mt-4 py-3 bg-orange-500 text-white rounded-xl font-bold transition-opacity"
                :class="isPolling ? 'opacity-50 cursor-not-allowed' : ''">
            <span x-show="!isPolling">雅な献立を求める</span>
            <span x-show="isPolling">慈江美仁麻呂が只今思案中でおじゃる…</span>
        </button>
    </form>

    {{-- 結果表示（x-for で recipeData を回す） --}}
    <template x-for="recipe in recipeData" :key="recipe.name">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 mt-4">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white" x-text="recipe.name"></h3>

            {{-- 不足材料 --}}
            <template x-if="recipe.missing_ingredients && recipe.missing_ingredients.length > 0">
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm font-bold text-yellow-800 dark:text-yellow-400">不足している材料</p>
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 list-disc list-inside">
                        <template x-for="missing in recipe.missing_ingredients" :key="missing">
                            <li x-text="missing"></li>
                        </template>
                    </ul>
                </div>
            </template>

            {{-- 材料 --}}
            <div class="mt-2">
                <p class="font-bold text-sm text-gray-700 dark:text-gray-300">材料</p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                    <template x-for="ingredient in recipe.ingredients" :key="ingredient">
                        <li x-text="ingredient"></li>
                    </template>
                </ul>
            </div>

            {{-- 全体時間と難易度 --}}
            <template x-if="recipe.metadata">
                <div class="flex gap-4 mt-2 text-xs text-orange-600 font-bold">
                    <span x-text="'難易度: ' + recipe.metadata.difficulty"></span>
                    <span x-text="'全体時間: ' + recipe.metadata.total_time"></span>
                </div>
            </template>

            {{-- 作り方 --}}
            <template x-if="recipe.steps && recipe.steps.length > 0">
                <div class="mt-3 text-sm">
                    <p class="font-bold border-b border-gray-300 dark:border-gray-600 pb-1 mb-2 text-gray-900 dark:text-white">作り方</p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-800 dark:text-gray-200">
                        <template x-for="step in recipe.steps" :key="step.description">
                            <li>
                                <span x-text="step.description"></span>
                                <template x-if="step.duration">
                                    <span class="text-xs bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded ml-1 text-gray-700 dark:text-gray-300" x-text="'(' + step.duration + ')'"></span>
                                </template>
                            </li>
                        </template>
                    </ol>
                </div>
            </template>
        </div>
    </template>
</section>