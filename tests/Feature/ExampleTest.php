<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_driving_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('home'));
        $this->assertTrue(Route::has('routes.index'));
        $this->assertTrue(Route::has('about'));
        $this->assertTrue(Route::has('blog'));
        $this->assertTrue(Route::has('contact'));
        $this->assertTrue(Route::has('contact.submit'));
        $this->assertTrue(Route::has('driving-routes.index'));
        $this->assertTrue(Route::has('driving-routes.my'));
        $this->assertTrue(Route::has('driving-routes.checkout'));
        $this->assertTrue(Route::has('driving-routes.payment-intent'));
        $this->assertTrue(Route::has('driving-routes.start'));
        $this->assertTrue(Route::has('admin.dashboard'));
        $this->assertTrue(Route::has('admin.purchases.index'));
        $this->assertTrue(Route::has('admin.users.index'));
        $this->assertTrue(Route::has('admin.cities.index'));
        $this->assertTrue(Route::has('admin.driving-routes.index'));
    }
}
