<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_QC_LEADER = 'qc_leader';
    public const ROLE_USER = 'user';

    /**
     * Supported application roles mapped to display labels.
     *
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_USER => __('qc.roles.user'),
            self::ROLE_QC_LEADER => __('qc.roles.qc_leader'),
            self::ROLE_ADMIN => __('qc.roles.admin'),
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'role',
        'theme',
        'locale',
        'password',
    ];

    /**
     * Get avatar public URL or null if none uploaded.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && file_exists(public_path('uploads/avatars/' . $this->avatar))) {
            return asset('uploads/avatars/' . $this->avatar);
        }
        return null;
    }

    /**
     * User initials fallback for avatar.
     */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr((string) $this->name, 0, 2));
    }

    /**
     * Check if user has administrator privileges.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user has QC Leader role.
     */
    public function isQcLeader(): bool
    {
        return $this->role === self::ROLE_QC_LEADER;
    }

    /**
     * Human-readable role label.
     */
    public function roleLabel(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ONLINE_WINDOW_SECONDS = 90;

    public const PRESENCE_TOUCH_INTERVAL_SECONDS = 20;

    private static ?bool $cachedHasLastSeenColumn = null;

    public static function hasLastSeenColumn(): bool
    {
        if (self::$cachedHasLastSeenColumn !== null) {
            return self::$cachedHasLastSeenColumn;
        }

        try {
            return self::$cachedHasLastSeenColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_at');
        } catch (\Throwable) {
            return self::$cachedHasLastSeenColumn = false;
        }
    }

    public static function touchPresence(?self $user, ?\Illuminate\Http\Request $request = null, bool $force = false): void
    {
        if (! $user) {
            return;
        }

        $nowTs = time();
        if (! $force && $request && $request->hasSession()) {
            $lastTouch = (int) $request->session()->get('_qc_presence_ts', 0);
            if (($nowTs - $lastTouch) < self::PRESENCE_TOUCH_INTERVAL_SECONDS) {
                return;
            }
        }

        if ($request && $request->hasSession()) {
            $request->session()->put('_qc_presence_ts', $nowTs);
        }

        if (! self::hasLastSeenColumn()) {
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->getKey())
                ->update(['last_seen_at' => now()]);
        } catch (\Throwable) {
            // Silently ignore transient presence write failures
        }
    }

    public static function clearPresenceIfNoOtherSessions(?int $userId, ?string $currentSessionId = null): void
    {
        if (! $userId || ! self::hasLastSeenColumn()) {
            return;
        }

        try {
            $cutoffTs = now()->subSeconds(self::ONLINE_WINDOW_SECONDS)->timestamp;
            $hasOtherActiveSession = false;

            if (config('session.driver') === 'database' && \Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                $query = \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $userId)
                    ->where('last_activity', '>=', $cutoffTs);

                if ($currentSessionId) {
                    $query->where('id', '!=', $currentSessionId);
                }

                $hasOtherActiveSession = $query->exists();
            }

            if (! $hasOtherActiveSession) {
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $userId)
                    ->update(['last_seen_at' => null]);
            }
        } catch (\Throwable) {
            // Silently ignore transient presence cleanup failures
        }
    }

    public static function getOnlinePresenceSummary(): array
    {
        $roleLabels = self::roleLabels();
        $onlineByRole = [
            self::ROLE_ADMIN => 0,
            self::ROLE_QC_LEADER => 0,
            self::ROLE_USER => 0,
        ];

        try {
            $totalUsers = self::count();
            $cutoff = now()->subSeconds(self::ONLINE_WINDOW_SECONDS);
            $cutoffTs = $cutoff->timestamp;

            $hasSessionsTable = config('session.driver') === 'database'
                && \Illuminate\Support\Facades\Schema::hasTable('sessions');
            $sessionUserIds = $hasSessionsTable
                ? \Illuminate\Support\Facades\DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->where('last_activity', '>=', $cutoffTs)
                    ->orderByDesc('last_activity')
                    ->pluck('user_id')
                    ->unique()
                    ->values()
                    ->all()
                : [];

            if (self::hasLastSeenColumn()) {
                $onlineCollection = self::query()
                    ->where(function ($query) use ($cutoff, $sessionUserIds) {
                        $query->where(function ($q) use ($cutoff) {
                            $q->whereNotNull('last_seen_at')
                                ->where('last_seen_at', '>=', $cutoff);
                        });

                        if (! empty($sessionUserIds)) {
                            $query->orWhere(function ($q) use ($sessionUserIds) {
                                $q->whereNull('last_seen_at')
                                    ->whereIn('id', $sessionUserIds);
                            });
                        }
                    })
                    ->orderByRaw('CASE WHEN last_seen_at IS NULL THEN 1 ELSE 0 END, last_seen_at DESC')
                    ->get();
            } elseif (! empty($sessionUserIds)) {
                $onlineCollection = self::whereIn('id', $sessionUserIds)->get();
            } else {
                $onlineCollection = collect();
            }
        } catch (\Throwable) {
            $totalUsers = 0;
            $onlineCollection = collect();
        }

        $onlineUsers = [];
        foreach ($onlineCollection as $account) {
            $roleKey = array_key_exists($account->role, $onlineByRole) ? $account->role : self::ROLE_USER;
            $onlineByRole[$roleKey]++;

            if (count($onlineUsers) < 12) {
                $onlineUsers[] = [
                    'id' => $account->id,
                    'name' => $account->name,
                    'role' => $account->role,
                    'role_label' => $roleLabels[$account->role] ?? ucfirst((string) $account->role),
                    'initials' => $account->initials(),
                    'avatar_url' => $account->avatar_url,
                ];
            }
        }

        $onlineCount = array_sum($onlineByRole);
        $totalUsers = max($totalUsers, $onlineCount);
        $offlineCount = max(0, $totalUsers - $onlineCount);
        $onlineRate = $totalUsers > 0 ? round(($onlineCount / $totalUsers) * 100, 1) : 0;

        return [
            'totalUsers' => $totalUsers,
            'onlineCount' => $onlineCount,
            'offlineCount' => $offlineCount,
            'onlineRate' => $onlineRate,
            'onlineByRole' => $onlineByRole,
            'onlineUsers' => $onlineUsers,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
