<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Data Edukasi
const slides = [
    {
        id: 1,
        title: "PHBS: Anak Sehat, Anak Shalih 🌟",
        icon: "✨",
        color: "bg-purple-500",
        bg: "bg-purple-50",
        text: "text-purple-600",
        btnColor: "bg-purple-500 hover:bg-purple-600 shadow-[0_4px_0_0_#7e22ce]",
        content: [
            "Apa itu PHBS?",
            "PHBS (Perilaku Hidup Bersih dan Sehat) adalah kebiasaan baik sehari-hari agar tubuh kita sehat, kuat, dan tidak mudah sakit.",
            "Contoh PHBS:",
            "👉 Cuci tangan pakai sabun",
            "👉 Gosok gigi",
            "👉 Makan makanan sehat",
            "👉 Buang sampah pada tempatnya",
            "Rasulullah ﷺ bersabda:",
            "\"Kebersihan adalah sebagian dari iman\" (HR. Muslim).",
            "\"Kalau kita hidup bersih, berarti kita anak yang beriman dan disayang Allah\" ❤️"
        ],
        source: "Sumber: Hadits HR. Muslim"
    },
    {
        id: 2,
        title: "Jurus Ampuh Cuci Tangan 🧼",
        icon: "👐",
        color: "bg-green-500",
        bg: "bg-green-50",
        text: "text-green-600",
        btnColor: "bg-green-500 hover:bg-green-600 shadow-[0_4px_0_0_#15803d]",
        content: [
            "Apa itu mencuci tangan?",
            "Membersihkan tangan dan jari-jari dengan air mengalir dan sabun.",
            "Kenapa harus cuci tangan?",
            "Karena di tangan banyak kuman tak terlihat. Jika tidak, bisa menyebabkan: Sakit perut, Diare (mencret), & Cacingan.",
            "Kapan Harus Cuci Tangan? 🕒",
            "✅ Sebelum & setelah makan.",
            "✅ Sebelum menyajikan makanan & pegang bahan mentah.",
            "✅ Sesudah BAB & BAK (Buang Air).",
            "✅ Sesudah bermain & menyentuh hewan.",
            "✅ Sesudah batuk/bersin & buang sampah.",
            "6 Langkah Cuci Tangan (WHO):",
            "1. Tuang sabun, gosok antar telapak tangan.",
            "2. Gosok punggung tangan.",
            "3. Bersihkan sela-sela jari.",
            "4. Usap ujung jari (gerakan mengunci).",
            "5. Ibu jari digenggam lalu gosok memutar.",
            "6. Gosok ujung jari di telapak tangan."
        ],
        source: "Sumber: Panduan WHO"
    },
    {
        id: 3,
        title: "Gigi Bersih, Senyum Ceria 😁",
        icon: "🦷",
        color: "bg-blue-500",
        bg: "bg-blue-50",
        text: "text-blue-600",
        btnColor: "bg-blue-500 hover:bg-blue-600 shadow-[0_4px_0_0_#1d4ed8]",
        content: [
            "Kenapa harus gosok gigi? Agar gigi bersih, tidak bau mulut, dan tidak sakit gigi.",
            "Rasulullah ﷺ bersabda:",
            "\"Gosok gigi membuat Allah senang dan mulut kita menjadi bersih.\"",
            "Waktu Tepat:",
            "☀️ Pagi setelah sarapan",
            "🌙 Malam sebelum tidur",
            "Cara Gosok Gigi:",
            "👉 Gunakan pasta sebesar biji kacang.",
            "👉 Gosok bagian depan (naik-turun).",
            "👉 Gosok bagian dalam & luar (memutar).",
            "👉 Gosok permukaan kunyah (maju-mundur).",
            "👉 Sikat lidah dengan lembut & kumur air bersih."
        ],
        source: "Sumber: HR. Bukhari Muslim & Kemenkes"
    },
    {
        id: 4,
        title: "Tubuhku & Masa Remaja ❤️",
        icon: "🛡️",
        color: "bg-pink-500",
        bg: "bg-pink-50",
        text: "text-pink-600",
        btnColor: "bg-pink-500 hover:bg-pink-600 shadow-[0_4px_0_0_#be185d]",
        content: [
            "Apa itu Pubertas? 🌱",
            "Masa kamu tumbuh besar dari anak-anak menjadi remaja. Suara berubah, tubuh makin tinggi, dan mulai ada jerawat. Itu Normal!",
            "👧 Untuk Perempuan: Haid (Menstruasi)",
            "Keluar darah kotor dari jalan lahir. Jangan takut, itu tanda tubuhmu sehat! Pakai 'Pembalut' dan ganti tiap 4 jam ya.",
            "👦 Untuk Laki-laki: Mimpi Basah",
            "Bangun tidur celana basah & lengket. Itu bukan ngompol, tapi tanda kamu sudah bujang (besar). Mandi wajib ya!",
            "Jaga Area Pribadi (Privasi) ⛔",
            "👉 Bagian tubuh yang tertutup baju renang TIDAK BOLEH dilihat/disentuh orang lain.",
            "👉 Ganti celana dalam pagi & sore.",
            "👉 Cebok dari DEPAN ke BELAKANG (agar kuman tidak masuk)."
        ],
        source: "Sumber: Kemdikbud & Kemenkes RI"
    },
    {
        id: 5,
        title: "Gizi: Rahasia Camilan Pahlawan! 🦸‍♂️",
        icon: "🍎",
        color: "bg-orange-500",
        bg: "bg-orange-50",
        text: "text-orange-600",
        btnColor: "bg-orange-500 hover:bg-orange-600 shadow-[0_4px_0_0_#ea580c]",
        content: [
            "Tahukah kamu? Makanan yang kita pilih bisa jadi 'Pahlawan' atau 'Monster' bagi tubuh kita!",
            "1. Makanan Pembersih (Self-Cleansing) 🧹",
            "Buah renyah (Apel 🍎, Bengkuang, Pepaya) & Sayur (Wortel 🥕) mengandung banyak SERAT dan AIR.",
            "👉 Seratnya menyapu sisa makanan di sela gigi.",
            "👉 Airnya membilas kuman jahat secara alami.",
            "2. Makanan Semen Kuat (Zat Pembangun) 🏗️",
            "Tubuh butuh Kalsium & Fosfor agar tulang dan gigi sekuat tembok beton!",
            "👉 Sumber Terbaik: Susu 🥛, Keju 🧀, Ikan Teri 🐟, dan Telur 🥚.",
            "3. Waspada Camilan Lengket (Monster Gula) 👾",
            "Permen, Cokelat, & Biskuit mengandung Gula Lengket yang disukai kuman.",
            "👉 Tips Gizi: Kalau mau makan manis, makanlah bersamaan jam makan besar (nasi), JANGAN dijadikan camilan sepanjang hari.",
            "4. Minuman Pintar 💧",
            "❌ Kurangi Soda & Susu Kental Manis (Gula terlalu tinggi!).",
            "✅ Pilih Air Putih atau Susu Segar. Air putih menetralkan asam jahat di mulut."
        ],
        source: "Sumber: Kemenkes RI, IDAI, & WHO"
    }
];

const currentSlide = ref(0);

// Logic Navigasi
const nextSlide = () => {
    if (currentSlide.value < slides.length - 1) {
        currentSlide.value++;
    }
};

const prevSlide = () => {
    if (currentSlide.value > 0) {
        currentSlide.value--;
    }
};

// Cek apakah baris adalah Header/Judul (untuk styling bold)
const isHeaderLine = (line) => {
    return line.includes('?') || line.includes(':') || line.length < 25; // Panjang disesuaikan
};
</script>

<template>
    <Head title="Belajar Dulu Yuk!" />

    <div class="font-comic h-[100dvh] w-full flex flex-col items-center justify-between transition-colors duration-700 overflow-hidden relative"
        :class="slides[currentSlide].bg">

        <div class="w-full px-4 pt-4 md:pt-6 mb-2 flex-shrink-0 z-20">
            <div class="flex gap-2 max-w-md mx-auto">
                <div v-for="(slide, index) in slides" :key="index" 
                    class="h-2 md:h-3 rounded-full flex-1 transition-all duration-500 shadow-sm"
                    :class="index <= currentSlide ? slides[index].color : 'bg-gray-200/50'">
                </div>
            </div>
        </div>

        <div class="flex-1 w-full max-w-md md:max-w-2xl px-4 flex flex-col justify-center items-center relative z-10 overflow-hidden py-2">
            
            <transition name="scale-fade" mode="out-in">
                <div :key="currentSlide" class="w-full bg-white rounded-[2rem] p-5 md:p-8 shadow-xl border-4 border-white flex flex-col items-center h-[80vh] md:h-auto max-h-[600px]">
                    
                    <div class="text-6xl md:text-8xl mb-2 animate-bounce cursor-pointer select-none">
                        {{ slides[currentSlide].icon }}
                    </div>

                    <h1 class="text-xl md:text-3xl font-black mb-4 leading-tight text-center px-2" 
                        :class="slides[currentSlide].text">
                        {{ slides[currentSlide].title }}
                    </h1>

                    <div class="bg-gray-50 rounded-xl p-4 w-full text-left border border-gray-100 shadow-inner flex-1 overflow-y-auto custom-scrollbar mb-4">
                        <ul class="space-y-1"> 
                            <li v-for="(line, idx) in slides[currentSlide].content" :key="idx" 
                                class="text-slate-600 font-medium text-sm md:text-base leading-relaxed flex items-start gap-2"
                                :class="{ 'mt-3 border-t border-dashed border-gray-200 pt-2': isHeaderLine(line) && idx > 0 }">
                                
                                <span v-if="!line.match(/^\d\./) && !line.startsWith('👉') && !line.startsWith('✅') && !line.startsWith('❌') && !line.includes('?') && !line.includes(':') && idx > 0" 
                                      class="text-orange-400 mt-0.5 flex-shrink-0">👉</span>
                                
                                <span :class="{
                                    'font-black text-slate-800 text-lg': line.includes('Rasulullah') || line.includes('Wajib') || line.includes('Pubertas'),
                                    'font-bold text-pink-500 text-lg': line.includes('Perempuan'),
                                    'font-bold text-blue-500 text-lg': line.includes('Laki-laki'),
                                    'font-bold text-red-500 text-lg': line.includes('Privasi') || line.startsWith('❌'),
                                    'font-bold text-slate-700 underline decoration-wavy decoration-orange-300': isHeaderLine(line) && !line.includes('Perempuan') && !line.includes('Laki-laki') && idx > 0,
                                }">
                                    {{ line }}
                                </span>
                            </li>
                        </ul>
                        
                        <div v-if="currentSlide === 1" class="mt-4 p-3 bg-blue-100/50 border border-blue-100 rounded-xl text-center text-blue-700 font-bold italic text-xs md:text-sm">
                            “Anak shalih itu suka hidup bersih. Rajin cuci tangan, rajin gosok gigi!” 🚿
                        </div>
                    </div>

                    <p class="text-[10px] md:text-xs text-gray-400 italic mb-4 w-full text-center border-t border-dashed border-gray-200 pt-2 flex-shrink-0">
                        {{ slides[currentSlide].source }}
                    </p>

                    <div class="flex gap-3 w-full flex-shrink-0">
                        <button v-if="currentSlide > 0" 
                            @click="prevSlide"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-2xl w-14 flex items-center justify-center shadow-sm transition-transform active:scale-95">
                            ↩️
                        </button>

                        <button v-if="currentSlide < slides.length - 1" 
                            @click="nextSlide"
                            class="flex-1 text-white text-lg md:text-xl font-black py-3 md:py-4 rounded-2xl shadow-lg active:shadow-none active:translate-y-1 transition-all flex items-center justify-center gap-2"
                            :class="slides[currentSlide].btnColor">
                            LANJUT 👉
                        </button>

                        <Link v-else 
                            href="/siswa/login"
                            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-lg md:text-xl font-black py-3 md:py-4 rounded-2xl shadow-[0_4px_0_0_#c2410c] active:shadow-none active:translate-y-1 transition-all flex items-center justify-center gap-2">
                            <span>MASUK YUK</span> 🚀
                        </Link>
                    </div>

                </div>
            </transition>

            <p class="mt-3 font-bold text-gray-400/60 text-xs md:text-sm">
                Halaman {{ currentSlide + 1 }} dari {{ slides.length }}
            </p>

        </div>
    </div>
</template>

<style scoped>
/* 1. GUNAKAN IMPORT YANG SAMA DENGAN WELCOME.VUE */
@import url('https://fonts.cdnfonts.com/css/comic-sans-ms');

/* 2. PAKSA FONT AGAR TIDAK JADI LATIN/SERIF DI HP */
.font-comic {
    font-family: 'Comic Sans MS', 'Comic Sans', cursive, sans-serif !important;
}

/* Transisi Kartu (Zoom In/Out) */
.scale-fade-enter-active,
.scale-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.scale-fade-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}

.scale-fade-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

/* Scrollbar Imut */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>