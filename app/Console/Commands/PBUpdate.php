<?php

namespace App\Console\Commands;

use App\Http\Fetch;
use App\Models\Log;
use App\Models\LogType;
use App\Models\ProcessType;
use App\Models\SRCUser;
use App\Models\Tracker;
use App\Models\WebhookState;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PBUpdate extends Command
{
    protected $signature = 'pbs:update';
    protected $description = 'Updates PB times and triggers the webhooks';

    private $process_uuid;

    public function __construct()
    {
        parent::__construct();
        $this->process_uuid = Str::uuid()->toString();
    }

    /**
     * Loads the PBs, updates usernames and triggers the Discord webhooks if necessary
     */
    public function handle()
    {
        Log::createEntry(LogType::PROCESS_START, 'PB update started', ProcessType::PB_UPDATE, $this->process_uuid, null, null, null, null);
        $users = SRCUser::all();

        foreach ($users as $user) {
            sleep(1);
            $pbs = $this->fetchPersonalBests($user->src_id);
            $fetch_dt = new \DateTime();

            if (isset($pbs) && isset($pbs['data']) && is_array($pbs['data']) && count($pbs['data']) > 0) {
                $userName = $this->findPlayerName($pbs['data'][0]['players']['data'], $user->src_id);
                if ($userName !== $user->src_name && ($userName) && !empty($userName)) {
                    $user->src_name = $userName;
                    $user->save();
                }

                $trackers = $users = Tracker::with(['webhook', 'src_user'])->where(['src_user_id' => $user->id])->whereHas('webhook', function ($q) {
                    $q->where(['state' => WebhookState::ACTIVE]);
                })->get();

                foreach ($trackers as $tracker) {
                    $tracker_cnt++;
                    $tracker_dt = $this->parseTimeString($tracker->last_updated);

                    foreach ($pbs['data'] as $pb) {
                        if ($pb['category']['data']['type'] === 'per-game' && isset($pb['run']['status'])
                            && isset($pb['run']['status']['verify-date'])
                            && $pb['run']['status']['status'] === 'verified'
                            && $this->parseTimeString($pb['run']['status']['verify-date']) > $tracker_dt) {
                            try {
                                $tracker->last_updated = $fetch_dt;
                                $tracker->save();
                                $this->post_pb($tracker, $pb);
                                Log::createEntry(LogType::PB_POSTED, 'PB => ' . json_encode($pb), ProcessType::PB_UPDATE, $this->process_uuid, null, $user, $tracker->webhook, $tracker);
                                sleep(2);
                            } catch (\Exception $ex) {
                            }
                        }

                        Log::createEntry(LogType::PB_UPDATED, 'PB => ' . json_encode($pb), ProcessType::PB_UPDATE, $this->process_uuid, null, $user, $tracker->webhook, $tracker);
                    }

                    Log::createEntry(LogType::TRACKER_UPDATED, null, ProcessType::PB_UPDATE, $this->process_uuid, null, $user, $tracker->webhook, $tracker);
                }
            }
        }

        Log::createEntry(LogType::PROCESS_END, 'PB update finished', ProcessType::PB_UPDATE, $this->process_uuid, null, null, null, null);
    }

    /**
     * Finds the players name in a collection of players
     * @param $players array The player list
     * @param $id mixed The users id
     * @return mixed Returns the users name
     */
    private function findPlayerName($players, $id)
    {
        foreach ($players as $player) {
            if ($player['id'] === $id) return $player['names']['international'];
        }

        return null;
    }

    /**
     * Truncates the comment string if necessary to avoid
     * exceeding discords character limit on field values
     * @param $comment string The comment to prepare
     * @return string Returns the (truncated) comment
     */
    private function prepareComment($comment)
    {
        if (strlen($comment) > 980) return substr($comment, 0, 970) . '...';
        return $comment;
    }

    /**
     * Parses a datetime string to it's object equivalent
     * @param $dt mixed The datetime string
     * @return Carbon The parsed date
     */
    private function parseTimeString($dt)
    {
        return Carbon::parse($dt);
    }

    /**
     * Fetches the personal bests for the given user
     * @param $uid string The speedrun.com user id
     * @return mixed Returns the users deserialized PB's
     */
    private function fetchPersonalBests($uid)
    {
        $data = Fetch::load("https://www.speedrun.com/api/v1/users/" . $uid . "/personal-bests?embed=players,game,category");

        if ($data) {
            return json_decode($data, true);
        }
    }

    /**
     * Posts a PB with the given tracker
     * @param Tracker $tracker The tracker
     * @param $pb mixed The run
     */
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
                        "name" => "New PB by " . $tracker->src_user->src_name,
                        "url" => $run_url,
                        "icon_url" => "https://pbs.twimg.com/profile_images/500500884757831682/L0qajD-Q_400x400.png"
                    ),

                    "fields" => array(
                        array(
                            "name" => $game_name . " - " . $game_category . " in " . $run_time . "!",
                            "value" => "Rank: $run_place, Comment: " . $this->prepareComment($run_comment),
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
