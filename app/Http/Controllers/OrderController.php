<?php

namespace App\Http\Controllers;

use App\Enums\SideEnum;
use App\Http\Requests\OrderRequest;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\OrderMatchingService;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::where('user_id', $user->id);

        if ($request->filled('symbol')) {
            $query->where('symbol', $request->symbol);
        }

        $orders = $query
            ->orderByDesc('id')
            ->get();

        return response()->json($orders);
    }

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
                $asset = Asset::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'symbol' => $request->symbol,
                    ],
                    [
                        'amount' => 0,
                        'locked_amount' => 0,
                    ]
                );

                $asset->lockForUpdate();

                if ($asset->amount < $request->amount) {
                    abort(422, 'Insufficient asset balance');
                }

                $asset->amount = bcsub($asset->amount, $request->amount, 8);
                $asset->locked_amount = bcadd($asset->locked_amount, $request->amount, 8);
                $asset->save();
            }

            $order = Order::create([
                'user_id' => $user->id,
                'symbol'  => $request->symbol,
                'side'    => $request->side,
                'price'   => $request->price,
                'amount'  => $request->amount,
                'status'  => 1,
            ]);

            app(OrderMatchingService::class)->match($order);

            return $order;
        });
    }

    public function cancel(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Only open orders can be cancelled
        if ($order->status !== Order::STATUS_OPEN) {
            abort(422, 'Order cannot be cancelled');
        }

        DB::transaction(function () use ($order, $user) {

            $order->lockForUpdate();

            if ($order->side === 'buy') {
                // Refunding USD
                $refund = bcmul($order->price, $order->amount, 2);

                $user->lockForUpdate();
                $user->balance = bcadd($user->balance, $refund, 2);
                $user->save();

            } else {
                // Unlocking asset
                $asset = Asset::where('user_id', $user->id)
                    ->where('symbol', $order->symbol)
                    ->lockForUpdate()
                    ->firstOrFail();

                $asset->locked_amount = bcsub($asset->locked_amount, $order->amount, 8);
                $asset->amount = bcadd($asset->amount, $order->amount, 8);
                $asset->save();
            }

            $order->status = Order::STATUS_CANCELLED;
            $order->save();
        });

        return response()->json([
            'message' => 'Order cancelled successfully'
        ]);
    }
}
