<?php

namespace App\Providers;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Small counters shown next to sidebar links (e.g. unread chats for the admin).
        View::composer('partials.shell.sidebar', function ($view) {
            $badges = [];

            if (($view->getData()['role'] ?? null) === 'admin') {
                $badges['unreadChats'] = ChatMessage::where('is_read', false)->count();
            }

            $view->with('lmsBadges', $badges);
        });
    }
}
