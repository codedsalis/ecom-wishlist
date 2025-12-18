<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Wishlist API', function () {

    it('fetches paginated wishlist products', function () {
        $user = User::factory()->create();
        $products = Product::factory()->count(15)->create();

        $wishlist = Wishlist::factory()->create([
            'user_id' => $user->id,
        ]);
        $wishlist->products()->attach($products->pluck('id'));

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('wishlist.fetch', ['per_page' => 10]));

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
    });

    it('filters wishlist products by search', function () {
        $user = User::factory()->create();
        $iphone = Product::factory()->create(['name' => 'iPhone 15 Pro']);
        $macbook = Product::factory()->create(['name' => 'MacBook Pro']);

        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $wishlist->products()->attach([$iphone->id, $macbook->id]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('wishlist.fetch', ['search' => 'iPhone']));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.products.data');

        expect($response->json('data.products.data.0.name'))
            ->toBe('iPhone 15 Pro');
    });

    it('adds a product to wishlist', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(route('wishlist.add', $product));

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Product successfully added to wishlist',
            ]);

        $wishlist = Wishlist::where('user_id', $user->id)->first();

        expect($wishlist)->not->toBeNull()
            ->and(
                $wishlist->products()->where('product_id', $product->id)->exists()
            )->toBeTrue();
    });

    it('removes a product from wishlist', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = Wishlist::factory()->create(['user_id' => $user->id]);
        $wishlist->products()->attach($product->id);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->deleteJson(route('wishlist.remove', $product));

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Product successfully removed from wishlist',
            ]);

        expect(
            $wishlist->products()->where('product_id', $product->id)->exists()
        )->toBeFalse();
    });

    it('returns empty products array when wishlist does not exist', function () {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(route('wishlist.fetch'));

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'products' => [],
                ],
            ]);
    });

    it('prevents unauthenticated access', function () {
        $this
            ->getJson(route('wishlist.fetch'))
            ->assertUnauthorized();
    });
});
