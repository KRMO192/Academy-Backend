<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Http\Resources\InstructorResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;

class InstructorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instructors=Instructor::all();
        return $this->sendResponse(InstructorResource::collection($instructors),'All instructors');
    }

    public function trachedInstructors()
    {
        $instructors=Instructor::onlyTrached()->latest();
        return $this->sendResponse(InstructorResource::collection($instructors),'All trached instructors');
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
            'instructor_image' => 'required|image',
            'address' => 'required',
            'bio' => 'required',
            'phone' => 'required'
        ]);


        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('image')) {
            $destinationPath='images/instructors';
            $instructorImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$instructorImage);
            $input['image']="$instructorImage";
        }
        $instructor=Instructor::create($input);
        return $this->sendResponse(new InstructorResource($instructor),'instructor added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        $instructor=Instructor::find($instructor->id);
        if(is_null($instructor)){
            return $this->sendError('instructor not found', $validate->errors());
        }

        return $this->sendResponse(new InstructorResource($instructor),'instructor found successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instructor $instructor)
    {
        $input=$request->all();
        $validate=validator::make($input,[
            'name' => 'required',
            'instructor_image' => 'required',
            'address' => 'required',
            'bio' => 'required',
            'phone' => 'required'
        ]);

        if($validate->fails()){
            return $this->sendError('Validate error', $validate->errors());
        }

        if ($image=$request->file('image')) {
            $destinationPath='images/instructors/';
            $instructorImage=date('YmdHis').".".$image->getClientOriginalExtention();
            $image->move($destinationPath,$instructorImage);
            $input['image']="$instructorImage";
            $instructor->instructor_image=$input['instructor_image'];
        }else{
            unset($input['image']);
        }

        $instructor->name=$input['name'];

        $instructor->address=$input['address'];
        $instructor->bio=$input['bio'];
        $instructor->phone=$input['phone'];


        return $this->sendResponse(new InstructorResource($instructor),'instructor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        $instructor->delete();
        return $this->sendResponse(new InstructorResource($instructor),'instructor deleted successfully');
    }

    public function softDelete($id)
    {
        $instructor=Instructor::find($id)->delete();
        return $this->sendResponse(new InstructorResource($instructor),'instructor deleted successfully');

    }
    public function forceDelete($id)
    {
        $id=Instructor::onlyTrashed()->where('id',$id)->forceDelete();
        return $this->sendResponse(new InstructorResource($instructor),'instructor deleted successfully');

    }
    public function back($id)
    {
        $id=Instructor::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse(new InstructorResource($instructor),'instructor retreive successfully');
    }
}
