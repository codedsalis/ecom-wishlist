<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Product API', function () {

    it('fetches paginated list of products', function () {
        $user = User::factory()->create();
        Product::factory()->count(15)->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('product.fetch-all', ['per_page' => 10]));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'products' => [
                        'data',
                        'links',
                        'meta',
                    ],
                ],
            ]);

        expect($response->json('data.products.data'))->toHaveCount(10);
        expect($response->json('data.products.meta.total'))->toBe(15);
    });

    it('searches products by name', function () {
        $user = User::factory()->create();

        $iphone = Product::factory()->create(['name' => 'iPhone 15 Pro']);
        $macbook = Product::factory()->create(['name' => 'MacBook Pro']);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('product.fetch-all', ['search' => 'iPhone']));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.products.data');

        expect($response->json('data.products.data.0.name'))->toBe('iPhone 15 Pro');
    });

    it('fetches a single product', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('product.fetch', $product));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ]);

        expect($response->json('data.id'))->toBe($product->id);
    });

    it('prevents unauthenticated access', function () {
        $product = Product::factory()->create();

        $this
            ->getJson(route('product.fetch-all'))
            ->assertUnauthorized();

        $this
            ->getJson(route('product.fetch', $product))
            ->assertUnauthorized();
    });
});
