<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Leave;
use App\LeaveStatus;
use App\Scholar;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$factory->define(Leave::class, function (Faker $faker) {
    return [
        'from' => $faker->date('Y-m-d'),
        'to' => $faker->date('Y-m-d'),
        'reason' => $faker->sentence,
        'status' => LeaveStatus::APPLIED,
        'document_path' => function () {
            Storage::fake();
            return UploadedFile::fake()
                ->create('file.pdf', 100, 'application/pdf')
                ->store('scholar_leaves');
        },
        'scholar_id' => function () {
            return factory(Scholar::class)->create()->id;
        },
    ];
});
