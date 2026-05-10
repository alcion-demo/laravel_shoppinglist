@props(['for' => null, 'type' => 'status'])

@if ($for)
    {{-- 特定の項目のエラー --}}
    @error($for)
        <p class="text-[10px] text-red-500 dark:text-red-400 mt-1 ml-0.5 font-medium">※ {{ $message }}</p>
    @enderror
@else
    {{-- forがない場合は、全エラーまたは成功メッセージ --}}
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <p class="text-[10px] text-red-500 dark:text-red-400 font-medium px-1">※ {{ $error }}</p>
        @endforeach
    @elseif (session($type))
        <p class="text-[10px] text-green-600 dark:text-green-400 font-medium px-1">
            {{ session($type) }}
        </p>
    @endif
@endif