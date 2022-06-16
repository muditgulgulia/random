<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class UserSeeder extends CsvSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function __construct()
    {
        $this->table = 'users';
        $this->filename = base_path().'/database/seeds/csvs/users.csv';
    }
    public function run()
    {
        parent::run();
    }
}