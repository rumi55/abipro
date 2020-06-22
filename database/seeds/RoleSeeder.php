<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name'=>'super-admin', 'display_name'=>'Super Admin', 'created_at'=>date('Y-m-d H:i:s')],
            ['name'=>'admin', 'display_name'=>'Admin', 'created_at'=>date('Y-m-d H:i:s')],
            ['name'=>'user', 'display_name'=>'User', 'created_at'=>date('Y-m-d H:i:s')]
        ];
        $table = DB::table('roles');
        $table->truncate();
        $table->insert($data);
    }
}
