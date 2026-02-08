<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Models\SchoolClass; // Import Model Baru
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter Kelas dari URL (jika ada)
        $filterClass = $request->input('class_name');

        // 2. Query Dasar untuk Statistik
        $studentQuery = Student::query();
        $logQuery = ActivityLog::query();

        // Terapkan Filter jika Guru memilih kelas tertentu
        if ($filterClass) {
            $studentQuery->where('class', $filterClass);
            
            // Filter log berdasarkan siswa yang ada di kelas tersebut
            $logQuery->whereHas('student', function($q) use ($filterClass) {
                $q->where('class', $filterClass);
            });
        }

        // 3. Hitung Statistik (Sesuai Filter)
        $totalStudents = $studentQuery->count();
        $activitiesToday = $logQuery->whereDate('activity_date', Carbon::today())->count();

        // 4. Data Feed & Leaderboard (Sesuai Filter)
        $recentActivities = $logQuery->with('student')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'student_name' => $log->student->name,
                    'student_class' => $log->student->class ?? '-',
                    'type' => $log->activity_type,
                    'proof_image' => $log->proof_image,
                    'time' => Carbon::parse($log->created_at)->locale('id')->diffForHumans(),
                    'date' => Carbon::parse($log->activity_date)->format('d M Y'),
                ];
            });

        $students = $studentQuery->orderBy('total_points', 'desc')->get();

        // 5. Ambil Daftar Kelas untuk Dropdown
        $allClasses = SchoolClass::orderBy('name')->get();

        return Inertia::render('teacher/Dashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'activities_today' => $activitiesToday,
                'filter_active' => $filterClass // Kirim info filter aktif ke Vue
            ],
            'recentActivities' => $recentActivities,
            'students' => $students,
            'classList' => $allClasses // Kirim daftar kelas ke Vue
        ]);
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'gender' => 'required|in:L,P',
            'class_name' => 'required|string', // Dropdown kelas
            'absent_no' => 'required|integer',
            'password' => 'required|string',
        ]);

        Student::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'gender' => $request->gender,
            'class' => $request->class_name,
            'absent_no' => $request->absent_no,
            'age' => 7, // Default
            'total_points' => 0,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Berhasil menambahkan murid baru!');
    }
    // --- FITUR MANAJEMEN ---

    public function storeClass(Request $request)
    {
        $request->validate(['name' => 'required|unique:school_classes,name']);
        SchoolClass::create(['name' => $request->name]);
        return back();
    }

    public function destroyClass($id)
    {
        SchoolClass::destroy($id);
        return back();
    }

    public function destroyStudent($id)
    {
        // Hapus siswa (Otomatis hapus log aktivitasnya jika relasi di database cascade)
        // Atau kita hapus manual logs-nya dulu
        ActivityLog::where('student_id', $id)->delete();
        Student::destroy($id);
        return back();
    }
}