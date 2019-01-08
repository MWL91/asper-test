<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exceptions\ImportException;
use Exception;
use App\Imports\ObjectsImport;
use Maatwebsite\Excel\Facades\Excel;

class XlsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xls:import {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import objects data form xls';

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
        try
        {
            $filename = $this->argument('filename');
            Excel::import(new ObjectsImport, $filename);

            $this->info('Data import is now completed');
        }
        catch(Exception $ex)
        {
            $this->error($ex->getMessage());
        }
    }
}
