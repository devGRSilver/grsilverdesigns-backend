<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;

class GlobalSearchController extends ResponseController
{
    public function globalSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100'
        ]);

        $query = $request->input('query');

        // Search users (ONLY users with role = User)
        $users = User::with('roles')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'title'    => $user->name,
                    'subtitle' => $user->email . ' • ID: #' . $user->id,
                    'url'      => route('users.show', encrypt($user->id)),
                ];
            });



        // Search orders
        $orders = Order::where('order_number', 'LIKE', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->with('user')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'title' => $order->order_number,
                    'subtitle' => $order->user->name . ' • $' . number_format($order->total_amount, 2),
                    'status' => $order->status,
                    'created_at' => $order->created_at->diffForHumans(),
                    'url' => route('orders.show', encrypt($order->id))
                ];
            });

        $total = $users->count() + $orders->count();

        return response()->json([
            'success' => true,
            'query' => $query,
            'total' => $total,
            'results' => [
                'users' => $users,
                'orders' => $orders
            ]
        ]);
    }
}
