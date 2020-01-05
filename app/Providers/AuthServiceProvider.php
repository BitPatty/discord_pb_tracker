<?php

namespace App\Providers;

use App\Models\Tracker;
use App\Models\Webhook;
use App\Policies\TrackerPolicy;
use App\Policies\WebhookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Webhook::class => WebhookPolicy::class,
        Tracker::class => TrackerPolicy::class
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->is_global_admin ? true : null;
        });
    }
}
