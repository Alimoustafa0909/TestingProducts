<?php

namespace Tests\Feature;

use App\Models\Product;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createUser(true);
        $this->user = $this->createUser(false);
    }

    public function test_the_application_return_a_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }


    public function test_that_anyone_can_access_product_page()
    {
        $response = $this->get('/product');
        $response->assertStatus(302);
    }

    public function test_product_page_contains_empty_table()
    {

        $response = $this->actingAs($this->user)->get('/product');
        $response->assertStatus(200);
        $response->assertSee(__('No Products Found'));
    }

    public function test_product_page_contains_non_empty_table()
    {

        $products = Product::factory()->create([
            'name' => 'product 1'
        ]);
        $response = $this->actingAs($this->user)->get('/product');
        $response->assertStatus(200);
        $response->assertDontSee(__('No Products Found'));
        $response->assertSee('product 1');

        $response->assertViewHas('products', function ($collection) use ($products) {
            return $collection->contains($products);
        });
    }


    public function test_admin_can_see_products_create_button()
    {
        $response = $this->actingAs($this->admin)->get('/product');
        $response->assertStatus(200);
        $response->assertSee('Create a Product');
    }

    public function test_non_admin_cannot_see_products_create_button()
    {
        $response = $this->actingAs($this->user)->get('/product');
        $response->assertStatus(200);
        $response->assertDontSee('Create Product');
    }


    public function test_non_admin_cannot_access_product_create_page()
    {
        $response = $this->actingAs($this->user)->get('/product/create');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_product_create_page()
    {
        $response = $this->actingAs($this->admin)->get('/product/create');
        $response->assertStatus(200);

    }

    public function test_admin_can_make_a_product()
    {
        $product = [
            'name' => 'teto',
            'description' => 'asdmnajksdnajsdnasd',
        ];

        $response = $this->actingAs($this->admin)->post('product/store',$product);
        $response->assertStatus(302);
        $response->assertRedirect('product');

        $this->assertDatabaseHas('products',$product);
    }

    public function test_admin_can_edit_a_product()
    {
        $product = [
            'name' => 'teto',
            'description' => 'asdmnajksdnajsdnasd',
        ];

        $response = $this->actingAs($this->admin)->post('product/update',$product);
        $response->assertStatus(302);
        $response->assertRedirect('product');

        $this->assertDatabaseHas('products',$product);
    }

    private function createUser($isAdmin): User
    {
        return User::factory()->create([
            'is_admin' => $isAdmin
        ]);
    }

}
