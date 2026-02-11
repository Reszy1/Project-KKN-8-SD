<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    stats: Object,
    recentActivities: Array,
    students: Array,
    classList: Array,
    analysis: Array // <--- DATA ANALISA DARI CONTROLLER
});

// --- STATE MANAGEMENT ---
const showClassModal = ref(false);
const showStudentModal = ref(false);
const showDetailModal = ref(false); 
const showImageModal = ref(false); 

const selectedClass = ref(props.stats.filter_active || '');
const selectedStudent = ref(null); 
const selectedImage = ref(''); 

const formClass = useForm({ name: '' });
const formStudent = useForm({
    name: '',
    gender: 'L',
    class_name: '',
    absent_no: '',
    password: '123'
});

// --- HELPER FUNCTIONS ---

// 1. Helper untuk Diagram Lingkaran (Analisa)
const calculateDashOffset = (score, radius) => {
    const circumference = 2 * Math.PI * radius;
    const value = score || 0;
    return circumference - (value / 100) * circumference;
};

const getScoreCategory = (score) => {
    if (!score) return { label: 'Belum Ada Data', color: 'text-gray-400' };
    if (score >= 80) return { label: 'Sangat Paham', color: 'text-green-600' };
    if (score >= 60) return { label: 'Cukup Paham', color: 'text-yellow-600' };
    return { label: 'Perlu Bimbingan', color: 'text-red-500' };
};

// 2. Helper Format Label Aktivitas
const formatActivityLabel = (type) => {
    if (type === 'brushing') return 'Sikat Gigi';
    if (type === 'handwashing') return 'Cuci Tangan';
    if (type === 'reproductive') return 'Kuis Tubuhku';
    return 'Aktivitas';
};

const formatActivityValue = (log) => {
    if (log.duration_seconds === null || log.duration_seconds === undefined) return '-';

    // Jika ada foto, berarti Timer (Menit & Detik)
    if (log.proof_image) {
        const minutes = Math.floor(log.duration_seconds / 60);
        const seconds = log.duration_seconds % 60;
        return `${minutes}m ${seconds}d`;
    } 
    
    // Jika tidak ada foto, berarti Kuis (Skor)
    const score = isNaN(log.duration_seconds) ? 0 : log.duration_seconds;
    return `Skor: ${score}`;
};

const getActivityColor = (type) => {
    if (type === 'brushing') return 'bg-blue-100 text-blue-700 border-blue-200';
    if (type === 'handwashing') return 'bg-green-100 text-green-700 border-green-200';
    if (type === 'reproductive') return 'bg-pink-100 text-pink-700 border-pink-200';
    return 'bg-gray-100 text-gray-700';
};

// Filter Log Khusus Siswa Terpilih (Untuk Modal Detail)
const studentHistory = computed(() => {
    if (!selectedStudent.value) return [];
    return props.recentActivities.filter(log => log.student_name === selectedStudent.value.name);
});

// --- ACTIONS ---

const openStudentDetail = (student) => {
    selectedStudent.value = student;
    showDetailModal.value = true;
};

const openImageZoom = (imageUrl) => {
    selectedImage.value = imageUrl;
    showImageModal.value = true;
};

const submitClass = () => {
    formClass.post('/teacher/class', {
        onSuccess: () => {
            showClassModal.value = false;
            formClass.reset();
        }
    });
};

const submitStudent = () => {
    formStudent.post('/teacher/student', {
        onSuccess: () => {
            showStudentModal.value = false;
            formStudent.reset();
        }
    });
};

const deleteClass = (id) => {
    if(confirm('Yakin hapus kelas ini? Data siswa di dalamnya juga akan terhapus.')) {
        router.delete(`/teacher/class/${id}`);
    }
};

const deleteStudent = (id) => {
    if(confirm('Yakin ingin mengeluarkan siswa ini? Data poin akan hilang.')) {
        router.delete(`/teacher/student/${id}`);
        showDetailModal.value = false; 
    }
};

const applyFilter = () => {
    router.get('/teacher/dashboard', { class_name: selectedClass.value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['stats', 'recentActivities', 'students', 'analysis']
    });
};

const getInitials = (name) => name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
</script>

<template>
    <Head title="Dashboard Guru" />

    <div class="min-h-screen bg-[#F8FAFC] font-sans text-gray-800 relative">
        
        <div v-if="showImageModal" class="fixed inset-0 z-[150] bg-black/90 flex items-center justify-center p-4 cursor-zoom-out" @click="showImageModal = false">
            <img :src="`/storage/${selectedImage}`" class="max-w-full max-h-screen rounded-lg shadow-2xl border-4 border-white">
            <button class="absolute top-4 right-4 text-white text-4xl">&times;</button>
        </div>

        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
            <div v-if="showDetailModal && selectedStudent" class="fixed inset-0 bg-black/40 z-[120] flex items-center justify-center p-4 backdrop-blur-sm">
                <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-white text-blue-600 flex items-center justify-center text-2xl font-bold shadow-lg">
                                {{ getInitials(selectedStudent.name) }}
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">{{ selectedStudent.name }}</h3>
                                <p class="opacity-90">Kelas {{ selectedStudent.class }} • No. Absen {{ selectedStudent.absent_no || '-' }}</p>
                            </div>
                        </div>
                        <button @click="showDetailModal = false" class="text-white/70 hover:text-white text-2xl">&times;</button>
                    </div>

                    <div class="p-6 overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-orange-50 p-4 rounded-xl border border-orange-100 text-center">
                                <p class="text-orange-600 text-xs font-bold uppercase">Total Poin</p>
                                <p class="text-3xl font-black text-orange-600">{{ selectedStudent.total_points }} 🌟</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center flex flex-col justify-center">
                                <p class="text-gray-500 text-xs font-bold uppercase">Jenis Kelamin</p>
                                <p class="text-lg font-bold text-gray-700">
                                    {{ selectedStudent.gender === 'L' ? 'Laki-laki 👦' : 'Perempuan 👧' }}
                                </p>
                            </div>
                        </div>

                        <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                            <span>📅</span> Riwayat Aktivitas Terkini
                        </h4>
                        
                        <div v-if="studentHistory.length > 0" class="space-y-3">
                            <div v-for="log in studentHistory" :key="log.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-lg shadow-sm">
                                    {{ ['brushing'].includes(log.activity_type) ? '🪥' : (['handwashing'].includes(log.activity_type) ? '🧼' : '📝') }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm text-gray-800">{{ formatActivityLabel(log.activity_type) }}</p>
                                    <p class="text-xs text-gray-500">{{ log.time }}</p>
                                </div>
                                <div class="font-bold text-sm" :class="log.proof_image ? 'text-blue-600' : 'text-orange-600'">
                                    {{ formatActivityValue(log) }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400 border-2 border-dashed rounded-xl">
                            Belum ada aktivitas tercatat di feed terbaru.
                        </div>
                    </div>

                    <div class="p-4 border-t bg-gray-50 flex justify-end">
                        <button @click="deleteStudent(selectedStudent.id)" class="text-red-500 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                            Hapus Siswa Ini 🗑️
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="showClassModal" class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
                <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl border border-gray-100">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Tambah Kelas Baru</h3>
                    <form @submit.prevent="submitClass" class="flex gap-2 mb-6">
                        <input v-model="formClass.name" type="text" placeholder="Nama Kelas (misal: 1B)" 
                            class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-blue-500 outline-none transition-colors" required>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-200">Simpan</button>
                    </form>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Kelas Terdaftar:</h4>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="cls in classList" :key="cls.id" class="bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm">
                                {{ cls.name }} <button @click="deleteClass(cls.id)" class="text-gray-400 hover:text-red-500 w-5 h-5 flex items-center justify-center rounded-full hover:bg-red-50">×</button>
                            </span>
                        </div>
                    </div>
                    <button @click="showClassModal = false" class="mt-6 text-gray-400 hover:text-gray-600 w-full text-center text-sm font-bold">Batal</button>
                </div>
            </div>
        </Transition>

        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="showStudentModal" class="fixed inset-0 bg-black/40 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
                <div class="bg-white w-full max-w-lg rounded-2xl p-8 shadow-2xl border border-gray-100">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Input Data Murid</h3>
                        <button @click="showStudentModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>
                    <form @submit.prevent="submitStudent" class="space-y-5">
                        <div><label class="block text-sm font-bold text-gray-600 mb-2">Nama</label><input v-model="formStudent.name" type="text" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3" required></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-bold text-gray-600 mb-2">Gender</label><select v-model="formStudent.gender" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-white"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                            <div><label class="block text-sm font-bold text-gray-600 mb-2">Kelas</label><select v-model="formStudent.class_name" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-white"><option v-for="cls in classList" :value="cls.name">{{ cls.name }}</option></select></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div><label class="block text-sm font-bold text-gray-600 mb-2">Absen</label><input v-model="formStudent.absent_no" type="number" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3"></div>
                             <div><label class="block text-sm font-bold text-gray-600 mb-2">Pass</label><input v-model="formStudent.password" type="text" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50"></div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white py-3.5 rounded-xl font-bold hover:bg-green-700 shadow-lg mt-4">Simpan Data</button>
                    </form>
                </div>
            </div>
        </Transition>


        <nav class="bg-white shadow-sm border-b px-6 md:px-10 py-4 flex justify-between items-center sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-lg shadow-blue-200">👨‍🏫</div>
                <div><h1 class="text-lg font-bold text-gray-800 leading-tight">Ruang Guru</h1><p class="text-xs text-gray-400 font-medium">Panel Admin Sekolah</p></div>
            </div>
            <Link href="/" method="post" as="button" class="text-gray-400 font-bold text-sm hover:text-red-500 transition-colors flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-lg"><span>Keluar</span><span class="text-lg">↪</span></Link>
        </nav>

        <main class="p-4 md:p-8 max-w-7xl mx-auto space-y-8">
            
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div><h2 class="text-2xl font-bold text-gray-800">Ringkasan Sekolah</h2><p class="text-gray-500 mt-1">Pantau perkembangan siswa</p></div>
                <div class="flex items-center gap-3 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
                    <div class="relative"><select v-model="selectedClass" @change="applyFilter" class="bg-white border border-gray-200 rounded-lg pl-4 pr-10 py-2.5 text-sm font-bold text-gray-600 outline-none cursor-pointer focus:border-blue-500 shadow-sm hover:border-gray-300 transition-colors appearance-none"><option value="">Semua Kelas</option><option v-for="cls in classList" :key="cls.id" :value="cls.name">Kelas {{ cls.name }}</option></select><div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400 text-xs">▼</div></div>
                    <button @click="showClassModal = true" class="bg-white text-blue-600 px-4 py-2.5 rounded-lg text-sm font-bold border border-blue-100 hover:bg-blue-50 flex items-center gap-2"><span>⚙️</span> <span class="hidden sm:inline">Kelola Kelas</span></button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5"><div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">👶</div><div><p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Siswa</p><p class="text-3xl font-black text-gray-800">{{ stats.total_students }}</p></div></div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5"><div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl">📸</div><div><p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Laporan Hari Ini</p><p class="text-3xl font-black text-gray-800">{{ stats.activities_today }}</p></div></div>
                <div class="bg-gradient-to-r from-orange-400 to-pink-500 p-6 rounded-2xl shadow-lg text-white flex items-center justify-between"><div class="relative z-10"><p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">Filter Aktif</p><p class="text-2xl font-black">{{ stats.filter_active ? `Kelas ${stats.filter_active}` : 'Semua Data' }}</p></div><span class="text-6xl opacity-20">📊</span></div>
            </div>

            <div v-if="analysis && analysis.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-2xl">📊</span>
                    <div>
                        <h2 class="text-xl font-bold text-gray-700">Analisa Pemahaman Siswa</h2>
                        <p class="text-xs text-gray-400">Rata-rata skor kuis per kelas</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(item, index) in analysis" :key="index" class="bg-gray-50 rounded-2xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
                        
                        <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-200">
                            <span class="font-black text-gray-800 text-xl">Kelas {{ item.class }}</span>
                            <span class="text-xs font-bold bg-white px-2 py-1 rounded border border-gray-200 text-gray-500">Rata-rata</span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2">
                            
                            <div class="flex flex-col items-center text-center">
                                <div class="relative w-16 h-16 md:w-20 md:h-20">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" stroke="#E2E8F0" stroke-width="8" fill="transparent" />
                                        <circle cx="50" cy="50" r="40" stroke="#3B82F6" stroke-width="8" fill="transparent" 
                                            stroke-linecap="round"
                                            :stroke-dasharray="2 * Math.PI * 40"
                                            :stroke-dashoffset="calculateDashOffset(item.avg_brushing, 40)"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center font-black text-blue-600 text-sm md:text-base">
                                        {{ Math.round(item.avg_brushing || 0) }}
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 mt-2 uppercase">Gigi</span>
                                <span class="text-[9px] font-bold mt-1" :class="getScoreCategory(item.avg_brushing).color">
                                    {{ getScoreCategory(item.avg_brushing).label }}
                                </span>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="relative w-16 h-16 md:w-20 md:h-20">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" stroke="#E2E8F0" stroke-width="8" fill="transparent" />
                                        <circle cx="50" cy="50" r="40" stroke="#22C55E" stroke-width="8" fill="transparent" 
                                            stroke-linecap="round"
                                            :stroke-dasharray="2 * Math.PI * 40"
                                            :stroke-dashoffset="calculateDashOffset(item.avg_handwashing, 40)"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center font-black text-green-600 text-sm md:text-base">
                                        {{ Math.round(item.avg_handwashing || 0) }}
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 mt-2 uppercase">Tangan</span>
                                <span class="text-[9px] font-bold mt-1" :class="getScoreCategory(item.avg_handwashing).color">
                                    {{ getScoreCategory(item.avg_handwashing).label }}
                                </span>
                            </div>

                            <div class="flex flex-col items-center text-center">
                                <div class="relative w-16 h-16 md:w-20 md:h-20">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" stroke="#E2E8F0" stroke-width="8" fill="transparent" />
                                        <circle cx="50" cy="50" r="40" stroke="#EC4899" stroke-width="8" fill="transparent" 
                                            stroke-linecap="round"
                                            :stroke-dasharray="2 * Math.PI * 40"
                                            :stroke-dashoffset="calculateDashOffset(item.avg_reproductive, 40)"
                                            class="transition-all duration-1000 ease-out" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center font-black text-pink-600 text-sm md:text-base">
                                        {{ Math.round(item.avg_reproductive || 0) }}
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 mt-2 uppercase">Tubuh</span>
                                <span class="text-[9px] font-bold mt-1" :class="getScoreCategory(item.avg_reproductive).color">
                                    {{ getScoreCategory(item.avg_reproductive).label }}
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <div class="xl:col-span-2 space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">📥</span><h2 class="text-xl font-bold text-gray-700">Aktivitas Terbaru</h2>
                    </div>

                    <div class="grid gap-4">
                        <div v-for="log in recentActivities" :key="log.id" 
                            class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-5 hover:shadow-md transition-all group">
                            
                            <div class="w-full sm:w-32 h-32 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 border border-gray-200 relative group/img">
                                <img v-if="log.proof_image" :src="`/storage/${log.proof_image}`" 
                                    class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 group-hover/img:scale-110"
                                    @click="openImageZoom(log.proof_image)">
                                <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300 text-xs text-center p-2 bg-orange-50 text-orange-400 font-bold">
                                    <span class="text-2xl mb-1">📝</span>
                                    <span>Hasil Kuis<br>(Teori)</span>
                                </div>
                                <div v-if="log.proof_image" class="absolute inset-0 bg-black/20 opacity-0 group-hover/img:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                    <span class="text-white font-bold text-xs bg-black/50 px-2 py-1 rounded">🔍 Zoom</span>
                                </div>
                            </div>

                            <div class="flex-1 py-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-gray-800 text-lg hover:text-blue-600 cursor-pointer" @click="openStudentDetail({ name: log.student_name, class: log.student_class })">
                                                {{ log.student_name }}
                                            </h3>
                                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Kelas {{ log.student_class }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 font-medium bg-gray-50 px-2 py-1 rounded-md">{{ log.time }}</span>
                                    </div>
                                    
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-lg border" 
                                            :class="getActivityColor(log.activity_type)">
                                            {{ formatActivityLabel(log.activity_type) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-black px-2.5 py-1.5 rounded-lg border bg-gray-50 text-gray-600 border-gray-200">
                                            {{ formatActivityValue(log) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                    <span class="text-green-500">✔</span> Diverifikasi Otomatis
                                </p>
                            </div>
                        </div>

                        <div v-if="recentActivities.length === 0" class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
                            <div class="text-4xl mb-3 opacity-30">📭</div>
                            <p class="text-gray-400 font-medium">Belum ada laporan aktivitas baru.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2"><span class="text-xl">🎓</span><h2 class="text-xl font-bold text-gray-700">Data Murid</h2></div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
                        <div class="p-4 border-b border-gray-100 bg-white sticky top-0 z-10 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ students.length }} Siswa</span>
                            <button @click="showStudentModal = true" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-2 rounded-lg shadow-md flex items-center gap-1.5 transition-all active:scale-95"><span class="text-sm">+</span> Tambah</button>
                        </div>

                        <div class="overflow-y-auto flex-1 p-2 space-y-1 custom-scrollbar">
                            <div v-for="student in students" :key="student.id" 
                                @click="openStudentDetail(student)"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 cursor-pointer transition-colors group border border-transparent hover:border-blue-100">
                                
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm transition-transform group-hover:scale-110"
                                    :class="student.gender === 'P' ? 'bg-pink-400' : 'bg-blue-400'">
                                    {{ getInitials(student.name) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm text-gray-800 truncate group-hover:text-blue-600">{{ student.name }}</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="bg-gray-100 px-1.5 rounded text-gray-600 font-medium">Kls {{ student.class }}</span>
                                        <span class="text-orange-500 font-bold">⭐ {{ student.total_points }}</span>
                                    </div>
                                </div>
                                <span class="text-gray-300 text-lg group-hover:text-blue-400">›</span>
                            </div>

                            <div v-if="students.length === 0" class="h-full flex flex-col items-center justify-center text-center p-8 text-gray-400">
                                <span class="text-3xl mb-2 opacity-30">👥</span>
                                <p class="text-sm">Belum ada data murid.</p>
                            </div>
                        </div>
                    </div>
                </div>

                

            </div>
        </main>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #cbd5e1; }
</style>