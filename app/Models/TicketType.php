<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'max_per_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function remaining(): int
    {
        return max(0, $this->quantity - $this->sold);
    }

    public function isSoldOut(): bool
    {
        return $this->remaining() === 0;
    }

    public function isFree(): bool
    {
        return (float) $this->price === 0.0;
    }
}
