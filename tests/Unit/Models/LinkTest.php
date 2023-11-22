<?php

namespace Tests\Unit\Models;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use PHPUnit\Framework\TestCase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_create_link()
    {
        $user = \App\Models\User::factory()->create();
        $link = Link::create([
            'link_path' => 'Mskeimas',
            'title' => 'Mskeimas',
            'description' => 'Mskeimas',
            'registration_info' => 'Mskeimas',
            'banner' => 'Mskeimas',
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'event_date' => Carbon::now()->addDays(2),
            'created_by' => $user->id,
            'link_type' => 'pay',
            'price' => 100,
            'has_member_limit' => true,
            'member_limit' => 100,
            'is_multiple_registrant_allowed' => true,
            'sub_member_limit' => 100,
        ]);

        $this->assertNotNull($link);
    }

    public function test_create_link_exception_query()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $link = Link::create([
            'link_path' => 'Mskeimas',
            'title' => 'Mskeimas',
            'description' => 'Mskeimas',
            'registration_info' => 'Mskeimas',
            'banner' => 'Mskeimas',
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'event_date' => Carbon::now()->addDays(2),
            'created_by' => 1,
            'link_type' => 'pay',
            'price' => 100,
            'has_member_limit' => true,
            'member_limit' => 100,
            'is_multiple_registrant_allowed' => true,
            'sub_member_limit' => 100,
        ]);
    }

    public function test_fillable_properties()
    {
        $link = new Link([
            'link_path' => 'Mskeimas',
            'title' => 'Mskeimas',
            'description' => 'Mskeimas',
            'registration_info' => 'Mskeimas',
            'banner' => 'Mskeimas',
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'event_date' => Carbon::now()->addDays(2),
            'created_by' => 1,
            'link_type' => 'pay',
            'price' => 100,
            'has_member_limit' => true,
            'member_limit' => 100,
            'is_multiple_registrant_allowed' => true,
            'sub_member_limit' => 100,
        ]);

        $this->assertEquals('Mskeimas', $link->link_path);
        $this->assertEquals('Mskeimas', $link->title);
        $this->assertEquals('Mskeimas', $link->description);
        $this->assertEquals('Mskeimas', $link->registration_info);
        $this->assertEquals('Mskeimas', $link->banner);
        $this->assertEquals(1, $link->created_by);
        $this->assertEquals('pay', $link->link_type);
        $this->assertEquals(100, $link->price);
        $this->assertEquals(100, $link->member_limit);
        $this->assertEquals(100, $link->sub_member_limit);

    }

    public function test_casts_properties()
    {
        $link = new Link([
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'event_date' => Carbon::now()->addDays(2),
            'has_member_limit' => true,
            'is_multiple_registrant_allowed' => true,
        ]);

        $this->assertInstanceOf(Carbon::class, $link->active_from);
        $this->assertInstanceOf(Carbon::class, $link->active_until);
        $this->assertInstanceOf(Carbon::class, $link->event_date);
        $this->assertTrue($link->has_member_limit);
        $this->assertTrue($link->is_multiple_registrant_allowed);
    }
}
