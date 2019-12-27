<?php

namespace App\Http\Controllers;

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
        $hooks = Webhook::where(['manager_id' => $request->user()->id])->get();
        return view('dashboard', ['webhooks' => $hooks]);
    }

    public function show(Request $request, Webhook $hook)
    {
        if (!Gate::allows('read', $hook)) abort(403);
        return view('webhook', ['webhook' => $hook]);
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
