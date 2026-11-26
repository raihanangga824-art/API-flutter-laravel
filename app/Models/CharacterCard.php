<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterCard extends Model
{
    // nama tabel otomatis: character_cards
    protected $fillable = ['user_id', 'faction_id', 'title', 'race', 'ki', 'notes', 'image_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Kartu milik 1 faksi
    public function faction(): BelongsTo
    {
        return $this->belongsTo(Faction::class);
    }
}
