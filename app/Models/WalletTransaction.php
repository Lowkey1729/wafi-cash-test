<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'wallet_id', 'user_id', 'reference', 'amount', 'charges', 'prev_balance', 'new_balance', 'status', 'type', 'info'
    ];

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// ACCESSORS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    protected function prevBalance(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string)$value,
        );
    }


    protected function newBalance(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string)$value,
        );
    }


    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string)$value,
        );
    }

    protected function charges(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string)$value,
        );
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// RELATIONSHIP
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
