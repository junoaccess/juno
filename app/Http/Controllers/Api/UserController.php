<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        return UserResource::collection(
            $this->userService->paginate()
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'User created successfully!',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource(
            $this->userService->loadRelationships($user)
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return response()->json([
            'message' => 'User updated successfully!',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return response()->json([
            'message' => 'User deleted successfully!',
        ]);
    }
}
