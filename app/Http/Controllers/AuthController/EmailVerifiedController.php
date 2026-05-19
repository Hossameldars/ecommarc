<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailVerifiedController extends Controller
{
   public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user->otp !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'الـ OTP غلط أو انتهت صلاحيته ',
            ], 422);
        }
        $user->update([
            'email_verified_at' => now(),
            'otp'               => null,
            'otp_expires_at'    => null,
        ]);

        //$token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'تم تأكيد الإيميل بنجاح ',
            'user'    => $request->email,
        ]);
    } ////
     public function resendOtp(Request $request)
    {
        
  $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
          
    ]);
if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }
        $user = User::where('email', $request->email)->first();

      
      $otp   = generateOtp(); 

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),

        ]);

        Mail::to($user->email)->send(new EmailVerified($otp));

        return response()->json([
            'status'  => true,
            'message' => 'تم إرسال كود OTP جديد ',
        ]);
    }
}
