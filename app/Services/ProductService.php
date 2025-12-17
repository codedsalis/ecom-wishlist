<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

final class ProductService
{
    public function fetchPaginated(int $perPage, ?string $search = null)
    {
        $products = Product::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends(['per_page', 'search']);

        return $products;
    }
}
