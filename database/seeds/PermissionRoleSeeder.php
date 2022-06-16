<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class PermissionRoleSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'permission_role';
        $this->filename = base_path().'/database/seeds/csvs/permission_role.csv';
    }

    /**
     *
     */
    public function run()
    {
        parent::run();
    }
}