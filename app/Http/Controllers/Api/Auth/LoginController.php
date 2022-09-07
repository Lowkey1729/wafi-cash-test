<?php


namespace App\Http\Controllers\Api\Auth;


use App\Actions\CreateReservedAccount;
use App\Enums\ApiResponseEnum;
use App\Enums\ServiceType;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController
{

    use AuthTrait;


    public function __invoke(Request $request): JsonResponse
    {

        $request->validate($this->rules());
        try {
            $user = User::query()->where('email', $request->email)->first();


            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => ApiResponseEnum::statusFailed()->value,
                    'message' => 'The provided credentials are incorrect.',
                ], 401);
            }

            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'status' => ApiResponseEnum::statusSuccess()->value,
                    'is_verified' => false,
                    'message' => 'Email has not been verified.',

                ], 401);
            }


            $token = $user->createToken('Wafi-cash')->plainTextToken;

            return response()->json([
                'status' => ApiResponseEnum::statusSuccess()->value,
                'is_verified' => true,
                'message' => 'User logged in successfully',
                'token' => $token,
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => ApiResponseEnum::statusFailed(),
                'message' => 'An unexpected error occurred.',
                'error' => $exception->getMessage()
            ], 500);
        }


    }


}
