<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

#[Group('Products')]
class ProductController extends ApiController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    /**
     * Fetch a list of all the products
     * 
     * @response array{status: 'success', data: array{products: AnonymousResourceCollection<LengthAwarePaginator<ProductResource>>}}
     */
    public function fetchAll(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'search' => ['nullable', 'string'],
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $search = $validated['search'] ?? null;

        $products = $this->productService->fetchPaginated($perPage, $search);

        return $this->success([
            'products' => ProductResource::collection($products)
                ->response()->getData(true),
        ]);
    }

    /**
     * Fetch a single product
     * 
     * @response: array{status: success, data: ProductResource}
     */
    public function fetch(Product $product)
    {
        return $this->success(new ProductResource($product));
    }
}
