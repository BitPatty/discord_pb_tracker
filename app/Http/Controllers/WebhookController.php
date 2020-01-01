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

    /**
     * Returns the webhooks managed by the current user
     * @param Request $request The request
     * @return mixed
     */
    public function index(Request $request)
    {
        return Webhook::where(['manager_id' => $request->user()->id])->get();
    }

    /**
     * Returns the hook if the current user has the READ permission
     * @param Request $request
     * @param Webhook $hook
     * @return Webhook
     */
    public function show(Request $request, Webhook $hook)
    {
        if (!Gate::allows('read', $hook)) abort(403);
        return $hook;
    }

    /**
     * Updates the hook if the current user has the UPDATE permission
     * @param Request $request
     * @param Webhook $hook
     * @return \Illuminate\Http\JsonResponse Returns the hook
     */
    public function update(Request $request, Webhook $hook)
    {
        if (!Gate::allows('update', $hook)) abort(403);

        $validator = Validator::make($request->post(), [
            'name' => 'required|regex:/^[ a-zA-Z0-9\._\-]+$/u',
            'description' => 'max: 2048',
            'state' => 'required|in:ACTIVE,DEAD'
        ]);

        if ($validator->fails())
            return $this->formatValidationErrors($validator->errors());

        // If state is set to active reset last_updated to avoid posting old PBs later
        if ($hook->state !== $request->post('state') && $request->post('state') === WebhookState::ACTIVE)
            $this->resetWebhookTrackersTimestamp($hook);

        $hook->name = $request->post('name');
        $hook->description = $request->post('description') ?? '';
        $hook->state = $request->post('state');
        $hook->save();

        return Webhook::find($hook->id);
    }

    /**
     * Resets the 'last_updated' field on the trackers of the given hook
     * @param Webhook $hook The hook
     */
    private function resetWebhookTrackersTimestamp(Webhook $hook)
    {
        $hook->load('trackers');
        foreach ($hook->trackers as $tracker) {
            $tracker->last_updated = new \DateTime();
            $tracker->save();
        }
    }

    /**
     * Formats the validation errors based on the failed parameters
     * @param \Illuminate\Support\MessageBag $errors The validation errors
     * @return \Illuminate\Http\JsonResponse Returns the error message
     */
    private function formatValidationErrors(\Illuminate\Support\MessageBag $errors)
    {
        if ($errors->has('name')) {
            return response()->json(['status' => 422, 'message' => 'Invalid name format (name)'], 422);
        }

        if ($errors->has('description')) {
            return response()->json(['status' => 422, 'message' => 'Invalid description format (description)'], 422);
        }

        if ($errors->has('state')) {
            return response()->json(['status' => 422, 'message' => 'Invalid state format (state)'], 422);
        }

        if ($errors->has('url')) {
            return response()->json(['status' => 422, 'message' => 'Invalid webhook URL format (url)'], 422);
        }
    }

    /**
     * Creates a new Webhook
     * @param Request $request The request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'name' => 'required|regex:/^[a-zA-Z0-9 -_\.]+$/u',
            'url' => 'required|regex:/^https\:\/\/discordapp\.com\/api\/webhooks[\/a-zA-Z0-9\-_]+$/u',
            'description' => 'max: 2048'
        ]);

        if ($validator->fails())
            return $this->formatValidationErrors($validator->errors());

        $webhook_url = $request->post('url');
        $webhook_data = Fetch::load($webhook_url);

        if (!isset($webhook_data))
            return response()->json(['status' => 500, 'message' => 'Failed to load webhook data'], 500);

        $webhook_data = json_decode($webhook_data, true);

        if (!$this->validateWebhookData($webhook_data))
            return response()->json(['status' => 500, 'message' => 'Invalid webhook data received'], 500);

        if (Webhook::where(['discord_id' => $webhook_data['id']])->first() != null)
            return response()->json(['status' => 422, 'message' => 'Webhook is already in use'], 422);

        $hook = new Webhook();
        $hook->url = $webhook_url;
        $hook->manager_id = $request->user()->id;
        $hook->discord_id = $webhook_data['id'];
        $hook->channel_id = $webhook_data['channel_id'];
        $hook->guild_id = $webhook_data['guild_id'];
        $hook->name = $request->post('name');
        $hook->description = $request->post('description') ?? '';
        $hook->avatar_url = isset($webhook_data['avatar']) ? 'https://cdn.discordapp.com/avatars/' . $webhook_data['id'] . '/' . $webhook_data['avatar'] . '.png' : null;
        $hook->state = WebhookState::CREATED;
        $hook->save();

        return Webhook::find($hook->id);
    }

    /**
     * Validates whether the webhook data returned by discord can be processed
     * @param $hook_data mixed The hook data returned by discord
     * @return bool Returns true if the validation succeeds
     */
    private function validateWebhookData($hook_data)
    {
        return (
            isset($hook_data['type']) &&
            isset($hook_data['id']) &&
            isset($hook_data['channel_id']) &&
            isset($hook_data['guild_id'])
        );
    }
}
