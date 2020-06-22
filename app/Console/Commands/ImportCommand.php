<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ImportData;
use App\Imports\POKImport;
use Excel;
class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {id}';

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
        $id = $this->argument('id');
        $import = ImportData::find($id);
        if($import==null){
            $this->info('File not found');
            return;
        }
        $satker_id = $import->satker_id;
        $user_id = $import->created_by;
        $year = $import->tahun;
        $date = $import->tanggal;
        $target = $import->target;

        $this->info('Importing from '.storage_path('/app/'.$import->file));
        if($target==='pok'){
            Excel::import(new POKImport($year, $date, $satker_id, $user_id), storage_path('/app/'.$import->file));

        }
        $this->info('Importing Done!!');
    }
}
