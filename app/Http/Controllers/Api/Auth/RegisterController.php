<?php


namespace App\Http\Controllers\Api\Auth;


use App\Enums\ApiResponseEnum;
use App\Helpers\ApiResponse;
use App\Models\User;
use App\Services\Traits\RegisterTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController
{
    use RegisterTrait;

    public function __invoke(Request $request): JsonResponse
    {

        $data = $request->validate($this->rules(), $this->messages());

        try {

            $user = $this->create($data);

            return response()->json([
                'status' => ApiResponseEnum::statusSuccess()->value,
                'message' => "Your signup was successful. Please check your email to activate your account.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => ApiResponseEnum::statusFailed()->value,
                'message' => $e->getMessage()
            ], 500);
        }

    }

}
