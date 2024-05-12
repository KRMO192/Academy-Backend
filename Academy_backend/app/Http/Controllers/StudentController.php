<?php

namespace App\Http\Controllers;

use App\Models\student;
use App\Models\User;
use App\Http\Resources\StudentResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Auth;

class StudentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */

    // public function __construct(){
    //     $this->middleware(['auth:api','user-access:student'])->except('index');
    // }



    public function index()
    {
        $students=Student::latest()->get();
        return $this->sendResponse(BaseController::collection($students),'All students');
    }

    public function trachedStudents()
    {
        $students = Student::onlyTrashed()->latest()->get();
        return $this->sendResponse(StudentController::collection($students), 'All trached students');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_type = Auth::user()->type;
        if($user_type == 'student' ){           // If the user who wants to create a student account is a Student
            $input = $request->all();
            $user_id = Auth::user()->id;
            $validate=validator::make($input,[
                'name' => 'required',
                'student_image' => 'required',
                'address' => 'required',
                'bio' => 'required',
                'phone' => 'required'
            ]);



            if($validate->fails()){
                return $this->sendError('Validate error', $validate->errors());
            }

            if ($image=$request->file('image')) {
                $destinationPath='images/students/';
                $studentImage=date('YmdHis').".".$image->getClientOriginalExtension();
                $image->move($destinationPath,$studentImage);
                $input['image']="$studentImage";
            }

            $student=Student::create($input);
            return $this->sendResponse($student,'student added successfully');

        }elseif($user_type == 'admin'){         // If the user who wants to create a student account is a Admin

            DB::beginTransaction();

            try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation error', $validator->errors());
                }

                $input = $request->only(['name', 'email', 'password']);
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;

                // this execute if the above success
                $input = $request->all();
                $input['user_id'] = $user->id; // Use the newly created user's ID
                $validate = Validator::make($input, [
                    'name' => 'required',
                    'student_image' => 'required',
                    'address' => 'required',
                    'bio' => 'required',
                    'phone' => 'required',
                ]);

                if ($validate->fails()) {
                    DB::rollBack();
                    return $this->sendError('Validation error', $validate->errors());
                }

                if ($image = $request->file('image')) {
                    $destinationPath = 'images/students/';
                    $studentImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $studentImage);
                    $input['student_image'] = $studentImage;
                }

                $student = Student::create($input);
                DB::commit();

                return $this->sendResponse($student, 'Student added successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError('An error occurred', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $student=Student::find($id);    // find the student who has this id
        $u_id=$student['user_id'];      // get the user_id which belongs this student
        $auth_id=auth()->user()->id;    // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;     // get user type want to show this page (id from users tabel)
        if(is_null($student)){
            return $this->sendError('student not found');
        }
        if ($auth_id==$u_id || $type == "admin" ) {          // The student can only see his profile and admin can see all student profiles
            return $this->sendResponse($student,'student found successfully');
        }else{
            return $this->sendError('You do not have permission to access for this page');
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $student=Student::find($id);            // find the student who has this id
        $u_id=$student['user_id'];              // get the user_id which belongs this student
        $auth_id=auth()->user()->id;            // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;             // get user type want to show this page (id from users tabel)
        if ($auth_id==$u_id || $type == "admin" ) {
            $input=$request->all();
            $validate=validator::make($input,[
                'name' => 'required',
                'student_image' => 'required',
                'address' => 'required',
                'bio' => 'required',
                'phone' => 'required'
            ]);

            if($validate->fails()){
                return $this->sendError('Validate error', $validate->errors());
            }

            if ($image=$request->file('image')) {
                $destinationPath='images/students/';
                $studentImage=date('YmdHis').".".$image->getClientOriginalExtension();
                $image->move($destinationPath,$studentImage);
                $input['image']="$studentImage";
                $student->student_image=$input['student_image'];
            }else{
                unset($input['image']);
            }

            $student->name=$input['name'];
            $student->address=$input['address'];
            $student->bio=$input['bio'];
            $student->phone=$input['phone'];
            $student->fill($input);
            $student->save(); // Save the changes into the database

            return $this->sendResponse($student,'student updated successfully');
        }else{
            return $this->sendError('You do not have permission to access for this page');
        }

    }

    public function softDelete($id)
    {
        $student=Student::find($id);                // find the student who has this id
        $u_id=$student['user_id'];                  // get the user_id which belongs this student
        $auth_id=auth()->user()->id;                // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;                 // get user type want to show this page (id from users tabel)
        if ($auth_id==$u_id || $type == "admin" ) {

        $student=Student::find($id)->delete();
        return $this->sendResponse($student,'student deleted successfully');
        }else{
            return $this->sendError('You do not have permission to delete this student');
        }

    }
    public function forceDelete($id)
    {
        $student=Student::onlyTrashed()->where('id',$id)->forceDelete();
        return $this->sendResponse($student,'student deleted successfully');

    }
    public function back($id)
    {
        $student=Student::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse($student,'student retreive successfully');
    }
}
