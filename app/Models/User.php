<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use RuntimeException;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public static function getEligibleUsers(): EligibleUsers
    {
        // Count
        $votePresent = User::where([
            'is_present' => true,
            'is_voter' => true,
        ])->count();

        $voteProxied = User::query()
            ->where('is_present', true)
            ->where(static fn ($query) =>
            $query->where('is_voter', true)
                ->orWhere('can_proxy', true))
            ->whereHas('proxyFor')
            ->count();

        $voteCount = $voteProxied + $votePresent;

        // Return present, proxied and total
        return new EligibleUsers($votePresent, $voteProxied, $voteCount);
    }

    /**
     * Ensure a totp_token is always set
     *
     * @return void
     */
    public static function booted()
    {
        self::saving(static function (User $user) {
            if (!empty($user->totp_secret)) {
                return;
            }

            $user->totp_secret = self::getTotp(null)->getSecret();
        });
    }

    /**
     * Returns TOTP configured to 8 digits
     *
     * @param string|null $secret
     * @return TOTPInterface
     */
    private static function getTotp(?string $secret): TOTPInterface
    {
        return TOTP::create($secret, 30, 'sha1', 8);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'phone',
        'totp_secret',
        'proxy_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_voter' => 'bool',
        'is_admin' => 'bool',
        'is_monitor' => 'bool',
        'is_present' => 'bool',
        'can_proxy' => 'bool',
        'conscribo_id' => 'int',
    ];

    public function getHasTotpAttribute(): bool
    {
        return !empty($this->totp_secret);
    }

    /**
     * Returns verification instance
     *
     * @return TOTPInterface
     * @throws RuntimeException
     */
    public function getTotpAttribute(): TOTPInterface
    {
        if (empty($this->totp_secret)) {
            throw new RuntimeException('TOTP secret not set');
        }

        return self::getTotp($this->totp_secret);
    }

    /**
     * The user that this user's vote is transferred to
     *
     * @return BelongsTo
     */
    public function proxy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proxy_id');
    }

    /**
     * The user that gave an extra vote to this user
     *
     * @return HasOne
     */
    public function proxyFor(): HasOne
    {
        return $this->hasOne(User::class, 'proxy_id');
    }

    /**
     * Returns all votes the user has cast
     *
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(UserVote::class);
    }

    /**
     * Returns all approvals the user has cast
     *
     * @return HasMany
     */
    public function pollApprovals(): HasMany
    {
        return $this->hasMany(PollApproval::class);
    }

    /**
     * Returns the role the user has
     *
     * @return string
     */
    public function getVoteLabelAttribute()
    {
        $rights = [];
        if ($this->is_voter) {
            $rights[] = 'S';
        }
        if ($this->can_proxy) {
            $rights[] = 'M';
        }
        if ($this->is_admin) {
            $rights[] = 'A';
        }

        return implode(', ', $rights) ?: '–';
    }

    public function scopeHasVoteRights(Builder $query): Builder
    {
        // phpcs:disable SlevomatCodingStandard.Functions.RequireArrowFunction.RequiredArrowFunction
        return $query->where(static function ($query) {
            $query->where('is_voter', '1')
                ->orWhere(static function ($query) {
                    return $query->has('proxyFor')
                        ->where('can_proxy', '1');
                });
        });
        // phpcs:enable
    }
}
