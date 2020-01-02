<?php

namespace App\Http\Controllers;

use App\Models\Tracker;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        if ($request->user()->is_global_admin) return view('dashboard', ['webhooks' => Webhook::orderBy('state')->get()]);
        $hooks = Webhook::where(['manager_id' => $request->user()->id])->orderBy('state')->get();
        return view('dashboard', ['webhooks' => $hooks]);
    }

    public function show(Request $request, Webhook $hook)
    {
        if (!Gate::allows('read', $hook)) abort(403);
        $hook->load('trackers');
        return view('webhook', ['webhook' => $hook]);
    }

    public function deleteRunner(Request $request, Webhook $hook, Tracker $tracker)
    {
        return app(TrackerController::class)->delete($tracker);
    }

    public function addRunner(Request $request, Webhook $hook)
    {
        return app(TrackerController::class)->create($request, $hook);
    }

    public function update(Request $request, Webhook $hook)
    {
        return app(WebhookController::class)->update($request, $hook);
    }

    public function create(Request $request)
    {
        return app(WebhookController::class)->create($request);
    }

    public function new()
    {
        return view('new');
    }
}
