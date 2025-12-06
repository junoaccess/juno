<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\PermissionService;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Permission::class);

        return Inertia::render('Permissions/Index', [
            'permissions' => $this->permissionService->paginate(),
        ]);
    }

    public function show(Permission $permission): Response
    {
        $this->authorize('view', $permission);

        return Inertia::render('Permissions/Show', [
            'permission' => $this->permissionService->loadRelationships($permission),
        ]);
    }
}
