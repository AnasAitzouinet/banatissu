<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Insert some stuff
        DB::table('servers')->updateOrInsert(
            ['id' => 1],
            array(
                'host' => 'mailtrap.io',
                'port' => '2525',
                'username' => 'test',
                'password' => 'test',
                'encryption' => 'tls',
            )
        );
    }
}
