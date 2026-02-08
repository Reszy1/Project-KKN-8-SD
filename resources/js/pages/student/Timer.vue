<script setup>
import { ref, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import confetti from 'canvas-confetti';

const props = defineProps({
    student: Object,
    type: String, // 'brushing' atau 'handwashing'
});

// Setting waktu berdasarkan tipe
const initialTime = props.type === 'brushing' ? 120 : 20;
const timeLeft = ref(initialTime);
const isRunning = ref(false);
const isFinished = ref(false);
const proofFile = ref(null);
const proofPreview = ref(null);
const isUploading = ref(false);
let timerInterval = null;

// Pesan interaktif untuk anak
const messages = {
    brushing: ["Mulai sikat!", "Sikat bagian depan...", "Sikat bagian kiri...", "Sikat bagian kanan...", "Jangan lupa lidahnya!", "Hampir berkilau!"],
    handwashing: ["Basahi tangan...", "Pakai sabun...", "Gosok sela jari...", "Punggung tangan...", "Bilas bersih!"]
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        proofFile.value = file;
        // Buat preview gambar agar anak bisa lihat hasilnya
        proofPreview.value = URL.createObjectURL(file);
    }
};

const submitProof = async () => {
    if (!proofFile.value) {
        alert("Jangan lupa foto dulu ya sebagai bukti!");
        return;
    }

    isUploading.value = true;

    // Gunakan FormData untuk kirim file
    const formData = new FormData();
    formData.append('student_id', props.student.id);
    formData.append('type', props.type);
    formData.append('duration', initialTime); // atau duration yang didapat
    formData.append('proof', proofFile.value);

    try {
        await axios.post('/activity/store', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        // Redirect balik ke dashboard setelah sukses
        alert("Hore! Bukti berhasil dikirim ke Ibu Guru! 📸");
        router.get(`/dashboard/${props.student.id}`);
        
    } catch (error) {
        console.error("Gagal upload", error);
        alert("Yah, gagal kirim. Coba lagi ya!");
        isUploading.value = false;
    }
};

const currentMessage = computed(() => {
    const msgList = messages[props.type];
    const index = Math.floor(((initialTime - timeLeft.value) / initialTime) * msgList.length);
    return msgList[Math.min(index, msgList.length - 1)];
});

const startTimer = () => {
    isRunning.value = true;
    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--;
        } else {
            finishActivity();
        }
    }, 1000);
};

const finishActivity = async () => {
    clearInterval(timerInterval);
    isRunning.value = false;
    isFinished.value = true;
    
    // Efek Konfeti Meriah!
    confetti({
        particleCount: 150,
        spread: 70,
        origin: { y: 0.6 }
    });

    try {
        await axios.post('/activity/store', {
            student_id: props.student.id,
            type: props.type,
            duration: initialTime
        });
    } catch (error) {
        console.error("Gagal menyimpan data", error);
    }
};

onUnmounted(() => clearInterval(timerInterval));
</script>

<template>
    <div class="min-h-screen bg-sky-50 flex flex-col items-center justify-center p-6 font-['Comic_Sans_MS']">
        <h1 class="text-4xl font-black text-sky-600 mb-4 text-center">
            {{ type === 'brushing' ? '🦷 ISTANA GIGI' : '🧼 MARKAS SABUN' }}
        </h1>

        <div class="h-16 flex items-center">
            <p v-if="isRunning" class="text-2xl font-bold text-orange-500 animate-pulse text-center">
                "{{ currentMessage }}"
            </p>
        </div>

        <div class="relative w-72 h-72 flex items-center justify-center mb-10">
            <svg class="w-full h-full -rotate-90">
                <circle cx="144" cy="144" r="120" stroke="currentColor" stroke-width="15" fill="transparent" class="text-gray-200" />
                <circle cx="144" cy="144" r="120" stroke="currentColor" stroke-width="15" fill="transparent" 
                    class="text-sky-500 transition-all duration-1000"
                    :stroke-dasharray="2 * Math.PI * 120"
                    :stroke-dashoffset="2 * Math.PI * 120 * (1 - timeLeft / initialTime)"
                />
            </svg>
            <div class="absolute flex flex-col items-center">
                <span class="text-7xl font-black text-sky-700">{{ timeLeft }}</span>
                <span class="text-xl font-bold text-sky-500">Detik Lagi</span>
            </div>
        </div>

        <div class="flex flex-col items-center gap-4">
            <button v-if="!isRunning && !isFinished" @click="startTimer"
                class="bg-orange-500 hover:bg-orange-600 text-white text-3xl font-black px-12 py-6 rounded-[40px] shadow-[0_10px_0_0_#c2410c] active:shadow-none active:translate-y-2 transition-all">
                MULAI SEKARANG! 🚀
            </button>

            <div v-if="isFinished" class="bg-white p-6 rounded-[2rem] shadow-xl w-full text-center border-4 border-sky-200 animate-fade-in-up">
                <h2 class="text-2xl font-black text-sky-600 mb-4">📸 Foto Bukti Dulu Yuk!</h2>
                
                <div class="mb-4 relative group">
                    <div v-if="!proofPreview" class="w-full h-48 bg-gray-100 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-300">
                        <span class="text-gray-400 text-sm">Hasil foto akan muncul di sini</span>
                    </div>
                    <img v-else :src="proofPreview" class="w-full h-64 object-cover rounded-2xl border-4 border-yellow-400 shadow-md">
                    
                    <input type="file" id="cameraInput" accept="image/*" capture="user" 
                        @change="handleFileUpload" class="hidden">
                </div>

                <label for="cameraInput" 
                    class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-xl font-bold py-4 rounded-xl cursor-pointer shadow-lg mb-3 transition-transform active:scale-95">
                    {{ proofPreview ? '🔄 Foto Ulang' : '📷 Buka Kamera' }}
                </label>

                <button v-if="proofPreview" @click="submitProof" :disabled="isUploading"
                    class="w-full bg-green-500 hover:bg-green-600 text-white text-xl font-bold py-4 rounded-xl shadow-[0_6px_0_0_#15803d] active:shadow-none active:translate-y-1 transition-all disabled:opacity-50">
                    {{ isUploading ? 'Mengirim...' : 'Kirim ke Ibu Guru! ✈️' }}
                </button>
            </div>

            <div v-if="isFinished" class="text-center">
                <h2 class="text-3xl font-black text-green-600 mb-6">HEBAT! KAMU BERHASIL! ✨</h2>
                <button @click="router.get(`/dashboard/${student.id}`)"
                    class="bg-sky-500 hover:bg-sky-600 text-white text-2xl font-black px-10 py-5 rounded-[30px] shadow-[0_8px_0_0_#0369a1]">
                    AMBIL HADIAHMU 🎁
                </button>
            </div>
        </div>
        
        <div class="fixed bottom-0 left-0 w-full p-4 flex justify-between opacity-30 pointer-events-none">
            <span class="text-9xl">{{ type === 'brushing' ? '🪥' : '🧼' }}</span>
            <span class="text-9xl">{{ type === 'brushing' ? '🦷' : '🦠' }}</span>
        </div>
    </div>
</template>