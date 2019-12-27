<?php

namespace App\Policies;

use App\Models\Webhook;
use App\Models\WebhookState;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\GenericUser;

class WebhookPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function read(GenericUser $user, Webhook $hook)
    {
        return $hook->manager_id === $user->id;
    }

    public function update(GenericUser $user, Webhook $hook)
    {
        return $hook->manager_id === $user->id && $hook->state !== WebhookState::INVALIDATED;
    }
}
