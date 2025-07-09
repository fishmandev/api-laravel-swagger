<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * User Collection Resource
 * 
 * Transforms paginated User collections into standardized JSON responses.
 * Provides consistent pagination metadata and data structure for API endpoints.
 * 
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $resource
 */
class UserCollection extends ResourceCollection
{
    /**
     * Transform the paginated user collection into an array
     * 
     * Converts a paginated collection of User models into a standardized
     * array structure with data and pagination metadata. Each user is
     * transformed using the UserResource class.
     * 
     * @param Request $request The HTTP request instance
     * @return array<int|string, mixed> Standardized collection response
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => UserResource::collection($this->collection),
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'has_more_pages' => $this->hasMorePages(),
            ],
        ];
    }
}
