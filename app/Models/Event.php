<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'starts_at',
        'ends_at',
        'registration_deadline',
        'location',
        'capacity',
        'status',
        'created_by',
    ];

    protected $casts = [
        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
        'registration_deadline' => 'datetime',
        'capacity'              => 'integer',
    ];

    protected array $auditExclude = ['updated_at'];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }

    /** Only those who said YES. */
    public function attendees(): HasMany
    {
        return $this->hasMany(EventRsvp::class)->where('response', 'yes');
    }

    // =========================================================
    //  SCOPES
    // =========================================================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now());
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    public function attendeeCount(): int
    {
        return $this->rsvps()->where('response', 'yes')->count();
    }

    public function actualAttendanceCount(): int
    {
        return $this->rsvps()
            ->where('response', 'yes')
            ->whereNotNull('attended_at')
            ->count();
    }

    public function isFull(): bool
    {
        return $this->capacity
            && $this->attendeeCount() >= $this->capacity;
    }

    public function registrationOpen(): bool
    {
        if ($this->status !== 'published') return false;
        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) return false;
        return true;
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed'
            || ($this->ends_at && now()->isAfter($this->ends_at));
    }

    /** Has this specific user RSVP'd? */
    public function rsvpFor(?int $userId): ?EventRsvp
    {
        if (! $userId) return null;
        return $this->rsvps()->where('user_id', $userId)->first();
    }

    // =========================================================
    //  ACCESSORS
    // =========================================================

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) return null;

        return filter_var($this->image, FILTER_VALIDATE_URL)
            ? $this->image
            : Storage::url($this->image);
    }
}