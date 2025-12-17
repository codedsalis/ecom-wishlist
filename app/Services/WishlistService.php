<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class WishlistService
{
    public function addProduct(User $user, Product $product): void
    {
        $wishlist = Wishlist::query()
            ->firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                ['product_id' => $product->id]
            );

        $wishlist->products()->sync($product);
    }

    public function removeProduct(User $user, Product $product)
    {
        $wishlist = Wishlist::query()
            ->firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                ['product_id' => $product->id]
            );

        $wishlist->products()->detach($product);
    }

    public function fetch(User $user, int $perPage, ?string $search = null): ?LengthAwarePaginator
    {
        $wishlist = Wishlist::query()
            ->where('user_id', $user->id)
            ->first();

        $products = $wishlist?->products()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('products.name', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        return $products;
    }
}
