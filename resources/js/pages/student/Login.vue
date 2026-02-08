<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    password: '',
});

const submit = () => {
    // Menggunakan URL manual untuk menghindari error Ziggy/Route
    form.post('/siswa/login');
};
</script>

<template>
    <Head title="Masuk Yuk!" />
    <div class="min-h-screen bg-sky-100 flex items-center justify-center p-6 font-['Comic_Sans_MS']">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-10 shadow-2xl border-4 border-sky-300 relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-5 bg-orange-400"></div>

            <div class="text-center mb-8">
                <div class="text-6xl mb-2">👋</div>
                <h1 class="text-3xl font-black text-sky-600">Selamat Datang!</h1>
                <p class="text-gray-500 font-bold">Masukkan nama dan sandi dari Bu Guru ya</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                
                <div>
                    <label class="block text-lg font-black text-gray-700 ml-2 mb-1">NAMA KAMU</label>
                    <input 
                        v-model="form.name" 
                        type="text" 
                        placeholder="Contoh: Budi" 
                        class="w-full border-4 border-sky-100 text-gray-700 rounded-2xl p-4 text-xl focus:border-sky-400 outline-none transition-colors"
                        required
                    >
                    <p v-if="form.errors.name" class="text-red-500 font-bold ml-2 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-lg font-black text-gray-700 ml-2 mb-1">PASSWORD RAHASIA 🔒</label>
                    <input 
                        v-model="form.password" 
                        type="password" 
                        placeholder="Ketikan sandimu..." 
                        class="w-full border-4 border-yellow-200 text-gray-700 bg-yellow-50 rounded-2xl p-4 text-xl focus:border-yellow-400 outline-none transition-colors"
                        required
                    >
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white text-2xl font-black py-5 rounded-3xl shadow-[0_8px_0_0_#c2410c] active:shadow-none active:translate-y-2 transition-all disabled:opacity-50">
                    {{ form.processing ? 'Sedang Masuk...' : 'MASUK SEKARANG! 🚀' }}
                </button>
            </form>

        </div>
    </div>
</template>