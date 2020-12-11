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
        $admin_user = new \App\User();
        $admin_user->fill([
            'name' => 'Сергій Ярославович Гаврищак',
            'email' => 's.ia.havryshchak@nuwm.edu.ua',
            'role' => \App\User::ROLE_ADMIN,
        ])->save();

        $admin_user = new \App\User();
        $admin_user->fill([
            'name' => 'Олександр Анатолійович Хоменчук',
            'email' => 'khomenchuk@nuwm.edu.ua',
            'role' => \App\User::ROLE_ADMIN,
        ])->save();

        $admin_user = new \App\User();
        $admin_user->fill([
            'name' => 'Адміністратор сайту НУВГП',
            'email' => 'admin@nuwm.edu.ua',
            'role' => \App\User::ROLE_ADMIN,
        ])->save();

        $admin_user = new \App\User();
        $admin_user->fill([
            'name' => 'Олег Анатолійович Лагоднюк',
            'email' => 'o.a.lahodniuk@nuwm.edu.ua',
            'role' => \App\User::ROLE_ADMIN,
        ])->save();
    }
}
