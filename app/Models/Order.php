<?php

namespace App\Models;

use App\Enums\SideEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    const STATUS_OPEN = 1;
    const STATUS_FILLED = 2;
    const STATUS_CANCELLED = 3;
    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'status',
    ];

    protected $casts = [
      'side'    => SideEnum::class
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
