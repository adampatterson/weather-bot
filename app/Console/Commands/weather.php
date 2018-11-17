<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class weather extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports IATA codes';

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
    public function handle(\App\WeatherBot\WeatherImportService $weatherImportService)
    {
        return $weatherImportService->import();
    }
}
