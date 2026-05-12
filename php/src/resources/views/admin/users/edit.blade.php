<x-app-layout>
    <div class="max-w-md mx-auto pb-32 min-h-screen bg-slate-100 dark:bg-slate-900 transition-colors">
        
        {{-- ヘッダー部分 --}}
        <header class="px-4 py-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-xl font-black text-white tracking-tighter uppercase drop-shadow-[0_0_8px_rgba(59,130,246,0.5)]">
                        ユーザー編集
                    </h1>
                </div>

                {{-- 戻るボタン（一覧へ） --}}
                <a href="{{ route('admin.users.index') }}" class="p-3 rounded-2xl bg-slate-800/50 dark:bg-white/5 border border-white/10 shadow-lg transition-all active:scale-90">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </header>

        <div class="px-4">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- 入力カード --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200/60 dark:border-white/5">
                    <div class="space-y-5">
                        {{-- 名前 --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 transition-all">
                            @error('name') <p class="text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- メール --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 transition-all">
                            @error('email') <p class="text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 管理者権限（スイッチ風デザイン） --}}
                        <div class="pt-2">
                            <label class="flex items-center justify-between px-1 cursor-pointer group">
                                <div class="relative inline-flex items-center">
                                    {{-- edit.blade.php 内 --}}
                                    <input type="hidden" name="is_admin" value="0">
                                    <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">&nbsp;管理者権限を付与する</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 更新ボタン --}}
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl shadow-lg shadow-blue-500/30 active:scale-[0.98] transition-all">
                        保存する
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>