<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $recipe['name'] }} - 献立共有</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // class方式でダークモードを制御
        }
    </script>
    <style>
        /* ダークモードの判定をOS設定に合わせるためのスクリプト */
        .dark-mode-check {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen p-4"
      x-data="{ darkMode: window.matchMedia('(prefers-color-scheme: dark)').matches }"
      :class="darkMode ? 'dark' : ''">

    <div class="max-w-md mx-auto">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
            
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                {{ $recipe['name'] }}
            </h1>

            {{-- 不足材料 --}}
            @if(!empty($recipe['missing_ingredients']))
                <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm font-bold text-yellow-800 dark:text-yellow-400">不足している材料</p>
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 list-disc list-inside">
                        @foreach($recipe['missing_ingredients'] as $missing)
                            <li>{{ $missing }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 材料 --}}
            <div class="mb-6">
                <p class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">材料</p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside space-y-1">
                    @foreach($recipe['ingredients'] as $ingredient)
                        <li>{{ $ingredient }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- メタデータ --}}
            @if(!empty($recipe['metadata']))
                <div class="flex gap-4 mb-6 text-xs text-orange-600 font-bold">
                    <span>難易度: {{ $recipe['metadata']['difficulty'] }}</span>
                    <span>全体時間: {{ $recipe['metadata']['total_time'] }}</span>
                </div>
            @endif

            {{-- 作り方 --}}
            @if(!empty($recipe['steps']))
                <div class="text-sm">
                    <p class="font-bold border-b border-gray-300 dark:border-gray-600 pb-1 mb-3 text-gray-900 dark:text-white">作り方</p>
                    <ol class="list-decimal list-inside space-y-3 text-gray-800 dark:text-gray-200">
                        @foreach($recipe['steps'] as $step)
                            <li>
                                {{ $step['description'] }}
                                @if(!empty($step['duration']))
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded ml-1 text-gray-600 dark:text-gray-300">
                                        ({{ $step['duration'] }})
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>