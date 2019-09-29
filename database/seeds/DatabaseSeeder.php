<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create();
        factory(App\Template::class, 0)->create();
        factory(App\Checklist::class, 0)->create();
        factory(App\Item::class, 0)->create();
        factory(App\History::class, 10)->create();
    }
}
