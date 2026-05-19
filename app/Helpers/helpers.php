<?php

if (!function_exists('successResponse')) {
    function successResponse($message, $data = null, $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message, $code = 400)
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }
}
if (!function_exists('generateOtp')) {
    function generateOtp()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}