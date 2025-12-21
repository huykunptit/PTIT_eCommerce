<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class RoleAndCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Force an in-memory SQLite database for fast, isolated tests
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        // Disable CSRF for request feature tests
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function seedRole(string $code, string $name, ?int $fixedId = null): Roles
    {
        return Roles::unguarded(function () use ($code, $name, $fixedId) {
            return Roles::create([
                'id' => $fixedId,
                'role_name' => $name,
                'role_code' => $code,
            ]);
        });
    }

    private function createProduct(User $seller): Product
    {
        $category = Category::create([
            'name' => 'Test Category',
        ]);

        return Product::create([
            'name' => 'Test Product',
            'description' => 'Checkout flow test item',
            'price' => 199000,
            'quantity' => 5,
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'status' => 'active',
        ]);
    }

    private function baseCheckoutPayload(): array
    {
        return [
            'payment_method' => 'cod',
            'shipping_name' => 'Tester',
            'shipping_phone' => '0123456789',
            'shipping_address' => '123 Test St',
            'shipping_email' => 'tester@example.com',
            'notes' => 'note',
        ];
    }

    public function test_guest_is_redirected_to_login_when_opening_checkout(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('auth.login'));
    }

    public function test_logged_in_user_with_cart_can_view_checkout_page(): void
    {
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $product = $this->createProduct($user);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id . '_default' => [
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->get(route('checkout.index'));

        $response->assertOk();
        $response->assertSee('Đơn hàng của bạn');
        $response->assertSee($product->name);
    }

    public function test_checkout_fails_when_cart_is_empty(): void
    {
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), $this->baseCheckoutPayload());

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_checkout_cod_creates_order_and_clears_cart(): void
    {
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $product = $this->createProduct($user);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id . '_default' => [
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->post(route('checkout.store'), $this->baseCheckoutPayload());

        $response->assertRedirect(route('checkout.success', ['order_id' => 1]));
        $response->assertSessionMissing('cart');

        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'user_id' => $user->id,
            'payment_method' => 'cod',
            'status' => 'pending',
            'shipping_status' => 'pending_pickup',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => 1,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_checkout_vnpay_redirects_to_gateway_and_keeps_cart(): void
    {
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $product = $this->createProduct($user);

        $payload = $this->baseCheckoutPayload();
        $payload['payment_method'] = 'vnpay';

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id . '_default' => [
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->post(route('checkout.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('cart'); // cart not cleared until payment success
        $this->assertDatabaseHas('orders', [
            'payment_method' => 'vnpay',
            'status' => 'pending_payment',
        ]);
    }

    public function test_checkout_validation_errors_return_back(): void
    {
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);
        $product = $this->createProduct($user);

        $payload = $this->baseCheckoutPayload();
        $payload['shipping_name'] = ''; // make invalid

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id . '_default' => [
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->post(route('checkout.store'), $payload);

        $response->assertSessionHasErrors(['shipping_name']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $adminRole = $this->seedRole('admin', 'Admin', 1); // role_id 1 is required by AdminMiddleware
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_non_admin_is_blocked_from_admin_area(): void
    {
        $this->seedRole('admin', 'Admin', 1);
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_employee_role_can_access_employee_dashboard(): void
    {
        $this->seedRole('admin', 'Admin', 1);
        $employeeRole = $this->seedRole('sales', 'Sales');
        $employee = User::factory()->create(['role_id' => $employeeRole->id]);

        $response = $this->actingAs($employee)->get(route('employee.dashboard'));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_employee_dashboard(): void
    {
        $this->seedRole('admin', 'Admin', 1);
        $userRole = $this->seedRole('user', 'User');
        $user = User::factory()->create(['role_id' => $userRole->id]);

        $response = $this->actingAs($user)->get(route('employee.dashboard'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }
}

