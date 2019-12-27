<?php

namespace App\Http\Controllers;

use App\Http\Fetch;
use App\Models\Webhook;
use App\Models\WebhookState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        return Webhook::where(['manager_id' => $request->user()->id])->get();
    }

    public function show(Request $request, Webhook $hook)
    {
        if (!Gate::allows('read', $hook)) abort(403);
        return $hook;
    }

    public function update(Request $request, Webhook $hook)
    {
        if (!Gate::allows('update', $hook)) abort(403);

        $validator = Validator::make($request->post(), [
            'name' => 'required|regex:/^[ a-zA-Z0-9]+$/u',
            'description' => 'max: 2048'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();

            if ($messages->has('name')) {
                return response()->json(['status' => 422, 'message' => 'Invalid name format (name)'], 422);
            }

            if ($messages->has('description')) {
                return response()->json(['status' => 422, 'message' => 'Invalid description format (description)'], 422);
            }
        }

        $hook->name = $request->post('name');
        $hook->description = $request->post('description') ?? '';
        $hook->save();

        return Webhook::find($hook->id);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'name' => 'required|regex:/^[a-zA-Z0-9 -_]+$/u',
            'url' => 'required|regex:/^https\:\/\/discordapp\.com\/api\/webhooks[\/a-zA-Z0-9\-_]+$/u',
            'description' => 'max: 2048'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors();

            if ($messages->has('url')) {
                return response()->json(['status' => 422, 'message' => 'Invalid webhook URL format (url)'], 422);
            }

            if ($messages->has('name')) {
                return response()->json(['status' => 422, 'message' => 'Invalid name format (name)'], 422);
            }

            if ($messages->has('description')) {
                return response()->json(['status' => 422, 'message' => 'Invalid description format (description)'], 422);
            }
        }

        $webhook_url = $request->post('url');
        $webhook_data = Fetch::load($webhook_url);

        if (!isset($webhook_data)) {
            return response()->json(['status' => 500, 'message' => 'Failed to load webhook data'], 500);
        }

        $webhook_data = json_decode($webhook_data, true);

        if (!$this->validateWebhookDetails($webhook_data)) {
            return response()->json(['status' => 500, 'message' => 'Invalid webhook data received'], 500);
        }

        if (Webhook::where(['discord_id' => $webhook_data['id']])->first() != null) {
            return response()->json(['status' => 422, 'message' => 'Webhook is already in use'], 422);
        }

        $hook = new Webhook();
        $hook->url = $webhook_url;
        $hook->manager_id = $request->user()->id;
        $hook->discord_id = $webhook_data['id'];
        $hook->channel_id = $webhook_data['channel_id'];
        $hook->guild_id = $webhook_data['guild_id'];
        $hook->name = $request->post('name');
        $hook->description = $request->post('description') ?? '';
        $hook->avatar_url = $webhook_data['avatar'];
        $hook->state = WebhookState::CREATED;
        $hook->save();

        return Webhook::find($hook->id);
    }

    private function validateWebhookDetails($hook)
    {
        return (
            isset($hook['type']) &&
            isset($hook['id']) &&
            isset($hook['channel_id']) &&
            isset($hook['guild_id'])
        );
    }
}
