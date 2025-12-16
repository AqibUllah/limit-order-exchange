<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('symbol',10);
            $table->enum('side',['buy','sell']);
            $table->decimal('price',16);
            $table->decimal('amount',16,8);
            $table->tinyInteger('status')->default(1); // 1 for open , 2 for filled and 3 for cancelled
            $table->timestamps();

            $table->index(['symbol', 'side', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
