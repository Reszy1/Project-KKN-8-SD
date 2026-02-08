<script setup>
import { useForm, Head } from '@inertiajs/vue3';

// Menerima props daftar kelas dari Controller
const props = defineProps({
    classList: Array 
});

const form = useForm({
    name: '',
    class: '',
    absent_no: '',
    password: '', // Tambahan field
});

const submit = () => {
    form.post('/student-register');
};
</script>

<template>
    <Head title="Daftar Baru" />
    <div class="min-h-screen bg-sky-100 flex items-center justify-center p-6 font-['Comic_Sans_MS']">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl border-4 border-sky-300 relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-4 bg-orange-400"></div>

            <h1 class="text-3xl font-black text-sky-600 text-center mb-2">Halo Murid Baru! 👋</h1>
            <p class="text-center text-gray-500 mb-8 font-bold">Isi datamu untuk mulai bermain</p>
            
            <form @submit.prevent="submit" class="space-y-4">
                
                <div>
                    <label class="block text-sm font-black text-gray-700 ml-2 mb-1">NAMA LENGKAP</label>
                    <input v-model="form.name" type="text" placeholder="Contoh: Budi Santoso" 
                        class="w-full border-4 border-sky-100 rounded-2xl p-3 text-lg focus:border-sky-400 outline-none transition-colors" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-black text-gray-700 ml-2 mb-1">KELAS</label>
                        <select v-model="form.class" class="w-full border-4 border-sky-100 rounded-2xl p-3 text-lg outline-none bg-white" required>
                            <option value="" disabled selected>Pilih...</option>
                            <option v-for="cls in classList" :key="cls.id" :value="cls.name">
                                {{ cls.name }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-black text-gray-700 ml-2 mb-1">NO. ABSEN</label>
                        <input v-model="form.absent_no" type="number" placeholder="00" 
                            class="w-full border-4 border-sky-100 rounded-2xl p-3 text-lg outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 ml-2 mb-1">PASSWORD RAHASIA 🔒</label>
                    <input v-model="form.password" type="text" placeholder="Buat sandi (misal: 1234)" 
                        class="w-full border-4 border-yellow-200 bg-yellow-50 rounded-2xl p-3 text-lg focus:border-yellow-400 outline-none transition-colors" required>
                    <p class="text-xs text-gray-400 ml-2 mt-1">*Ingat baik-baik sandimu ya!</p>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white text-2xl font-black py-4 rounded-3xl shadow-[0_8px_0_0_#c2410c] active:shadow-none active:translate-y-2 transition-all mt-4">
                    {{ form.processing ? 'Menyimpan...' : 'SIMPAN & MAIN! 🚀' }}
                </button>
            </form>
        </div>
    </div>
</template>