<?php

namespace App\Support;

use App\Models\Department;
use App\Models\Event;
use App\Models\Post;
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
            'notifications' => self::unreadNotifications($user->id, $user->last_seen_posts_at, $user->created_at),
        ];
    }

    public static function pendingMessages(int $userId): int
    {
        return Cache::remember("badge.pending_messages.$userId", 20, function () use ($userId) {
            $allowed = self::allowedAuthorIds();
            if (empty($allowed)) {
                return 0;
            }

            return Event::query()
                ->where('status', 'published')
                ->whereIn('created_by', $allowed)
                ->whereDoesntHave('rsvps', fn ($q) =>
                    $q->where('user_id', $userId)
                        ->whereIn('response', ['yes', 'maybe', 'no'])
                )
                ->count();
        });
    }

    public static function unreadNotifications(int $userId, $lastSeen, $createdAt): int
    {
        $key = "badge.unread_posts.$userId." . optional($lastSeen)->timestamp;

        return Cache::remember($key, 20, function () use ($lastSeen, $createdAt) {
            $allowed = self::allowedAuthorIds();
            if (empty($allowed)) {
                return 0;
            }

            return Post::query()
                ->where('status', 'public')
                ->whereIn('user_id', $allowed)
                ->where('created_at', '>', $lastSeen ?? $createdAt)
                ->count();
        });
    }

    protected static function allowedAuthorIds(): array
    {
        return array_values(array_unique(array_merge(
            self::registrarIds(),
            self::programHeadIdsForMyDepartments(),
        )));
    }

    protected static function registrarIds(): array
    {
        return Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->all();
        });
    }

    public static function forgetFor(int $userId): void
    {
        Cache::forget("badge.pending_messages.$userId");
        // unread_posts is keyed by last_seen_posts_at's timestamp, so it
        // self-invalidates the moment that column changes — nothing to forget there.
    }

    protected static function programHeadIdsForMyDepartments(): array
    {
        $profile = Auth::user()?->userProfile;
        if (! $profile) {
            return [];
        }

        $deptIds = $profile->courses()
            ->pluck('department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($deptIds)) {
            return [];
        }

        sort($deptIds);
        $key = 'program_head_ids_by_dept_' . implode('_', $deptIds);

        return Cache::remember($key, now()->addHour(), function () use ($deptIds) {
            return Department::query()
                ->whereIn('id', $deptIds)
                ->whereNotNull('program_head_id')
                ->pluck('program_head_id')
                ->unique()
                ->values()
                ->all();
        });
    }
}