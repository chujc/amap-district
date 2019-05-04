<?php

namespace ChuJC\AMapDistrict\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;

class DistrictTableCommand extends Command
{

    protected $name = 'district:table';

    protected $description = 'Create a migration for the district table';

    protected $files;

    protected $composer;

    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $fullPath = $this->createBaseMigration();

        $this->files->put($fullPath, $this->files->get(__DIR__.'/create_district_table.txt'));

        $this->info('successfully! ');

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a base migration file for the district.
     *
     * @return string
     */
    protected function createBaseMigration()
    {
        $name = 'create_district_table';

        $path = $this->laravel->databasePath().'/migrations';

        return $this->laravel['migration.creator']->create($name, $path);
    }
}
