<?php

use Illuminate\Database\Seeder;

class ControllersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('controllers')->insert([
            [
                'CARNO' => 'Z12-123456',
                'controller'    => 'エアコン',
                'created_at' => new DateTime(),
            ],
            [
                'CARNO' => 'Z12-121111',
                'controller'    => 'エアコン',
                'created_at' => new DateTime(),
            ],
            [
                'CARNO' => 'GGD2-111456',
                'controller'    => 'エアコン',
                'created_at' => new DateTime(),
            ],
            [
                'CARNO' => 'G-332-22',
                'controller'    => 'エアコン',
                'created_at' => new DateTime(),
            ],
            [
                'CARNO' => 'EEQs-212k22',
                'controller'    => 'エアコン',
                'created_at' => new DateTime(),
            ],


        ]);
    }
}
