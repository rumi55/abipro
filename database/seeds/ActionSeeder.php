<?php

use Illuminate\Database\Seeder;

class actionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['group'=>'users', 'display_group'=>'Kelola Pengguna','name'=>'users-list', 'display_name'=>'Daftar Pengguna', 'created_at'=>date('Y-m-d H:i:s')],
            ['group'=>'users', 'display_group'=>'Kelola Pengguna','name'=>'users-detail', 'display_name'=>'Detail Pengguna', 'created_at'=>date('Y-m-d H:i:s')],
            ['group'=>'users', 'display_group'=>'Kelola Pengguna','name'=>'users-add', 'display_name'=>'Tambah Pengguna', 'created_at'=>date('Y-m-d H:i:s')],
            ['group'=>'users', 'display_group'=>'Kelola Pengguna','name'=>'users-edit', 'display_name'=>'Edit Pengguna', 'created_at'=>date('Y-m-d H:i:s')],
            ['group'=>'users', 'display_group'=>'Kelola Pengguna','name'=>'users-delete', 'display_name'=>'Hapus Pengguna', 'created_at'=>date('Y-m-d H:i:s')],
        ];
        $table = DB::table('actions');
        $table->truncate();
        $table->insert($data);
    }
}
