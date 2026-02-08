<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Models\Badge;
use App\Models\Question; // Pastikan model Question diimport jika dipakai

class ActivityController extends Controller
{
    /**
     * Menampilkan Halaman Login Khusus Siswa
     * Menggantikan fungsi register() yang lama.
     */
    public function loginPage()
    {
        return Inertia::render('student/Login');
    }

    /**
     * Memproses Login Siswa
     * Memeriksa kecocokan Nama dan Password di database.
     */
    public function loginAttempt(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Cari siswa berdasarkan nama (Case insensitive jika database mendukung, defaultnya sensitive)
        $student = Student::where('name', $request->name)->first();

        // 2. Cek apakah siswa ditemukan DAN password cocok
        if ($student && Hash::check($request->password, $student->password)) {
            // Jika sukses, arahkan ke dashboard siswa tersebut
            return redirect()->route('student.dashboard', ['studentId' => $student->id]);
        }

        // 3. Jika gagal, kembalikan dengan pesan error
        return back()->withErrors(['name' => 'Nama atau Password salah. Coba tanya Bu Guru ya!']);
    }

    public function education()
    {
        // Render file Education.vue yang baru kita buat
        return Inertia::render('student/Education');
    }

    /**
     * Menampilkan Dashboard Siswa
     */
    public function dashboard($studentId)
    {
        $student = Student::with('badges')->findOrFail($studentId);
        $allBadges = Badge::all();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $todayDate = Carbon::now()->format('Y-m-d'); // Tanggal hari ini

        // 1. Ambil Log Kalender (Bulanan) - KODE LAMA TETAP DIPAKAI
        $logs = ActivityLog::where('student_id', $studentId)
            ->whereMonth('activity_date', $currentMonth)
            ->whereYear('activity_date', $currentYear)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->activity_date)->format('Y-m-d');
            });

        $monthlyProgress = $logs->map(function ($dayLogs) {
            return $dayLogs->pluck('activity_type')->unique()->values();
        });

        // 2. CEK STATUS HARIAN (BARU)
        // Kita cek apakah hari ini sudah ada log untuk tipe tertentu
        $todaysMission = [
            'brushing' => ActivityLog::where('student_id', $studentId)
                ->where('activity_type', 'brushing')
                ->whereDate('activity_date', $todayDate)
                ->exists(), // Returns true/false
            
            'handwashing' => ActivityLog::where('student_id', $studentId)
                ->where('activity_type', 'handwashing')
                ->whereDate('activity_date', $todayDate)
                ->exists(),
        ];

        return Inertia::render('student/Dashboard', [
            'student' => $student,
            'availableBadges' => $allBadges,
            'monthlyProgress' => $monthlyProgress,
            'currentDate' => $todayDate,
            'todaysMission' => $todaysMission, // <--- Kirim data checklist ke Vue
        ]);
    }

    /**
     * Menampilkan Halaman Kuis
     */
    public function quiz($studentId, $type)
    {
        $student = Student::findOrFail($studentId);
        
        // 1. Filter pertanyaan berdasarkan kategori (type)
        // Pastikan di database tabel 'questions' kolom 'category' isinya: 'brushing', 'handwashing', 'reproductive'
        $questions = Question::where('category', $type)
            ->inRandomOrder()
            ->get() 
            ->map(function($q) {
                $originalOptions = json_decode($q->options);
                $correctAnswerText = $originalOptions[$q->answer_index];
                $shuffledOptions = $originalOptions;
                shuffle($shuffledOptions);
                $newAnswerIndex = array_search($correctAnswerText, $shuffledOptions);

                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'options' => $shuffledOptions,
                    'answer' => $newAnswerIndex
                ];
            });

        return Inertia::render('student/Quiz', [
            'student' => $student,
            'questions' => $questions,
            'type' => $type, // Kirim tipe kuis ke Vue agar tahu judul & logikanya
        ]);
    }

    /**
     * Menampilkan Halaman Timer (Sikat Gigi atau Cuci Tangan).
     */
    public function timer($studentId, $type)
    {
        $student = Student::findOrFail($studentId);

        return Inertia::render('student/Timer', [
            'student' => $student,
            'type' => $type // 'brushing' atau 'handwashing'
        ]);
    }

    /**
     * Menyimpan hasil aktivitas (Foto Bukti & Poin).
     */
    public function store(Request $request)
    {
        // 1. CEK ANTI-SPAM (Mencegah double click/input dalam waktu 1 menit)
        $latestLog = ActivityLog::where('student_id', $request->student_id)
            ->where('activity_type', $request->type)
            ->latest()
            ->first();

        if ($latestLog && Carbon::parse($latestLog->created_at)->diffInSeconds(Carbon::now()) < 60) {
            return response()->json(['message' => 'Tunggu sebentar sebelum kirim lagi!'], 429);
        }

        // 2. VALIDASI KETAT
        // Kita minta frontend kirim flag 'is_quiz'.
        // Jika is_quiz ada, foto BOLEH kosong. Jika tidak ada (Timer), foto WAJIB ada.
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:brushing,handwashing,reproductive',
            'duration' => 'required|integer',
            'is_quiz' => 'nullable|boolean', // Parameter baru
            
            // LOGIKA KUNCI: 
            // Foto wajib ada KECUALI jika ini adalah 'is_quiz' atau tipe 'reproductive'
            'proof' => [
                Rule::requiredIf(fn () => !$request->is_quiz && $request->type !== 'reproductive'),
                'nullable',
                'image',
                'max:5120'
            ],
        ]);

        return DB::transaction(function () use ($request) {
            $student = Student::findOrFail($request->student_id);
            
            $imagePath = null;
            if ($request->hasFile('proof')) {
                $imagePath = $request->file('proof')->store("proofs/{$student->id}", 'public');
            }

            ActivityLog::create([
                'student_id' => $student->id,
                'activity_type' => $request->type,
                'duration_seconds' => $request->duration,
                'proof_image' => $imagePath,
                'activity_date' => Carbon::now()->format('Y-m-d')
            ]);

            // Hitung Poin
            // Jika ada foto (Timer), poin fix 50. Jika tidak (Kuis), poin sesuai skor (duration).
            $pointsEarned = $request->hasFile('proof') ? 50 : $request->duration;
            
            $student->increment('total_points', $pointsEarned);

            return response()->json([
                'message' => 'Berhasil disimpan!',
                'points_earned' => $pointsEarned
            ]);
        });
    }
}