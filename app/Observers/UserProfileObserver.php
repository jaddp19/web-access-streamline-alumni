<?php

namespace App\Observers;

use App\Models\UserProfile;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserProfileObserver
{
    /** Only notify when one of these fields changes. */
    protected array $notifyOn = ['is_verified', 'batch_id'];

    public function updated(UserProfile $profile): void
    {
        $changed = array_keys($profile->getChanges());

        if (empty(array_intersect($changed, $this->notifyOn))) {
            return;
        }

        $user = $profile->user;

        if (! $user || ! $user->email) {
            return;
        }

        // Pick the right template.
        if (in_array('is_verified', $changed, true)) {
            $template = $profile->is_verified
                ? 'your-alumni-profile-has-been-verified'
                : 'your-alumni-verification-status-was-updated';
        } else {
            $template = 'your-alumni-profile-was-updated';
        }

        try {
            EmailTemplateService::send($template, $user->email, [
                'name'      => $user->name,
                'login_url' => route('login'),
                'changes'   => implode(', ', array_map(
                    fn ($f) => Str::headline($f),
                    $changed
                )),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Profile update email failed', [
                'user_id' => $user->id,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);
        }
    }
}