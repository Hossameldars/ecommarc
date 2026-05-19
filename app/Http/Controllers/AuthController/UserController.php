<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Http\Requests\loginRequest;
use App\Http\Requests\ProfileUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Mail\EmailVerified;
use App\Mail\verfiyEmai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
 
    $otp   = generateOtp(); 
 $user = User::create([
            'name'      => $request->first_name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);
      
    Mail::to($user->email)->send(new EmailVerified($otp));

      return response()->json([
            'message' => 'User successfully registered',
            'user'=>$user,
        ], 201);
    }
  public function login(loginRequest $request)
{
  

    if (!$token = auth()->guard('api')->attempt([
        'email' => $request->email,
        'password' => $request->password
    ])) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->createNewToken($token);
}


  function createNewToken( $token)
{
    return response()->json([
        'access_token' => $token,
        'token_type'   => 'bearer',
        'expires_in'   => config('jwt.ttl') * 60,
        'user'         => auth()->guard('api')->user()
    ]);
}
public function logout()
{
    auth()->guard('api')->logout();

    return response()->json([
        'message' => 'تم تسجيل الخروج بنجاح',
    ]);

}



}
