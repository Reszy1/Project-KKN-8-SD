<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function dashboard($studentId)
    {
        $student = Student::with('badges')->findOrFail($studentId);
        $allBadges = Badge::all();

        return Inertia::render('student/Dashboard', [
            'student' => $student,
            'availableBadges' => $allBadges
        ]);
    }
}
