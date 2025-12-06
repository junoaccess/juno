<?php

namespace App\Http\Controllers\Web;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function index(UserFilter $filter): Response
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('users/index', [
            'users' => $this->userService->paginate(15, $filter),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->create($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User created successfully!');
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        return Inertia::render('Users/Show', [
            'user' => $this->userService->loadRelationships($user),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
}
