<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Petualangan Sehat" />

    <div class="min-h-[100dvh] bg-gradient-to-b from-sky-200 to-white flex flex-col items-center justify-center p-6 font-['Comic_Sans_MS',_'Comic_Sans',_cursive] relative overflow-hidden text-[#1e293b]">
        
        <div class="absolute top-5 -left-4 md:top-10 md:-left-10 text-7xl md:text-9xl opacity-10 animate-pulse select-none pointer-events-none">☁️</div>
        <div class="absolute bottom-16 -right-4 md:bottom-20 md:-right-10 text-7xl md:text-9xl opacity-10 animate-bounce select-none pointer-events-none">🫧</div>
        <div class="absolute top-1/4 right-5 md:right-10 text-4xl md:text-6xl opacity-20 select-none animate-spin-slow pointer-events-none">✨</div>

        <div class="relative z-10 w-full max-w-md text-center flex flex-col items-center">
            
            <div class="mb-6 relative group cursor-pointer hover:scale-105 transition-transform duration-500">
                <div class="text-8xl md:text-[140px] drop-shadow-2xl select-none leading-none">🧒</div>
                
                <div class="absolute -top-2 -right-2 md:-right-6 text-4xl md:text-6xl animate-bounce">🪥</div>
                <div class="absolute bottom-0 -left-2 md:-left-6 text-4xl md:text-6xl animate-bounce" style="animation-delay: 0.5s;">🧼</div>
            </div>

            <h1 class="text-3xl md:text-6xl font-black text-sky-600 mb-3 drop-shadow-sm leading-tight tracking-tight">
                PETUALANGAN SEHAT
            </h1>
            
            <p class="text-base md:text-xl text-sky-800 font-bold mb-8 leading-relaxed max-w-[280px] md:max-w-md mx-auto">
                Ayo belajar sikat gigi & cuci tangan biar badan jadi kuat! 💪
            </p>

            <Link
                href="/edukasi" 
                class="group relative w-full max-w-xs bg-orange-500 hover:bg-orange-600 text-white text-xl md:text-3xl font-black py-4 md:py-6 rounded-[2rem] shadow-[0_6px_0_0_#9a3412] active:shadow-none active:translate-y-2 transition-all duration-200 overflow-hidden"
            >
                <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></div>
                
                <span class="relative flex items-center justify-center gap-2 md:gap-3">
                    MULAI MAIN! 🚀
                </span>
            </Link>
            
            <p class="mt-4 text-sky-600/70 font-bold text-xs md:text-sm animate-pulse">
                Klik tombol oranye di atas ya! 👆
            </p>
        </div>

        <div class="absolute bottom-4 text-sky-400/60 font-bold text-[10px] md:text-xs uppercase tracking-widest text-center px-4">
            KKN 2026 KELOMPOK 8 UMPKU • Generasi Sehat
        </div>

    </div>
</template>

<style scoped>
/* Animasi Bintang */
.animate-spin-slow {
    animation: spin 8s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Pastikan font Comic Sans ter-load dengan baik */
@font-face {
    font-family: 'Comic Sans MS';
    src: local('Comic Sans MS'), local('Comic Sans'), url('https://fonts.cdnfonts.com/s/13677/ComicSansMS3.woff') format('woff');
}
</style>