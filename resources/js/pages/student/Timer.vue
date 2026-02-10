<script setup>
import { ref, onUnmounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import confetti from 'canvas-confetti';

const props = defineProps({
    student: Object,
    type: String, // 'brushing' atau 'handwashing'
});

// --- KONFIGURASI STANDAR ILMIAH (WAKTU & LANGKAH) ---
const config = {
    brushing: {
        title: '🦷 SIKAT GIGI CERIA',
        duration: 120, // 2 Menit (Standar Dokter Gigi)
        steps: [
            { time: 0, text: "Siapkan sikat & pasta gigi sebesar biji jagung 🌽" },
            { time: 10, text: "Sikat gigi depan bagian luar (Naik-Turun) ↕️" },
            { time: 30, text: "Sikat gigi belakang bagian luar (Memutar) 🔄" },
            { time: 50, text: "Sikat permukaan kunyah gigi (Maju-Mundur) ↔️" },
            { time: 70, text: "Sikat gigi bagian dalam (Cungkil keluar) ↗️" },
            { time: 90, text: "Sikat lidahmu pelan-pelan 👅" },
            { time: 110, text: "Kumur-kumur dengan air bersih 💧" },
        ],
        color: 'text-blue-600',
        bg: 'bg-blue-50'
    },
    handwashing: {
        title: '🧼 CUCI TANGAN SEHAT',
        duration: 60, // 60 Detik (Standar WHO)
        steps: [
            { time: 0, text: "Basahi tangan & tuang sabun secukupnya 🧴" },
            { time: 10, text: "Gosok telapak tangan dengan memutar 👏" },
            { time: 20, text: "Gosok punggung tangan kanan & kiri 🤚" },
            { time: 30, text: "Gosok sela-sela jari tangan 🤞" },
            { time: 40, text: "Kunci jari-jari (Gerakan mengunci) ✊" },
            { time: 50, text: "Putar ibu jari & ujung kuku di telapak tangan 👌" },
            { time: 58, text: "Bilas air mengalir & keringkan! 🌬️" }
        ],
        color: 'text-green-600',
        bg: 'bg-green-50'
    }
};

const currentConfig = config[props.type];
const totalTime = currentConfig.duration;
const timeLeft = ref(totalTime);
const isRunning = ref(false);
const isFinished = ref(false);
const proofFile = ref(null);
const proofPreview = ref(null);
const isUploading = ref(false);
let timerInterval = null;

// --- LOGIKA PESAN PANDUAN ---
const currentInstruction = computed(() => {
    // Cari langkah yang sesuai dengan waktu berjalan (Elapsed Time)
    const elapsedTime = totalTime - timeLeft.value;
    // Ambil langkah terakhir yang waktunya sudah lewat
    const step = currentConfig.steps.slice().reverse().find(s => elapsedTime >= s.time);
    return step ? step.text : "Siap-siap...";
});

// --- PROGRESS BAR LINGKARAN ---
const circleDasharray = 2 * Math.PI * 120; // Keliling lingkaran (r=120)
const circleDashoffset = computed(() => {
    return circleDasharray * (1 - timeLeft.value / totalTime);
});

// --- FUNGSI TIMER ---
const startTimer = () => {
    if (isRunning.value) return;
    isRunning.value = true;
    
    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
        } else {
            finishTimer();
        }
    }, 1000);
};

const finishTimer = () => {
    clearInterval(timerInterval);
    isRunning.value = false;
    isFinished.value = true;
    
    // Efek Konfeti Pertama (Selesai Waktu)
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
};

// --- FUNGSI UPLOAD BUKTI ---
const handleFileChange = (event) => {
    const file = event.target.files[0];
    
    // --- TAMBAHAN KODE: Pengecekan Ukuran ---
    if (file) {
        // Cek jika ukuran lebih dari 5MB (5 * 1024 * 1024)
        if (file.size > 5 * 1024 * 1024) { 
            alert("Waduh! Foto terlalu besar (Max 5MB). Coba turunkan resolusi kamera atau pilih foto lain ya! 📸");
            return; // Batalkan proses
        }

        proofFile.value = file;
        proofPreview.value = URL.createObjectURL(file);
    }
};

const submitActivity = async () => {
    // 1. Validasi Foto
    if (!proofFile.value) {
        alert("Eits, foto dulu buktinya ya biar dapat bintang! 📸");
        return;
    }

    isUploading.value = true;
    
    // 2. BUNGKUS DATA (SESUAIKAN DENGAN CONTROLLER)
    const formData = new FormData();
    formData.append('student_id', parseInt(props.student.id)); 
    
    // --- PERBAIKAN DI SINI ---
    // Controller minta 'type', JANGAN pakai 'activity_type'
    formData.append('type', props.type); 
    
    // Controller minta 'proof', JANGAN pakai 'proof_image'
    formData.append('proof', proofFile.value); 
    
    // Durasi tetap dikirim
    formData.append('duration', currentConfig.duration); 
    // Flag bukan kuis
    formData.append('is_quiz', 0);

    try {
        const response = await axios.post('/activity/store', formData, {
            headers: { 
                'Content-Type': 'multipart/form-data' 
            }
        });

        // 3. SUKSES
        confetti({ particleCount: 200, spread: 100, origin: { y: 0.6 } });
        
        setTimeout(() => {
            alert("Hore! Laporanmu diterima Pak Dokter! 🌟");
            router.visit(`/dashboard/${props.student.id}`); 
        }, 1500);

    } catch (error) {
        console.error("Gagal Upload:", error.response?.data || error);
        let pesan = "Gagal mengirim laporan.";
        
        if (error.response?.status === 422) {
            pesan = "Data tidak lengkap (Cek Tipe/Foto).";
            // Debugging: Cek error detail di console browser
            console.log("Detail Error:", error.response.data.errors);
        } else if (error.response?.status === 413) {
            pesan = "Ukuran foto terlalu besar! Maksimal 5MB.";
        }
        
        alert(pesan);
        isUploading.value = false;
    }
};

onUnmounted(() => clearInterval(timerInterval));
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center p-4 font-['Comic_Sans_MS',_sans-serif] relative overflow-hidden transition-colors duration-500"
        :class="currentConfig.bg">
        
        <h1 class="text-3xl md:text-5xl font-black mb-6 text-center drop-shadow-sm uppercase tracking-wide"
            :class="currentConfig.color">
            {{ currentConfig.title }}
        </h1>

        <div class="h-24 md:h-28 flex items-center justify-center px-4 w-full max-w-2xl text-center mb-4">
            <transition name="fade" mode="out-in">
                <p :key="currentInstruction" 
                   v-if="isRunning || isFinished"
                   class="text-xl md:text-3xl font-bold text-gray-700 bg-white/80 px-6 py-4 rounded-3xl shadow-lg border-2 border-white backdrop-blur-sm animate-pulse-slow">
                    {{ currentInstruction }}
                </p>
                <p v-else class="text-lg md:text-xl font-bold text-gray-400">
                    Tekan tombol MULAI di bawah ya! 👇
                </p>
            </transition>
        </div>

        <div class="relative w-64 h-64 md:w-80 md:h-80 flex items-center justify-center mb-10">
            <svg class="w-full h-full -rotate-90 drop-shadow-xl">
                <circle cx="50%" cy="50%" r="45%" stroke="white" stroke-width="20" fill="white" class="opacity-50" />
                <circle cx="50%" cy="50%" r="45%" 
                    :stroke="type === 'brushing' ? '#3b82f6' : '#22c55e'" 
                    stroke-width="20" 
                    fill="transparent" 
                    stroke-linecap="round"
                    class="transition-all duration-1000 ease-linear"
                    :stroke-dasharray="circleDasharray"
                    :stroke-dashoffset="circleDashoffset"
                />
            </svg>
            
            <div class="absolute flex flex-col items-center">
                <span class="text-7xl md:text-8xl font-black text-gray-700 tabular-nums tracking-tighter">
                    {{ timeLeft }}
                </span>
                <span class="text-lg md:text-xl font-bold text-gray-400 uppercase tracking-widest">Detik</span>
            </div>
        </div>

        <div class="w-full max-w-md px-4 relative z-20">
            
            <button v-if="!isRunning && !isFinished" @click="startTimer"
                class="w-full text-white text-2xl md:text-3xl font-black py-5 md:py-6 rounded-[2.5rem] shadow-[0_8px_0_0_rgba(0,0,0,0.2)] active:shadow-none active:translate-y-2 transition-all hover:scale-105"
                :class="type === 'brushing' ? 'bg-blue-500 hover:bg-blue-600 shadow-blue-700' : 'bg-green-500 hover:bg-green-600 shadow-green-700'">
                MULAI SEKARANG! 🚀
            </button>

            <div v-if="isFinished" class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-2xl border-4 animate-in fade-in slide-in-from-bottom-10 duration-500"
                 :class="type === 'brushing' ? 'border-blue-100' : 'border-green-100'">
                
                <h2 class="text-2xl font-black text-gray-700 text-center mb-2">📸 Wajib Foto Bukti!</h2>
                <p class="text-gray-400 text-center text-xs font-bold mb-6">Tanpa foto, bintang tidak bisa diambil :(</p>

                <div class="mb-4 relative group cursor-pointer" @click="$refs.fileInput.click()">
                    <div v-if="!proofPreview" class="w-full h-48 bg-gray-100 rounded-3xl flex flex-col items-center justify-center border-4 border-dashed border-gray-300 hover:bg-gray-50 transition-colors">
                        <span class="text-4xl mb-2">📷</span>
                        <span class="text-gray-400 font-bold text-sm">Ketuk untuk buka kamera</span>
                    </div>
                    <img v-else :src="proofPreview" class="w-full h-64 object-cover rounded-3xl border-4 border-yellow-400 shadow-md">
                    
                    <input ref="fileInput" type="file" accept="image/*" capture="environment" 
                        @change="handleFileChange" class="hidden">
                </div>

                <div class="space-y-3">
                    <button v-if="proofPreview" @click="submitActivity" :disabled="isUploading"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xl font-black py-4 rounded-2xl shadow-[0_6px_0_0_#ca8a04] active:shadow-none active:translate-y-1 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <span v-if="isUploading" class="animate-spin">⏳</span>
                        {{ isUploading ? 'Mengirim...' : 'KIRIM & AMBIL HADIAH 🎁' }}
                    </button>
                    
                    <button v-else @click="$refs.fileInput.click()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold py-4 rounded-2xl border-2 border-gray-200">
                        Buka Kamera
                    </button>
                </div>
            </div>

            <p v-if="isRunning" class="text-center text-gray-400 font-bold mt-6 animate-pulse">
                Ikuti langkah di atas ya! Semangat! 💪
            </p>

        </div>

        <div class="fixed bottom-0 left-0 p-4 opacity-10 pointer-events-none text-9xl -rotate-12">
            {{ type === 'brushing' ? '🦷' : '🧼' }}
        </div>
        <div class="fixed top-0 right-0 p-4 opacity-10 pointer-events-none text-9xl rotate-12">
            {{ type === 'brushing' ? '🪥' : '🦠' }}
        </div>

    </div>
</template>

<style scoped>
/* Animasi kedip pelan untuk teks panduan */
.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .8; }
}

/* Transisi Halus */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>