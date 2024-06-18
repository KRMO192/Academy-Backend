<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\student;
use App\Models\Course;

class JoinToCourseController extends Controller
{

    public function enrollStudentToCourse(Request $request)
    {
        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        // Enroll the student to the course
        $student->courses()->attach($course);

        return response()->json(['message' => 'Student enrolled to course successfully!']);
    }

    public function withdrawStudentFromCourse(Request $request)
    {
        $student = Student::find($request->student_id);
        $course = Course::find($request->course_id);

        // Withdraw the student from the course
        $student->courses()->detach($course);

        return response()->json(['message' => 'Student withdrawn from course successfully!']);
    }
}


