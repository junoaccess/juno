<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInvitationRequest;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AcceptInvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService
    ) {}

    /**
     * Show the invitation acceptance page.
     */
    public function show(Request $request, string $token): Response
    {
        $invitation = $this->invitationService->findByToken($token);

        if (! $invitation) {
            abort(404, 'Invitation not found.');
        }

        if (! $invitation->canBeAccepted()) {
            $message = $invitation->is_expired
                ? 'This invitation has expired.'
                : 'This invitation has already been accepted.';

            return Inertia::render('Invitations/Invalid', [
                'message' => $message,
            ]);
        }

        // Check if user exists with this email
        $existingUser = User::where('email', $invitation->email)->first();
        $isLoggedIn = Auth::check();
        $emailMatches = $isLoggedIn && Auth::user()->email === $invitation->email;

        return Inertia::render('Invitations/Accept', [
            'invitation' => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'name' => $invitation->name,
                'organization' => [
                    'name' => $invitation->organization->name,
                    'slug' => $invitation->organization->slug,
                ],
                'inviter' => $invitation->inviter?->full_name,
                'roles' => $invitation->roles,
                'expires_at' => $invitation->expires_at?->toDateString(),
            ],
            'token' => $token,
            'userExists' => (bool) $existingUser,
            'isLoggedIn' => $isLoggedIn,
            'emailMatches' => $emailMatches,
            'needsRegistration' => ! $existingUser && ! $isLoggedIn,
        ]);
    }

    /**
     * Accept the invitation and create/link user account.
     */
    public function store(AcceptInvitationRequest $request, string $token)
    {
        $invitation = $this->invitationService->findByToken($token);

        if (! $invitation || ! $invitation->canBeAccepted()) {
            return back()->withErrors([
                'token' => 'This invitation is no longer valid.',
            ]);
        }

        $validated = $request->validated();

        // Determine or create the user
        $user = User::where('email', $invitation->email)->first();

        if (! $user) {
            // Create new user from registration data
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email' => $invitation->email,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(), // Consider invitation as email verification
            ]);
        }

        // Accept the invitation (attach user to org and roles)
        $this->invitationService->accept($invitation, $user);

        // Log the user in
        Auth::login($user, $request->boolean('remember', false));

        // Redirect to the organization dashboard
        return redirect()->route('organizations.show', $invitation->organization)
            ->with('success', "Welcome to {$invitation->organization->name}!");
    }
}
