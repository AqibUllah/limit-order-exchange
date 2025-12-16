<?php

namespace App\Http\Controllers;

use App\Enums\SideEnum;
use App\Http\Requests\OrderRequest;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {

        $user = $request->user();

        return DB::transaction(function () use ($request, $user) {

            if (SideEnum::tryFrom($request->side) === SideEnum::BUY) {
                $total = bcmul($request->price, $request->amount, 2);

                $user->lockForUpdate();

                if ($user->balance < $total) {
                    abort(422, 'Insufficient USD balance');
                }


                $user->balance = bcsub($user->balance, $total, 2);
                $user->save();
            }

            if (SideEnum::tryFrom($request->side) === SideEnum::SELL) {
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $request->symbol)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($asset->amount < $request->amount) {
                    abort(422, 'Insufficient asset balance');
                }

                $asset->amount = bcsub($asset->amount, $request->amount, 8);
                $asset->locked_amount = bcadd($asset->locked_amount, $request->amount, 8);
                $asset->save();
            }

            return Order::create([
                'user_id' => $user->id,
                'symbol'  => $request->symbol,
                'side'    => $request->side,
                'price'   => $request->price,
                'amount'  => $request->amount,
                'status'  => 1,
            ]);
        });
    }

    public function cancel(Order $order)
    {
        $user = auth()->user();

        if ($order->user_id !== $user->id || $order->status !== 1) {
            abort(403);
        }

        DB::transaction(function () use ($order, $user) {

            if ($order->side === SideEnum::BUY) {
                $refund = bcmul($order->price, $order->amount, 2);

                $user->lockForUpdate();
                $user->balance = bcadd($user->balance, $refund, 2);
                $user->save();
            }

            if ($order->side === SideEnum::SELL) {
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $order->symbol)
                    ->lockForUpdate()
                    ->first();

                $asset->locked_amount = bcsub($asset->locked_amount, $order->amount, 8);
                $asset->amount = bcadd($asset->amount, $order->amount, 8);
                $asset->save();
            }

            $order->status = 3;
            $order->save();
        });
    }
}
