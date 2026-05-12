<section x-show="activeTab === 'settings'" class="space-y-6" x-cloak>
    <h1 class="text-2xl font-bold dark:text-white">設定</h1>

    <div class="space-y-3">
        {{-- プロフィール項目 --}}
        <a href="{{ route('profile.edit') }}"
        class="flex items-center justify-between p-5 rounded-2xl bg-white/50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 shadow-sm active:scale-[0.98] transition-all">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold dark:text-white">プロファイル</div>
                    <div class="text-[10px] text-slate-500">アカウント情報の管理</div>
                </div>
            </div>
            <span class="text-slate-400">›</span>
        </a>

        {{-- 管理者専用メニュー --}}
        @can('admin')
            <div class="pt-4 mt-4 border-t border-slate-200 dark:border-white/10">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 mb-2">管理者専用</h2>
                
                <a href="{{ route('admin.users.index') }}"
                class="flex items-center justify-between p-5 rounded-2xl bg-red-50/50 dark:bg-red-500/5 border border-red-100 dark:border-red-500/20 shadow-sm active:scale-[0.98] transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-red-400">ユーザー編集</div>
                            <div class="text-[10px] text-slate-500">ユーザー一覧・権限の管理</div>
                        </div>
                    </div>
                    <span class="text-red-300">›</span>
                </a>
            </div>
        @endcan

        {{-- ログアウト項目 --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-between p-5 rounded-2xl bg-white/50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 shadow-sm active:scale-[0.98] transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="font-bold text-red-500">ログアウト</div>
                        <div class="text-[10px] text-slate-500">セッションを終了します</div>
                    </div>
                </div>
                <span class="text-red-400/50">↪</span>
            </button>
        </form>
    </div>
</section>