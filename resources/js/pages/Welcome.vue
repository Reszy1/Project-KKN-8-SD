<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Petualangan Sehat" />

    <div class="min-h-screen bg-gradient-to-b from-sky-200 to-white flex flex-col items-center justify-center p-6 font-['Comic_Sans_MS',_'Comic_Sans',_cursive] relative overflow-hidden text-[#1e293b]">
        
        <div class="absolute top-10 -left-10 text-9xl opacity-10 animate-pulse select-none">☁️</div>
        <div class="absolute bottom-20 -right-10 text-9xl opacity-10 animate-bounce select-none">🫧</div>
        <div class="absolute top-1/4 right-10 text-6xl opacity-20 select-none animate-spin-slow">✨</div>

        <div class="relative z-10 w-full max-w-lg text-center flex flex-col items-center">
            
            <div class="mb-4 relative group cursor-pointer hover:scale-110 transition-transform duration-500">
                <div class="text-[100px] md:text-[140px] drop-shadow-2xl select-none">🧒</div>
                <div class="absolute -top-2 -right-6 text-5xl md:text-6xl animate-bounce">🪥</div>
                <div class="absolute bottom-0 -left-6 text-5xl md:text-6xl animate-bounce" style="animation-delay: 0.5s;">🧼</div>
            </div>

            <h1 class="text-4xl md:text-6xl font-black text-sky-600 mb-4 drop-shadow-sm leading-tight">
                PETUALANGAN SEHAT
            </h1>
            
            <p class="text-lg md:text-xl text-sky-800 font-bold mb-10 leading-relaxed max-w-xs md:max-w-md mx-auto">
                Ayo belajar sikat gigi & cuci tangan biar badan jadi kuat! 💪
            </p>

            <Link
                href="/edukasi" 
                class="group relative w-full bg-orange-500 hover:bg-orange-600 text-white text-2xl md:text-3xl font-black py-6 rounded-[2rem] shadow-[0_8px_0_0_#9a3412] active:shadow-none active:translate-y-2 transition-all duration-200 overflow-hidden"
            >
                <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></div>
                
                <span class="relative flex items-center justify-center gap-3">
                    MULAI MAIN! 🚀
                </span>
            </Link>
            
            <p class="mt-6 text-sky-600/70 font-bold text-sm animate-pulse">
                Klik tombol oranye di atas ya! 👆
            </p>
        </div>

        <div class="absolute bottom-4 text-sky-400/60 font-bold text-[10px] md:text-xs uppercase tracking-widest">
            KKN 2026 KELOMPOK 8 UMPKU • Generasi Sehat Indonesia
        </div>

    </div>
</template>

<style scoped>
/* Tambahan animasi putar pelan untuk bintang */
.animate-spin-slow {
    animation: spin 8s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>