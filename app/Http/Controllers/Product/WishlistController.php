<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

#[Group('Wishlist')]
class WishlistController extends ApiController
{
    public function __construct(
        private readonly WishlistService $wishlistService,
    ) {
    }

    /**
     * Fetch list of products in user's wishlist
     * 
     * @response array{status: 'success', data: array{products: AnonymousResourceCollection<LengthAwarePaginator<ProductResource>>}}
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'search' => ['nullable', 'string'],
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $search = $validated['search'] ?? null;
        $user = Auth::user();

        $products = $this->wishlistService->fetch($user, $perPage, $search);

        return $this->success([
            'products' => $products ? ProductResource::collection($products)
                ->response()->getData(true) : [],
        ]);
    }

    /**
     * Add product to wishlist
     * 
     * @response: array{status: 'success', message: 'Product successfully added to wishlist'}
     */
    public function addProduct(Product $product)
    {
        $user = Auth::user();

        $this->wishlistService->addProduct($user, $product);

        return $this->ok('Product successfully added to wishlist');
    }

    /**
     * Remove product from wishlist
     * 
     * @response: array{status: success, message: 'Product successfully removed from wishlist'}
     */
    public function removeProduct(Product $product)
    {
        $user = Auth::user();

        $this->wishlistService->removeProduct($user, $product);

        return $this->ok('Product successfully removed from wishlist');
    }
}
