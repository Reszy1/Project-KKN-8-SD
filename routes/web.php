<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TeacherController;


// --- 1. HALAMAN DEPAN ---
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Penanganan Error "POST method not supported for route /"
// Ini terjadi jika tombol 'Keluar' siswa mengirim method="post" ke halaman utama
Route::post('/', function () {
    return redirect('/');
});


// --- 2. AREA SISWA (LOGIN & AKTIVITAS) ---

Route::get('/edukasi', [ActivityController::class, 'education'])
    ->name('student.education');
// Halaman Login Siswa (Pengganti Register)
Route::get('/siswa/login', [ActivityController::class, 'loginPage'])
    ->name('student.login');

// Proses Login Siswa (Cek Nama & Password)
Route::post('/siswa/login', [ActivityController::class, 'loginAttempt'])
    ->name('student.login.attempt');

// Dashboard Siswa
Route::get('/dashboard/{studentId}', [ActivityController::class, 'dashboard'])
    ->name('student.dashboard');

// Timer (Sikat Gigi / Cuci Tangan)
Route::get('/timer/{studentId}/{type}', [ActivityController::class, 'timer'])
    ->name('student.timer');

// Kuis (DENGAN TIPE: brushing/handwashing/reproductive)
Route::get('/quiz/{studentId}/{type}', [ActivityController::class, 'quiz'])
    ->name('student.quiz');

// Simpan Aktivitas (Poin & Foto)
Route::post('/activity/store', [ActivityController::class, 'store'])
    ->name('activity.store');

Route::get('/game/{studentId}', [ActivityController::class, 'game'])->name('student.game');
Route::get('/game-food/{studentId}', [ActivityController::class, 'gameFood'])->name('student.game.food');


// --- 3. AREA GURU (AUTHENTICATION) ---

// Halaman Login Guru
Route::get('/login', function () {
    return Inertia::render('auth/Login', [
        'status' => session('status'),
    ]);
})->name('login')->middleware('guest');

// Proses Login Guru
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/teacher/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
});

// Proses Logout Guru
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// --- 4. DASHBOARD GURU (DILINDUNGI PASSWORD) ---
Route::middleware(['auth'])->group(function () {
    
    // Halaman Utama Guru
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])
        ->name('teacher.dashboard');

    // Manajemen Kelas (Tambah & Hapus)
    Route::post('/teacher/class', [TeacherController::class, 'storeClass'])
        ->name('teacher.class.store');
    
    Route::delete('/teacher/class/{id}', [TeacherController::class, 'destroyClass'])
        ->name('teacher.class.destroy');

    // Manajemen Siswa (Tambah & Hapus)
    Route::post('/teacher/student', [TeacherController::class, 'storeStudent'])
        ->name('teacher.student.store');
    
    Route::delete('/teacher/student/{id}', [TeacherController::class, 'destroyStudent'])
        ->name('teacher.student.destroy');
});