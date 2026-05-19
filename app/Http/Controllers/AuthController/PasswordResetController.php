<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function Emaivar(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email, // ✅ 2
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        Mail::to($request->email)->send(new ResetPasswordMail($token));

        return response()->json([
            'status'  => true,
            'message' => 'تم إرسال رابط إعادة التعيين على بريدك الإلكتروني',
        ]);
    }

    public function Sendtoken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // ✅ 4 — شرطين منفصلين
        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token) || now()->diffInMinutes($resetRecord->created_at) > 60) {
            return response()->json([
                'status'  => false,
                'message' => 'الـ token غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        return response()->json([
            'status'  => true,
            'message' => 'كود التحقق صحيح',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'status'  => false,
                'message' => 'الـ token غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'status'  => false,
                'message' => 'انتهت صلاحية الـ token، أعد المحاولة',
            ], 422);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم تغيير كلمة المرور بنجاح',
        ]);
    }
}