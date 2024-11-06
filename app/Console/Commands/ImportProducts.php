<?php

namespace App\Console\Commands;

use App\Jobs\ImportProducts as JobsImportProducts;
use Illuminate\Console\Command;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--id= : The ID of the product to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Fake Store API';

    protected $request;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product import job.');

        JobsImportProducts::dispatch($this->option('id'));

        $this->info('Product import job dispatched successfully.');
    }
}
