<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Pagination\CustomPaginator;

/**
 * User Collection Resource
 * 
 * Transforms paginated User collections into standardized JSON responses.
 * Provides consistent pagination metadata and data structure for API endpoints.
 * 
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $resource
 */
class UserCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = UserResource::class;
}
