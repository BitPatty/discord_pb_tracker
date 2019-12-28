<?php

namespace App\Console\Commands;

use App\Http\Fetch;
use App\Models\Tracker;
use App\Models\Webhook;
use App\Models\WebhookState;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PBUpdate extends Command
{
    protected $signature = 'pbs:update';
    protected $description = 'Updates PB times and triggers the webhooks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = Tracker::whereHas('webhook', function ($q) {
            $q->where(['state' => WebhookState::ACTIVE]);
        })->distinct()->get('src_id');

        foreach ($users as $user) {
            sleep(1);
            printf("Updating PBs for user " . $user->src_id . "\r\n");

            $pbs = $this->fetchPersonalBests($user->src_id);
            $fetch_dt = new \DateTime();

            if ($pbs) {
                $trackers = $users = Tracker::with('webhook')->where(['src_id' => $user->src_id])->whereHas('webhook', function ($q) {
                    $q->where(['state' => WebhookState::ACTIVE]);
                })->get();

                foreach ($trackers as $tracker) {
                    try {
                        foreach ($pbs['data'] as $pb) {
                            printf("Checking " . $pb['run']['id'] . "\r\n");

                            if ($pb['category']['data']['type'] === 'per-game' && isset($pb['run']['status'])
                                && isset($pb['run']['status']['verify-date'])
                                && $pb['run']['status']['status'] === 'verified'
                                && $this->parseTimeString($pb['run']['status']['verify-date']) > $this->parseTimeString($tracker->last_updated)) {
                                printf("Posting PB: " . $pb['run']['id'] . "\r\n");
                                $this->post_pb($tracker, $pb);
                                sleep(2);
                            }
                        }
                    } catch (\Exception $ex) {
                    } finally {
                        $tracker->last_updated = $fetch_dt;
                        $tracker->save();
                    }
                }
            }
        }
    }

    private function parseTimeString($dt)
    {
        return Carbon::parse($dt);
    }

    private function fetchPersonalBests($uid)
    {
        $data = Fetch::load("https://www.speedrun.com/api/v1/users/" . $uid . "/personal-bests?embed=game,category");

        if ($data) {
            return json_decode($data, true);
        }
    }

    private function post_pb(Tracker $tracker, $pb)
    {
        $run_date = $pb['run']['submitted'];
        $run_url = $pb['run']['weblink'];
        $run_place = $pb['place'];

        $run_comment = $pb['run']['comment'];
        if (!isset($run_comment) || empty($run_comment)) $run_comment = "-";

        $run_time = gmdate('H:i:s', floor(floatval($pb['run']['times']['primary_t'])));
        $game_name = $pb['game']['data']['names']['international'];
        $game_category = $pb['category']['data']['name'];
        $game_icon = $pb['game']['data']['assets']['icon']['uri'];
        $game_cover = $pb['game']['data']['assets']['cover-medium']['uri'];

        $payload = array(
            "content" => "",
            "username" => $tracker->webhook->name,
            "embeds" => array(
                array(
                    "title" => "",
                    "description" => "",
                    "url" => $run_url,
                    "color" => 6964921,
                    "timestamp" => $run_date,
                    "footer" => array(
                        "icon_url" => $game_icon,
                        "text" => "Verified"
                    ),

                    "image" => array(
                        "url" => $game_cover
                    ),

                    "author" => array(
                        "name" => "New PB by " . $tracker->src_name,
                        "url" => $run_url,
                        "icon_url" => "https://pbs.twimg.com/profile_images/500500884757831682/L0qajD-Q_400x400.png"
                    ),

                    "fields" => array(
                        array(
                            "name" => $game_name . " - " . $game_category . " in " . $run_time . "!",
                            "value" => "Rank: $run_place, Comment: $run_comment",
                            "inline" => true
                        )
                    )
                )
            )
        );

        $ch = curl_init($tracker->webhook->url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
