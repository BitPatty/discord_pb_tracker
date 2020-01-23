<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function tracker()
    {
        return $this->hasOne(Tracker::class, 'id', 'tracker_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function src_user()
    {
        return $this->hasOne(SRCUser::class, 'id', 'src_user_id');
    }

    public function webhook()
    {
        return $this->hasOne(Webhook::class, 'id', 'webhook_id');
    }

    protected $fillable = [
        'type',
        'message',
        'process_type',
        'process_uuid',
        'user_id',
        'user_name',
        'tracker_id',
        'tracker_last_updated',
        'src_user_id',
        'src_user_src_id',
        'src_user_name',
        'webhook_id',
        'webhook_channel_id',
        'webhook_discord_id',
        'webhook_channel_name',
        'webhook_guild_name',
        'webhook_state',
        'webhook_name',
    ];

    protected $table = "t_log";

    public static function createEntry(
        $type,
        $message,
        $process_type,
        $process_uuid,
        ?User $user,
        ?SRCUser $srcUser,
        ?Webhook $hook,
        ?Tracker $tracker
    )
    {
        try {
            $l = new Log();

            $l->type = $type;
            $l->message = $message;
            $l->process_type = $process_type;
            $l->process_uuid = $process_uuid;

            if ($user) {
                $l->user_id = $user->id;
                $l->user_name = $user->name;
            }

            if ($srcUser) {
                $l->src_user_id = $srcUser->id;
                $l->src_user_src_id = $srcUser->src_id;
                $l->src_user_name = $srcUser->src_name;
            }

            if ($tracker) {
                $l->tracker_id = $tracker->id;
                $l->tracker_src_user_id = $tracker->src_id;
                $l->tracker_src_name = $tracker->src_name;
                $l->tracker_last_updated = $tracker->last_updated;
            }

            if ($hook) {
                $l->webhook_id;
                $l->webhook_channel_id = $hook->channel_id;
                $l->webhook_discord_id = $hook->discord_id;
                $l->webhook_channel_name = $hook->channel_name;
                $l->webhook_guild_name = $hook->guild_name;
                $l->webhook_state = $hook->state;
                $l->webhook_name = $hook->name;
            }

            $l->save();
        } catch (\Exception $ex) {
            try {
                $l = new Log();
                $l->type = LogType::ERROR;
                $l->message = "Failed to create log entry: " . $ex->getMessage();
                $l->save();
            } catch (\Exception $ex) {
            }
        }
    }
}
