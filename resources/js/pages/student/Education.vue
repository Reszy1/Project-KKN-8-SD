<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Data Edukasi Terbaru (PHBS + Materi Lama Reproduksi)
const slides = [
    {
        id: 1,
        title: "PHBS: Anak Sehat, Anak Shalih 🌟",
        icon: "✨",
        color: "bg-purple-500",
        bg: "bg-purple-50",
        text: "text-purple-600",
        content: [
            "Apa itu PHBS? Kebiasaan baik sehari-hari agar tubuh sehat, kuat, dan tidak mudah sakit.",
            "Contoh PHBS:",
            "👉 Cuci tangan pakai sabun",
            "👉 Gosok gigi",
            "👉 Makan makanan sehat",
            "👉 Buang sampah pada tempatnya",
            "Rasulullah ﷺ bersabda:",
            "\"Kebersihan adalah sebagian dari iman\" (HR. Muslim).",
            "Kalau kita hidup bersih, berarti kita anak yang beriman dan disayang Allah ❤️"
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
        content: [
            "Kenapa harus cuci tangan? Agar terhindar dari Sakit Perut, Diare, dan Cacingan!",
            "Kapan Wajib Cuci Tangan?",
            "✅ Sebelum & sesudah makan.",
            "✅ Sesudah buang air besar/kecil.",
            "✅ Sesudah bermain & pegang hewan.",
            "✅ Sesudah batuk/bersin & buang sampah.",
            "6 Langkah Cuci Tangan (WHO):",
            "1. Gosok antar telapak tangan.",
            "2. Gosok punggung tangan.",
            "3. Bersihkan sela-sela jari.",
            "4. Kunci jari-jari (gerakan mengunci).",
            "5. Putar ibu jari dalam genggaman.",
            "6. Putar ujung jari di telapak tangan."
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
        title: "Tubuhku, Privasiku ❤️",
        icon: "🛡️",
        color: "bg-pink-500",
        bg: "bg-pink-50",
        text: "text-pink-600",
        content: [
            "Bagian tubuh yang tertutup baju renang adalah 'Area Pribadi'. Tidak boleh ada yang melihat/menyentuh sembarangan.",
            "Tips Kebersihan:",
            "1. Ganti celana dalam minimal 2x sehari (Pagi & Sore) atau saat lembab.",
            "2. Cara Cebok: Basuh dari DEPAN ke BELAKANG (bukan sebaliknya) agar kuman dari kotoran tidak masuk ke area depan.",
            "3. Keringkan dengan handuk/tisu agar tidak jamuran."
        ],
        source: "Sumber: UNESCO Technical Guidance & Kemenkes RI"
    }
];

const currentSlide = ref(0);

const nextSlide = () => {
    if (currentSlide.value < slides.length - 1) {
        currentSlide.value++;
    }
};
</script>

<template>
    <Head title="Belajar Dulu Yuk!" />

    <div class="min-h-screen flex flex-col items-center justify-center p-4 md:p-6 font-['Comic_Sans_MS'] transition-colors duration-700"
        :class="slides[currentSlide].bg">

        <div class="fixed top-6 flex gap-2 w-full max-w-md px-4 z-20">
            <div v-for="(slide, index) in slides" :key="index" 
                class="h-3 rounded-full flex-1 transition-all duration-500 shadow-sm"
                :class="index <= currentSlide ? slides[index].color : 'bg-gray-200'">
            </div>
        </div>

        <div class="w-full max-w-3xl text-center relative z-10">
            
            <transition name="slide-fade" mode="out-in">
                <div :key="currentSlide" class="bg-white rounded-[2.5rem] p-6 md:p-10 shadow-2xl border-[6px] border-white flex flex-col items-center">
                    
                    <div class="text-[80px] md:text-[100px] mb-4 animate-bounce cursor-pointer hover:scale-110 transition-transform select-none">
                        {{ slides[currentSlide].icon }}
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black mb-6 leading-tight" 
                        :class="slides[currentSlide].text">
                        {{ slides[currentSlide].title }}
                    </h1>

                    <div class="bg-gray-50 rounded-2xl p-5 w-full text-left mb-8 border border-gray-100 shadow-inner max-h-[45vh] overflow-y-auto custom-scrollbar">
                        <ul class="space-y-3">
                            <li v-for="(line, idx) in slides[currentSlide].content" :key="idx" 
                                class="text-gray-600 font-bold text-base md:text-lg leading-relaxed flex gap-2">
                                <span v-if="!line.match(/^\d\./) && !line.startsWith('👉') && !line.startsWith('✅') && idx > 0" class="text-orange-400 mt-1 min-w-[15px]">👉</span>
                                <span :class="line.includes('Rasulullah') || line.includes('Wajib') || line.includes('Penting') ? 'text-green-600 font-black' : ''">
                                    {{ line }}
                                </span>
                            </li>
                        </ul>
                        <div v-if="currentSlide === 2" class="mt-4 p-3 bg-blue-100 rounded-xl text-center text-blue-700 font-bold italic text-sm">
                            “Anak shalih itu suka hidup bersih. Rajin cuci tangan, rajin gosok gigi, supaya sehat dan disayang Allah.”
                        </div>
                    </div>

                    <p class="text-[10px] md:text-xs text-gray-400 italic mb-6 w-full text-center border-t pt-2">
                        {{ slides[currentSlide].source }}
                    </p>

                    <div class="flex justify-center w-full">
                        <button v-if="currentSlide < slides.length - 1" 
                            @click="nextSlide"
                            class="text-white text-xl md:text-2xl font-black px-12 py-4 rounded-full shadow-lg hover:scale-105 active:scale-95 transition-all w-full md:w-auto"
                            :class="slides[currentSlide].color">
                            LANJUT BELAJAR 👉
                        </button>

                        <Link v-else 
                            href="/siswa/login"
                            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-xl md:text-2xl font-black px-10 py-4 rounded-full shadow-[0_6px_0_0_#c2410c] hover:scale-105 active:scale-95 active:shadow-none active:translate-y-1 transition-all flex items-center justify-center gap-2 w-full md:w-auto">
                            <span>MASUK SEKARANG</span> 🚀
                        </Link>
                    </div>

                </div>
            </transition>

            <p class="mt-6 font-bold text-gray-400 text-sm">
                Materi {{ currentSlide + 1 }} dari {{ slides.length }}
            </p>

        </div>
    </div>
</template>

<style scoped>
/* Transisi Slide */
.slide-fade-enter-active { transition: all 0.5s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from { transform: translateX(50px); opacity: 0; }
.slide-fade-leave-to { transform: translateX(-50px); opacity: 0; }

/* Scrollbar Cantik */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
</style>