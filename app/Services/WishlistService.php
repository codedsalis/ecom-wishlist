<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class WishlistService
 *
 * Handles wishlist management for users.
 */
final class WishlistService
{
    /**
     * Add a product to a user's wishlist.
     *
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function addProduct(User $user, Product $product): void
    {
        $wishlist = Wishlist::query()
            ->firstOrCreate(['user_id' => $user->id]);

        $wishlist->products()->sync($product);
    }

    /**
     * Remove a product from a user's wishlist.
     *
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function removeProduct(User $user, Product $product): void
    {
        $wishlist = Wishlist::query()
            ->firstOrCreate(['user_id' => $user->id]);

        $wishlist->products()->detach($product);
    }

    /**
     * Fetch paginated wishlist products for a user.
     *
     * Optionally filters products by name.
     *
     * @param User $user
     * @param int $perPage
     * @param string|null $search
     * @return LengthAwarePaginator<Product>|null
     */
    public function fetch(
        User $user,
        int $perPage,
        ?string $search = null
    ): ?LengthAwarePaginator {
        $wishlist = Wishlist::query()
            ->where('user_id', $user->id)
            ->first();

        return $wishlist?->products()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('products.name', 'like', "%{$search}%");
            })
            ->paginate($perPage)
            ->appends([
                'per_page' => $perPage,
                'search' => $search,
            ]);
    }
}
