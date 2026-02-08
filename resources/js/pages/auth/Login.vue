<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login Guru" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100 font-sans">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
            <div class="text-center mb-8">
                <div class="text-6xl mb-2">👨‍🏫</div>
                <h2 class="text-2xl font-bold text-gray-800">Login Guru</h2>
                <p class="text-gray-500 text-sm">Masuk untuk memantau aktivitas siswa</p>
            </div>

            <div v-if="status" class="mb-4 text-green-600 text-sm font-medium">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" required autofocus
                        class="w-full px-4 py-3 rounded-lg text-gray-900 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="guru@sekolah.id">
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input v-model="form.password" type="password" required
                        class="w-full px-4 py-3 rounded-lg text-gray-900 border border-gray-300  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="••••••••">
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all active:scale-95 disabled:opacity-50">
                    {{ form.processing ? 'Memproses...' : 'Masuk Sekarang' }}
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="/" class="text-sm text-gray-500 hover:text-blue-600">Kembali ke Halaman Depan</a>
            </div>
        </div>
    </div>
</template>