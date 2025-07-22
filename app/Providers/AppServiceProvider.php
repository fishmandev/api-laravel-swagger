<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use App\Pagination\CustomPaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom pagination macro
        Builder::macro('customPaginate', function (int $perPage = 15, array $columns = ['*'], ?int $page = null) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            $page = $page ?: (int) request()->input('page', 1);
            
            // Get total count
            $total = $this->toBase()->getCountForPagination();
            
            // Get items for current page
            $items = $this->forPage($page, $perPage)->get($columns);
            
            return new CustomPaginator($items, $total, $perPage, $page, [
                'path' => request()->url(),
                'pageName' => 'page',
            ]);
        });
    }
}
