<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use App\Models\Webhook;
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
        // TODO
    }
}
