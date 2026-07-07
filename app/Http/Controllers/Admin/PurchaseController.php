<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoutePurchase;

class PurchaseController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $purchases = RoutePurchase::with(['user', 'route'])
            ->latest('purchased_at')
            ->paginate(15);

        return view('admin.purchases.index', compact('purchases'));
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
