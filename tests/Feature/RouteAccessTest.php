<?php

namespace Tests\Feature;

use App\Models\DrivingRoute;
use App\Models\RoutePurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not available.');
        }

        parent::setUp();
    }

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Driver Test Routes');
    }

    public function test_public_pages_load(): void
    {
        $this->get('/routes')
            ->assertOk()
            ->assertSee('Driving Test Routes');

        $this->get('/about')
            ->assertOk()
            ->assertSee('About us');

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Route practice notes');

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Contact');
    }

    public function test_checkout_unlocks_route_and_start_consumes_one_access(): void
    {
        $user = User::factory()->create();

        $route = DrivingRoute::create([
            'title' => 'Sample Paid Route',
            'city' => 'Karachi',
            'province' => 'Sindh',
            'description' => 'A paid test route.',
            'start_label' => 'Start',
            'destination_label' => 'Midpoint',
            'price' => 10,
            'access_limit' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('driving-routes.checkout.store', $route), [
                'student_name' => $user->name,
                'student_email' => $user->email,
                'student_phone' => '555-0100',
                'student_city' => 'Karachi',
                'billing_name' => $user->name,
                'billing_email' => $user->email,
                'terms' => '1',
            ])
            ->assertRedirect(route('driving-routes.show', $route));

        $purchase = RoutePurchase::first();

        $this->assertNotNull($purchase);
        $this->assertSame(1, $purchase->access_limit);
        $this->assertSame(0, $purchase->access_used);
        $this->assertSame(1, $purchase->remainingStarts());
        $this->assertSame($user->name, $purchase->student_name);
        $this->assertSame($user->email, $purchase->student_email);
        $this->assertSame('555-0100', $purchase->student_phone);

        $this->actingAs($user)
            ->postJson(route('driving-routes.start', $route))
            ->assertOk()
            ->assertJson([
                'remaining_starts' => 0,
                'access_used' => 1,
            ]);

        $this->assertSame(0, $purchase->fresh()->remainingStarts());

        $this->actingAs($user)
            ->postJson(route('driving-routes.start', $route))
            ->assertStatus(402);
    }
}
