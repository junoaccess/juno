<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Role::class);

        return Inertia::render('Roles/Index', [
            'roles' => $this->roleService->paginate(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Role::class);

        return Inertia::render('Roles/Create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = $this->roleService->create($request->validated());

        return redirect()
            ->route('roles.show', $role)
            ->with('success', 'Role created successfully!');
    }

    public function show(Role $role): Response
    {
        $this->authorize('view', $role);

        return Inertia::render('Roles/Show', [
            'role' => $this->roleService->loadRelationships($role),
        ]);
    }

    public function edit(Role $role): Response
    {
        $this->authorize('update', $role);

        return Inertia::render('Roles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->update($role, $request->validated());

        return redirect()
            ->route('roles.show', $role)
            ->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
