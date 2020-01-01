<?php

namespace App\Http\Controllers;

use App\Http\Fetch;
use App\Models\Tracker;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TrackerController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Returns the trackers managed by the current user
     * @param Request $request The request
     * @return mixed Returns the users trackers
     */
    public function index(Request $request)
    {
        if ($request->user()->is_global_admin) return Tracker::all();

        $uid = $request->user()->id;
        return Tracker::whereHas('webhook', function ($q) use ($uid) {
            $q->where(['manager_id' => $uid]);
        })->get();
    }

    /**
     * Returns the tracker if the current user has the READ permission
     * @param Tracker $tracker The tracker
     * @return Tracker Returns the tracker
     */
    public function show(Tracker $tracker)
    {
        if (!Gate::allows('read', $tracker)) abort(403);
        return $tracker;
    }

    /**
     * Deletes the tracker if the current user has the DELETE permission
     * @param Tracker $tracker The tracker
     */
    public function delete(Tracker $tracker)
    {
        if (!Gate::allows('delete', $tracker)) abort(403);
        $tracker->delete();
    }

    /**
     * Creates a new trackers if the current user has the UPDATE permission
     * on the hook
     * @param Request $request The request
     * @param Webhook $hook The associated webhook
     * @return \Illuminate\Http\JsonResponse Returns the tracker
     */
    public function create(Request $request, Webhook $hook)
    {
        if (!Gate::allows('update', $hook)) abort(403);

        $validator = Validator::make($request->post(), [
            'runner' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/u'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'message' => 'Invalid runner format (runner)'], 422);
        }

        $runner_name = $request->post('runner');
        $existingTracker = Tracker::where(['src_name' => $runner_name])->first();
        $tracker = new Tracker();

        if ($existingTracker) {
            if (Tracker::where(['src_id' => $existingTracker->src_id, 'webhook_id' => $hook->id])->first() != null) {
                return response()->json(['status' => 422, 'message' => 'Tracker is already in use'], 422);
            }

            $tracker->src_name = $existingTracker->src_name;
            $tracker->src_id = $existingTracker->src_id;
            $tracker->webhook_id = $hook->id;
            $tracker->last_updated = new \DateTime();
            $tracker->save();
        } else {
            $runner_data = Fetch::load('http://speedrun.com/api/v1/users/' . $runner_name);

            if (!isset($runner_data)) {
                return response()->json(['status' => 500, 'message' => 'Failed to load runner data'], 500);
            }

            $runner_data = json_decode($runner_data, true)['data'];

            if (!$this->validateRunnerDetails($runner_data)) {
                return response()->json(['status' => 500, 'message' => 'Invalid runner data received'], 500);
            }

            if (Tracker::where(['src_id' => $runner_data['id'], 'webhook_id' => $hook->id])->first() != null) {
                return response()->json(['status' => 422, 'message' => 'Tracker is already in use'], 422);
            }

            $tracker->src_name = $runner_data['names']['international'];
            $tracker->src_id = $runner_data['id'];
            $tracker->webhook_id = $hook->id;
            $tracker->last_updated = new \DateTime();
            $tracker->save();
        }

        return Tracker::find($tracker->id);
    }

    /**
     * Validates whether the runner data returned by speedrun.com
     * can be processed
     * @param $runner mixed The runner
     * @return bool Returns true if the validation succeeds
     */
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
