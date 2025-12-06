<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\InvitationService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AcceptInvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService,
        protected UserService $userService,
    ) {}

    /**
     * Show the invitation acceptance page.
     * - For guests: show registration form
     * - For authenticated users: show acceptance confirmation
     */
    public function show(Request $request, string $token): Response
    {
        $invitation = $this->invitationService->findByToken($token);

        if (! $invitation) {
            abort(404, 'Invalid or expired invitation.');
        }

        $this->invitationService->validateInvitation($invitation);
        $this->invitationService->loadRelationships($invitation);

        // Check if a user account exists for this email
        $hasAccount = $this->userService->existsByEmail($invitation->email);

        $view = Auth::check() ? 'auth/accept-invitation' : 'auth/register-with-invitation';

        return Inertia::render($view, [
            'invitation' => $invitation,
            'token' => $token,
            'hasAccount' => $hasAccount,
        ]);
    }

    /**
     * Accept the invitation.
     * - For guests: create account and accept invitation
     * - For authenticated users: just accept invitation
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (! $invitation) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired invitation.'],
            ]);
        }

        $this->invitationService->validateInvitation($invitation);

        // If user is not authenticated, create account
        if (! Auth::check()) {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = $this->userService->createFromInvitation($validated, $invitation->email);
            Auth::login($user);
        } else {
            $user = Auth::user();

            // Verify the invitation is for this user
            if ($user->email !== $invitation->email) {
                throw ValidationException::withMessages([
                    'email' => ['This invitation is for a different email address.'],
                ]);
            }
        }

        $this->invitationService->accept($invitation, $user);

        return redirect()->away($this->invitationService->getInvitationUrl($invitation))
            ->with('success', "You've successfully joined {$invitation->organization->name}!");
    }
}
