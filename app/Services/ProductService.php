<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductService
 *
 * Handles product-related business logic.
 */
final class ProductService
{
    /**
     * Fetch paginated products with optional search filtering.
     *
     * @param  int $perPage Number of items per page
     * @param  string|null $search Optional search keyword (matches product name)
     * @return LengthAwarePaginator<Product>
     */
    public function fetchPaginated(int $perPage, ?string $search = null): LengthAwarePaginator
    {
        return Product::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($perPage)
            ->appends([
                'per_page' => $perPage,
                'search' => $search,
            ]);
    }
}
