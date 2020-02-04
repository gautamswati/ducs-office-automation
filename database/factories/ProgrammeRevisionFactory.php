<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\ProgrammeRevision;
use Faker\Generator as Faker;
use App\Programme;

$factory->define(ProgrammeRevision::class, function (Faker $faker) {
    return [
        'programme_id' => function () {
            return factory(Programme::class)->create()->id;
        },
        'revised_at' => $faker->dateTime(),
    ];
});
