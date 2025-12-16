<?php

namespace App\Services;

use App\Enums\SideEnum;
use App\Models\Order;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class OrderMatchingService
{
    public function match(Order $order): void
    {
        DB::transaction(function () use ($order) {

            if (SideEnum::tryFrom($order->side) === SideEnum::BUY) {
                $counter = Order::where('symbol', $order->symbol)
                    ->where('side', SideEnum::SELL)
                    ->where('status', Order::STATUS_OPEN)
                    ->where('price', '<=', $order->price)
                    ->where('amount', $order->amount)
                    ->orderBy('price')
                    ->lockForUpdate()
                    ->first();
            } else {
                $counter = Order::where('symbol', $order->symbol)
                    ->where('side', SideEnum::BUY)
                    ->where('status', Order::STATUS_OPEN)
                    ->where('price', '>=', $order->price)
                    ->where('amount', $order->amount)
                    ->orderByDesc('price')
                    ->lockForUpdate()
                    ->first();
            }

            if (!$counter) {
                return;
            }

            $this->executeTrade($order, $counter);
        });
    }

    private function executeTrade(Order $order, Order $counter): void
    {
        $buyOrder  = SideEnum::tryFrom($order->side) === SideEnum::BUY ? $order : $counter;
        $sellOrder = SideEnum::tryFrom($order->side) === SideEnum::SELL ? $order : $counter;

        $price  = $sellOrder->price;
        $amount = $sellOrder->amount;

        $volume = bcmul($price, $amount, 8);
        $fee    = bcmul($volume, '0.015', 8); // 1.5%

        // Locking users
        $buyer  = User::where('id', $buyOrder->user_id)->lockForUpdate()->first();
        $seller = User::where('id', $sellOrder->user_id)->lockForUpdate()->first();

        $buyerAsset = Asset::firstOrCreate(
            ['user_id' => $buyer->id, 'symbol' => $buyOrder->symbol],
            ['amount' => 0, 'locked_amount' => 0]
        );
        $buyerAsset->lockForUpdate();

        $sellerAsset = Asset::where('user_id', $seller->id)
            ->where('symbol', $sellOrder->symbol)
            ->lockForUpdate()
            ->first();

        // ---- BALANCE MOVEMENTS ----

        // Buyer receives asset
        $buyerAsset->amount = bcadd($buyerAsset->amount, $amount, 8);
        $buyerAsset->save();

        // Seller loses locked asset
        $sellerAsset->locked_amount = bcsub($sellerAsset->locked_amount, $amount, 8);
        $sellerAsset->save();

        // Seller receives USD (fee applied here)
        $seller->balance = bcadd($seller->balance, bcsub($volume, $fee, 8), 2);
        $seller->save();

        // Buyer already locked USD → no refund needed (full match)

        // Mark orders filled
        $buyOrder->status  = Order::STATUS_FILLED;
        $sellOrder->status = Order::STATUS_FILLED;

        $buyOrder->save();
        $sellOrder->save();
    }
}
