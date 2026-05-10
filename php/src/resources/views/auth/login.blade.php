<x-guest-layout>
    <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
         x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
         :class="{ 'dark': darkMode }" 
         class="min-h-screen">
        
        {{-- w-full と h-screen で画面全体を支配する --}}
        <div class="min-h-screen w-full bg-slate-50 dark:bg-slate-950 transition-colors duration-500 flex flex-col justify-center items-center p-4">
            
            {{-- ダークモード切り替え：absoluteで右上に固定 --}}
            <div class="absolute top-8 right-8 z-50">
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

            <div class="mb-12 text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-[28px] flex items-center justify-center mx-auto shadow-2xl shadow-blue-500/40">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <h1 class="mt-5 text-2xl font-black dark:text-white text-slate-900 tracking-tight">買い物メモ</h1>
            </div>

            <div class="w-full max-w-sm p-8 rounded-[48px] bg-white dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50 shadow-2xl backdrop-blur-xl transition-all duration-500">
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-2 px-4 uppercase tracking-[0.2em]">Email</label>
                        <input type="email" name="email" :value="old('email')" required autofocus class="w-full px-6 py-4 rounded-3xl bg-gray-50 dark:bg-gray-900/80  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 mb-2 px-4 uppercase tracking-[0.2em]">Password</label>
                        <input type="password" name="password" required class="w-full px-6 py-4 rounded-3xl bg-gray-50 dark:bg-gray-900/80  dark:text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <button type="submit" class="w-full py-5 rounded-3xl bg-blue-600 text-white font-black text-lg shadow-xl shadow-blue-600/30 active:scale-[0.97] transition-all">
                        ログイン
                    </button>
                </form>
                <div class="mt-8 text-center">
                    <a href="{{ route('register') }}" class="text-sm font-bold text-blue-500 hover:text-blue-400 transition-colors">新規アカウント登録</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>