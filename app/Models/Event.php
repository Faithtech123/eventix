<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    public const CATEGORIES = [
        'General', 'Music', 'Tech', 'Business',
        'Church', 'Sports', 'Comedy', 'Education',
    ];

    protected $fillable = [
        'title', 'category', 'description', 'venue',
        'city', 'starts_at', 'ends_at', 'banner_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            $event->slug = static::makeSlug($event->title);
        });
    }

    public static function makeSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(5));
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now()->startOfDay());
    }

    public function isOnSale(): bool
    {
        return $this->status === 'published'
            && $this->starts_at->isFuture();
    }

    public function lowestPrice(): ?float
    {
        $price = $this->ticketTypes->min('price');

        return $price === null ? null : (float) $price;
    }

    public function bannerOrPlaceholder(): string
    {
        if ($this->banner_url) {
            return $this->banner_url;
        }

        $seed = urlencode($this->slug ?? 'event');

        return "https://picsum.photos/seed/{$seed}/1200/600";
    }
}
