<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BadgeCounts
{
    public static function forCurrentUser(): array
    {
        $user = Auth::user();
        if (! $user) {
            return ['messages' => 0, 'notifications' => 0];
        }

        return [
            'messages'      => self::pendingMessages($user->id),
            'notifications' => self::unreadNotifications($user->id),
        ];
    }

    public static function pendingMessages(int $userId): int
    {
        return Cache::remember("badge.pending_messages.$userId", 20, function () use ($userId) {
            $allowed = self::allowedAuthorIds($userId);
            if (empty($allowed)) {
                return 0;
            }

            return \App\Models\Event::query()
                ->where('status', 'published')
                ->whereIn('created_by', $allowed)
                ->whereDoesntHave('rsvps', fn ($q) =>
                    $q->where('user_id', $userId)
                        ->whereIn('response', ['yes', 'maybe', 'no'])
                )
                ->count();
        });
    }

    public static function unreadNotifications(int $userId): int
    {
        return Cache::remember("badge.unread_posts.$userId", 20, function () use ($userId) {
            $allowed = self::allowedAuthorIds($userId);
            if (empty($allowed)) {
                return 0;
            }

            return \App\Models\Post::query()
                ->where('status', 'public')
                ->whereIn('user_id', $allowed)
                ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId))
                ->count();
        });
    }

    public static function forgetFor(int $userId): void
    {
        Cache::forget("badge.pending_messages.$userId");
        Cache::forget("badge.unread_posts.$userId");
    }

    // =========================================================
    //  INTERNAL
    // =========================================================

    protected static function allowedAuthorIds(int $userId): array
    {
        return array_values(array_unique(array_merge(
            self::registrarIds(),
            self::programHeadIdsForUser($userId),
        )));
    }

    protected static function registrarIds(): array
    {
        return Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        });
    }

    /**
     * Program-head user IDs whose department contains the given user.
     *
     * Explicitly qualified column + role-verified lookup +
     * v2 cache key + short TTL — so a freshly-assigned head
     * reaches their alumni within 15 minutes.
     */
    protected static function programHeadIdsForUser(int $userId): array
    {
        $profile = User::find($userId)?->userProfile;
        if (! $profile) {
            return [];
        }

        $deptIds = $profile->courses()
            ->pluck('courses.department_id')
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($deptIds)) {
            return [];
        }

        sort($deptIds);
        $key = 'program_head_ids_by_dept_v2_' . implode('_', $deptIds);

        return Cache::remember($key, now()->addMinutes(15), function () use ($deptIds) {
            return User::query()
                ->role('program head')
                ->whereHas('department', fn ($q) => $q->whereIn('id', $deptIds))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        });
    }
}