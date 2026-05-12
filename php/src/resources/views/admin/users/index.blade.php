<x-app-layout>
    <div class="max-w-md mx-auto pb-32 min-h-screen bg-slate-100 dark:bg-slate-900 transition-colors">
        <x-message type="status" />

        {{-- ヘッダー部分（買い物メモのデザインと統一） --}}
        <header class="px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-blue-600 rounded-lg text-white shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-950 dark:text-white tracking-tighter uppercase">ユーザー管理</h1>
                    </div>
                </div>
                {{-- 戻るボタン（設定タブへ） --}}
                <a href="{{ route('shopping.index') }}" class="p-3 rounded-2xl bg-slate-800/50 dark:bg-white/5 border border-white/10 shadow-lg transition-all active:scale-90">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            {{-- 検索フォーム --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative group">
                {{-- inputのパディングを pr-24 くらいに広げる --}}
                <input type="text" name="keyword" placeholder="名前・メールで検索..." value="{{ $keyword }}" 
                    class="w-full pl-11 pr-24 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl shadow-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 transition-all">
                
                {{-- 虫眼鏡アイコン --}}
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                {{-- ボタン類をまとめるコンテナを「right-1.5」にする --}}
            <div class="absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center">
                @if(!empty(request('keyword')))
                    {{-- 検索済みの時は「解除（クリア）」を大きく出す --}}
                    <a href="{{ route('admin.users.index') }}" 
                    class="px-4 py-1.5 bg-slate-200 dark:bg-slate-700 rounded-xl text-[11px] font-bold text-slate-600 dark:text-slate-300 active:scale-95 transition-all">
                        クリア
                    </a>
                @else
                    {{-- 未入力の時だけ「検索」ボタンを出す --}}
                    <button
                        type="submit"
                        class="px-4 py-1.5 bg-blue-600 text-white text-[11px] font-bold rounded-xl active:scale-95 transition-all shadow-md shadow-blue-500/20"
                    >
                        検索
                    </button>
                @endif
            </div>
            </form>
        </header>

        <div class="px-4 space-y-3">
            {{-- ページネーション --}}
            <div class="px-1">
                {{ $users->links() }}
            </div>

            <div class="mb-4">
                <a href="{{ route('admin.users.create') }}" 
                class="flex items-center justify-between p-3 rounded-2xl bg-white/50 dark:bg-white/5 border border-blue-500/30 dark:border-blue-500/20 shadow-sm active:scale-[0.98] transition-all">
                    <div class="flex items-center gap-3">
                        {{-- アイコンも一回り小さく --}}
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">New User</div>
                            <div class="text-[10px] text-slate-500 font-bold">新規ユーザー登録</div>
                        </div>
                    </div>
                    <div class="mr-1 text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            {{-- ユーザーリスト（スマホ向けカード型） --}}
            <div class="space-y-3">
                @foreach ($users as $user)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 shadow-sm border border-slate-200/60 dark:border-white/5 transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-black text-slate-400 text-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</h2>
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded-full {{ $user->is_admin ? 'bg-purple-500 text-white shadow-sm shadow-purple-500/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                            {{ $user->is_admin ? 'ADMIN' : 'USER' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-mono text-slate-300 dark:text-slate-600">#{{ $user->id }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-50 dark:border-white/5">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center justify-center gap-2 py-3 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-2xl active:scale-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                編集
                            </a>

                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('本当に削除しますか？')" class="contents">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center gap-2 py-3 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold rounded-2xl active:scale-95 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($users->isEmpty())
                <div class="text-center py-20">
                    <p class="text-slate-400 font-bold">ユーザーが見つかりません</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>