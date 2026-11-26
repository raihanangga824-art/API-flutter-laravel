<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faction extends Model
{
    protected $fillable = ['user_id', 'name', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 1 Faksi punya banyak kartu karakter (One-to-Many)
    public function characterCards(): HasMany
    {
        return $this->hasMany(CharacterCard::class);
    }
}