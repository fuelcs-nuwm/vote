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


        //Події
        $event1 = new \App\Event();
        $event1->fill([
            'title' => 'Тестова подія',
        ])->save();

        $event2 = new \App\Event();
        $event2->fill([
            'title' => 'Збори трудового колективу',
        ])->save();

        //Групи
        \App\Group::insert([
            ['name' => 'Викладачі', 'event_id' => $event1->id],
            ['name' => 'Студенти', 'event_id' => $event1->id],
            ['name' => 'Персонал', 'event_id' => $event1->id],
        ]);

        //Учасники
        $customer1 = new \App\Customer();
        $customer1->fill([
            'email' => 'hesk_user@nuwm.edu.ua',
            'event_id' => $event1->id,
        ])->save();
        $customer1->groups()->sync([1]);

        $customer2 = new \App\Customer();
        $customer2->fill([
            'email' => 'hesk_staff@nuwm.edu.ua',
            'event_id' => $event1->id,
        ])->save();
        $customer2->groups()->sync([2]);

        $customer3 = new \App\Customer();
        $customer3->fill([
            'email' => 'hesk_admin@nuwm.edu.ua',
            'event_id' => $event1->id,
        ])->save();
        $customer3->groups()->sync([2,3]);
    }
}
