<?php

namespace App\Models;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRsvp extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'response',
        'responded_at',
        'attended_at',
        'notes',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'attended_at'  => 'datetime',
    ];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    public function hasAttended(): bool
    {
        return $this->attended_at !== null;
    }

    public function markAttended(): void
    {
        if (! $this->attended_at) {
            $this->update(['attended_at' => now()]);
        }
    }

    public function undoAttendance(): void
    {
        $this->update(['attended_at' => null]);
    }
}