<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import confetti from 'canvas-confetti';

const props = defineProps({ student: Object });

// --- KONFIGURASI GAME ---
const PLAYER_EMOJI = '🧺';
const GOOD_ITEMS = ['🍎', '🥦', '🥕', '🍗', '🐟', '🥛', '🍇', '🍌', '🌽'];
const BAD_ITEMS = ['🍬', '🍩', '🍟', '🦠', '🪰', '🥤', '🕷️'];
const GAME_DURATION = 60; 

// --- STATE ---
const score = ref(0);
const timeLeft = ref(GAME_DURATION);
const isPlaying = ref(false);
const isFinished = ref(false);
const playerX = ref(50); // Posisi 0-100%
const playerRotation = ref(0); // Efek miring saat gerak
const items = ref([]); 

// Loop Variables
let lastFrameTime = 0;
let animationFrameId = null;
let spawnTimeoutId = null;
let timerIntervalId = null;

// Input State (Untuk gerakan smooth)
const keys = {
    left: false,
    right: false
};

// --- LOGIC GAME ---

const startGame = () => {
    resetGame();
    isPlaying.value = true;
    lastFrameTime = performance.now();

    // Timer Detik
    timerIntervalId = setInterval(() => {
        timeLeft.value--;
        if (timeLeft.value <= 0) gameOver();
    }, 1000);

    // Start Loops
    animationFrameId = requestAnimationFrame(gameLoop);
    scheduleNextSpawn();
};

const resetGame = () => {
    score.value = 0;
    timeLeft.value = GAME_DURATION;
    items.value = [];
    playerX.value = 50;
    playerRotation.value = 0;
    isFinished.value = false;
    keys.left = false;
    keys.right = false;
};

const scheduleNextSpawn = () => {
    if (!isPlaying.value) return;
    spawnItem();
    // Jarak antar item muncul (Makin lama makin cepat dikit)
    // Minimal 600ms (supaya tidak banjir item)
    const currentRate = Math.max(600, 1200 - (score.value * 1.5)); 
    spawnTimeoutId = setTimeout(scheduleNextSpawn, currentRate);
};

const spawnItem = () => {
    const isGood = Math.random() > 0.35; 
    const content = isGood 
        ? GOOD_ITEMS[Math.floor(Math.random() * GOOD_ITEMS.length)]
        : BAD_ITEMS[Math.floor(Math.random() * BAD_ITEMS.length)];

    items.value.push({
        id: Date.now() + Math.random(),
        x: Math.random() * 80 + 10, // Margin aman 10% kiri-kanan
        y: -15, 
        content: content,
        type: isGood ? 'good' : 'bad',
        // KECEPATAN: Diperlambat drastis.
        // Base speed 0.1 (sangat pelan) + sedikit variasi
        speed: (Math.random() * 0.1 + 0.1) + (score.value * 0.0002) 
    });
};

const gameLoop = (timestamp) => {
    if (!isPlaying.value) return;

    const deltaTime = timestamp - lastFrameTime;
    lastFrameTime = timestamp;

    // 1. UPDATE POSISI PEMAIN (SMOOTH MOVEMENT)
    // Bergerak sedikit demi sedikit setiap frame, bukan loncat.
    const moveSpeed = 0.08 * deltaTime; // Kecepatan keranjang
    
    if (keys.left && playerX.value > 5) {
        playerX.value -= moveSpeed;
        playerRotation.value = -10; // Miring kiri
    } else if (keys.right && playerX.value < 95) {
        playerX.value += moveSpeed;
        playerRotation.value = 10; // Miring kanan
    } else {
        playerRotation.value = 0; // Tegak lurus
    }

    // 2. UPDATE ITEM JATUH
    for (let i = items.value.length - 1; i >= 0; i--) {
        const item = items.value[i];
        
        // Faktor DeltaTime agar kecepatan sama di HP laggy maupun lancar
        item.y += item.speed * (deltaTime / 2); 

        // Deteksi Tabrakan (Hitbox diperbesar sedikit agar mudah)
        if (item.y > 80 && item.y < 95 && Math.abs(item.x - playerX.value) < 15) {
            if (item.type === 'good') {
                score.value += 10;
            } else {
                score.value = Math.max(0, score.value - 20); 
                if (navigator.vibrate) navigator.vibrate(200);
                triggerShake();
            }
            items.value.splice(i, 1);
            continue;
        }

        // Hapus jika lewat layar
        if (item.y > 105) items.value.splice(i, 1);
    }

    animationFrameId = requestAnimationFrame(gameLoop);
};

// --- INPUT HANDLER (KEYBOARD & TOUCH) ---

const handleKeyDown = (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'a') keys.left = true;
    if (e.key === 'ArrowRight' || e.key === 'd') keys.right = true;
};

const handleKeyUp = (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'a') keys.left = false;
    if (e.key === 'ArrowRight' || e.key === 'd') keys.right = false;
};

// Touch Control (Tekan Tahan)
const startMoveLeft = () => keys.left = true;
const stopMoveLeft = () => keys.left = false;
const startMoveRight = () => keys.right = true;
const stopMoveRight = () => keys.right = false;

const triggerShake = () => {
    const gameArea = document.getElementById('game-area');
    if(gameArea) {
        gameArea.classList.add('shake-anim');
        setTimeout(() => gameArea.classList.remove('shake-anim'), 500);
    }
};

const gameOver = () => {
    isPlaying.value = false;
    isFinished.value = true;
    cancelAnimationFrame(animationFrameId);
    clearTimeout(spawnTimeoutId);
    clearInterval(timerIntervalId);
    if (score.value > 100) confetti({ particleCount: 200, spread: 120, origin: { y: 0.6 } });
};

const exitGame = () => {
    gameOver();
    router.visit(`/dashboard/${props.student.id}`);
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('keyup', handleKeyUp);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('keyup', handleKeyUp);
    cancelAnimationFrame(animationFrameId);
    clearTimeout(spawnTimeoutId);
    clearInterval(timerIntervalId);
});
</script>

<template>
    <Head title="Tangkap Makanan Sehat" />

    <div id="game-area" class="min-h-screen bg-gradient-to-b from-sky-200 to-sky-100 font-['Comic_Sans_MS'] overflow-hidden relative select-none">
        
        <div class="absolute top-10 left-10 text-8xl opacity-30 animate-pulse delay-700">☁️</div>
        <div class="absolute top-32 right-10 text-7xl opacity-30 animate-pulse">☁️</div>
        <div class="absolute bottom-0 w-full h-24 bg-gradient-to-t from-green-300 to-transparent opacity-50"></div>

        <div class="absolute top-4 left-4 right-4 z-30 flex justify-between items-center max-w-2xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm px-6 py-2 rounded-full shadow-xl border-4 border-sky-300 flex flex-col items-center min-w-[100px]">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Skor</p>
                <p class="text-3xl font-black text-sky-600 leading-none">{{ score }}</p>
            </div>
            <div class="bg-white/90 backdrop-blur-sm px-6 py-2 rounded-full shadow-xl border-4 border-red-300 flex flex-col items-center min-w-[100px]">
                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Waktu</p>
                <p class="text-3xl font-black leading-none" :class="timeLeft <= 10 ? 'text-red-600 animate-ping' : 'text-red-500'">{{ timeLeft }}</p>
            </div>
        </div>

        <div v-if="!isPlaying && !isFinished" class="absolute inset-0 z-50 bg-black/60 backdrop-blur-md flex flex-col items-center justify-center text-white p-6 text-center animate-in fade-in duration-300">
            <div class="bg-white p-8 rounded-[3rem] shadow-2xl border-8 border-sky-400 max-w-sm w-full transform hover:scale-105 transition-transform">
                <div class="text-8xl mb-4 animate-bounce">🥗</div>
                <h1 class="text-3xl font-black mb-2 text-sky-600">PANEN SEHAT</h1>
                <div class="bg-sky-50 p-4 rounded-2xl mb-6">
                    <p class="text-sky-800 font-bold text-sm leading-relaxed">
                        Tekan Tombol Kiri/Kanan <br>atau Panah di Keyboard ⬅️➡️<br>
                        Tangkap <span class="text-xl">🍎</span> Hindari <span class="text-xl">🦠</span>
                    </p>
                </div>
                <button @click="startGame" class="w-full bg-gradient-to-b from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white text-xl font-black py-4 rounded-2xl shadow-[0_6px_0_#15803d] active:shadow-none active:translate-y-1 transition-all">
                    MULAI MAIN ▶
                </button>
                <button @click="exitGame" class="mt-6 text-sm text-gray-400 hover:text-gray-600 font-bold underline">Kembali ke Dashboard</button>
            </div>
        </div>

        <div v-if="isFinished" class="absolute inset-0 z-50 bg-black/70 backdrop-blur-md flex flex-col items-center justify-center text-white p-6 text-center animate-in zoom-in duration-300">
            <div class="bg-white p-8 rounded-[3rem] shadow-2xl border-8 border-yellow-400 max-w-sm w-full relative overflow-hidden">
                <div class="text-8xl mb-2">{{ score > 200 ? '🏆' : (score > 100 ? '🌟' : '😅') }}</div>
                <h2 class="text-4xl font-black mb-1 text-gray-800">SELESAI!</h2>
                <div class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-b from-yellow-400 to-orange-500 mb-8 drop-shadow-sm filter">
                    {{ score }}
                </div>
                <div class="flex gap-3 w-full">
                    <button @click="exitGame" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3 rounded-2xl border-b-4 border-gray-300 active:border-b-0 active:translate-y-1">Keluar</button>
                    <button @click="startGame" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-2xl shadow-[0_4px_0_#1d4ed8] active:shadow-none active:translate-y-1 border-b-4 border-blue-700 active:border-b-0">Main Lagi 🔄</button>
                </div>
            </div>
        </div>

        <div class="absolute inset-0 w-full h-full pointer-events-none">
            <div v-for="item in items" :key="item.id" 
                class="absolute text-5xl will-change-transform filter drop-shadow-md z-10"
                :style="{ transform: `translate(${item.x}vw, ${item.y}vh)` }">
                {{ item.content }}
            </div>

            <div class="absolute bottom-6 text-8xl will-change-transform drop-shadow-2xl z-20 transition-transform duration-100 ease-out"
                :style="{ left: `${playerX}%`, transform: `translateX(-50%) rotate(${playerRotation}deg)` }">
                {{ PLAYER_EMOJI }}
            </div>
        </div>

        <div v-if="isPlaying" class="absolute inset-0 z-40 flex pointer-events-auto">
            <div 
                @mousedown="startMoveLeft" @mouseup="stopMoveLeft" @mouseleave="stopMoveLeft"
                @touchstart.prevent="startMoveLeft" @touchend.prevent="stopMoveLeft"
                class="w-1/2 h-full flex items-center justify-start pl-4 cursor-pointer active:bg-white/10 transition-colors group tap-highlight-transparent">
                <div class="bg-white/40 backdrop-blur-sm p-6 rounded-full shadow-lg transform transition-all group-active:scale-90 opacity-60">
                    <span class="text-4xl text-sky-700">⬅️</span>
                </div>
            </div>
            <div 
                @mousedown="startMoveRight" @mouseup="stopMoveRight" @mouseleave="stopMoveRight"
                @touchstart.prevent="startMoveRight" @touchend.prevent="stopMoveRight"
                class="w-1/2 h-full flex items-center justify-end pr-4 cursor-pointer active:bg-white/10 transition-colors group tap-highlight-transparent">
                <div class="bg-white/40 backdrop-blur-sm p-6 rounded-full shadow-lg transform transition-all group-active:scale-90 opacity-60">
                    <span class="text-4xl text-sky-700">➡️</span>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.shake-anim { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }
@keyframes shake {
  10%, 90% { transform: translate3d(-2px, 0, 0); }
  20%, 80% { transform: translate3d(4px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-8px, 0, 0); }
  40%, 60% { transform: translate3d(8px, 0, 0); }
}
/* Hilangkan highlight biru di mobile saat tap */
.tap-highlight-transparent { -webkit-tap-highlight-color: transparent; }
</style>