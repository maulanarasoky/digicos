<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetails extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'price',
        'quantity',
        'cosmetic_id',
        'order_transaction_id',
    ];

    public function orderTransaction(): BelongsTo {
        return $this->belongsTo(OrderTransaction::class, 'order_transaction_id');
    }

    public function cosmetic(): BelongsTo {
        return $this->belongsTo(Cosmetic::class, 'cosmetic_id');
    }
}
