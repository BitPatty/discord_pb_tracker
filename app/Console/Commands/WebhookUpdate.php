<?php

namespace App\Console\Commands;

use App\Http\Fetch;
use App\Models\Webhook;
use App\Models\WebhookState;
use Illuminate\Console\Command;

class WebhookUpdate extends Command
{
    protected $signature = 'webhook:update';
    protected $description = 'Updates webhook details';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        printf("Loading hooks\r\n");
        $hooks = Webhook::where('state', '<>', WebhookState::INVALIDATED)->get();

        foreach ($hooks as $hook) {
            $hook_data = Fetch::load($hook->url);

            printf('Updating hook (id: ' . $hook->id . ', discord_id: ' . $hook->discord_id . ') at ' . $hook->url . "\r\n");
            sleep(1);

            if ($hook_data) {
                $hook_data = json_decode($hook_data, true);

                if ($this->isWebhookInvalid($hook_data)) {
                    $hook->state = WebhookState::INVALIDATED;
                    $hook->save();
                    printf("Hook invalidated\r\n");
                } elseif ($this->validateWebhookDetails($hook_data)) {
                    $hook->discord_id = $hook_data['id'];
                    $hook->channel_id = $hook_data['channel_id'];
                    $hook->guild_id = $hook_data['guild_id'];
                    $hook->avatar_url = 'https://cdn.discordapp.com/avatars/' . $hook_data['id'] . '/' . $hook_data['avatar'] . '.png';
                    $hook->save();
                    printf("Hook updated\r\n");
                }
            }
        }
    }

    private function isWebhookInvalid($data)
    {
        return isset($data['code']) && $data['code'] === 10015;
    }

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
