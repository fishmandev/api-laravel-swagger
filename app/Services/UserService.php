<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Models\User;
use App\Pagination\CustomPaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * User Service
 * 
 * Handles all business logic related to User operations.
 * Encapsulates database interactions and provides a clean API for controllers.
 */
class UserService
{
    /**
     * Retrieve all users with pagination
     * 
     * Fetches users from database with pagination support. Default page size is 15.
     * Results are ordered by creation date (newest first).
     * 
     * @param int $perPage Number of users per page (default: 15, max: 100)
     * @return CustomPaginator Paginated collection of users
     * 
     * @throws \InvalidArgumentException When perPage is less than 1 or greater than 100
     */
    public function getAllUsers(int $perPage = 15): CustomPaginator
    {
        $this->validatePerPage($perPage);

        return User::orderBy('created_at', 'desc')
            ->customPaginate($perPage);
    }

    /**
     * Retrieve a user by their ID
     * 
     * Finds a user by their primary key. Returns null if user doesn't exist.
     * 
     * @param int $id User's primary key
     * @return User|null User instance or null if not found
     * 
     * @throws \InvalidArgumentException When ID is not a positive integer
     */
    public function getUserById(int $id): ?User
    {
        $this->validateUserId($id);

        return User::find($id);
    }

    /**
     * Create a new user
     * 
     * Creates a new user record in the database using data from UserDto.
     * Automatically handles password hashing and default values.
     * 
     * @param UserDto $dto Data transfer object containing user information
     * @return User Newly created user instance
     * 
     * @throws \Illuminate\Database\QueryException When database constraints are violated
     */
    public function createUser(UserDto $dto): User
    {
        return User::create($dto->toArray());
    }

    /**
     * Update an existing user
     * 
     * Updates user data with information from UserDto. Only provided fields are updated.
     * Password is re-hashed if provided, otherwise existing password is preserved.
     * 
     * @param User $user User instance to update
     * @param UserDto $dto Data transfer object containing updated information
     * @return User Updated user instance with fresh data from database
     * 
     * @throws \Illuminate\Database\QueryException When database constraints are violated
     */
    public function updateUser(User $user, UserDto $dto): User
    {
        $data = $dto->toArray();
        
        // Remove password if not provided to preserve existing password
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Delete a user
     * 
     * Permanently removes a user from the database. This action cannot be undone.
     * 
     * @param User $user User instance to delete
     * @return bool True if deletion was successful, false otherwise
     * 
     * @throws \Exception When deletion fails due to database constraints
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Get all active users
     * 
     * Retrieves all users where is_active flag is true.
     * Results are ordered by name alphabetically.
     * 
     * @return Collection<User> Collection of active users
     */
    public function getActiveUsers(): Collection
    {
        return User::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if email address exists
     * 
     * Verifies if an email address is already registered in the system.
     * Optionally excludes a specific user ID (useful for updates).
     * 
     * @param string $email Email address to check
     * @param int|null $excludeId User ID to exclude from check (optional)
     * @return bool True if email exists, false otherwise
     * 
     * @throws \InvalidArgumentException When email format is invalid
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $this->validateEmail($email);

        $query = User::where('email', $email);
        
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Toggle user's active status
     * 
     * Switches user's is_active flag between true and false.
     * Useful for enabling/disabling user accounts.
     * 
     * @param User $user User instance to toggle
     * @return User Updated user instance with fresh data
     * 
     * @throws \Illuminate\Database\QueryException When update fails
     */
    public function toggleUserStatus(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);
        
        return $user->fresh();
    }

    /**
     * Get users by level range
     * 
     * Retrieves users whose level falls within the specified range.
     * 
     * @param int $minLevel Minimum level (inclusive)
     * @param int $maxLevel Maximum level (inclusive)
     * @return Collection<User> Collection of users within level range
     * 
     * @throws \InvalidArgumentException When level range is invalid
     */
    public function getUsersByLevelRange(int $minLevel, int $maxLevel): Collection
    {
        $this->validateLevelRange($minLevel, $maxLevel);

        return User::whereBetween('level', [$minLevel, $maxLevel])
            ->orderBy('level', 'desc')
            ->get();
    }

    /**
     * Update user's rating
     * 
     * Updates only the rating field for a user.
     * 
     * @param User $user User instance to update
     * @param float $rating New rating value (0-10)
     * @return User Updated user instance
     * 
     * @throws \InvalidArgumentException When rating is out of range
     */
    public function updateUserRating(User $user, float $rating): User
    {
        $this->validateRating($rating);

        $user->update(['rating' => $rating]);
        
        return $user->fresh();
    }

    /**
     * Validate pagination parameters
     * 
     * @param int $perPage Number of items per page
     * @throws \InvalidArgumentException When perPage is invalid
     */
    private function validatePerPage(int $perPage): void
    {
        if ($perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Per page must be between 1 and 100');
        }
    }

    /**
     * Validate user ID
     * 
     * @param int $id User ID to validate
     * @throws \InvalidArgumentException When ID is invalid
     */
    private function validateUserId(int $id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('User ID must be a positive integer');
        }
    }

    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @throws \InvalidArgumentException When email format is invalid
     */
    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    /**
     * Validate level range
     * 
     * @param int $minLevel Minimum level
     * @param int $maxLevel Maximum level
     * @throws \InvalidArgumentException When level range is invalid
     */
    private function validateLevelRange(int $minLevel, int $maxLevel): void
    {
        if ($minLevel > $maxLevel) {
            throw new \InvalidArgumentException('Minimum level cannot be greater than maximum level');
        }
    }

    /**
     * Validate rating value
     * 
     * @param float $rating Rating to validate
     * @throws \InvalidArgumentException When rating is out of range
     */
    private function validateRating(float $rating): void
    {
        if ($rating < 0 || $rating > 10) {
            throw new \InvalidArgumentException('Rating must be between 0 and 10');
        }
    }
} 