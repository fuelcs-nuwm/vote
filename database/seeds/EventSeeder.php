<?php

use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $event1 = new \App\Event();
        $event1->fill([
            'title' => 'Тестова подія',
        ])->save();

        $event2 = new \App\Event();
        $event2->fill([
            'title' => 'Збори трудового колективу',
        ])->save();

        $customer1 = new \App\Customer();
        $customer1->fill([
            'email' => 'hesk_user@nuwm.edu.ua',
            'event_id' => $event1->id,
        ])->save();

        $customer2 = new \App\Customer();
        $customer2->fill([
            'email' => 'hesk_staff@nuwm.edu.ua',
            'event_id' => $event1->id,
        ])->save();
    }
}
