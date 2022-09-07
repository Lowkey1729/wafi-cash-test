<?php


namespace App\Http\Controllers\Api\Auth;


use App\Enums\ApiResponseEnum;
use App\Helpers\ApiResponse;
use App\Models\User;
use App\Services\Traits\PasswordTrait;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController
{

    use PasswordTrait;

    public function sendResetLink(Request $request): JsonResponse
    {
        try {
            $request->validate($this->resendLinkRules());

            $user = User::where('email', $request->only('email'))->first();
            if (empty($user)) {
                return response()->json([
                    'status' => ApiResponseEnum::statusFailed()->value,
                    'message' => 'invalid user email.'
                ]);
            }

            $response = Password::broker()->sendResetLink(
                $request->only('email')
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($response)
                : $this->sendResetLinkFailedResponse($request, $response);
        } catch (\RuntimeException $e) {
            return response()->json([
                'status' => ApiResponseEnum::statusFailed()->value,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function sendResetLinkResponse(string $response): JsonResponse
    {
        return response()->json([
            'status' => ApiResponseEnum::statusSuccess()->value,
            'message' => trans($response)
        ]);
    }

    protected function sendResetLinkFailedResponse(Request $request, string $response): JsonResponse
    {
        return response()->json([
            'status' => ApiResponseEnum::statusFailed()->value,
            'message' => trans($response),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate($this->resetPasswordrules());
        $data = $request->only('email', 'password', 'password_confirmation', 'token');
        try {
            $response = Password::broker()->reset($data, function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            });

            $return_value = $this->checkIfPasswordReset($response);


            return response()->json([
                'status' => $return_value['status'],
                'message' => $return_value['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => ApiResponseEnum::statusFailed()->value,
                'message' => "An unexpected error occurred",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate($this->updatePasswordrules());
        $user = $request->user();
        try {
            if (!Hash::check($validated['old_password'], $request->user()->password)) {
                return ApiResponse::failed('The retrieved password does not match our record.');
            }

            $user->update([
                'password' => bcrypt($validated['password'])
            ]);

            return response()->json([
                'status' => ApiResponseEnum::statusSuccess(),
                'message' => 'Password updated successfully.',
                'result' => [
                    'data' => null
                ]
            ]);
        } catch (\Exception $e) {
            ApiResponse::exceptionResponse($e);
        }
    }

    protected function checkIfPasswordReset($response): array
    {
        return match ($response) {
            Password::PASSWORD_RESET => [
                'message' => 'Password reset successful',
                'status' => ApiResponseEnum::statusSuccess()->value
            ],

            Password::INVALID_TOKEN => [
                'message' => 'Invalid Token',
                'status' => ApiResponseEnum::statusFailed()->value
            ],
            default => [
                'message' => 'Unable to send info to this email',
                'status' => ApiResponseEnum::statusFailed()->value
            ],

        };
    }
}
