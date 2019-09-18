<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OutgoingLetter;
use Illuminate\Support\Collection;
use App\User;
use Illuminate\Support\Carbon;

class FilterOutgoingLettersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function user_can_view_filtered_letters_based_on_before_given_date()
    {
        factory(OutgoingLetter::class)->create(['date' => '2017-08-09']);
        factory(OutgoingLetter::class)->create(['date' => '2017-08-15']);
        factory(OutgoingLetter::class)->create(['date' => '2017-10-12']);

        $beforeFilter = '2017-09-01';

        $this->be(factory(User::class)->create());

        $viewOutgoingLetters = $this->withoutExceptionHandling()
            ->get('/outgoing-letters?filters[before]=' . $beforeFilter)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this->assertInstanceOf(Collection::class, $viewOutgoingLetters);
        $this->assertCount(2, $viewOutgoingLetters, 'Only 2 letters were expected but :actual letters were returned');

        $lettersAfterBeforeFilter = $viewOutgoingLetters->filter(function($letter) use ($beforeFilter){
            return Carbon::parse($beforeFilter)->lessThan($letter->date);
        });
        
        $this->assertCount(0, $lettersAfterBeforeFilter, 'filtered logs do not respect `before` filter');
    }

    /** @test */
    public function user_can_view_filtered_letters_based_on_after_given_date()
    {
        factory(OutgoingLetter::class)->create(['date' => '2017-08-09']);
        factory(OutgoingLetter::class)->create(['date' => '2017-08-15']);
        factory(OutgoingLetter::class)->create(['date' => '2017-10-12']);

        $afterFilter = '2017-09-01';
        
        $this->be(factory(User::class)->create());
        
        $viewOutgoingLetters = $this->withoutExceptionHandling()
            ->get('/outgoing-letters?filters[after]=' . $afterFilter)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this->assertInstanceOf(Collection::class, $viewOutgoingLetters);
        $this->assertCount(1, $viewOutgoingLetters, 'Only 1 letter was expected but :actual letters were returned');

        $lettersBeforeAfterFilter = $viewOutgoingLetters->filter(function($letter) use ($afterFilter){
            return Carbon::parse($afterFilter)->greaterThan($letter->date);
        });
        
        $this->assertCount(0, $lettersBeforeAfterFilter, 'filtered letters do not respect `after` filter');
    }

    /** @test */
    public function user_can_view_filtered_letters_based_on_before_and_after_given_date()
    {
        factory(OutgoingLetter::class)->create(['date' => '2017-07-20']);
        factory(OutgoingLetter::class)->create(['date' => '2017-08-09']);
        factory(OutgoingLetter::class)->create(['date' => '2017-08-15']);
        factory(OutgoingLetter::class)->create(['date' => '2017-10-12']);

        $afterFilter = '2017-08-01';
        $beforeFilter = '2017-09-01';
        
        $this->be(factory(User::class)->create());
        
        $viewOutgoingLetters = $this->withoutExceptionHandling()
            ->get('/outgoing-letters?filters[after]=' . $afterFilter . '&filters[before]='. $beforeFilter)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this->assertInstanceOf(Collection::class, $viewOutgoingLetters);
        $this->assertCount(2, $viewOutgoingLetters, 'Only 2 letters were expected but :actual letters were returned');

        $lettersOutOfFilterRange = $viewOutgoingLetters->filter(function($letter) use ($afterFilter, $beforeFilter){
            return Carbon::parse($beforeFilter)->lessThan($letter->date)
                && Carbon::parse($afterFilter)->greaterThan($letter->date);
        });
        
        $this->assertCount(0, $lettersOutOfFilterRange, 'filtered letters do not respect `before` and `after` filter');
    }

    /** @test */
    public function user_can_view_filtered_letters_even_if_after_date_not_given()
    {
        factory(OutgoingLetter::class)->create(['date' => '1997-07-15']);
        $this -> be(factory(User::class)->create());
        $after_date = '';

        $viewLetters = $this -> withoutExceptionHandling()
            ->get('/outgoing-letters?filters[after]='.$after_date)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this -> assertCount(1,$viewLetters);
    }

    /** @test */
    public function user_can_view_filtered_letters_even_if_before_date_not_given()
    {
        factory(OutgoingLetter::class)->create(['date' => '1997-07-15']);
        $this -> be(factory(User::class)->create());
        $before_date = '';

        $viewLetters = $this -> withoutExceptionHandling()
            ->get('/outgoing-letters?before='.$before_date)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this -> assertCount(1,$viewLetters);
    }

    /** @test */
    public function user_can_view_filtered_letters_even_if_before_and_after_date_not_given()
    {
        factory(OutgoingLetter::class)->create(['date' => '1997-07-15']);
        $this -> be(factory(User::class)->create());
        $after_date = '';
        $before_date = '';

        $viewLetters = $this -> withoutExceptionHandling()
            ->get('/outgoing-letters?filters[after]='.$after_date.'&before='.$before_date)
            ->assertSuccessful()
            ->assertViewIs('outgoing_letters.index')
            ->assertViewHas('outgoing_letters')
            ->viewData('outgoing_letters');

        $this -> assertCount(1,$viewLetters);
    }
}
