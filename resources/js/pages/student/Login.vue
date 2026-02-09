<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    password: '',
});

const submit = () => {
    // Menggunakan URL manual untuk menghindari error Ziggy/Route
    form.post('/siswa/login');
};
</script>

<template>
    <Head title="Masuk Yuk!" />

    <div class="h-[100dvh] bg-gradient-to-b from-sky-200 to-sky-50 flex items-center justify-center p-4 font-['Comic_Sans_MS',_'Comic_Sans',_cursive] overflow-hidden relative">
        
        <div class="absolute top-10 -left-10 text-8xl opacity-10 select-none">☁️</div>
        <div class="absolute bottom-10 -right-10 text-8xl opacity-10 select-none">☁️</div>

        <div class="bg-white w-full max-w-sm md:max-w-md rounded-[2.5rem] shadow-2xl border-4 border-white relative overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="absolute top-0 left-0 w-full h-4 bg-orange-400"></div>
            
            <Link href="/edukasi" class="absolute top-6 left-6 text-gray-400 hover:text-orange-500 transition-colors">
                <span class="text-xl">⬅️</span> Kembali
            </Link>

            <div class="p-6 md:p-10 overflow-y-auto custom-scrollbar">
                
                <div class="text-center mb-6 md:mb-8 mt-4">
                    <div class="text-6xl md:text-7xl mb-2 animate-bounce select-none">👋</div>
                    <h1 class="text-2xl md:text-3xl font-black text-sky-600 leading-tight">Selamat Datang!</h1>
                    <p class="text-gray-400 font-bold text-xs md:text-sm mt-1">Masukkan nama & sandi dari Bu Guru ya</p>
                </div>
                
                <form @submit.prevent="submit" class="space-y-4 md:space-y-6">
                    
                    <div class="group">
                        <label class="block text-sm md:text-base font-black text-gray-600 ml-3 mb-1 group-focus-within:text-sky-500 transition-colors">
                            NAMA KAMU
                        </label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            placeholder="Contoh: Budi" 
                            class="w-full border-[3px] border-sky-100 bg-sky-50/50 text-gray-700 rounded-2xl px-4 py-3 md:py-4 text-base md:text-lg focus:border-sky-400 focus:bg-white outline-none transition-all placeholder:text-gray-300"
                            required
                        >
                        <p v-if="form.errors.name" class="text-red-500 font-bold text-xs ml-3 mt-1 animate-pulse">{{ form.errors.name }}</p>
                    </div>

                    <div class="group">
                        <label class="block text-sm md:text-base font-black text-gray-600 ml-3 mb-1 group-focus-within:text-yellow-500 transition-colors">
                            PASSWORD RAHASIA 🔒
                        </label>
                        <input 
                            v-model="form.password" 
                            type="password" 
                            placeholder="Ketik sandimu..." 
                            class="w-full border-[3px] border-yellow-200 bg-yellow-50/50 text-gray-700 rounded-2xl px-4 py-3 md:py-4 text-base md:text-lg focus:border-yellow-400 focus:bg-white outline-none transition-all placeholder:text-gray-300 font-sans"
                            required
                        >
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-xl md:text-2xl font-black py-4 md:py-5 rounded-[2rem] shadow-[0_6px_0_0_#c2410c] active:shadow-none active:translate-y-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 group">
                            
                            <span v-if="form.processing" class="animate-spin">⏳</span>
                            <span>{{ form.processing ? 'Sabar ya...' : 'MASUK SEKARANG!' }}</span>
                            <span v-if="!form.processing" class="group-hover:translate-x-1 transition-transform">🚀</span>

                        </button>
                    </div>
                </form>
            </div>

        </div>
        
        <div class="absolute bottom-3 text-sky-400/50 font-bold text-[10px] uppercase tracking-widest">
            KKN Kelompok 8 • UMPKU
        </div>

    </div>
</template>

<style scoped>
/* Font Comic Sans */
@font-face {
    font-family: 'Comic Sans MS';
    src: local('Comic Sans MS'), local('Comic Sans'), url('https://fonts.cdnfonts.com/s/13677/ComicSansMS3.woff') format('woff');
}

/* Scrollbar halus jika konten kepanjangan di HP landscape */
.custom-scrollbar::-webkit-scrollbar { width: 0px; }
</style>