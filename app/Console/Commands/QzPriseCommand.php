<?php

namespace Zhiyi\Plus\Console\Commands;

use Illuminate\Console\Command;
use Zhiyi\Plus\Models\QzOkdata;
use Log;
class QzPriseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:okdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $exercise = new QzOkdata();
        $do_exercise = $exercise->okdata();
        Log::info($do_exercise);
    }
}