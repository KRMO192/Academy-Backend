<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController as RegisterController;
use App\Http\Controllers\StudentController as StudentController;
use App\Http\Controllers\CourseController as CourseController;
use App\Http\Controllers\InstructorController as InstructorController;
use App\Http\Controllers\AdminController as AdminController;
use App\Http\Controllers\JoinToCourseController as JoinToCourseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Auth::routes();
Route::middleware('auth:passport')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:api', 'prevent-access:student'])->group(function () {
    Route::get('students', [StudentController::class,'index']);
});

// student Routes List
Route::middleware(['auth:api','user-access:student'])->group(function () {

    Route::post('student/create', [StudentController::class,'store']);
    Route::get('student/show/{id}', [StudentController::class,'show']);
    Route::put('student/update/{id}', [StudentController::class,'update']);
    Route::delete('student/delete/{id}', [StudentController::class,'softDelete']);
    Route::get('student/courses', [CourseController::class,'index']);
    Route::get('student/course/{id}', [CourseController::class,'show']);
    Route::post('enroll-student', [JoinToCourseController::class,'enrollStudentToCourse']);
    Route::post('withdraw-student', [JoinToCourseController::class,'withdrawStudentFromCourse']);
});


// instructor Routes List
Route::middleware(['auth:api','user-access:instructor'])->group(function () {

    Route::post('instructor/create', [InstructorController::class,'store']);
    Route::get('instructor/show/{id}', [InstructorController::class,'show']);
    Route::put('instructor/update/{id}', [InstructorController::class,'update']);
    Route::delete('instructor/delete/{id}', [InstructorController::class,'softDelete']);

    Route::get('instructor/show/courses', [CourseController::class,'index']);
    Route::get('instructor/show/trashed/courses', [CourseController::class,'trashedCourses']);
    Route::get('instructor/show/course/{id}', [CourseController::class,'show']);
    Route::post('instructor/create/course', [CourseController::class,'store']);
    Route::put('instructor/update/course/{id}', [CourseController::class,'update']);
    Route::delete('instructor/delete/course/{id}', [CourseController::class,'softDelete']);
    Route::delete('instructor/forcedelete/course/{id}', [CourseController::class,'forceDelete']);
    Route::post('instructor/retrive/course/{id}', [CourseController::class,'back']);
});

// admin Route List
Route::middleware(['auth:api','user-access:admin'])->group(function () {
        //admins
    Route::get('admin/show/admins', [AdminController::class ,'index']);
    Route::post('admin/create/admin', [AdminController::class,'store']);
    Route::delete('admin/delete/admin/{id}', [AdminController::class,'destroy']);

        //courses
    Route::get('admin/show/courses', [CourseController::class,'index']);
    Route::get('admin/create/courses', [CourseController::class,'store']);

        // students
    Route::get('admin/show/students', [StudentController::class,'index']);
    Route::get('admin/show/trashed/students', [StudentController::class,'trachedStudents']);
    Route::post('admin/create/student', [StudentController::class,'store']);
    Route::put('admin/update/student/{id}', [StudentController::class,'update']);
    Route::get('admin/show/student/{id}', [StudentController::class,'show']);
    Route::delete('admin/delete/student/{id}', [StudentController::class,'softDelete']);
    Route::delete('admin/forcedelete/student/{id}', [StudentController::class,'forceDelete']);
    Route::post('admin/retrieve/student/{id}', [StudentController::class,'back']);

        // instructors
    Route::get('admin/show/instructors', [InstructorController::class,'index']);
    Route::get('admin/show/trashed/instructors', [InstructorController::class,'trachedStudents']);
    Route::post('admin/create/instructor', [InstructorController::class,'store']);
    Route::put('admin/update/instructor/{id}', [InstructorController::class,'update']);
    Route::get('admin/show/instructor/{id}', [InstructorController::class,'show']);
    Route::delete('admin/delete/instructor/{id}', [InstructorController::class,'softDelete']);
    Route::delete('admin/forcedelete/instructor/{id}', [InstructorController::class,'forceDelete']);
    Route::post('admin/retrieve/instructor/{id}', [InstructorController::class,'back']);




});







// Route::resource('students', StudentController::class);

// Route::resource('courses', Cou/rseController::class);
//instructor Routes List
// Route::middleware(['auth', 'user-access:admin'])->group(function () {

    // Route::resource('instructors', InstructorController::class);
// });

Route::post('register',[RegisterController::class,'Register']);
Route::post('login',[RegisterController::class,'Login']);

// Route::middleware(['auth', 'user-access:instructor'])->group(function () {

//     Route::resource('courses', CourseController::class);
// });


// Route::resource('courses', CourseController::class);
//Route::resource('instructors', InstructorsController::class);
//Route::resource('students', StudentController::class);
// Route::post('users/{id}', function ($id) {

// // });
