<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Get validated data but exclude email
        $validated = $request->validated();
        unset($validated['email']); // Remove email from update data
        
        // Handle checkbox: if not present in request, set to false
        if (!isset($validated['auto_follow_tasks'])) {
            $validated['auto_follow_tasks'] = false;
        } else {
            $validated['auto_follow_tasks'] = (bool) $validated['auto_follow_tasks'];
        }

        // Normalize empty WhatsApp group link to null
        if (array_key_exists('whatsapp_group_url', $validated)) {
            $validated['whatsapp_group_url'] = $validated['whatsapp_group_url'] ?: null;
        }

        if (array_key_exists('whatsapp_group_jid', $validated)) {
            $validated['whatsapp_group_jid'] = $validated['whatsapp_group_jid'] ?: null;
        }
        
        $request->user()->fill($validated);
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
