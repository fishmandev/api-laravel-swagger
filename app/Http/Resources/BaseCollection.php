<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Pagination\CustomPaginator;

class BaseCollection extends ResourceCollection
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        // If it's our CustomPaginator, return its structure directly
        if ($this->resource instanceof CustomPaginator) {
            return response()->json($this->resource->toArray());
        }

        // Otherwise, use standard Laravel behavior
        return parent::toResponse($request);
    }
}