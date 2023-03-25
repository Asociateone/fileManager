<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(User $profile): View
    {
        return view('profile.edit', [
            'user' => $profile,
        ]);
    }

	/**
	 * @param ProfileUpdateRequest $request
	 * @param User $profile
	 * @return RedirectResponse
	 */
    public function update(ProfileUpdateRequest $request, User $profile): RedirectResponse
    {
        $profile->fill($request->safe()->all());

        if ($profile->isDirty('email')) {
            $profile->email_verified_at = null;
        }

        $profile->save();

        return Redirect::route('profile.show', ['profile' => $profile])->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
