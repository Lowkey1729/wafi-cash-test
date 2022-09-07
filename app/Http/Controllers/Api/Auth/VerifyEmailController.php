<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\ApiResponseEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verifyEmail(Request $request)
    {
        try {
            $user = User::query()->find($request->route('id'));
            if($user->hasVerifiedEmail()){
                return response()->json([
                    'status' => ApiResponseEnum::statusSuccess()->value,
                    'message' => 'Email has been verified.'
                ]);
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            return response()->json([
                'status' => ApiResponseEnum::statusSuccess()->value,
                'message' => 'Email verified successfully.'
            ]);

        }catch (\Exception $exception)
        {
            return response()->json([
                'status'=> ApiResponseEnum::statusFailed()->value,
                'message' => $exception->getMessage()
            ]);
        }


    }
}
