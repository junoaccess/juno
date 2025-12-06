<?php

namespace App\Http\Controllers\Web;

use App\Filters\InvitationFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $invitationService,
    ) {}

    public function index(InvitationFilter $filter): Response
    {
        $this->authorize('viewAny', Invitation::class);

        return Inertia::render('Invitations/Index', [
            'invitations' => $this->invitationService->paginate(15, $filter),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Invitation::class);

        return Inertia::render('Invitations/Create');
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $invitation = $this->invitationService->create($request->validated());

        return redirect()
            ->route('invitations.show', $invitation)
            ->with('success', 'Invitation sent successfully!');
    }

    public function show(Invitation $invitation): Response
    {
        $this->authorize('view', $invitation);

        return Inertia::render('Invitations/Show', [
            'invitation' => $this->invitationService->loadRelationships($invitation),
        ]);
    }

    public function edit(Invitation $invitation): Response
    {
        $this->authorize('update', $invitation);

        return Inertia::render('Invitations/Edit', [
            'invitation' => $invitation,
        ]);
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation): RedirectResponse
    {
        $this->invitationService->update($invitation, $request->validated());

        return redirect()
            ->route('invitations.show', $invitation)
            ->with('success', 'Invitation updated successfully!');
    }

    public function destroy(Invitation $invitation): RedirectResponse
    {
        $this->authorize('delete', $invitation);

        $this->invitationService->delete($invitation);

        return redirect()
            ->route('invitations.index')
            ->with('success', 'Invitation deleted successfully!');
    }
}
