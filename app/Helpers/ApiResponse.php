<?php


namespace App\Helpers;


use App\Enums\ApiResponseEnum;
use Illuminate\Http\JsonResponse;

class ApiResponse
{

    public static function success($message = '', $data = [], $renameDataTo = 'data', $showEmptyData = false): JsonResponse
    {
        $response_array = ['status' => 'success'];
        $message ? $response_array['message'] = $message : '';
        $response_array['result'] = [
            'data' => $data ?? null
        ];


//        if ( empty($data) && $showEmptyData === true ) {
//            $response_array[$renameDataTo] = $data;
//        }

        return response()->json($response_array);
    }


    public static function failed($message = '', $data = null, $renameDataTo = 'data', $statusCode = 200): JsonResponse
    {
        $response_array = ['status' => 'failed',];
        $message ? $response_array['message'] = $message : '';
        $response_array['result'] = [
            'data' => $data ?? null
        ];

        return response()->json($response_array, $statusCode);
    }

    public static function exceptionResponse($exception): JsonResponse
    {
        return response()->json([
            'status' => ApiResponseEnum::statusFailed()->value,
            'message' => 'An unexpected error was encountered.',
            'result' => [
                'data' => null
            ],
            'error' => $exception->getMessage()
        ], 500);
    }
}
