<?php

namespace App\Http\Controllers;

use App\Models\student;
use App\Http\Resources\StudentResource;
use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;

class StudentController extends BaseController
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('auth');
    }


    public function index()
    {
        $students=student::latest()->get();
        return $this->sendResponse(BaseController::collection($students),'All students');
    }


    public function trachedInstructors()
    {
        $students=student::onlyTrached()->latest();
        return $this->sendResponse(BaseController::collection($students),'All trached students');
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
            $studentImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$studentImage);
            $input['image']="$studentImage";
        }

        $student=student::create($input);
        return $this->sendResponse(new BaseController($student),'student added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(student $student)
    {
        $student=student::find($student->id);
        if(is_null($student)){
            return $this->sendError('student not found', $validate->errors());
        }

        return $this->sendResponse(new BaseController($student),'student found successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, student $student)
    {
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
            $studentImage=date('YmdHis').".".$image->getClientOriginalExtention();
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


        return $this->sendResponse(new BaseController($student),'student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(student $student)
    {
        $student->delete();
        return $this->sendResponse(new BaseController($student),'student deleted successfully');
    }

    public function softDelete($id)
    {
        $student=student::find($id)->delete();
        return $this->sendResponse(new BaseController($student),'student deleted successfully');

    }
    public function forceDelete($id)
    {
        $id=student::onlyTrashed()->where('id',$id)->forceDelete();
        return $this->sendResponse(new BaseController($student),'student deleted successfully');

    }
    public function back($id)
    {
        $id=student::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse(new BaseController($student),'student retreive successfully');
    }



}
