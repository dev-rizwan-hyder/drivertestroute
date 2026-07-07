<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrivingRoute;
use App\Models\RoutePurchase;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $this->authorizeAdmin();

        $stats = [
            'routes' => DrivingRoute::count(),
            'active_routes' => DrivingRoute::where('is_active', true)->count(),
            'sales' => RoutePurchase::where('payment_status', 'paid')->count(),
            'revenue' => RoutePurchase::where('payment_status', 'paid')->sum('amount_paid'),
            'starts_used' => RoutePurchase::where('payment_status', 'paid')->sum('access_used'),
            'users' => User::count(),
        ];

        $recentPurchases = RoutePurchase::with(['user', 'route'])
            ->where('payment_status', 'paid')
            ->latest('purchased_at')
            ->take(8)
            ->get();

        $topRoutes = DrivingRoute::withCount(['purchases as paid_purchases_count' => function ($query) {
            $query->where('payment_status', 'paid');
        }])
            ->orderByDesc('paid_purchases_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPurchases', 'topRoutes'));
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
