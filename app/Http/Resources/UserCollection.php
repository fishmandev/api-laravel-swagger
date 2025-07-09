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
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;
}
