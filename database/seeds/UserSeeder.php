<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name'=>'Super Admin', 'email'=>'super@admin.com', 'password'=>bcrypt('12345678'), 'role_id'=>1, 'created_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Admin', 'email'=>'admin@admin.com', 'password'=>bcrypt('12345678'), 'role_id'=>2, 'created_at'=>date('Y-m-d H:i:s')],
            ['name'=>'User', 'email'=>'user@user.com', 'password'=>bcrypt('12345678'), 'role_id'=>3, 'created_at'=>date('Y-m-d H:i:s')]
        ];
        $table = DB::table('users');
        $table->truncate();
        $table->insert($data);
    }
}
