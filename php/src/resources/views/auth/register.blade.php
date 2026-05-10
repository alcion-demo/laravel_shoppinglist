<x-guest-layout>
    <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
         x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
         :class="{ 'dark': darkMode }" 
         class="min-h-screen">
        
        <div class="min-h-screen w-full bg-slate-50 dark:bg-slate-950 transition-colors duration-500 flex flex-col justify-center items-center p-6 relative">
            
            {{-- ダークモード切り替え --}}
            <div class="absolute top-6 right-6">
                <button @click="darkMode = !darkMode" 
                        class="p-3 rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 text-amber-500 dark:text-yellow-300 active:scale-90 transition-all">
                    <template x-if="!darkMode">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" /></svg>
                    </template>
                    <template x-if="darkMode">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" /></svg>
                    </template>
                </button>
            </div>

            {{-- タイトルエリア --}}
            <div class="mb-10 text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-[28px] flex items-center justify-center mx-auto shadow-2xl shadow-blue-500/40">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                </div>
                <h1 class="mt-4 text-2xl font-black dark:text-white text-slate-900 tracking-tight">新規アカウント作成</h1>
            </div>

            {{-- 登録フォーム：項目が増えるので max-w-sm (約380px) でバランス調整 --}}
            <div class="w-full max-w-sm p-8 rounded-[40px] bg-white dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 shadow-2xl backdrop-blur-xl transition-all overflow-y-auto max-h-[85vh]">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    {{-- 名前 --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-1 px-3 uppercase tracking-widest">Name</label>
                        <input type="text" name="name" :value="old('name')" required autofocus class="w-full px-5 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 px-3" />
                    </div>

                    {{-- メールアドレス --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-1 px-3 uppercase tracking-widest">Email</label>
                        <input type="email" name="email" :value="old('email')" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 px-3" />
                    </div>

                    {{-- パスワード --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-1 px-3 uppercase tracking-widest">Password</label>
                        <input type="password" name="password" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 px-3" />
                    </div>

                    {{-- パスワード確認 --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-1 px-3 uppercase tracking-widest">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 dark:bg-gray-900/50  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <button type="submit" class="w-full py-4 mt-2 rounded-2xl bg-blue-600 text-white font-black text-lg shadow-xl shadow-blue-600/30 active:scale-95 transition-all">
                        登録する
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-blue-500 hover:text-blue-400">すでに登録済みの方はこちら</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>