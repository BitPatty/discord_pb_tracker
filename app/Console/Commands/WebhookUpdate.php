<?php

namespace App\Console\Commands;

use App\Http\Fetch;
use App\Models\Log;
use App\Models\LogType;
use App\Models\ProcessType;
use App\Models\Webhook;
use App\Models\WebhookState;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class WebhookUpdate extends Command
{
    protected $signature = 'webhook:update';
    protected $description = 'Updates webhook details';

    private $process_uuid;

    public function __construct()
    {
        parent::__construct();
        $this->process_uuid = Str::uuid()->toString();
    }

    /**
     * Updates and (in)validates existing hooks
     */
    public function handle()
    {
        Log::createEntry(LogType::PROCESS_START, 'Webhook update started', ProcessType::WEBHOOK_UPDATE, $this->process_uuid, null, null, null, null);
        $hooks = Webhook::where('state', '<>', WebhookState::INVALIDATED)->get();

        foreach ($hooks as $hook) {
            $hook_data = Fetch::load($hook->url);

            sleep(1);
            printf('Updating hook (id: ' . $hook->id . ', discord_id: ' . $hook->discord_id . ")\r\n");

            if ($hook_data) {
                $hook_data = json_decode($hook_data, true);

                if ($this->isWebhookInvalid($hook_data)) {
                    $hook->state = WebhookState::INVALIDATED;
                    $hook->save();

                    Log::createEntry(LogType::WEBHOOK_INVALIDATED, 'Response => ' . json_encode($hook_data), ProcessType::WEBHOOK_UPDATE, $this->process_uuid, null, null, $hook, null);
                } elseif ($this->validateWebhookDetails($hook_data)) {
                    $hook->discord_id = $hook_data['id'];
                    $hook->channel_id = $hook_data['channel_id'];
                    $hook->guild_id = $hook_data['guild_id'];
                    $hook->avatar_url = isset($hook_data['avatar']) ? 'https://cdn.discordapp.com/avatars/' . $hook_data['id'] . '/' . $hook_data['avatar'] . '.png' : null;
                    $hook->save();

                    Log::createEntry(LogType::WEBHOOK_UPDATED, null, ProcessType::WEBHOOK_UPDATE, $this->process_uuid, null, null, $hook, null);
                }
            }
        }

        Log::createEntry(LogType::PROCESS_END, 'Webhook update finished', ProcessType::WEBHOOK_UPDATE, $this->process_uuid, null, null, null, null);
    }

    /**
     * Checks whether the webhook data returned by discord
     * contains an invalidation code
     * @param $data mixed The discord data
     * @return bool Returns true if the webhook is invalid
     */
    private function isWebhookInvalid($data)
    {
        return isset($data['code']) && $data['code'] === 10015;
    }

    /**
     * Validates whether the webhook data returned by discord
     * can be processed
     * @param $data mixed The discord data
     * @return bool Returns true if the validation succeeds
     */
    private function validateWebhookDetails($data)
    {
        return (
            isset($data['type']) &&
            isset($data['id']) &&
            isset($data['channel_id']) &&
            isset($data['guild_id'])
        );
    }
}
