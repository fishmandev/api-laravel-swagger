<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User API Resource
 * 
 * Transforms User model instances into standardized JSON responses for API endpoints.
 * Provides consistent data structure and handles null values appropriately.
 * 
 * @property-read \App\Models\User $resource
 */
class UserResource extends JsonResource
{
    /**
     * Transform the user resource into an array
     * 
     * Converts a User model instance into a standardized array structure
     * suitable for JSON API responses. Handles nullable fields and formats
     * dates consistently.
     * 
     * @param Request $request The HTTP request instance
     * @return array<string, mixed> Standardized user data array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'dob' => $this->dob?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'level' => $this->level,
            'rating' => $this->rating,
            'metadata' => $this->metadata,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array
     * 
     * @param Request $request The HTTP request instance
     * @return array<string, mixed> Additional resource data
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => route('users.show', $this->id),
                'edit' => route('users.update', $this->id),
                'delete' => route('users.destroy', $this->id),
            ],
        ];
    }
}
