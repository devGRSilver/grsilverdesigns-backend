<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Constant;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\ForgetPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthController extends ResponseController
{
    public function showLoginForm()
    {

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login', ['title' => 'Admin Login']);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return $this->errorResponse('Invalid email or password.', 401);
        }

        if ($user->status != Constant::ACTIVE) {
            return $this->errorResponse('Your account is inactive. Please contact administrator.', 403);
        }

        $roles = $user->roles->pluck('name')->toArray();
        $allowRole = Role::where(['guard_name' => 'admin', 'status' => Constant::ACTIVE])->pluck('name')->toArray();
        if (empty(array_intersect($roles, $allowRole))) {
            return $this->errorResponse(
                'The administration has disabled this role. Users with this role will not be able to log in until it is reactivated.',
                403
            );
        }


        if (!Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Invalid email or password.', 401);
        }

        // Delete old sessions
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Login using admin guard
        Auth::guard('admin')->login($user);

        return $this->successResponse([], 'Login successful', route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        $adminId = Auth::guard('admin')->id(); // Get admin ID before logout

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear session for this admin
        DB::table('sessions')->where('user_id', $adminId)->delete();

        return $this->successResponse([], 'Logout successful', route('admin.login'));
    }

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forget_password', ['title' => 'Forget Password']);
    }

    public function sendResetLinkEmail(ForgetPasswordRequest $request)
    {
        try {
            $plainToken = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token'      => Hash::make($plainToken),
                    'created_at' => now(),
                ]
            );

            $resetUrl = url('/admin/reset-password') .
                '?token=' . urlencode($plainToken) .
                '&email=' . urlencode($request->email);

            Mail::send('admin.emails.forget_password', [
                'resetUrl' => $resetUrl,
                'token'    => $plainToken,
                'email'    => $request->email
            ], function ($message) use ($request) {
                $message->to($request->email)
                    ->from(config('mail.from.address'), Constant::APP_NAME)
                    ->subject('Password Reset Link');
            });

            return $this->successResponse([], 'Reset password link has been sent to your email.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to process request: ' . $e->getMessage(), 500);
        }
    }

    public function showResetForm(Request $request)
    {
        return view('admin.auth.reset_password', [
            'title' => 'Reset Password',
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->merge(['email' => trim($request->email)]);

        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => [
                'required',
                'string',
                'min:6',
                'max:30',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'not_regex:/\s/',
                'confirmed',
            ],
        ]);

        try {
            $record = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$record || !Hash::check($request->token, $record->token)) {
                return $this->errorResponse('Invalid or expired token.', 400);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return $this->successResponse([], 'Password reset successfully.', route('admin.login'));
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reset password: ' . $e->getMessage(), 500);
        }
    }
}
