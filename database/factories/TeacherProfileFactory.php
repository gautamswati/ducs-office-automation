<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\College;
use App\Teacher;
use App\TeacherProfile;
use Faker\Generator as Faker;

$factory->define(TeacherProfile::class, function (Faker $faker) {
    return [
        'phone_no' => $faker->regexify('[6-9][0-9]{9}'),
        'address' => $faker->address,
        'designation' => $faker->randomElement(array_keys(config('options.teachers.designations'))),
        'ifsc' => $faker->bothify('????0######', 12),
        'account_no' => $faker->bankAccountNumber, // @todo make it 'account_number' or 'bank_account'
        'bank_name' => $faker->words(4, true),
        'bank_branch' => $faker->city,
        'college_id' => function () {
            return factory(College::class)->create()->id;
        },
        'teacher_id' => function () {
            return factory(Teacher::class)->create()->id;
        },
    ];
});
