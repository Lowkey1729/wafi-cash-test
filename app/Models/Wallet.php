<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'balance',
        'status',
        'can_be_negative',
        'is_virtual',
        'created_at',
        'updated_at',
        'user_id',
        'uuid'
    ];

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// QUERY SCOPES
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function scopeIsVirtual($query)
    {
        return $query->where('is_virtual', true);
    }


    public function scopeCanBeNegative($query)
    {
        return $query->where('can_be_negative', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    protected static function generateUUID(): string
    {
        $uuid = Str::uuid();
        if (self::query()->where('uuid', $uuid)->first()) {
            self::generateUUID();
        }
        return $uuid;
    }

    protected static function generateAccountNumber(): string
    {
        $uuid = substr(str_shuffle("0123456789"), 0, 10);
        if (self::query()->where('account_number', $uuid)->first()) {
            self::generateAccountNumber();
        }
        return $uuid;
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = self::generateUUID();
            $model->account_number = self::generateAccountNumber();
        });
    }
}
