<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Passkey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'credential_id',
        'public_key',
        'algorithm',
        'sign_count',
        'transports',
        'rp_id',
        'last_used_at',
    ];

    protected $hidden = [
        'public_key',
    ];

    protected function casts(): array
    {
        return [
            'transports' => 'array',
            'sign_count' => 'integer',
            'algorithm' => 'integer',
            'last_used_at' => 'datetime',
        ];
    }

    private static ?bool $cachedTableExists = null;

    /**
     * Aman dipanggil sebelum `php artisan migrate` dijalankan.
     */
    public static function tableExists(): bool
    {
        if (self::$cachedTableExists !== null) {
            return self::$cachedTableExists;
        }

        try {
            return self::$cachedTableExists = Schema::hasTable('passkeys');
        } catch (\Throwable) {
            return self::$cachedTableExists = false;
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
