<?php

namespace App\Http\Controllers;

use App\Http\Fetch;
use App\Models\Tracker;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackerController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $uid = $request->user()->id;
        return Tracker::whereHas('webhook', function ($q) use ($uid) {
            $q->where(['manager_id' => $uid]);
        })->get();
    }

    public function show(Request $request, $id)
    {
        $uid = $request->user()->id;
        return Tracker::whereHas('webhook', function ($q) use ($uid) {
            $q->where(['manager_id' => $uid]);
        })->find($id);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'hook' => 'required|numeric',
            'runner' => 'required|alpha_num'
        ]);

        if ($validator->fails()) {

            $messages = $validator->errors();
            if ($messages->has('hook')) return response()->json(['status' => 422, 'message' => 'Invalid hook format (hook)'], 422);
            elseif ($messages->has('runner')) return response()->json(['status' => 422, 'message' => 'Invalid runner format (runner)'], 422);
        }

        $hook_id = $request->post('hook');
        $hook = Webhook::find($hook_id);

        if (!isset($hook)) {
            return response()->json(['status' => 422, 'message' => 'Webhook not registered'], 422);
        }

        $runner_name = $request->post('runner');
        $runner_data = Fetch::load('http://speedrun.com/api/v1/users/' . $runner_name);

        if (!isset($runner_data)) {
            return response()->json(['status' => 500, 'message' => 'Failed to load runner data'], 500);
        }

        $runner_data = json_decode($runner_data, true)['data'];

        if (!$this->validateRunnerDetails($runner_data)) {
            return response()->json(['status' => 500, 'message' => 'Invalid runner data received'], 500);
        }

        if (Tracker::where(['src_id' => $runner_data['id'], 'webhook_id' => $hook_id])->first() != null) {
            return response()->json(['status' => 422, 'message' => 'Tracker is already in use'], 422);
        }

        $tracker = new Tracker();
        $tracker->src_name = $runner_data['names']['international'];
        $tracker->src_id = $runner_data['id'];
        $tracker->webhook_id = $hook_id;
        $tracker->last_updated = new \DateTime();
        $tracker->save();

        return Tracker::find($tracker->id);
    }

    private function validateRunnerDetails($runner)
    {
        return (
            isset($runner['id']) &&
            isset($runner['names']['international']) &&
            isset($runner['role']) &&
            $runner['role'] === 'user'
        );
    }
}
