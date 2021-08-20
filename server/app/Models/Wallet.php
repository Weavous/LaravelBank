<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Transaction;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ["code", "users_id", "deleted_at", "created_at", "updated_at"];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
