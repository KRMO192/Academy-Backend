<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseController extends Controller
{
    public function sendResponse($result , $message , $code=200)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];

        return response()->json($response,$code);
    }

    public function sendError($error , $errorMessage=[] , $code=404)
    {
        $response = [
            'success' => false,
            'data' => $error
        ];

        if (!empty($errorMessage)){
            $response['data'] = $errorMessage;
        }

        return response()->json($response, $code);
    }


    public static function collection($resource)
    {
        return new ResourceCollection($resource);
    }
}
