<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import confetti from 'canvas-confetti';

const props = defineProps({
    student: Object,
    availableBadges: Array,
    monthlyProgress: Object, 
    currentDate: String,
    todaysMission: Object 
});

// --- LOGIKA KALENDER ---
const today = new Date();
const currentMonthName = today.toLocaleString('id-ID', { month: 'long', year: 'numeric' });

const getDaysInMonth = (year, month) => new Date(year, month + 1, 0).getDate();
const getFirstDayOfMonth = (year, month) => new Date(year, month, 1).getDay(); // 0 = Minggu

const daysInMonth = getDaysInMonth(today.getFullYear(), today.getMonth());
const startDay = getFirstDayOfMonth(today.getFullYear(), today.getMonth());

const calendarDays = computed(() => {
    let days = [];
    for (let i = 0; i < startDay; i++) { days.push({ date: null }); }
    for (let i = 1; i <= daysInMonth; i++) {
        const dateString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        const activities = props.monthlyProgress[dateString] || [];
        
        let status = 'empty';
        if (activities.length === 2) status = 'perfect';
        else if (activities.length === 1) status = 'partial';
        else if (dateString < props.currentDate && activities.length === 0) status = 'missed';

        days.push({ date: i, fullDate: dateString, activities: activities, status: status });
    }
    return days;
});

const monthlyStats = computed(() => {
    let brushing = 0, handwashing = 0, perfectDays = 0;
    Object.values(props.monthlyProgress).forEach(acts => {
        if (acts.includes('brushing')) brushing++;
        if (acts.includes('handwashing')) handwashing++;
        if (acts.length === 2) perfectDays++;
    });
    return { brushing, handwashing, perfectDays };
});

// --- STATE MODAL ---
const showGuide = ref(false);
const showNutritionModal = ref(false);
const showBodyModal = ref(false); 
const showVideoModal = ref(false); // <--- State Modal Video
const activeVideo = ref(''); // <--- Video yang sedang diputar

// Daftar Video Edukasi
const videoList = [
    { title: 'Cara Sikat Gigi', url: 'https://ik.imagekit.io/w1beqfjn8/Animation%20Gosok%20Gigi-KKN.mp4', icon: '🪥', color: 'bg-blue-50 text-blue-700 border-blue-100' },
    { title: 'Cuci Tangan Pakai Sabun', url: 'https://ik.imagekit.io/w1beqfjn8/Animasi%20AI%20Cuci%20Tangan.mp4', icon: '🧼', color: 'bg-green-50 text-green-700 border-green-100' },
    { title: 'Jaga Tubuhmu', url: 'https://ik.imagekit.io/w1beqfjn8/Animasi%20AI%20Kespro.mp4', icon: '🛡️', color: 'bg-pink-50 text-pink-700 border-pink-100' }
];

const nextBadge = computed(() => {
    return props.availableBadges.find(b => props.student.total_points < b.required_points) || props.availableBadges[props.availableBadges.length - 1];
});
const progressPercent = computed(() => {
    return Math.min((props.student.total_points / nextBadge.value.required_points) * 100, 100);
});

// --- NAVIGASI & AKSI ---
const mulaiMain = (tipe) => { router.get(`/timer/${props.student.id}/${tipe}`); };
const mulaiKuis = (tipe) => { router.get(`/quiz/${props.student.id}/${tipe}`); };

const selesaiMembaca = async () => {
    // Fungsi ini dikosongkan karena Modal Tubuhku sekarang hanya tombol Close (X)
};
</script>

<template>
    <Head title="Petualangan Sehat" />

    <div class="min-h-screen bg-[#F0F9FF] font-['Comic_Sans_MS',_sans-serif] text-[#1E293B] relative overflow-x-hidden pb-24">
        
        <div class="fixed top-0 left-0 text-[150px] md:text-[200px] opacity-5 -translate-x-1/2 -translate-y-1/2 pointer-events-none">☀️</div>
        <div class="fixed top-1/2 right-0 text-[100px] md:text-[150px] opacity-5 translate-x-1/2 pointer-events-none">☁️</div>

        <nav class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-40 px-4 md:px-6 py-3 flex justify-between items-center border-b border-sky-100">
            <div class="flex items-center gap-2">
                <span class="text-xl md:text-2xl">🎒</span>
                <span class="font-black text-sky-600 text-base md:text-lg">Petualangan Sehat</span>
            </div>
            <Link href="/" method="post" as="button" class="text-red-400 font-bold text-xs md:text-sm hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-full transition-colors border border-red-100">
                Keluar 🏃‍♂️
            </Link>
        </nav>

        <div class="p-4 md:p-8 max-w-6xl mx-auto space-y-6 md:space-y-10 relative z-10">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                <div class="lg:col-span-2 bg-white rounded-[2rem] p-5 md:p-8 shadow-xl border-4 border-white flex flex-col sm:flex-row items-center gap-4 md:gap-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-sky-100 w-24 h-24 rounded-bl-[100%] -mr-8 -mt-8 opacity-50"></div>
                    <div class="relative w-24 h-24 md:w-28 md:h-28 shrink-0">
                        <div class="w-full h-full bg-gradient-to-br from-yellow-300 to-orange-400 rounded-full border-[5px] border-white shadow-lg flex items-center justify-center text-5xl md:text-6xl transform hover:scale-105 transition-transform">
                            {{ student.gender === 'P' ? '👧' : '👦' }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 bg-white text-[10px] md:text-xs font-black px-2 py-1 rounded-full shadow border border-gray-100">
                            Kelas {{ student.class }}
                        </div>
                    </div>
                    <div class="flex-1 text-center sm:text-left w-full">
                        <h1 class="text-2xl md:text-4xl font-black text-sky-600 mb-1 md:mb-2">Hai, {{ student.name }}! 👋</h1>
                        <p class="text-gray-400 font-bold mb-3 md:mb-4 text-xs md:text-base">Siap basmi kuman hari ini?</p>
                        <div class="relative w-full bg-gray-100 h-5 md:h-6 rounded-full overflow-hidden border border-gray-200 shadow-inner">
                            <div class="bg-gradient-to-r from-sky-400 via-blue-400 to-indigo-500 h-full transition-all duration-1000 flex items-center justify-end pr-2" :style="{ width: progressPercent + '%' }">
                                <span class="text-[9px] md:text-[10px] text-white font-black animate-pulse" v-if="progressPercent > 10">{{ Math.round(progressPercent) }}%</span>
                            </div>
                        </div>
                        <div class="flex justify-between mt-1.5 text-[10px] md:text-xs font-bold">
                            <span class="text-gray-400">Mulai</span>
                            <span class="text-sky-500 uppercase tracking-widest truncate max-w-[150px]">Target: {{ nextBadge.name }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-400 to-pink-500 rounded-[2rem] p-5 md:p-6 shadow-xl text-white flex flex-row lg:flex-col items-center justify-between lg:justify-center border-4 border-white/20 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex flex-col items-start lg:items-center">
                        <span class="text-xs md:text-sm font-black uppercase opacity-80 tracking-widest mb-1">Total Bintang</span>
                        <div class="flex items-center gap-2">
                            <span class="text-4xl md:text-6xl font-black drop-shadow-md">{{ student.total_points }}</span>
                        </div>
                    </div>
                    <div class="text-4xl md:text-5xl animate-bounce delay-75">🌟</div>
                </div>
            </div>

            <div v-if="todaysMission">
                <h3 class="text-lg md:text-2xl font-black text-gray-700 mb-4 flex items-center gap-2 px-1">
                    <span>📋</span> Misi Hari Ini
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div @click="mulaiMain('brushing')" 
                        class="relative rounded-[2rem] p-4 md:p-5 flex items-center gap-3 md:gap-4 border-4 transition-all duration-200 cursor-pointer group shadow-sm active:scale-95"
                        :class="todaysMission.brushing ? 'bg-green-50 border-green-200' : 'bg-white border-blue-50 hover:border-blue-200 shadow-md'">
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center text-2xl md:text-3xl border-4 shadow-sm shrink-0 transition-transform group-hover:scale-110"
                             :class="todaysMission.brushing ? 'bg-green-500 border-white text-white' : 'bg-blue-100 border-white'">
                            {{ todaysMission.brushing ? '✅' : '🪥' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base md:text-lg font-black truncate" :class="todaysMission.brushing ? 'text-green-700' : 'text-gray-700'">Sikat Gigi</h4>
                            <p class="text-[10px] md:text-xs font-bold truncate" :class="todaysMission.brushing ? 'text-green-600' : 'text-gray-400'">
                                {{ todaysMission.brushing ? 'Hebat! Sudah selesai.' : 'Yuk, sikat gigimu sekarang!' }}
                            </p>
                        </div>
                        <div v-if="!todaysMission.brushing" class="bg-blue-500 text-white px-3 py-1.5 rounded-full text-[10px] md:text-xs font-black shadow-blue-200 shadow-lg shrink-0">MULAI ▶</div>
                    </div>

                    <div @click="mulaiMain('handwashing')" 
                        class="relative rounded-[2rem] p-4 md:p-5 flex items-center gap-3 md:gap-4 border-4 transition-all duration-200 cursor-pointer group shadow-sm active:scale-95"
                        :class="todaysMission.handwashing ? 'bg-green-50 border-green-200' : 'bg-white border-green-50 hover:border-green-200 shadow-md'">
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center text-2xl md:text-3xl border-4 shadow-sm shrink-0 transition-transform group-hover:scale-110"
                             :class="todaysMission.handwashing ? 'bg-green-500 border-white text-white' : 'bg-green-100 border-white'">
                            {{ todaysMission.handwashing ? '✅' : '🧼' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base md:text-lg font-black truncate" :class="todaysMission.handwashing ? 'text-green-700' : 'text-gray-700'">Cuci Tangan</h4>
                            <p class="text-[10px] md:text-xs font-bold truncate" :class="todaysMission.handwashing ? 'text-green-600' : 'text-gray-400'">
                                {{ todaysMission.handwashing ? 'Tanganmu sudah bersih!' : 'Kuman pergi jauh-jauh!' }}
                            </p>
                        </div>
                        <div v-if="!todaysMission.handwashing" class="bg-green-500 text-white px-3 py-1.5 rounded-full text-[10px] md:text-xs font-black shadow-green-200 shadow-lg shrink-0">MULAI ▶</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-5 md:p-8 shadow-xl border-4 border-sky-50 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <h2 class="text-lg md:text-2xl font-black text-gray-700 flex items-center gap-2">
                        📅 Jurnal Kegiatan <span class="text-sky-500">{{ currentMonthName }}</span>
                    </h2>
                    <div class="text-[10px] md:text-sm font-bold text-gray-400 bg-gray-50 px-3 py-1.5 rounded-lg border mt-2 md:mt-0">
                        Hari ini: <span class="text-sky-600">{{ currentDate }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 md:gap-4 mb-6">
                    <div class="bg-blue-50 rounded-2xl p-3 text-center border border-blue-100">
                        <div class="text-xl md:text-2xl mb-1">🪥</div>
                        <div class="text-blue-700 font-black text-lg md:text-xl">{{ monthlyStats.brushing }}x</div>
                        <div class="text-[9px] md:text-xs text-blue-400 font-bold uppercase">Sikat Gigi</div>
                    </div>
                    <div class="bg-green-50 rounded-2xl p-3 text-center border border-green-100">
                        <div class="text-xl md:text-2xl mb-1">🧼</div>
                        <div class="text-green-700 font-black text-lg md:text-xl">{{ monthlyStats.handwashing }}x</div>
                        <div class="text-[9px] md:text-xs text-green-400 font-bold uppercase">Cuci Tangan</div>
                    </div>
                    <div class="bg-orange-50 rounded-2xl p-3 text-center border border-orange-100">
                        <div class="text-xl md:text-2xl mb-1">🏆</div>
                        <div class="text-orange-700 font-black text-lg md:text-xl">{{ monthlyStats.perfectDays }}</div>
                        <div class="text-[9px] md:text-xs text-orange-400 font-bold uppercase">Hari Sempurna</div>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1 md:gap-3 text-center mb-4">
                    <div v-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']" :key="day" 
                        class="font-black text-gray-400 uppercase text-[10px] md:text-sm py-2">
                        {{ day }}
                    </div>

                    <div v-for="(day, index) in calendarDays" :key="index"
                        class="relative aspect-square rounded-xl md:rounded-2xl flex flex-col items-center justify-center border transition-all duration-300"
                        :class="[
                            day.status === 'perfect' ? 'bg-green-100 border-green-200' : 
                            day.status === 'partial' ? 'bg-yellow-50 border-yellow-200' :
                            day.status === 'missed' ? 'bg-gray-100 border-gray-200 opacity-70' :
                            day.date ? 'bg-white border-slate-100' : 'bg-transparent border-transparent',

                            day.fullDate === currentDate ? 'ring-4 ring-orange-400 ring-offset-2 z-10 shadow-lg' : ''
                        ]"
                    >
                        <span v-if="day.date" class="text-xs md:text-base font-black mb-0.5"
                            :class="day.status === 'missed' ? 'text-gray-400' : 'text-gray-600'">
                            {{ day.date }}
                        </span>
                        
                        <div v-if="day.date" class="flex gap-0.5 items-center justify-center h-4 md:h-6">
                            <div v-if="day.activities.includes('brushing')" class="text-[10px] md:text-lg" title="Sikat Gigi OK">🪥</div>
                            <div v-else-if="day.status !== 'empty' && day.status !== 'missed'" class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-gray-200"></div>

                            <div v-if="day.activities.includes('handwashing')" class="text-[10px] md:text-lg" title="Cuci Tangan OK">🧼</div>
                            <div v-else-if="day.status !== 'empty' && day.status !== 'missed'" class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-gray-200"></div>

                            <div v-if="day.status === 'missed'" class="text-[10px] md:text-sm text-gray-400 font-bold">❌</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 justify-center pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 md:w-4 md:h-4 bg-green-100 border border-green-300 rounded-full"></div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500">Lengkap</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 md:w-4 md:h-4 bg-yellow-50 border border-yellow-300 rounded-full"></div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500">Kurang</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 md:w-4 md:h-4 bg-gray-100 border border-gray-300 rounded-full"></div>
                        <span class="text-[10px] md:text-xs font-bold text-gray-500">Bolong</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg md:text-2xl font-black text-gray-700 mb-4 flex items-center gap-2 px-1">
                    <span>🧩</span> Zona Pintar
                </h3>
                <div class="flex overflow-x-auto gap-4 pb-4 px-2 snap-x snap-mandatory md:grid md:grid-cols-4 md:overflow-visible">
                    
                    <div @click="mulaiKuis('brushing')" class="min-w-[160px] md:min-w-0 flex-1 snap-center bg-indigo-50 active:bg-indigo-100 cursor-pointer p-5 md:p-6 rounded-[2rem] border-2 border-indigo-100 active:scale-95 transition-transform text-center shadow-sm">
                        <div class="text-4xl md:text-5xl mb-3">🦷</div>
                        <h4 class="text-base md:text-xl font-black text-indigo-700 mb-1">Kuis Gigi</h4>
                        <p class="text-indigo-400 text-[10px] md:text-xs font-bold">Cek ilmu gigimu!</p>
                    </div>

                    <div @click="mulaiKuis('handwashing')" class="min-w-[160px] md:min-w-0 flex-1 snap-center bg-teal-50 active:bg-teal-100 cursor-pointer p-5 md:p-6 rounded-[2rem] border-2 border-teal-100 active:scale-95 transition-transform text-center shadow-sm">
                        <div class="text-4xl md:text-5xl mb-3">🧼</div>
                        <h4 class="text-base md:text-xl font-black text-teal-700 mb-1">Kuis Tangan</h4>
                        <p class="text-teal-400 text-[10px] md:text-xs font-bold">Lawan kuman!</p>
                    </div>

                    <div @click="showVideoModal = true" class="min-w-[160px] md:min-w-0 flex-1 snap-center bg-red-50 active:bg-red-100 cursor-pointer p-5 md:p-6 rounded-[2rem] border-2 border-red-100 active:scale-95 transition-transform text-center shadow-sm">
                        <div class="text-4xl md:text-5xl mb-3">🎬</div>
                        <h4 class="text-base md:text-xl font-black text-red-700 mb-1">Video Seru</h4>
                        <p class="text-red-400 text-[10px] md:text-xs font-bold">Nonton animasi!</p>
                    </div>

                    <div @click="showBodyModal = true" class="min-w-[160px] md:min-w-0 flex-1 snap-center bg-pink-50 active:bg-pink-100 cursor-pointer p-5 md:p-6 rounded-[2rem] border-2 border-pink-100 active:scale-95 transition-transform text-center shadow-sm">
                        <div class="text-4xl md:text-5xl mb-3">❤️</div>
                        <h4 class="text-base md:text-xl font-black text-pink-700 mb-1">Tubuhku</h4>
                        <p class="text-pink-400 text-[10px] md:text-xs font-bold">Jaga tubuhmu!</p>
                    </div>

                    <div @click="showNutritionModal = true" class="min-w-[160px] md:min-w-0 flex-1 snap-center bg-orange-50 active:bg-orange-100 cursor-pointer p-5 md:p-6 rounded-[2rem] border-2 border-orange-100 active:scale-95 transition-transform text-center shadow-sm">
                        <div class="text-4xl md:text-5xl mb-3">🍱</div>
                        <h4 class="text-base md:text-xl font-black text-orange-700 mb-1">Isi Piringku</h4>
                        <p class="text-orange-400 text-[10px] md:text-xs font-bold">Makan sehat yuk!</p>
                    </div>

                </div>
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <Link :href="`/game/${student.id}`" class="bg-red-50 p-6 rounded-[2rem] border-2 border-red-100 text-center shadow-sm hover:scale-105 transition-transform block decoration-0">
                        <div class="text-5xl mb-2 animate-pulse">🦠</div>
                        <h4 class="text-xl font-black text-red-700">Basmi Kuman</h4>
                        <p class="text-red-400 font-bold text-xs">Pukul kuman nakal!</p>
                    </Link>
                    <Link :href="`/game-food/${student.id}`" class="bg-green-50 p-6 rounded-[2rem] border-2 border-green-100 text-center shadow-sm hover:scale-105 transition-transform block decoration-0">
                        <div class="text-5xl mb-2 animate-bounce">🥗</div>
                        <h4 class="text-xl font-black text-green-700">Panen Sehat</h4>
                        <p class="text-green-400 font-bold text-xs">Tangkap buah segar!</p>
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-5 md:p-8 shadow-xl border-4 border-yellow-100 relative overflow-hidden">
                <h3 class="text-lg md:text-2xl font-black text-yellow-600 mb-4 flex items-center gap-2 relative z-10 px-1">
                    <span>🏆</span> Koleksi Lencana
                </h3>
                <div class="flex overflow-x-auto gap-4 pb-4 px-1 snap-x snap-mandatory md:grid md:grid-cols-4 lg:grid-cols-5 md:overflow-visible">
                    <div v-for="badge in availableBadges" :key="badge.id" 
                        class="flex flex-col items-center min-w-[100px] snap-center group transition-all duration-300"
                        :class="student.total_points < badge.required_points ? 'opacity-50 grayscale' : 'hover:-translate-y-1'">
                        <div class="w-20 h-20 md:w-24 md:h-24 bg-white rounded-full shadow-md border-4 border-gray-50 flex items-center justify-center p-3 mb-2 group-hover:shadow-lg transition-all"
                             :class="student.total_points >= badge.required_points ? 'border-yellow-300' : ''">
                            <img :src="`/storage/${badge.image_path}`" class="w-full h-full object-contain">
                        </div>
                        <span class="font-black text-gray-600 text-[10px] md:text-sm text-center px-1 leading-tight h-8 flex items-center">{{ badge.name }}</span>
                        <span v-if="student.total_points < badge.required_points" class="mt-1 text-[9px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full whitespace-nowrap">{{ badge.required_points }} ⭐</span>
                        <span v-else class="mt-1 text-[9px] font-black text-green-500 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">TERBUKA!</span>
                    </div>
                </div>
            </div>

            <div class="text-center text-gray-400 text-[10px] font-bold tracking-widest uppercase">
                KKN 2026 KELOMPOK 8 UMPKU • Generasi Sehat
            </div>
        </div>

        <button @click="showGuide = true" class="fixed bottom-6 right-6 z-40 bg-green-500 hover:bg-green-600 text-white w-14 h-14 md:w-16 md:h-16 rounded-full shadow-lg border-4 border-white flex items-center justify-center text-3xl md:text-4xl animate-bounce cursor-pointer active:scale-90 transition-transform" title="Bantuan">❔</button>

        <div v-if="showGuide" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showGuide = false"></div>
            <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-6 md:p-8 shadow-2xl border-4 border-green-100 relative animate-in fade-in zoom-in duration-300 max-h-[90vh] flex flex-col">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-2">📢</div>
                    <h2 class="text-2xl font-black text-green-600">Panduan Orang Tua</h2>
                    <p class="text-gray-500 font-bold text-xs mt-1">Sinergi Sekolah & Rumah untuk Generasi Sehat</p>
                </div>
                <div class="overflow-y-auto custom-scrollbar space-y-4 pr-2 flex-1 text-sm text-gray-700">
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                        <div class="flex items-center gap-3 mb-2"><span class="text-2xl">📅</span><h4 class="font-black text-blue-800">Monitoring Konsistensi Harian</h4></div>
                        <p class="leading-relaxed text-xs md:text-sm text-blue-900/80 text-justify">
                            Kalender aktivitas dirancang untuk membangun habituasi PHBS. Indikator warna membantu Anda memantau kedisiplinan anak:
                            <br>• <strong>Hijau (Lengkap):</strong> Anak telah menuntaskan seluruh misi kebersihan hari ini.
                            <br>• <strong>Kuning (Parsial):</strong> Ada aktivitas yang terlewat, mohon diingatkan.
                            <br>• <strong>Merah/Abu (Absen):</strong> Tidak ada aktivitas tercatat. Lakukan evaluasi bersama anak.
                        </p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-2xl border border-orange-100">
                        <div class="flex items-center gap-3 mb-2"><span class="text-2xl">🏆</span><h4 class="font-black text-orange-800">Sistem Reward & Motivasi</h4></div>
                        <p class="leading-relaxed text-xs md:text-sm text-orange-900/80 text-justify">
                            Lencana bukan sekadar hiasan, melainkan apresiasi atas ketekunan jangka panjang. Dorong anak untuk mencapai target poin guna membuka lencana baru. Ini mengajarkan konsep <em>delayed gratification</em> (menunda kesenangan demi hasil lebih besar).
                        </p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100">
                        <div class="flex items-center gap-3 mb-2"><span class="text-2xl">🤝</span><h4 class="font-black text-purple-800">Peran Aktif Orang Tua</h4></div>
                        <p class="leading-relaxed text-xs md:text-sm text-purple-900/80 text-justify">
                            Dampingi anak saat mengisi aktivitas, terutama saat mengunggah foto bukti sikat gigi atau cuci tangan. Validasi Anda adalah kunci kejujuran dan semangat mereka.
                        </p>
                    </div>
                </div>
                <button @click="showGuide = false" class="mt-6 w-full bg-green-500 hover:bg-green-600 text-white font-black py-3 rounded-2xl shadow-[0_4px_0_0_#15803d] active:translate-y-1 transition-all">SAYA MENGERTI</button>
            </div>
        </div>

        <div v-if="showNutritionModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showNutritionModal = false"></div>
            <div class="bg-white w-full max-w-md rounded-[2.5rem] p-6 md:p-8 shadow-2xl border-4 border-orange-100 relative animate-in fade-in zoom-in duration-300 max-h-[90vh] flex flex-col">
                <button @click="showNutritionModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl font-bold transition-colors z-10">✕</button>
                <div class="text-center mb-4"><div class="text-5xl mb-2">🍽️</div><h2 class="text-2xl font-black text-orange-600">Panduan Isi Piringku</h2><p class="text-xs text-gray-500 font-bold">Sekali Makan (Sumber: Kemenkes RI)</p></div>
                <div class="overflow-y-auto custom-scrollbar pr-2 flex-1 space-y-4">
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <h4 class="font-black text-orange-800 text-sm mb-2 border-b border-orange-200 pb-1">1/2 Piring: Makanan Pokok & Lauk</h4>
                        <div class="space-y-2"><div class="flex items-start gap-2 text-xs text-gray-700"><span class="text-lg">🍚</span><div><strong>Makanan Pokok (2/3 bagian):</strong> Nasi, Jagung, Ubi, Singkong. Sumber tenaga.</div></div><div class="flex items-start gap-2 text-xs text-gray-700"><span class="text-lg">🍗</span><div><strong>Lauk Pauk (1/3 bagian):</strong> Ikan, Ayam, Telur, Tahu, Tempe. Zat pembangun tubuh.</div></div></div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                        <h4 class="font-black text-green-800 text-sm mb-2 border-b border-green-200 pb-1">1/2 Piring: Sayur & Buah</h4>
                        <div class="space-y-2"><div class="flex items-start gap-2 text-xs text-gray-700"><span class="text-lg">🥦</span><div><strong>Sayuran (2/3 bagian):</strong> Bayam, Wortel, Kangkung. Kaya serat & vitamin.</div></div><div class="flex items-start gap-2 text-xs text-gray-700"><span class="text-lg">🍎</span><div><strong>Buah-buahan (1/3 bagian):</strong> Pisang, Pepaya, Jeruk. Penangkal penyakit alami.</div></div></div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 text-center"><p class="text-xs text-blue-800 font-bold">💧 Jangan Lupa: Minum Air Putih 8 Gelas/Hari, Cuci Tangan Pakai Sabun, & Rutin Olahraga!</p></div>
                </div>
            </div>
        </div>

        <div v-if="showBodyModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showBodyModal = false"></div>
            <div class="bg-white w-full max-w-md rounded-[2.5rem] p-6 md:p-8 shadow-2xl border-4 border-pink-100 relative animate-in fade-in zoom-in duration-300 max-h-[90vh] flex flex-col">
                <button @click="showBodyModal = false" class="absolute top-5 right-5 text-gray-400 hover:text-red-500 text-3xl font-black transition-colors z-50 bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-sm border border-gray-100">✕</button>
                <div class="text-center mb-4"><div class="text-5xl mb-2">🛡️</div><h2 class="text-2xl font-black text-pink-600">Jaga Tubuhku</h2><p class="text-gray-400 font-bold text-xs">Edukasi Kesehatan Reproduksi Dini</p></div>
                <div class="overflow-y-auto custom-scrollbar pr-2 flex-1 space-y-3 pb-4">
                    <div class="bg-pink-50 p-4 rounded-2xl border border-pink-100"><div class="flex items-center gap-2 mb-2"><span class="text-2xl">👙</span><h4 class="font-black text-pink-700 text-sm">Aturan Pakaian Dalam</h4></div><p class="text-xs text-gray-700 font-medium leading-relaxed text-justify">Bagian tubuh yang tertutup baju renang (Dada, Alat Kelamin, Pantat) adalah <strong>AREA PRIBADI</strong>. <br>🚫 <strong>DILARANG KERAS</strong> disentuh atau dilihat orang lain, kecuali:<br>1. Diri sendiri saat mandi.<br>2. Orang tua saat memandikan.<br>3. Dokter saat memeriksa (didampingi Orang Tua).</p></div>
                    <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100"><div class="flex items-center gap-2 mb-2"><span class="text-2xl">✋</span><h4 class="font-black text-purple-700 text-sm">Sentuhan Aman vs Tidak</h4></div><ul class="text-xs text-gray-700 font-medium space-y-2 ml-1 text-justify"><li>✅ <strong>Sentuhan Boleh:</strong> Bersalaman, 'Tos' dengan teman, Tepuk bahu semangat.</li><li>❌ <strong>Sentuhan TIDAK Boleh:</strong> Menyentuh area pribadi, memaksa mencium, atau meminta merahasiakan sentuhan.</li><li class="bg-white p-3 rounded-lg border border-purple-200 text-purple-700 font-bold text-center mt-2 shadow-sm">"Jika ada yang memaksa, KATAKAN TIDAK, LARI, dan LAPOR ke Guru/Ortu!"</li></ul></div>
                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100"><div class="flex items-center gap-2 mb-2"><span class="text-2xl">✨</span><h4 class="font-black text-blue-700 text-sm">Kebersihan Organ Reproduksi</h4></div><ul class="text-xs text-gray-700 font-medium space-y-1 ml-1 list-disc pl-3 text-justify"><li>Ganti celana dalam minimal 2x sehari (Pagi & Sore/setelah mandi).</li><li>Cara Cebok yang Benar: Basuh dari arah <strong>DEPAN ke BELAKANG</strong> (bukan sebaliknya) agar kuman dari anus tidak masuk ke alat kelamin.</li><li>Pastikan area kering (lap dengan tisu/handuk) sebelum memakai celana agar tidak berjamur.</li></ul></div>
                </div>
            </div>
        </div>

        <div v-if="showVideoModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" @click="showVideoModal = false"></div>
            <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-6 shadow-2xl relative animate-in fade-in zoom-in duration-300 border-4 border-sky-100">
                <button @click="showVideoModal = false; activeVideo = ''" class="absolute -top-12 right-0 md:-right-12 text-white text-4xl font-black hover:scale-110 transition-transform">✕</button>
                
                <div v-if="!activeVideo">
                    <h2 class="text-2xl font-black text-center mb-6 text-sky-600 uppercase tracking-wide">Pilih Video 🎥</h2>
                    <div class="space-y-3">
                        <button v-for="(vid, index) in videoList" :key="index" @click="activeVideo = vid.url" 
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
                    <button @click="activeVideo = ''" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-3 rounded-xl transition-colors">⬅️ PILIH VIDEO LAIN</button>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>