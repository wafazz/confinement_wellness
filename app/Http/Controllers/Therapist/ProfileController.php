<?php

namespace App\Http\Controllers\Therapist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('therapist.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'state' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'kkm_cert_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
