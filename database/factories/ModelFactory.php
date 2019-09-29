<?php

use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => "Wahyu Nugroho Indrawinata",
        'email' => "korodarmo@gmail.com",
        'password'   => Hash::make("passw0rd")
    ];
});

$factory->define(App\Template::class, function (Faker\Generator $faker) {
    return [
        'name'          => $faker->name
    ];
});


$factory->define(App\Checklist::class, function (Faker\Generator $faker) {
    return [
        'template_id'       => App\Template::all()->random()->id,
        'object_domain'     => $faker->jobTitle,
        'object_id'         => $faker->randomDigit,
        'description'       => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'is_completed'      => $faker->boolean,
        'completed_at'      => $faker->dateTime(),
        'created_by'        => App\User::all()->random()->id,
        'updated_by'        => App\User::all()->random()->id,
        'due'               => $faker->dateTime(),
        'due_interval'      => $faker->randomDigit,
        'due_unit'          => $faker->randomElement(['minute','hour','day','week','month']),
        'urgency'           => $faker->randomDigit
    ];
});

$factory->define(App\Item::class, function (Faker\Generator $faker) {
    return [
        'checklist_id'      => App\Checklist::all()->random()->id,
        'user_id'           => App\User::all()->random()->id,
        'description'       => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'is_completed'      => $faker->boolean,
        'completed_at'      => $faker->dateTime(),
        'created_by'        => App\User::all()->random()->id,
        'updated_by'        => App\User::all()->random()->id,
        'due'               => $faker->dateTime(),
        'due_interval'      => $faker->randomDigit,
        'due_unit'          => $faker->randomElement(['minute','hour','day','week','month']),
        'urgency'           => $faker->randomDigit,
        'assignee_id'       => App\User::all()->random()->id,
        'task_id'           => $faker->randomDigit
    ];
});

$factory->define(App\History::class, function (Faker\Generator $faker) {
    return [
        'loggable_type'     => $faker->randomElement(['items','checklist','templates']),
        'loggable_id'       => $faker->numberBetween(1,5000),
        'action'            => $faker->sentence($nbWords = 1, $variableNbWords = true),
        'kwuid'             => App\User::all()->random()->id,
        'value'             => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'created_by'        => App\User::all()->random()->id,
        'updated_by'        => App\User::all()->random()->id,
    ];
});
