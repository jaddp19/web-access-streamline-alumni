<?php

namespace App\Livewire\Alumni;

use App\Models\UserProfile;
use App\Models\WorkHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    // =========================================================
    //  USER + PROFILE
    // =========================================================

    #[Computed]
    public function alumni()
    {
        return Auth::user();
    }

    #[Computed]
    public function userProfile(): ?UserProfile
    {
        return UserProfile::query()
            ->select(
                'id', 'user_id', 'avatar', 'gender',
                'contact_number_1', 'contact_number_2',
                'location', 'batch_id',
                'board_taken', 'board_rate', 'is_verified'
            )
            ->with([
                'batch:id,batch_name',
                'courses:id,course_title,course_type,department_id',
                'courses.department:id,dept_name',
            ])
            ->where('user_id', $this->alumni->id)
            ->first();
    }

    #[Computed]
    public function course()
    {
        return $this->userProfile?->courses->first();
    }

    // =========================================================
    //  CONTACT / LOCATION
    // =========================================================

    /** Decoded location array — safe against both array & JSON string. */
    #[Computed]
    public function location(): array
    {
        $raw = $this->userProfile?->location;

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    #[Computed]
    public function contact(): array
    {
        $loc = $this->location;
        $p   = $this->userProfile;

        return [
            'gender'         => $p?->gender ?? ($loc['gender'] ?? null),
            'phone_number_1' => $p?->contact_number_1,
            'phone_number_2' => $p?->contact_number_2,
            'latitude'       => $loc['latitude'] ?? null,
            'longitude'      => $loc['longitude'] ?? null,
            'address'        => $loc['address'] ?? null,
        ];
    }

    // =========================================================
    //  AVATAR
    // =========================================================

    #[Computed]
    public function avatarUrl(): ?string
    {
        $avatar = $this->userProfile?->avatar;

        if (blank($avatar)) {
            return null;
        }

        return filter_var($avatar, FILTER_VALIDATE_URL)
            ? $avatar
            : Storage::url($avatar);
    }

    // =========================================================
    //  WORK HISTORY
    // =========================================================

    #[Computed]
    public function workHistories()
    {
        return WorkHistory::query()
            ->select(
                'id', 'user_id', 'work_name', 'company_id',
                'date_hired', 'is_current_job'
            )
            ->where('user_id', $this->alumni->id)
            ->with(['company:id,company_name,company_logo,company_address'])
            ->orderByDesc('is_current_job')
            ->orderByDesc('date_hired')
            ->get();
    }

    /**
     * Tenure label for a work history entry.
     * NOTE: WorkHistory has no `date_ended` column yet — every record is
     * treated as ongoing from `date_hired`. Add `date_ended` later for accuracy.
     */
    public function durationLabel(WorkHistory $history): string
    {
        if (! $history->date_hired) {
            return '—';
        }

        $months = (int) $history->date_hired->diffInMonths(now());

        if ($months < 1) {
            return 'Less than a month';
        }

        $years = intdiv($months, 12);
        $rem   = $months % 12;

        $parts = [];
        if ($years > 0) $parts[] = $years . ' yr' . ($years > 1 ? 's' : '');
        if ($rem > 0)   $parts[] = $rem   . ' mo' . ($rem   > 1 ? 's' : '');

        return implode(' ', $parts);
    }
};