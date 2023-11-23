<?php

namespace App\Http\Controllers\Api;

use App\Models\Code;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|confirmed',
            'course_code' => 'required|exists:codes,code',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => true,
        ]);

        $code = Code::where('code', $request->course_code)->first();
        $course = Course::find($code->codeable_id);
        $department = $course->department;
        $school = $department->school;

        $user->assignRole('student');
        $user->schools()->attach($school->id);
        $user->departments()->attach($department->id);
        $user->courses()->attach($course->id);

        return response()->json([
            'message' => "Selamat datang $user->name",
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            if (! $user->hasRole('student')) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Versi aplikasi hanya diperuntukan untuk siswa, admin dan guru silakan gunakan versi web',
                ], 401);
            } else {
                if (! $user->status) {
                    return response()->json([
                        'error' => 'Unauthorized',
                        'message' => 'Akun anda dinonaktifkan, silakan hubungi admin'
                    ], 401);
                } else {
                    $user->update([
                        'last_login' => now(),
                        'last_login_ip' => $request->ip()
                    ]);

                    return response()->json([
                        'message' => "Selamat datang $user->name",
                        'access_token' => $user->createToken('auth_token')->plainTextToken,
                        'token_type' => 'Bearer',
                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Email atau password salah',
            ], 401);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $userId = auth()->user()->id;
        $user = User::find($userId);

        if (! Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Password lama salah',
            ], 422);
        } else {
            $user->update([
                'password' => bcrypt($request->password),
                'password_by_admin' => 0,
            ]);
            return response()->json([
                'message' => 'Password berhasil diubah',
            ]);
        }
    }

    public function storeFCMToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required',
        ]);

        try {
            $user = auth()->user();
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);

            return response()->json([
                'message' => 'FCM token berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'FCM token gagal disimpan',
            ], 500);
        }
    }
}
