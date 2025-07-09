<?php

namespace App\DTOs;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;

/**
 * User Data Transfer Object
 * 
 * Immutable object that carries user data between layers of the application.
 * Provides type safety and validation for user data transformation.
 */
class UserDto
{
    /**
     * Initialize UserDto with user data
     * 
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string|null $password Hashed password (null for updates without password change)
     * @param string|null $dob Date of birth in Y-m-d format (nullable)
     * @param bool $isActive Whether user account is active
     * @param int $level User's level (1-100)
     * @param float|null $rating User's rating (0-10, nullable)
     * @param array|null $metadata Additional user metadata as JSON (nullable)
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $dob,
        public readonly bool $isActive,
        public readonly int $level,
        public readonly ?float $rating,
        public readonly ?array $metadata,
    ) {}

    /**
     * Create DTO from StoreUserRequest
     * 
     * Creates a new UserDto instance from validated StoreUserRequest data.
     * Automatically hashes the password and sets default values for optional fields.
     * 
     * @param StoreUserRequest $request Validated request for creating a new user
     * @return self New UserDto instance with data from request
     * 
     * @throws \InvalidArgumentException When request data is invalid
     */
    public static function fromStoreRequest(StoreUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: Hash::make($request->validated('password')),
            dob: $request->validated('dob'),
            isActive: $request->validated('is_active', true),
            level: $request->validated('level', 1),
            rating: $request->validated('rating'),
            metadata: $request->validated('metadata'),
        );
    }

    /**
     * Create DTO from UpdateUserRequest
     * 
     * Creates a new UserDto instance from validated UpdateUserRequest data.
     * Password is hashed only if provided, otherwise set to null to preserve existing password.
     * Uses existing values as defaults for optional fields.
     * 
     * @param UpdateUserRequest $request Validated request for updating a user
     * @return self New UserDto instance with data from request
     * 
     * @throws \InvalidArgumentException When request data is invalid
     */
    public static function fromUpdateRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->has('password') ? Hash::make($request->validated('password')) : null,
            dob: $request->validated('dob'),
            isActive: $request->validated('is_active', true),
            level: $request->validated('level', 1),
            rating: $request->validated('rating'),
            metadata: $request->validated('metadata'),
        );
    }

    /**
     * Convert DTO to array for model operations
     * 
     * Transforms the DTO into an associative array suitable for Eloquent model
     * creation or update operations. Filters out null values to prevent
     * overwriting existing data with null values.
     * 
     * @return array<string, mixed> Array of user data with null values filtered out
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'dob' => $this->dob,
            'is_active' => $this->isActive,
            'level' => $this->level,
            'rating' => $this->rating,
            'metadata' => $this->metadata,
        ], fn($value) => $value !== null);
    }

    /**
     * Check if DTO contains password data
     * 
     * Utility method to determine if the DTO includes password information.
     * Useful for conditional logic in update operations.
     * 
     * @return bool True if password is set, false otherwise
     */
    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    /**
     * Get user data without sensitive information
     * 
     * Returns user data array excluding password for logging or debugging purposes.
     * 
     * @return array<string, mixed> Array of user data without password
     */
    public function toArrayWithoutPassword(): array
    {
        $data = $this->toArray();
        unset($data['password']);
        
        return $data;
    }
} 