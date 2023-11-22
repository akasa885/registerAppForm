<?php

namespace Tests\Unit\Models;

use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_attendance()
    {
        $link = \App\Models\Link::factory()->create();
        $user = \App\Models\User::factory()->create();

        $attendance = Attendance::create([
            'attendance_path' => 'Mskeimas',
            'link_id' => $link->id,
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'confirmation_mail' => '<p>This confirmation mail you will get</p>',
            'with_verification_certificate' => true,
            'allow_non_register' => true,
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($attendance);
    }

    public function test_create_attendance_exception_query()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $attendance = Attendance::create([
            'attendance_path' => 'Mskeimas',
            'link_id' => 1,
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'confirmation_mail' => '<p>This confirmation mail you will get</p>',
            'with_verification_certificate' => true,
            'allow_non_register' => true,
            'created_by' => 1,
        ]);
    }

    public function test_fillable_properties()
    {
        $attendance = new Attendance([
            'attendance_path' => 'Mskeimas',
            'link_id' => 1,
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'confirmation_mail' => '<p>This confirmation mail you will get</p>',
            'with_verification_certificate' => true,
            'allow_non_register' => true,
            'created_by' => 1,
        ]);

        $this->assertEquals('Mskeimas', $attendance->attendance_path);
        $this->assertEquals(1, $attendance->link_id);
        $this->assertEquals('<p>This confirmation mail you will get</p>', $attendance->confirmation_mail);
        $this->assertEquals(1, $attendance->created_by);
    }

    public function test_casts_properties()
    {
        $attendance = new Attendance([
            'active_from' => Carbon::now(),
            'active_until' => Carbon::now()->addDays(1),
            'with_verification_certificate' => true,
        ]);

        $this->assertInstanceOf(Carbon::class, $attendance->active_from);
        $this->assertInstanceOf(Carbon::class, $attendance->active_until);
        $this->assertTrue($attendance->with_verification_certificate);
    }

    public function test_is_cert_need_verification()
    {
        $attendance = new Attendance([
            'with_verification_certificate' => true,
        ]);

        $this->assertTrue($attendance->isCertNeedVerification());
    }

    public function test_scope_own_attendance()
    {
        // This test depends on your implementation of the scopeOwnAttendance method
        // Please replace the following line with your own test
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
