<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController ;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\InstructorResource;
use Validator;
use Auth;

class InstructorController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instrtuctors=Instructor::latest()->get();
        return $this->sendResponse(BaseController::collection($instrtuctors),'All instrtuctors');
    }

    public function trachedInstructors()
    {
        $instrtuctors=Instructor::onlyTrashed()->latest()->get();
        return $this->sendResponse(InstructorController::collection($instrtuctors), 'All trached instrtuctors');
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
        if($user_type == 'instructor' ){           // If the user who wants to create a instrtuctor account is a instrtuctor
            $input = $request->all();
            $user_id = Auth::user()->id;
            $input["user_id"]=$user_id;
            $validate=validator::make($input,[
                'name' => 'required',
                'instructor_image' => 'required',
                'address' => 'required',
                'bio' => 'required',
                'phone' => 'required',
                'user_id' => 'required'
            ]);


            if($validate->fails()){
                return $this->sendError('Validate error', $validate->errors());
            }

            if ($image=$request->file('instructor_image')) {
                $destinationPath='images/instructors/';
                $instructorImage=date('YmdHis').".".$image->getClientOriginalExtension();
                $image->move($destinationPath,$instructorImage);
                $input['instructor_image']=$instructorImage;
            }

            $instructor=Instructor::create($input);
            return $this->sendResponse($instructor,'instructor added successfully');

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
                    'instructor_image' => 'required',
                    'address' => 'required',
                    'bio' => 'required',
                    'phone' => 'required',
                ]);

                if ($validate->fails()) {
                    DB::rollBack();
                    return $this->sendError('Validation error', $validate->errors());
                }

                if ($image = $request->file('image')) {
                    $destinationPath = 'images/instructors/';
                    $instructorImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                    $image->move($destinationPath, $instructorImage);
                    $input['instructor_image'] = $instructorImage;
                }

                $instructor = Instructor::create($input);
                DB::commit();

                return $this->sendResponse($instructor, 'Instructor added successfully');
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
        $instructor=Instructor::find($id);    // find the instructor who has this id
        $u_id=$instructor['user_id'];      // get the user_id which belongs this instructor
        $auth_id=auth()->user()->id;    // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;     // get user type want to show this page (id from users tabel)
        if(is_null($instructor)){
            return $this->sendError('instructor not found');
        }
        if ($auth_id==$u_id || $type == "admin" ) {          // The instructor can only see his profile and admin can see all instructor profiles
            return $this->sendResponse($instructor,'instructor found successfully');
        }else{
            return $this->sendError('You do not have permission to access for this page');
        }
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
    public function update(Request $request,$id)
    {
        $instructor=Instructor::find($id);            // find the instructor who has this id
        $u_id=$instructor['user_id'];              // get the user_id which belongs this instructor
        $auth_id=auth()->user()->id;            // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;             // get user type want to show this page (id from users tabel)
        if ($auth_id==$u_id || $type == "admin" ) {
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

            if ($image=$request->file('instructor_image')) {
                $destinationPath='images/instructors/';
                $instructorImage=date('YmdHis').".".$image->getClientOriginalExtension();
                $image->move($destinationPath,$instructorImage);
                $input['instructor_image']="$instructorImage";
                $instructor->instructor_image=$input['instructor_image'];
            }else{
                unset($input['image']);
            }

            $instructor->name=$input['name'];
            $instructor->address=$input['address'];
            $instructor->bio=$input['bio'];
            $instructor->phone=$input['phone'];
            $instructor->fill($input);
            $instructor->save(); // Save the changes into the database

            return $this->sendResponse($instructor,'instructor updated successfully');
        }else{
            return $this->sendError('You do not have permission to access for this page');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        //
    }

    public function softDelete($id)
    {
        $instructor=Instructor::find($id);                // find the instructor who has this id
        $u_id=$instructor['user_id'];                  // get the user_id which belongs this instructor
        $auth_id=auth()->user()->id;                // get user id who want to show this page (id from users tabel)
        $type=auth()->user()->type;                 // get user type want to show this page (id from users tabel)
        if ($auth_id==$u_id || $type == "admin" ) {

        $instructor=Instructor::find($id)->delete();
        return $this->sendResponse($instructor,'instructor deleted successfully');
        }else{
            return $this->sendError('You do not have permission to delete this instructor');
        }
    }


    public function forceDelete($id)
    {
        $instructor=Instructor::onlyTrashed()->where('id',$id)->forceDelete();
        if($instructor == true){
            return $this->sendResponse($instructor,'instructor deleted successfully');
        }else{
            return $this->sendError('you must first send this account to the trash');
        }
    }


    public function back($id)
    {
        $instructor=Instructor::onlyTrashed()->where('id',$id)->first()->restore();
        return $this->sendResponse($instructor,'instructor retreive successfully');
    }
}
