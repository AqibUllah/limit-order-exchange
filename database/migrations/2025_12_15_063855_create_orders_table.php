<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('symbol');
            $table->enum('side',['buy','sell'])->default('buy');
            $table->string('price');
            $table->string('amount');
            $table->enum('status',[1,2,3]); // 1 for open , 2 for filled and 3 for cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
