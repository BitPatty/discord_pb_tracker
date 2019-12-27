<?php

namespace App\Policies;

use App\Models\Tracker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\GenericUser;

class TrackerPolicy
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


    public function read(GenericUser $user, Tracker $tracker)
    {
        return $tracker->webhook()->first()->manager_id === $user->id;
    }

    public function update(GenericUser $user, Tracker $tracker)
    {
        return $tracker->webhook()->first()->manager_id === $user->id;
    }
}
