<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import confetti from 'canvas-confetti';

const props = defineProps({
    student: Object
});

// Assets (Emoji)
const GERM = '🦠';
const BONK = '💥';
const HOLE = '🕳️';

// Game State
const score = ref(0);
const timeLeft = ref(30); // 30 Detik
const isPlaying = ref(false);
const isFinished = ref(false);
const holes = ref(Array(9).fill(false)); // 9 Lubang (3x3)
const activeHole = ref(null); // Lubang mana yang ada kumannya
const gameInterval = ref(null);
const timerInterval = ref(null);
const difficulty = ref(800); // Kecepatan muncul kuman (ms)

// --- LOGIC GAME ---

const startGame = () => {
    score.value = 0;
    timeLeft.value = 30;
    isPlaying.value = true;
    isFinished.value = false;
    difficulty.value = 800;
    
    // Mulai Timer Mundur
    timerInterval.value = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) stopGame();
    }, 1000);

    // Mulai Munculkan Kuman
    popUpGerm();
};

const popUpGerm = () => {
    if (!isPlaying.value) return;

    // Reset lubang sebelumnya
    activeHole.value = null;

    // Pilih lubang random baru
    const randomTime = Math.floor(Math.random() * difficulty.value) + 400;
    const randomHole = Math.floor(Math.random() * 9);
    
    activeHole.value = randomHole;

    // Hilangkan kuman setelah waktu tertentu jika tidak dipukul
    gameInterval.value = setTimeout(() => {
        if (activeHole.value === randomHole) {
            activeHole.value = null; // Kuman sembunyi lagi
            popUpGerm(); // Munculkan lagi di tempat lain
        }
    }, randomTime);
};

const whack = (index, event) => {
    if (!isPlaying.value || activeHole.value !== index) return;

    // LOGIKA PUKUL
    score.value += 10;
    activeHole.value = 'hit'; // Tandai kena pukul (untuk animasi)
    
    // Efek Getar di HP
    if (navigator.vibrate) navigator.vibrate(50);

    // Percepat game setiap kelipatan 50 poin (Makin susah)
    if (score.value % 50 === 0 && difficulty.value > 300) {
        difficulty.value -= 50;
    }

    // Langsung muncul kuman baru tanpa menunggu timeout
    clearTimeout(gameInterval.value);
    setTimeout(popUpGerm, 200); // Jeda dikit biar animasi 'bonk' kelihatan
};

const stopGame = () => {
    isPlaying.value = false;
    isFinished.value = true;
    clearInterval(timerInterval.value);
    clearTimeout(gameInterval.value);
    activeHole.value = null;

    if (score.value > 100) {
        confetti({ particleCount: 200, spread: 100, origin: { y: 0.6 } });
    }
};

const exitGame = () => {
    clearInterval(timerInterval.value);
    clearTimeout(gameInterval.value);
    router.visit(`/dashboard/${props.student.id}`);
};

onUnmounted(() => {
    clearInterval(timerInterval.value);
    clearTimeout(gameInterval.value);
});
</script>

<template>
    <Head title="Basmi Kuman!" />

    <div class="min-h-screen bg-orange-50 font-['Comic_Sans_MS'] flex flex-col items-center justify-center p-4 relative overflow-hidden select-none">
        
        <div class="absolute top-10 left-10 text-6xl opacity-10 animate-pulse">🦠</div>
        <div class="absolute bottom-10 right-10 text-8xl opacity-10 animate-bounce">🦠</div>

        <div class="z-10 text-center mb-6 w-full max-w-md">
            <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-lg border-4 border-orange-200">
                <div class="text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase">Skor</p>
                    <p class="text-3xl font-black text-orange-600">{{ score }}</p>
                </div>
                <div class="text-4xl animate-bounce">🔨</div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 font-bold uppercase">Waktu</p>
                    <p class="text-3xl font-black" :class="timeLeft <= 5 ? 'text-red-500 animate-ping' : 'text-blue-600'">{{ timeLeft }}s</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-200 p-6 rounded-[2rem] shadow-2xl border-b-8 border-orange-300 w-full max-w-md relative">
            
            <div v-if="!isPlaying && !isFinished" class="absolute inset-0 z-20 bg-black/60 backdrop-blur-sm rounded-[2rem] flex flex-col items-center justify-center text-center p-6 text-white">
                <div class="text-6xl mb-4">🦠🔨</div>
                <h1 class="text-3xl font-black mb-2 text-yellow-300 drop-shadow-md">MISI BASMI KUMAN</h1>
                <p class="mb-6 font-bold text-sm opacity-90">Kuman jahat menyerang! Ketuk mereka secepatnya sebelum waktu habis!</p>
                <button @click="startGame" class="bg-green-500 hover:bg-green-600 text-white text-xl font-black py-4 px-10 rounded-full shadow-[0_6px_0_#15803d] active:shadow-none active:translate-y-1 transition-all">
                    MULAI MAIN ▶
                </button>
                <button @click="exitGame" class="mt-4 text-sm font-bold text-gray-300 hover:text-white underline">Kembali ke Menu</button>
            </div>

            <div v-if="isFinished" class="absolute inset-0 z-20 bg-black/70 backdrop-blur-sm rounded-[2rem] flex flex-col items-center justify-center text-center p-6 text-white animate-in fade-in zoom-in duration-300">
                <div class="text-6xl mb-2">{{ score > 100 ? '🏆' : '😅' }}</div>
                <h2 class="text-2xl font-black mb-1">WAKTU HABIS!</h2>
                <p class="text-sm font-bold opacity-80 mb-4">Kamu berhasil membasmi:</p>
                <div class="text-5xl font-black text-yellow-400 mb-6 drop-shadow-lg">{{ score / 10 }} Kuman</div>
                
                <div class="flex gap-3 w-full">
                    <button @click="exitGame" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-[0_4px_0_#374151] active:shadow-none active:translate-y-1">
                        Keluar
                    </button>
                    <button @click="startGame" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-xl shadow-[0_4px_0_#1d4ed8] active:shadow-none active:translate-y-1">
                        Main Lagi 🔄
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div v-for="(hole, index) in 9" :key="index" 
                    class="aspect-square bg-[#78350f] rounded-full relative shadow-inner overflow-hidden border-b-4 border-[#451a03] ring-4 ring-orange-300/50">
                    
                    <div class="absolute bottom-[-10px] w-full text-center text-5xl opacity-50 grayscale">{{ HOLE }}</div>

                    <transition name="pop">
                        <div v-if="activeHole === index" 
                             @mousedown="whack(index)"
                             @touchstart.prevent="whack(index)"
                             class="absolute inset-0 flex items-center justify-center cursor-pointer active:scale-90 transition-transform z-10 hover:brightness-110">
                            <span class="text-6xl filter drop-shadow-lg animate-bounce-fast">{{ GERM }}</span>
                        </div>
                    </transition>

                    <div v-if="activeHole === 'hit' && activeHole === index" class="absolute inset-0 flex items-center justify-center z-20">
                        <span class="text-6xl animate-ping">{{ BONK }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-6 text-gray-400 text-xs font-bold text-center">Tips: Jangan biarkan kuman lolos!</p>

    </div>
</template>

<style scoped>
/* Animasi Muncul Kuman */
.pop-enter-active { animation: pop-up 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.pop-leave-active { transition: all 0.1s ease-in; transform: translateY(100%); }

@keyframes pop-up {
    0% { transform: translateY(100%) scale(0.5); opacity: 0; }
    100% { transform: translateY(0) scale(1); opacity: 1; }
}

.animate-bounce-fast { animation: bounce 0.5s infinite; }
</style>