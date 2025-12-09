<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendBaseController;

use Illuminate\Http\Request;
use App\Http\Requests\Backend\ProfileUpdateRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends BackendBaseController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('backend.profile.index', ['admin' => $admin]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $admin = Admin::find(Auth::guard('admin')->user()->id);

        if ($request->has('name')) {
            $admin->name = $request->name;
        }
        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        if ($request->hasFile('image')) {
            $admin->clearMediaCollection('profile');
            $admin->addMediaFromRequest('image')->toMediaCollection('profile');
        }

        if ($request->has('current_password')) {
            if (Hash::check($request->current_password, $admin->password)) {
                $admin->password = Hash::make($request->new_password);
            } else {
                return redirect()->route('backend.profile.index')->withErrors([
                    'current_password' => 'Current password is incorrect.'
                ]);
            }
        }

        $admin->save();

        flash()->success('Profile updated successfully');
        return redirect()->route('backend.profile.index');
    }

}
