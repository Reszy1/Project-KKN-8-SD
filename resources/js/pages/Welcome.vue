<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// State untuk Modal Video
const showVideoModal = ref(false);
const activeVideo = ref('');

// Daftar Video (Menggunakan Link YouTube Embed)
const videoList = [
    { 
        title: 'Cara Sikat Gigi', 
        url: 'https://ik.imagekit.io/w1beqfjn8/Animation%20Gosok%20Gigi-KKN.mp4', // Animasi Sikat Gigi
        icon: '🪥',
        color: 'bg-blue-50 text-blue-700 border-blue-100'
    },
    { 
        title: 'Cuci Tangan Pakai Sabun', 
        url: 'https://ik.imagekit.io/w1beqfjn8/Animasi%20AI%20Cuci%20Tangan.mp4', // 6 Langkah Cuci Tangan
        icon: '🧼',
        color: 'bg-green-50 text-green-700 border-green-100'
    },
    { 
        title: 'Jaga Tubuhmu', 
        url: 'https://ik.imagekit.io/w1beqfjn8/Animasi%20AI%20Kespro.mp4', // Sentuhan Boleh/Tidak
        icon: '🛡️',
        color: 'bg-pink-50 text-pink-700 border-pink-100'
    }
];
</script>

<template>
    <Head title="Petualangan Sehat" />

    <div class="font-comic min-h-[100dvh] bg-gradient-to-b from-sky-200 to-white flex flex-col items-center justify-center p-6 relative overflow-hidden text-[#1e293b]">
        
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
                Ayo belajar kesehatan sikat gigi, cuci tangan, dan reproduksi biar badan jadi kuat! 💪
            </p>

            <div class="flex flex-col gap-4 w-full max-w-xs">
                <Link
                    href="/edukasi" 
                    class="group relative w-full bg-orange-500 hover:bg-orange-600 text-white text-xl md:text-2xl font-black py-4 rounded-[2rem] shadow-[0_6px_0_0_#9a3412] active:shadow-none active:translate-y-2 transition-all duration-200 overflow-hidden flex items-center justify-center gap-2"
                >
                    <span>MULAI MAIN! 🚀</span>
                </Link>

                <button 
                    @click="showVideoModal = true"
                    class="group relative w-full bg-sky-500 hover:bg-sky-600 text-white text-xl md:text-2xl font-black py-4 rounded-[2rem] shadow-[0_6px_0_0_#0369a1] active:shadow-none active:translate-y-2 transition-all duration-200 overflow-hidden flex items-center justify-center gap-2"
                >
                    <span>NONTON VIDEO 🎬</span>
                </button>
            </div>
            
            <p class="mt-6 text-sky-600/70 font-bold text-xs md:text-sm animate-pulse">
                Pilih salah satu tombol di atas ya! 👆
            </p>
        </div>

        <div class="absolute bottom-4 text-sky-400/60 font-bold text-[10px] md:text-xs uppercase tracking-widest text-center px-4">
            KKN 2026 KELOMPOK 8 UMPKU • Generasi Sehat
        </div>

        <div v-if="showVideoModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showVideoModal = false"></div>
            
            <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-6 shadow-2xl relative animate-in fade-in zoom-in duration-300 border-4 border-sky-100">
                <button @click="showVideoModal = false; activeVideo = ''" class="absolute -top-12 right-0 md:-right-12 text-white text-4xl font-black hover:scale-110 transition-transform">✕</button>
                
                <div v-if="!activeVideo">
                    <h2 class="text-2xl font-black text-center mb-6 text-sky-600 uppercase tracking-wide">Pilih Video 🎥</h2>
                    <div class="space-y-3">
                        <button v-for="(vid, index) in videoList" :key="index" 
                            @click="activeVideo = vid.url" 
                            class="w-full p-4 rounded-2xl flex items-center gap-4 transition-all hover:scale-105 border-b-4 border-transparent hover:border-black/10 shadow-sm active:scale-95"
                            :class="vid.color">
                            <span class="text-4xl filter drop-shadow-sm">{{ vid.icon }}</span>
                            <span class="font-black text-lg">{{ vid.title }}</span>
                        </button>
                    </div>
                </div>

                <div v-else>
                    <div class="aspect-video w-full overflow-hidden rounded-2xl border-4 border-sky-100 bg-black shadow-inner mb-4">
                        <iframe class="w-full h-full" :src="activeVideo + '?autoplay=1'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <button @click="activeVideo = ''" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-3 rounded-xl transition-colors">
                        ⬅️ PILIH VIDEO LAIN
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* 1. IMPORT FONT DARI INTERNET */
@import url('https://fonts.cdnfonts.com/css/comic-sans-ms');

/* 2. PAKSA FONT AGAR KONSISTEN DI HP */
.font-comic {
    font-family: 'Comic Sans MS', 'Comic Sans', cursive, sans-serif !important;
}

/* Animasi Bintang */
.animate-spin-slow {
    animation: spin 8s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>