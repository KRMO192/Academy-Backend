<?php

namespace App\Http\Controllers;

use App\Http\Resources\Course as CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class CourseController extends BaseController
{
    public function __construct(){
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses=Course::all()->latest();
        return $this->sendResponse(CourseResource::collection($courses),'All courses');
    }



    public function trachedCourses()
    {
        $courses=Course::onlyTrached()->latest();
        return $this->sendResponse(CourseResource::collection($courses),'All courses');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input=$request->all();
        $validate=validator::make($input,[
            'title' => 'required',
            'description' => 'required',
            'instructor_id' => 'required',
            'status' => 'required'
        ]);

        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('image')) {
            $destinationPath='images/courses';
            $courseImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$courseImage);
            $input['image']="$courseImage";
        }

        $course=Course::create($input);
        return $this->sendResponse(new CourseResource($course),'Course added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course=Course::find($course->id);
        if(is_null($course)){
            return $this->sendError('course not found', $validate->errors());
        }

        return $this->sendResponse(new CourseResource($course),'Course found successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $input=$request->all();
        $validate=validator::make($input,[
            'title' => 'required',
            'description' => 'required',
            'instructor_id' => 'required',
            'status' => 'required'
        ]);

        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('image')) {
            $destinationPath='images/courses';
            $courseImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$courseImage);
            $input['image']="$courseImage";
            $course->course_image=$input['course_image'];
        }else{
            unset($input['image']);
        }

        $course->status=$input['status'];
        $course->title=$input['title'];
        $course->description=$input['description'];
        $course->instructor_id=$input['instructor_id'];
        $course->start_at=$input['start_at'];
        $course->end_at=$input['end_at'];


        return $this->sendResponse(new CourseResource($course),'Course updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return $this->sendResponse(new CourseResource($course),'Course deleted successfully');
    }




    public function softDelete($id)
    {
        $course=Course::find($id)->delete();
        return $this->sendResponse(new CoursetResource($course),'Course deleted successfully');

    }
    public function forceDelete($id)
    {
        $id=Course::onlyTrashed()->where('id',$id)->forceDelete();
        return $this->sendResponse(new CoursetResource($course),'Course deleted successfully');

    }
    public function back($id)
    {
        $id=Course::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse(new CoursetResource($course),'Course retreive successfully');
    }
}
