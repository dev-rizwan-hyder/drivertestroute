<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $users = User::with(['routePurchases' => function ($query) {
            $query->where('payment_status', 'paid')
                ->latest('purchased_at');
        }])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
