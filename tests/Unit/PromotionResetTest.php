<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\StudentRecord;
use App\Models\Promotion;
use App\Helpers\Qs;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromotionResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resets_promotions_and_moves_students_back_to_current_session()
    {
        // Create a student record in the current session
        $currentSession = Qs::getCurrentSession(); // e.g., "2022-2023"
        $nextSession = Qs::getNextSession(); // e.g., "2023-2024"
        
        $studentRecord = StudentRecord::create([
            'user_id' => 1,
            'my_class_id' => 1,
            'section_id' => 1,
            'session' => $currentSession,
            'adm_no' => 'CPA/PRS802/23/1234',
            'grad' => 0,
        ]);
        
        // Create a promotion record
        $promotion = Promotion::create([
            'from_class' => 1,
            'from_section' => 1,
            'to_class' => 2,
            'to_section' => 1,
            'student_id' => 1,
            'from_session' => $currentSession,
            'to_session' => $nextSession,
            'status' => 'P', // Promoted
        ]);
        
        // Create a student record in the next session (simulating promotion)
        $promotedStudentRecord = StudentRecord::create([
            'user_id' => 1,
            'my_class_id' => 2, // New class
            'section_id' => 1,
            'session' => $nextSession, // Next session
            'adm_no' => 'CPA/PRS802/24/5678',
            'grad' => 0,
        ]);
        
        // Call the reset_single method (simulating what happens when reset_all is called)
        $controller = new \App\Http\Controllers\SupportTeam\PromotionController(
            app(\App\Repositories\MyClassRepo::class),
            app(\App\Repositories\StudentRepo::class)
        );
        
        // Use reflection to call the protected method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('reset_single');
        $method->setAccessible(true);
        $method->invoke($controller, $promotion->id);
        
        // Check that the promotion record has been deleted
        $this->assertDatabaseMissing('promotions', ['id' => $promotion->id]);
        
        // Check that there is now only one student record in the current session
        $studentRecords = StudentRecord::where(['user_id' => 1, 'session' => $currentSession])->get();
        $this->assertCount(1, $studentRecords);
        
        // Check that the student record has the original class
        $this->assertEquals(1, $studentRecords->first()->my_class_id);
        
        // Check that there are no student records in the next session
        $nextSessionRecords = StudentRecord::where(['user_id' => 1, 'session' => $nextSession])->get();
        $this->assertCount(0, $nextSessionRecords);
    }
}