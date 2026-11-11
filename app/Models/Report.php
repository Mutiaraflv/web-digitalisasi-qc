<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_WAITING_REVIEW = 'waiting_review';
    public const STATUS_REVIEWED = 'reviewed';

    protected $fillable = [
        'created_by',
        'checker_id',
        'reviewer_id',
        'ssc_id',
        'report_no',
        'date',
        'customer',
        'part_name',
        'inspection_type',
        'job_no',
        'model_unit',
        'quote_no',
        'part_no',
        'doc_no',
        'rev',
        'responsibility',
        'effective_date',
        'page',
        'sections',
        'tir',
        'recommendations',
        'signatures',
        'photos',
        'has_rejects',
        'review_status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'sections' => 'array',
        'tir' => 'array',
        'recommendations' => 'array',
        'signatures' => 'array',
        'photos' => 'array',
        'has_rejects' => 'boolean',
        'date' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope submitted/reviewed reports (excluding drafts).
     */
    public function scopeNonDraft(Builder $query): Builder
    {
        return $query->where('review_status', '!=', self::STATUS_DRAFT);
    }

    /**
     * Scope reports owned/created by or assigned as Checked by to the user.
     */
    public function scopeOwnedOrCheckedBy(Builder $query, User $user): Builder
    {
        $userName = mb_strtolower(trim($user->name));

        return $query->where(function (Builder $q) use ($user, $userName) {
            $q->where('created_by', $user->id)
                ->orWhere('checker_id', $user->id)
                ->orWhere(function (Builder $sub) use ($userName) {
                    $sub->whereNull('checker_id')
                        ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(signatures, '$.checkedBy.name')))) = ?", [$userName]);
                });
        });
    }

    /**
     * Scope reports awaiting review by the specified user.
     */
    public function scopePendingApprovalFor(Builder $query, User $user): Builder
    {
        $userName = mb_strtolower(trim($user->name));

        return $query->where('review_status', self::STATUS_WAITING_REVIEW)
            ->where(function (Builder $q) use ($user, $userName) {
                $q->where('reviewer_id', $user->id)
                    ->orWhere(function (Builder $sub) use ($userName) {
                        $sub->whereNull('reviewer_id')
                            ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(signatures, '$.reviewedBy.name')))) = ?", [$userName]);
                    });
            });
    }

    public function isDraft(): bool
    {
        return $this->review_status === self::STATUS_DRAFT;
    }

    public function isWaitingReview(): bool
    {
        return $this->review_status === self::STATUS_WAITING_REVIEW;
    }

    public function isReviewed(): bool
    {
        return $this->review_status === self::STATUS_REVIEWED;
    }

    public function canBeModifiedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        if ($this->created_by === null) {
            return $user->isAdmin();
        }
        if ((int) $this->created_by === (int) $user->id) {
            return true;
        }
        if ($this->checker_id !== null && (int) $this->checker_id === (int) $user->id) {
            return true;
        }
        $checkedName = mb_strtolower(trim((string) ($this->signatures['checkedBy']['name'] ?? '')));
        return $this->checker_id === null && $checkedName !== '' && $checkedName === mb_strtolower(trim($user->name));
    }

    public function canBeReviewedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        if ($this->reviewer_id !== null) {
            return (int) $this->reviewer_id === (int) $user->id;
        }
        $sigName = mb_strtolower(trim((string) ($this->signatures['reviewedBy']['name'] ?? '')));
        return $sigName !== '' && $sigName === mb_strtolower(trim($user->name));
    }
}
