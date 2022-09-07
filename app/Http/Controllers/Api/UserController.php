<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiResponseEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $user = $request->user();

        return response()->json([
            'status' => ApiResponseEnum::statusSuccess()->value,
            'message' => 'User details retrieved successfully.',
            'result' => [
                'data' => $user
            ],
        ]);
    }

}
