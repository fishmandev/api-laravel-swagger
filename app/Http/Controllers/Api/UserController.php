<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * User API Controller
 * 
 * Handles all CRUD operations for User resources through RESTful API endpoints.
 * Returns standardized JSON responses with proper HTTP status codes.
 */
class UserController extends Controller
{
    /**
     * Initialize controller with UserService dependency
     * 
     * @param UserService $userService Business logic service for user operations
     */
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a paginated listing of users
     * 
     * Retrieves all users with pagination support. Default pagination is 15 items per page.
     * Supports query parameters for pagination control with validation.
     * 
     * @param IndexUserRequest $request Validated request with pagination parameters
     * @return UserCollection Paginated user collection with custom pagination structure
     */
    public function index(IndexUserRequest $request): UserCollection
    {
        $perPage = (int) $request->get('per_page', 15);
        $users = $this->userService->getAllUsers($perPage);

        return new UserCollection($users);
    }

    /**
     * Store a newly created user
     * 
     * Creates a new user with validated data. Password is automatically hashed.
     * Email uniqueness is enforced at validation level.
     * 
     * @param StoreUserRequest $request Validated request containing user data
     * @return JsonResponse Created user resource
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $dto = UserDto::fromStoreRequest($request);
        $user = $this->userService->createUser($dto);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified user
     * 
     * Retrieves a single user by ID. Laravel's route model binding automatically
     * resolves the User model or returns 404 if not found.
     * 
     * @param User $user User model instance (auto-resolved by Laravel)
     * @return UserResource User resource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified user
     * 
     * Updates an existing user with validated data. Only provided fields are updated.
     * Email uniqueness is enforced excluding the current user.
     * Password is re-hashed if provided.
     * 
     * @param UpdateUserRequest $request Validated request containing updated user data
     * @param User $user User model instance to update (auto-resolved by Laravel)
     * @return UserResource Updated user resource
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $dto = UserDto::fromUpdateRequest($request);
        $updatedUser = $this->userService->updateUser($user, $dto);

        return new UserResource($updatedUser);
    }

    /**
     * Remove the specified user
     * 
     * Permanently deletes a user from the database. This action cannot be undone.
     * Returns a success message upon completion.
     * 
     * @param User $user User model instance to delete (auto-resolved by Laravel)
     * @return JsonResponse Success message
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->deleteUser($user);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
