<?php

use App\Course;
use App\OutgoingLetter;
use App\Paper;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $himani = factory(User::class)->create([
            'name' => 'Himani Saini',
            'email' => 'himani@ducs.in',
            'password' => bcrypt('secret'),
        ]);

        factory(OutgoingLetter::class, 10)->create();

        factory(Course::class, 5)->create()->each(function($course) {
            factory(Paper::class, 20)->create(['course_id' => $course->id]);
        });
    }
}
