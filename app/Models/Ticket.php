<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Ticket extends Model
{
    public const QR_PREFIX = 'EVX1:';

    protected $fillable = [
        'order_id', 'ticket_type_id', 'event_id',
        'code', 'holder_name',
        'checked_in_at', 'checked_in_by',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    public static function newCode(): string
    {
        // 12 chars, no 0/O/1/I so it's easy to type at the gate
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < 12; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    public function formattedCode(): string
    {
        return implode('-', str_split($this->code, 4));
    }

    public function qrPayload(): string
    {
        return self::QR_PREFIX.Crypt::encryptString($this->code);
    }

    public static function normalize(string $input): string
    {
        return Str::upper(preg_replace('/[^A-Za-z0-9]/', '', $input));
    }
}
