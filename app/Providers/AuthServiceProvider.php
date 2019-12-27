<?php

namespace App\Providers;

use App\Models\Tracker;
use App\Models\Webhook;
use App\Policies\TrackerPolicy;
use App\Policies\WebhookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Webhook::class => WebhookPolicy::class,
        Tracker::class => TrackerPolicy::class
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
