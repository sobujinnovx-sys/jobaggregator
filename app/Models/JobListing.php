<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'company_id', 'category_id', 'location_type', 'location',
        'experience_level', 'description', 'apply_link', 'salary_range',
        'status', 'source', 'external_id', 'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
        ];
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }

    // Scopes for filtering
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('location_type', 'remote');
    }

    public function scopeByLocationType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('location_type', $type) : $query;
    }

    public function scopeByExperience(Builder $query, ?string $level): Builder
    {
        return $level ? $query->where('experience_level', $level) : $query;
    }

    public function scopeByKeyword(Builder $query, ?string $keyword): Builder
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    public function scopeByRegion(Builder $query, ?string $region): Builder
    {
        if (!$region) return $query;

        if ($region === 'bd') {
            return $query->where(function ($q) {
                $q->where('source', 'bd_career')
                  ->orWhere('location', 'like', '%Bangladesh%')
                  ->orWhere('location', 'like', '%Dhaka%');
            });
        }

        if ($region === 'global') {
            return $query->where(function ($q) {
                $q->where('source', '!=', 'bd_career')
                  ->where('location', 'not like', '%Bangladesh%')
                  ->where('location', 'not like', '%Dhaka%');
            });
        }

        return $query;
    }
}
