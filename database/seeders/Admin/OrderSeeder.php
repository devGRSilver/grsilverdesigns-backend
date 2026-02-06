<?php

namespace Database\Seeders\Admin;

use App\Constants\Constant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Transaction;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Enums\TransactionStatus;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {

            $user = User::inRandomOrder()->first();
            $product = Product::inRandomOrder()->first();

            $quantity   = rand(1, 3);
            $unitPrice  = 1000;
            $subTotal   = $unitPrice * $quantity;
            $tax        = $subTotal * 0.09; // 9%
            $shipping   = 20;
            $discount   = 0;

            $totalAmount = $subTotal + $tax + $shipping;
            $grandTotal  = $totalAmount - $discount;

            /*
            |--------------------------------------------------
            | Order
            |--------------------------------------------------
            */
            $order = Order::create([
                'user_id'         => $user->id,
                'order_number'    => generateOrderNumber(),
                'sub_total'       => $subTotal,
                'tax_amount'      => $tax,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'total_amount'    => $totalAmount,
                'grand_total'     => $grandTotal,
                'currency_code'   => Constant::DEFAULT_CURRENCY,
                'status'          => OrderStatus::DELIVERED,
                'shipping_method' => 'standard',
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'shipped_at'      => now()->subDays(rand(2, 5)),
                'delivered_at'    => now(),
                'paid_at'         => now(),
                'notes' => 'Delivered successfully',
                'metadata' => [
                    'source' => 'admin_seeder',
                ],
            ]);

            /*
            |--------------------------------------------------
            | Order Item
            |--------------------------------------------------
            */
            OrderItem::create([
                'order_id'        => $order->id,
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'sku'             => $product->sku ?? 'SKU-' . $i,
                'unit_price'      => $unitPrice,
                'quantity'        => $quantity,
                'tax_amount'      => $tax,
                'discount_amount' => 0,
                'total'           => $subTotal,

                'variant_name' => 'Size',
                'variant_options' => [
                    'size'  => rand(7, 10),
                    'color' => 'Black',
                ],
            ]);

            /*
            |--------------------------------------------------
            | Addresses
            |--------------------------------------------------
            */
            foreach (['shipping', 'billing'] as $type) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type'     => $type,
                    'name'     => $user->name ?? "Customer {$i}",
                    'phone'    => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'email'    => $user->email,
                    'address_line_1' => 'MI Road',
                    'city'     => 'Jaipur',
                    'state'    => 'Rajasthan',
                    'postal_code' => '302001',
                    'country'  => 'India',
                ]);
            }

            /*
            |--------------------------------------------------
            | Transaction
            |--------------------------------------------------
            */
            Transaction::create([
                'user_id'       => $user->id,
                'order_id'      => $order->id,
                'transaction_id' => generateTransactionNumber(),
                'amount'        => $grandTotal,
                'currency_code' => '$',
                'status'        => TransactionStatus::COMPLETED,
                'payment_method' => 'upi',
                'payment_gateway' => 'razorpay',

                'gateway_transaction_id' => 'rzp_' . Str::random(10),
                'customer_email' => $user->email,
                'customer_phone' => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),

                'gateway_fee' => 50,
                'tax_on_fee'  => 9,
                'net_amount'  => $grandTotal - 59,
                'settled_at'  => now(),
            ]);
        }
    }
}
