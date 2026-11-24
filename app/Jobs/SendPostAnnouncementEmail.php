<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendPostAnnouncementEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    // 1. Add $senderId to the constructor
    public function __construct(public int $postId, public int $senderId) {}

    public function handle(): void
    {
        $post = Post::with('category')->find($this->postId);

        if (! $post || $post->status !== 'public') {
            return;
        }

        $sender = User::find($this->senderId);

        // Default query: all alumni
        $alumniQuery = User::role('alumni')->whereNotNull('email');

        // 2. If the sender is a Program Head, filter by their department
        if ($sender && $sender->hasRole('program head')) {
            $department = Department::where('program_head_id', $sender->id)->first();

            if ($department) {
                // Filter alumni who have a profile in this department
                $alumniQuery->whereHas('userProfile.courses', function ($q) use ($department) {
                    $q->where('department_id', $department->id);
                });
            }
        }

        // 3. Chunk and send
        $alumniQuery->chunkById(200, function ($alumni) use ($post) {
            foreach ($alumni as $u) {
                try {
                    EmailTemplateService::send(
                        'institutional-announcement',
                        $u->email,
                        [
                            'name' => $u->name,
                            'post_title' => $post->title,
                            'post_excerpt' => Str::limit(strip_tags($post->description ?? ''), 180),
                            'category' => $post->category?->cat_name ?? 'Announcement',
                            'post_url' => route('login'),
                            'login_url' => route('login'),
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::warning('Post announcement email failed', [
                        'post_id' => $post->id,
                        'email' => $u->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        Log::info('Post announcement blast complete', ['post_id' => $post->id]);
    }
}
