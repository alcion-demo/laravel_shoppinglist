<x-app-layout>
    <div class="min-h-screen p-4 pb-32 space-y-6 max-w-md mx-auto">
        
        <div class="flex items-center gap-4 py-2 text-slate-900 dark:text-white">
            <h1 class="text-2xl font-bold tracking-tight">設定</h1>
        </div>

        <div class="flex items-center justify-between py-2 text-slate-900 dark:text-white">
            <h1 class="text-2xl font-extrabold tracking-tight">プロファイル設定</h1>
            {{-- 右上にも閉じるボタンを置いておく --}}
                <a href="{{ route('shopping.index') }}" 
                class="p-3 rounded-2xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 shadow-sm active:scale-90 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </a>
        </div>

        {{-- フォーム群 --}}
        <div class="space-y-4">
            <div class="p-6 rounded-[32px] bg-white dark:bg-gray-800/40 border border-slate-200 dark:border-gray-700 shadow-sm backdrop-blur-md text-slate-900 dark:text-white">
                <h2 class="text-[10px] font-black text-slate-400 mb-6 uppercase tracking-[0.2em] px-2 text-center">User Info</h2>
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-6 rounded-[32px] bg-white dark:bg-gray-800/40 border border-slate-200 dark:border-gray-700 shadow-sm backdrop-blur-md text-slate-900 dark:text-white">
                <h2 class="text-[10px] font-black text-slate-400 mb-6 uppercase tracking-[0.2em] px-2 text-center">Password</h2>
                @include('profile.partials.update-password-form')
            </div>

            <div class="p-6 rounded-[32px] bg-red-500/5 border border-red-500/20 text-slate-900 dark:text-white">
                <h2 class="text-[10px] font-black text-red-400 mb-6 uppercase tracking-[0.2em] px-2 text-center">Delete Account</h2>
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>