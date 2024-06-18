<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Auth;

class AdminController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins=Admin::latest()->get();
        return $this->sendResponse(BaseController::collection($admins),'All Admins');
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
        if($user_type == 'admin' ){           // If the user who wants to create a student account is a Admin
            $input = $request->all();
            $user_id = Auth::user()->id;
            $input["user_id"]=$user_id;
            $validate=validator::make($input,[
                'name' => 'required',
                'brith_date' => 'required',
                'phone' => 'required',
                'user_id' => 'required'
            ]);

            if($validate->fails()){
                return $this->sendError('Validate error', $validate->errors());
            }

            $admin=Admin::create($input);
            return $this->sendResponse($admin,'Admin added successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $admin=Admin::find($id)->forceDelete();
        if($admin == true){
            return $this->sendResponse($admin,'admin deleted successfully');
        }
    }
}
