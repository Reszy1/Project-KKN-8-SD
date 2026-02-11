<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import confetti from 'canvas-confetti';

const props = defineProps({
    student: Object,
    questions: Array,
    type: String
});

// Definisi Tema Warna Berdasarkan Tipe Kuis
const theme = computed(() => {
    switch(props.type) {
        case 'brushing': 
            return { 
                title: '🦷 Kuis Gigi', 
                bgApp: 'bg-blue-50',
                textTitle: 'text-blue-600',
                bar: 'bg-blue-500',
                textCount: 'text-blue-600',
                optHover: 'hover:bg-blue-500 hover:border-blue-600',
                iconColor: 'text-blue-600',
                btnBg: 'bg-blue-500 hover:bg-blue-600',
                btnShadow: 'shadow-blue-200',
                textScore: 'text-blue-600'
            };
        case 'handwashing': 
            return { 
                title: '🧼 Kuis Tangan', 
                bgApp: 'bg-green-50',
                textTitle: 'text-green-600',
                bar: 'bg-green-500',
                textCount: 'text-green-600',
                optHover: 'hover:bg-green-500 hover:border-green-600',
                iconColor: 'text-green-600',
                btnBg: 'bg-green-500 hover:bg-green-600',
                btnShadow: 'shadow-green-200',
                textScore: 'text-green-600'
            };
        case 'reproductive': 
            return { 
                title: '❤️ Kuis Tubuhku', 
                bgApp: 'bg-pink-50',
                textTitle: 'text-pink-600',
                bar: 'bg-pink-500',
                textCount: 'text-pink-600',
                optHover: 'hover:bg-pink-500 hover:border-pink-600',
                iconColor: 'text-pink-600',
                btnBg: 'bg-pink-500 hover:bg-pink-600',
                btnShadow: 'shadow-pink-200',
                textScore: 'text-pink-600'
            };
        default: 
            return { 
                title: 'Kuis Seru', 
                bgApp: 'bg-purple-50',
                textTitle: 'text-purple-600',
                bar: 'bg-purple-500',
                textCount: 'text-purple-600',
                optHover: 'hover:bg-purple-500 hover:border-purple-600',
                iconColor: 'text-purple-600',
                btnBg: 'bg-purple-500 hover:bg-purple-600',
                btnShadow: 'shadow-purple-200',
                textScore: 'text-purple-600'
            };
    }
});

const currentStep = ref(0);
const score = ref(0);
const isFinished = ref(false);
const feedback = ref(null);
const isAnimating = ref(false);

const playSound = (soundType) => {
    try {
        const audio = new Audio(`/sounds/${soundType}.mp3`);
        audio.volume = 0.5;
        audio.play().catch(() => {});
    } catch (e) { console.error(e); }
};

// --- LOGIKA UTAMA: CEK JAWABAN & SISTEM POIN ---
const checkAnswer = (index) => {
    if (isAnimating.value) return;
    isAnimating.value = true;
    
    if (!props.questions || props.questions.length === 0) return;

    const isCorrect = index === props.questions[currentStep.value].answer;

    if (isCorrect) {
        // JIKA BENAR: Tambah 20 Poin
        score.value += 20;
        feedback.value = 'correct';
        playSound('success'); 
        confetti({ particleCount: 50, spread: 60, origin: { y: 0.7 } });
    } else {
        // JIKA SALAH: Kurangi 10 Poin (Tapi tidak boleh minus)
        score.value = Math.max(0, score.value - 10);
        
        feedback.value = 'wrong';
        playSound('wrong'); 
    }

    // Jeda sebentar sebelum lanjut soal berikutnya
    setTimeout(() => {
        feedback.value = null;
        isAnimating.value = false;
        
        if (currentStep.value < props.questions.length - 1) {
            currentStep.value++;
        } else {
            finishQuiz();
        }
    }, 1500);
};

const finishQuiz = async () => {
    isFinished.value = true;
    
    // Jika skor bagus, kasih konfeti lebih banyak
    if (score.value > 0) {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
        playSound('success');
    }

    const formData = new FormData();
    formData.append('student_id', props.student.id);
    formData.append('type', props.type);
    formData.append('duration', score.value); // Skor dikirim sebagai duration
    formData.append('is_quiz', 1); // Penanda bahwa ini kuis

    try {
        await axios.post('/activity/store', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    } catch (e) {
        console.error("Gagal simpan skor", e);
    }
};
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center p-6 font-['Comic_Sans_MS'] transition-colors duration-500"
        :class="theme.bgApp">

        <div v-if="!questions || questions.length === 0" class="text-center animate-fade-in-up">
            <div class="text-7xl mb-4 grayscale opacity-50">📭</div>
            <h2 class="text-2xl font-black text-gray-700">Soal Belum Tersedia</h2>
            <p class="text-gray-500 mb-6">Minta Bu Guru untuk membuat soal kategori ini ya!</p>
            <button @click="router.get('/dashboard/' + student.id)"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-transform active:scale-95">
                Kembali
            </button>
        </div>

        <div v-else class="w-full max-w-2xl">
            
            <div v-if="!isFinished">
                <div class="text-center mb-8">
                    <h1 class="text-4xl md:text-5xl font-black uppercase tracking-wider drop-shadow-sm" 
                        :class="theme.textTitle">
                        {{ theme.title }}
                    </h1>
                </div>

                <div class="mb-8 bg-white p-4 rounded-3xl shadow-md flex items-center gap-4 border-2 border-white">
                    <div class="flex-1 bg-gray-100 h-6 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full transition-all duration-500 ease-out" 
                            :class="theme.bar"
                            :style="{ width: ((currentStep + 1) / questions.length) * 100 + '%' }"></div>
                    </div>
                    <span class="font-black text-lg" :class="theme.textCount">
                        {{ currentStep + 1 }}/{{ questions.length }}
                    </span>
                    <span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded text-gray-500">
                        Skor: {{ score }}
                    </span>
                </div>

                <div class="bg-white rounded-[3rem] p-8 md:p-10 shadow-2xl border-4 border-white relative overflow-hidden transition-all">
                    <h2 class="text-2xl md:text-3xl font-black text-center text-gray-800 mb-10 leading-snug">
                        {{ questions[currentStep].question }}
                    </h2>

                    <div class="grid grid-cols-1 gap-4">
                        <button v-for="(opt, idx) in questions[currentStep].options" :key="idx"
                            @click="checkAnswer(idx)"
                            :disabled="isAnimating"
                            class="group relative bg-[#F8F7FF] p-5 rounded-2xl text-xl font-bold transition-all text-gray-700 hover:text-white border-b-4 border-gray-200 active:border-b-0 active:translate-y-1 text-left"
                            :class="`${theme.optHover} hover:shadow-lg`">
                            
                            <span class="flex items-center gap-4">
                                <span class="w-12 h-12 shrink-0 bg-white rounded-full flex items-center justify-center shadow-md text-lg font-black transition-transform group-hover:scale-110"
                                      :class="theme.iconColor">
                                    {{ String.fromCharCode(65 + idx) }}
                                </span>
                                <span class="leading-tight">{{ opt }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="text-center animate-fade-in-up">
                <div class="mb-8 relative inline-block">
                    <div class="text-[140px] animate-bounce drop-shadow-2xl">🏆</div>
                    <div class="absolute -top-4 -right-4 bg-orange-500 text-white w-20 h-20 rounded-full flex items-center justify-center text-3xl font-black border-4 border-white shadow-xl rotate-12">
                        {{ score }}
                    </div>
                </div>
                
                <h1 class="text-5xl font-black mb-2 tracking-tight" :class="theme.textTitle">
                    {{ score > 0 ? 'HEBAT!' : 'BELAJAR LAGI YUK!' }}
                </h1>
                <p class="text-2xl font-bold text-gray-500 mb-10">Skor Akhir Kamu: <span class="text-orange-500">{{ score }}</span></p>
                
                <button @click="router.get('/dashboard/' + student.id)"
                    class="text-white text-2xl font-black px-12 py-5 rounded-[40px] shadow-xl active:shadow-none active:translate-y-2 transition-all hover:scale-105"
                    :class="`${theme.btnBg} ${theme.btnShadow}`">
                    KEMBALI KE MENU 🏠
                </button>
            </div>

            <transition name="pop">
                <div v-if="feedback" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none bg-black/20 backdrop-blur-sm">
                    <div v-if="feedback === 'correct'" class="bg-green-500 text-white p-10 rounded-full shadow-2xl animate-bounce border-8 border-white flex flex-col items-center">
                        <span class="text-8xl filter drop-shadow-lg">✅</span>
                        <span class="font-black text-2xl mt-2">+20</span>
                    </div>
                    <div v-if="feedback === 'wrong'" class="bg-red-500 text-white p-10 rounded-full shadow-2xl animate-shake border-8 border-white flex flex-col items-center">
                        <span class="text-8xl filter drop-shadow-lg">❌</span>
                        <span class="font-black text-2xl mt-2">-10</span>
                    </div>
                </div>
            </transition>
        </div>

    </div>
</template>

<style scoped>
.pop-enter-active { animation: pop-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.pop-leave-active { animation: pop-in 0.2s reverse ease-in; }

@keyframes pop-in { 
    0% { transform: scale(0); opacity: 0; } 
    100% { transform: scale(1); opacity: 1; } 
}

.animate-shake { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
@keyframes shake { 
    10%, 90% { transform: translate3d(-2px, 0, 0); } 
    20%, 80% { transform: translate3d(4px, 0, 0); } 
    30%, 50%, 70% { transform: translate3d(-6px, 0, 0); } 
    40%, 60% { transform: translate3d(6px, 0, 0); } 
}

.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>