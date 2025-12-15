<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->update([
                'balance' => 50000
            ]);

            Asset::firstOrCreate([
                'user_id' => $user->id,
                'symbol' => 'BTC',
            ], [
                'amount' => 1,
                'locked_amount' => 0
            ]);

            Asset::firstOrCreate([
                'user_id' => $user->id,
                'symbol' => 'ETH',
            ], [
                'amount' => 10,
                'locked_amount' => 0
            ]);
        }
    }
}
