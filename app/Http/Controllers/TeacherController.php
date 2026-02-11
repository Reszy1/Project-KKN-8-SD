<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\ActivityLog;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $filterClass = $request->input('class_name');

        $studentQuery = Student::query();
        $logQuery = ActivityLog::query();

        if ($filterClass) {
            $studentQuery->where('class', $filterClass);
            $logQuery->whereHas('student', function($q) use ($filterClass) {
                $q->where('class', $filterClass);
            });
        }

        $totalStudents = $studentQuery->count();
        $activitiesToday = $logQuery->whereDate('activity_date', Carbon::today())->count();

        // Feed Aktivitas (Tetap menampilkan log 'reproductive' sebagai tanda sudah baca)
        $recentActivities = $logQuery->with('student')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'student_name' => $log->student->name,
                    'student_class' => $log->student->class ?? '-',
                    'activity_type' => $log->activity_type, 
                    'duration_seconds' => $log->duration_seconds, 
                    'proof_image' => $log->proof_image,
                    'time' => Carbon::parse($log->created_at)->locale('id')->diffForHumans(),
                    'date' => Carbon::parse($log->activity_date)->format('d M Y'),
                ];
            });

        $students = $studentQuery->orderBy('total_points', 'desc')->get();
        $allClasses = SchoolClass::orderBy('name')->get();

        // ==========================================
        // FITUR ANALISA (HANYA BRUSHING & HANDWASHING)
        // ==========================================
        $analysisQuery = Student::select('students.class')
            ->join('activity_logs', 'students.id', '=', 'activity_logs.student_id')
            ->where(function($q) {
                $q->where('activity_logs.is_quiz', 1)
                  ->orWhereNull('activity_logs.proof_image');
            });

        if ($filterClass) {
            $analysisQuery->where('students.class', $filterClass);
        }

        // HAPUS AVG(reproductive) KARENA BUKAN KUIS NILAI LAGI
        $analysis = $analysisQuery->selectRaw('
                students.class,
                AVG(CASE WHEN activity_logs.activity_type = "brushing" THEN activity_logs.duration_seconds ELSE NULL END) as avg_brushing,
                AVG(CASE WHEN activity_logs.activity_type = "handwashing" THEN activity_logs.duration_seconds ELSE NULL END) as avg_handwashing
            ')
            ->groupBy('students.class')
            ->orderBy('students.class')
            ->get();

        return Inertia::render('teacher/Dashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'activities_today' => $activitiesToday,
                'filter_active' => $filterClass
            ],
            'recentActivities' => $recentActivities,
            'students' => $students,
            'classList' => $allClasses,
            'analysis' => $analysis 
        ]);
    }

    // --- MANAJEMEN ---
    public function storeStudent(Request $request) {
        $request->validate(['name' => 'required','gender' => 'required','class_name' => 'required','absent_no' => 'required','password' => 'required']);
        Student::create(['user_id' => auth()->id(), 'name' => $request->name, 'gender' => $request->gender, 'class' => $request->class_name, 'absent_no' => $request->absent_no, 'age' => 7, 'total_points' => 0, 'password' => Hash::make($request->password)]);
        return back()->with('success', 'Berhasil!');
    }
    public function storeClass(Request $request) {
        $request->validate(['name' => 'required|unique:school_classes,name']);
        SchoolClass::create(['name' => $request->name]);
        return back();
    }
    public function destroyClass($id) { SchoolClass::destroy($id); return back(); }
    public function destroyStudent($id) { ActivityLog::where('student_id', $id)->delete(); Student::destroy($id); return back(); }
}