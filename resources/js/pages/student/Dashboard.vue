<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    student: Object,
    availableBadges: Array,
    monthlyProgress: Object, // Data dari controller
    currentDate: String,
    todaysMission: Object // Data checklist misi harian
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
    for (let i = 0; i < startDay; i++) {
        days.push({ date: null });
    }
    for (let i = 1; i <= daysInMonth; i++) {
        const dateString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
            date: i,
            fullDate: dateString,
            activities: props.monthlyProgress[dateString] || []
        });
    }
    return days;
});

// --- PROGRESS BAR ---
const nextBadge = computed(() => {
    return props.availableBadges.find(b => props.student.total_points < b.required_points) || props.availableBadges[props.availableBadges.length - 1];
});
const progressPercent = computed(() => {
    return Math.min((props.student.total_points / nextBadge.value.required_points) * 100, 100);
});

// Navigasi ke Timer (Aktivitas Harian)
const mulaiMain = (tipe) => {
    router.get(`/timer/${props.student.id}/${tipe}`);
};

// Navigasi ke Kuis (Tantangan)
const mulaiKuis = (tipe) => {
    router.get(`/quiz/${props.student.id}/${tipe}`);
};
</script>

<template>
    <Head title="Petualangan Sehat" />

    <div class="min-h-screen bg-[#F0F9FF] font-['Comic_Sans_MS',_sans-serif] text-[#1E293B] relative overflow-hidden">
        
        <div class="absolute top-0 left-0 text-[200px] opacity-5 -translate-x-1/2 -translate-y-1/2 pointer-events-none">☀️</div>
        <div class="absolute top-1/2 right-0 text-[150px] opacity-5 translate-x-1/2 pointer-events-none">☁️</div>

        <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 px-6 py-3 flex justify-between items-center border-b border-sky-100">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎒</span>
                <span class="font-black text-sky-600 text-lg">Petualangan Sehat</span>
            </div>
            <Link href="/" method="post" as="button" class="text-red-400 font-bold text-sm hover:text-red-600 hover:bg-red-50 px-3 py-1 rounded-full transition-colors">
                Keluar 🏃‍♂️
            </Link>
        </nav>

        <div class="p-4 md:p-8 max-w-6xl mx-auto space-y-10">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-6 md:p-8 shadow-xl border-4 border-white flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-sky-100 w-32 h-32 rounded-bl-[100%] -mr-10 -mt-10 opacity-50"></div>
                    
                    <div class="relative w-28 h-28 shrink-0">
                        <div class="w-full h-full bg-gradient-to-br from-yellow-300 to-orange-400 rounded-full border-[6px] border-white shadow-lg flex items-center justify-center text-6xl transform hover:scale-105 transition-transform cursor-pointer">
                            {{ student.gender === 'P' ? '👧' : '👦' }}
                        </div>
                        <div class="absolute bottom-0 right-0 bg-white text-xs font-black px-2 py-1 rounded-full shadow border border-gray-100">
                            Kelas {{ student.class }}
                        </div>
                    </div>
                    
                    <div class="flex-1 text-center md:text-left w-full">
                        <h1 class="text-3xl md:text-4xl font-black text-sky-600 mb-2">Hai, {{ student.name }}! 👋</h1>
                        <p class="text-gray-500 font-bold mb-4">Sudah siap mengalahkan kuman hari ini?</p>
                        
                        <div class="relative w-full bg-gray-100 h-6 rounded-full overflow-hidden border border-gray-200 shadow-inner">
                            <div class="bg-gradient-to-r from-sky-400 via-blue-400 to-indigo-500 h-full transition-all duration-1000 flex items-center justify-end pr-2" :style="{ width: progressPercent + '%' }">
                                <span class="text-[10px] text-white font-black animate-pulse" v-if="progressPercent > 10">{{ Math.round(progressPercent) }}%</span>
                            </div>
                        </div>
                        <div class="flex justify-between mt-2 text-xs font-bold">
                            <span class="text-gray-400">Mulai</span>
                            <span class="text-sky-500 uppercase tracking-widest">Target: {{ nextBadge.name }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-400 to-pink-500 rounded-[2.5rem] p-6 shadow-xl text-white flex flex-col items-center justify-center border-4 border-white/20 relative overflow-hidden group hover:scale-[1.02] transition-transform">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <span class="text-sm font-black uppercase opacity-80 tracking-widest mb-2">Total Bintang</span>
                    <div class="flex items-center gap-2">
                        <span class="text-6xl font-black drop-shadow-md">{{ student.total_points }}</span>
                        <span class="text-4xl animate-bounce delay-75">🌟</span>
                    </div>
                    <p class="text-xs font-bold mt-2 opacity-90 bg-white/20 px-3 py-1 rounded-full">Kumpulkan lebih banyak!</p>
                </div>
            </div>

            <div v-if="todaysMission">
                <h3 class="text-2xl font-black text-gray-700 mb-6 flex items-center gap-2">
                    <span>📋</span> Misi Hari Ini
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div @click="mulaiMain('brushing')" 
                        class="relative rounded-[2rem] p-4 flex items-center gap-4 border-4 transition-all duration-300 cursor-pointer group shadow-sm"
                        :class="todaysMission.brushing 
                            ? 'bg-green-100 border-green-300' 
                            : 'bg-white border-gray-100 hover:border-blue-200 hover:shadow-md'">
                        
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl border-4 shadow-sm transition-transform group-hover:scale-110"
                             :class="todaysMission.brushing ? 'bg-green-500 border-white rotate-12' : 'bg-gray-100 border-gray-50'">
                            {{ todaysMission.brushing ? '✅' : '🪥' }}
                        </div>

                        <div class="flex-1">
                            <h4 class="text-lg font-black" 
                                :class="todaysMission.brushing ? 'text-green-700' : 'text-gray-700'">
                                Sikat Gigi Pagi/Malam
                            </h4>
                            <p class="text-xs font-bold" 
                                :class="todaysMission.brushing ? 'text-green-600' : 'text-gray-400'">
                                {{ todaysMission.brushing ? 'Hebat! Sudah selesai.' : 'Yuk, sikat gigimu sekarang!' }}
                            </p>
                        </div>

                        <div v-if="!todaysMission.brushing" class="bg-blue-500 text-white px-4 py-2 rounded-full text-xs font-black shadow-blue-200 shadow-lg group-hover:bg-blue-600">
                            MULAI ▶
                        </div>
                    </div>

                    <div @click="mulaiMain('handwashing')" 
                        class="relative rounded-[2rem] p-4 flex items-center gap-4 border-4 transition-all duration-300 cursor-pointer group shadow-sm"
                        :class="todaysMission.handwashing 
                            ? 'bg-green-100 border-green-300' 
                            : 'bg-white border-gray-100 hover:border-green-200 hover:shadow-md'">
                        
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl border-4 shadow-sm transition-transform group-hover:scale-110"
                             :class="todaysMission.handwashing ? 'bg-green-500 border-white rotate-12' : 'bg-gray-100 border-gray-50'">
                            {{ todaysMission.handwashing ? '✅' : '🧼' }}
                        </div>

                        <div class="flex-1">
                            <h4 class="text-lg font-black" 
                                :class="todaysMission.handwashing ? 'text-green-700' : 'text-gray-700'">
                                Cuci Tangan Pakai Sabun
                            </h4>
                            <p class="text-xs font-bold" 
                                :class="todaysMission.handwashing ? 'text-green-600' : 'text-gray-400'">
                                {{ todaysMission.handwashing ? 'Tanganmu sudah bersih!' : 'Kuman pergi jauh-jauh!' }}
                            </p>
                        </div>

                        <div v-if="!todaysMission.handwashing" class="bg-green-500 text-white px-4 py-2 rounded-full text-xs font-black shadow-green-200 shadow-lg group-hover:bg-green-600">
                            MULAI ▶
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[3rem] p-8 shadow-xl border-4 border-sky-50 relative">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-sky-100 text-sky-600 px-6 py-2 rounded-full font-black border-4 border-white shadow-sm">
                    📅 Jurnal Kebersihan
                </div>

                <div class="flex items-center justify-between mb-8 mt-4">
                    <h2 class="text-2xl font-black text-gray-700">{{ currentMonthName }}</h2>
                    <div class="text-sm font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-lg border">
                        Hari ini: <span class="text-sky-600">{{ currentDate }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-2 md:gap-4 text-center">
                    <div v-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']" :key="day" 
                        class="font-black text-gray-400 uppercase text-xs md:text-sm">
                        {{ day }}
                    </div>

                    <div v-for="(day, index) in calendarDays" :key="index"
                        class="relative aspect-square rounded-2xl flex flex-col items-center justify-center border-2 transition-all duration-300 group"
                        :class="[
                            day.date ? 'bg-[#F8FAFC] border-slate-100 hover:border-sky-200' : 'bg-transparent border-transparent',
                            day.fullDate === currentDate ? 'ring-4 ring-orange-400 ring-offset-2 z-10 shadow-lg' : ''
                        ]"
                    >
                        <span v-if="day.date" class="text-sm md:text-lg font-bold text-gray-500 mb-1 group-hover:text-sky-600">{{ day.date }}</span>
                        
                        <div v-if="day.date" class="flex gap-0.5 md:gap-1">
                            <span v-if="day.activities.includes('brushing')" class="text-base md:text-xl drop-shadow-sm" title="Sudah Sikat Gigi">🪥</span>
                            <span v-if="day.activities.includes('handwashing')" class="text-base md:text-xl drop-shadow-sm" title="Sudah Cuci Tangan">🧼</span>
                        </div>

                        <div v-if="day.date && day.activities.length === 0 && day.fullDate < currentDate" 
                             class="absolute bottom-1 md:bottom-2 text-gray-300 text-[10px] md:text-xs font-bold opacity-50">
                             ❌
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-2xl font-black text-gray-700 mb-6 flex items-center gap-2">
                    <span>⏰</span> Waktunya Beraksi!
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div @click="mulaiMain('brushing')" 
                        class="group relative bg-white rounded-[2.5rem] p-8 shadow-lg border-b-[8px] border-blue-400 cursor-pointer hover:-translate-y-2 transition-all duration-300 active:border-b-0 active:translate-y-0">
                        <div class="absolute top-4 right-4 bg-blue-100 text-blue-600 w-12 h-12 rounded-full flex items-center justify-center font-black text-xl border-2 border-white shadow-sm">1</div>
                        <div class="text-[80px] mb-4 group-hover:scale-110 transition-transform origin-left">🪥</div>
                        <h2 class="text-3xl font-black text-blue-600 mb-2">Sikat Gigi</h2>
                        <p class="text-gray-400 font-bold mb-6 text-sm">Basmi kuman gigi dalam 2 menit!</p>
                        <div class="bg-blue-500 text-white text-center text-lg font-black py-3 rounded-2xl shadow-blue-200 shadow-lg group-hover:bg-blue-600 transition-colors">
                            MULAI SIKAT ▶
                        </div>
                    </div>

                    <div @click="mulaiMain('handwashing')" 
                        class="group relative bg-white rounded-[2.5rem] p-8 shadow-lg border-b-[8px] border-green-400 cursor-pointer hover:-translate-y-2 transition-all duration-300 active:border-b-0 active:translate-y-0">
                        <div class="absolute top-4 right-4 bg-green-100 text-green-600 w-12 h-12 rounded-full flex items-center justify-center font-black text-xl border-2 border-white shadow-sm">2</div>
                        <div class="text-[80px] mb-4 group-hover:scale-110 transition-transform origin-left">🧼</div>
                        <h2 class="text-3xl font-black text-green-600 mb-2">Cuci Tangan</h2>
                        <p class="text-gray-400 font-bold mb-6 text-sm">Gosok tanganmu pakai sabun!</p>
                        <div class="bg-green-500 text-white text-center text-lg font-black py-3 rounded-2xl shadow-green-200 shadow-lg group-hover:bg-green-600 transition-colors">
                            MULAI CUCI ▶
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-2xl font-black text-gray-700 mb-6 flex items-center gap-2">
                    <span>🧩</span> Tantangan Kuesioner
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div @click="mulaiKuis('brushing')" 
                        class="bg-indigo-50 hover:bg-indigo-100 cursor-pointer p-6 rounded-[2rem] border-2 border-indigo-100 hover:border-indigo-300 transition-all group text-center">
                        <div class="text-5xl mb-3 group-hover:scale-125 transition-transform duration-300">🦷</div>
                        <h4 class="text-xl font-black text-indigo-700 mb-1">Kuis Gigi</h4>
                        <p class="text-indigo-400 text-xs font-bold">Seberapa tahu kamu soal gigi?</p>
                    </div>

                    <div @click="mulaiKuis('handwashing')" 
                        class="bg-teal-50 hover:bg-teal-100 cursor-pointer p-6 rounded-[2rem] border-2 border-teal-100 hover:border-teal-300 transition-all group text-center">
                        <div class="text-5xl mb-3 group-hover:scale-125 transition-transform duration-300">🧼</div>
                        <h4 class="text-xl font-black text-teal-700 mb-1">Kuis Tangan</h4>
                        <p class="text-teal-400 text-xs font-bold">Lawan kuman dengan ilmu!</p>
                    </div>

                    <div @click="mulaiKuis('reproductive')" 
                        class="bg-pink-50 hover:bg-pink-100 cursor-pointer p-6 rounded-[2rem] border-2 border-pink-100 hover:border-pink-300 transition-all group text-center">
                        <div class="text-5xl mb-3 group-hover:scale-125 transition-transform duration-300">❤️</div>
                        <h4 class="text-xl font-black text-pink-700 mb-1">Kuis Tubuhku</h4>
                        <p class="text-pink-400 text-xs font-bold">Kenali dan jaga tubuhmu.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border-4 border-yellow-100 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 text-[150px] opacity-5 pointer-events-none">🏆</div>
                
                <h3 class="text-2xl font-black text-yellow-600 mb-8 flex items-center gap-3 relative z-10">
                    <span>🏆</span> Koleksi Lencana
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 relative z-10">
                    <div v-for="badge in availableBadges" :key="badge.id" 
                        class="flex flex-col items-center group transition-all duration-300"
                        :class="student.total_points < badge.required_points ? 'opacity-40 grayscale blur-[1px] hover:blur-0' : 'hover:-translate-y-2'">
                        
                        <div class="w-20 h-20 md:w-24 md:h-24 bg-white rounded-full shadow-md border-4 border-gray-50 flex items-center justify-center p-3 mb-3 group-hover:shadow-xl transition-all"
                             :class="student.total_points >= badge.required_points ? 'border-yellow-300' : ''">
                            <img :src="`/storage/${badge.image_path}`" class="w-full h-full object-contain">
                        </div>
                        
                        <span class="font-black text-gray-600 text-xs md:text-sm text-center px-2">{{ badge.name }}</span>
                        
                        <span v-if="student.total_points < badge.required_points" 
                            class="mt-1 text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                            Butuh {{ badge.required_points }}
                        </span>
                        <span v-else class="mt-1 text-[10px] font-black text-green-500 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">
                            TERBUKA!
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-center text-gray-400 text-xs font-bold tracking-widest uppercase pb-4">
                KKN 2026 KELOMPOK 8 UMPKU • Generasi Sehat Cerdas
            </div>

        </div>
    </div>
</template>