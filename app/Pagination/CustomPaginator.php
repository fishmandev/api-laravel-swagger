<?php

namespace App\Pagination;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Pagination\AbstractPaginator;
use JsonSerializable;

class CustomPaginator extends AbstractPaginator implements 
    Paginator, 
    Arrayable, 
    Jsonable, 
    JsonSerializable
{
    protected $items;
    protected $total;
    protected $perPage;
    protected $currentPage;
    protected $hasMore;

    /**
     * Create a new custom paginator instance.
     */
    public function __construct($items, int $total, int $perPage, ?int $currentPage = null, array $options = [])
    {
        $this->items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage ?: static::resolveCurrentPage();
        $this->hasMore = ($this->currentPage * $perPage) < $total;
        
        // Set the path for URL generation
        $this->path = $options['path'] ?? request()->url();
        $this->pageName = $options['pageName'] ?? 'page';
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'data' => $this->items->toArray(),
            'pagination' => [
                'total' => $this->total,
            ]
        ];
    }

    /**
     * Convert the object to its JSON representation.
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the items for the current page.
     */
    public function items(): array
    {
        return $this->items->all();
    }

    /**
     * Get the current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the number of items per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the previous page URL.
     */
    public function previousPageUrl(): ?string
    {
        return $this->getPreviousUrl();
    }

    /**
     * Get total items count.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Determine if there are more items in the data source.
     */
    public function hasMorePages(): bool
    {
        return $this->hasMore;
    }

    /**
     * Get the URL for the next page.
     */
    public function nextPageUrl(): ?string
    {
        return $this->hasMorePages() ? $this->url($this->currentPage() + 1) : null;
    }

    /**
     * Get the URL for the previous page.
     */
    protected function getPreviousUrl(): ?string
    {
        return $this->currentPage() > 1 ? $this->url($this->currentPage() - 1) : null;
    }

    /**
     * Render the paginator view (not needed for API, return empty string).
     */
    public function render($view = null, $data = []): string
    {
        return '';
    }
} 