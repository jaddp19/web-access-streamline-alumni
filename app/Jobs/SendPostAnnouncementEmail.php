<?php

namespace App\Jobs;

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

    public function __construct(public int $postId) {}

    public function handle(): void
    {
        $post = Post::with('category')->find($this->postId);

        if (! $post || $post->status !== 'public') {
            return;
        }

        User::role('alumni')
            ->whereNotNull('email')
            ->chunkById(200, function ($alumni) use ($post) {
                foreach ($alumni as $u) {
                    try {
                        EmailTemplateService::send(
                            'institutional-announcement',
                            $u->email,
                            [
                                'name'         => $u->name,
                                'post_title'   => $post->title,
                                'post_excerpt' => Str::limit(strip_tags($post->description ?? ''), 180),
                                'category'     => $post->category?->cat_name ?? 'Announcement',
                                'post_url'     => route('login'),
                                'login_url'    => route('login'),
                            ]
                        );
                    } catch (\Throwable $e) {
                        Log::warning('Post announcement email failed', [
                            'post_id' => $post->id,
                            'email'   => $u->email,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            });

        Log::info('Post announcement blast complete', ['post_id' => $post->id]);
    }
}