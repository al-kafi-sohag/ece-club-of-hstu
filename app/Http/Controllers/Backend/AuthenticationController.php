<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendBaseController;

use App\Http\Requests\Backend\LoginRequest;
use App\Http\Requests\Backend\ForgotPasswordRequest;
use App\Http\Requests\Backend\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\Admin;
use Illuminate\Foundation\Mix;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends BackendBaseController
{
    public function __construct()
    {
    }


    public function login()
    {
        $user = Auth::guard('admin')->user();
        if($user){
            return redirect()->route('backend.dashboard.index');
        }
        return view('backend.auth.login.index');
    }

    public function submit(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $admin = Admin::where('email', $credentials['email'])->first();

        if ($admin->status !== Admin::STATUS_ACTIVE) {
            flash()->error('Your account is not active.');
            return back()->withInput();
        }

        if (!Hash::check($credentials['password'], $admin->password)) {
            return back()->withInput()->withErrors([
                'password' => 'The provided password do not match our records.',
            ]);
        }

        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        flash()->success('Welcome back, ' . $admin->name . '!');

        $previous_logged_url = session()->get('previous_logged_visit_url'.$admin->id);
        if($previous_logged_url){
            return redirect($previous_logged_url);
        }

        return redirect()->route('backend.dashboard.index');

    }

    public function forgotPassword(): View
    {
        return view('backend.auth.forgot-password.index');
    }

    public function forgotPasswordSubmit(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        try {
            if ($status === Password::RESET_LINK_SENT) {
                flash()->success('Check your email for reset password instructions.');
                return redirect()->back();
            }
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        } catch (\Exception $e) {
            flash()->error('Something went wrong. Please try again.');
            Log::error($e->getMessage());
            return redirect()->back();
        }
    }

    public function resetPassword($token, Request $request): View
    {
        return view('backend.auth.reset-password.index', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPasswordSubmit(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);
                $admin->save();
            }
        );

        try {
            if ($status === Password::PASSWORD_RESET) {
                flash()->success('Your password has been reset! Please login.');
                return redirect()->route('backend.auth.login');
            }
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        } catch (\Exception $e) {
            flash()->error('Something went wrong. Please try again.');
            return redirect()->back();
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerate();
        return redirect()->route('backend.auth.login');
    }
}
