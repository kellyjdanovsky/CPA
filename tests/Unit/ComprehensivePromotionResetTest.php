<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\StudentRecord;
use App\Models\Promotion;
use App\Helpers\Qs;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComprehensivePromotionResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resets_all_promotions_and_removes_students_from_next_session()
    {
        // Create current session and next session values
        $currentSession = Qs::getCurrentSession(); // e.g., "2022-2023"
        $nextSession = Qs::getNextSession(); // e.g., "2023-2024"
        
        // Create a student record in the current session (original record)
        $originalStudentRecord = StudentRecord::create([
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
        
        // Create a NEW student record in the next session (this simulates what happens during promotion)
        // This is the key issue - these records need to be deleted when resetting promotions
        $newStudentRecord = StudentRecord::create([
            'user_id' => 1,
            'my_class_id' => 2, // New class
            'section_id' => 1,
            'session' => $nextSession, // Next session
            'adm_no' => 'CPA/PRS802/24/5678',
            'grad' => 0,
        ]);
        
        // Verify we have both records before reset
        $this->assertDatabaseHas('student_records', [
            'user_id' => 1,
            'session' => $currentSession
        ]);
        
        $this->assertDatabaseHas('student_records', [
            'user_id' => 1,
            'session' => $nextSession
        ]);
        
        // Mock the StudentRepo dependencies
        $myClassRepo = $this->createMock(\App\Repositories\MyClassRepo::class);
        $studentRepo = $this->createMock(\App\Repositories\StudentRepo::class);
        
        // Set up expectations for the studentRepo mock
        $studentRepo->method('getPromotions')->willReturn(collect([$promotion]));
        $studentRepo->method('findPromotion')->willReturn($promotion);
        $studentRepo->method('deletePromotion')->willReturn(true);
        
        // Set up the getRecord method to return appropriate collections
        $studentRepo->method('getRecord')->willReturnCallback(function ($data) use ($originalStudentRecord, $newStudentRecord) {
            if (isset($data['session']) && $data['session'] === Qs::getNextSession()) {
                // Return collection with the new student record
                $collection = new \Illuminate\Database\Eloquent\Collection([$newStudentRecord]);
                $collection->shouldReceive('get')->andReturn($collection);
                $collection->shouldReceive('first')->andReturn($newStudentRecord);
                return $collection;
            } elseif (isset($data['session']) && $data['session'] === Qs::getCurrentSession()) {
                // Return collection with the original student record
                $collection = new \Illuminate\Database\Eloquent\Collection([$originalStudentRecord]);
                $collection->shouldReceive('get')->andReturn($collection);
                $collection->shouldReceive('first')->andReturn($originalStudentRecord);
                return $collection;
            }
            return new \Illuminate\Database\Eloquent\Collection();
        });
        
        // Create the controller with mocked dependencies
        $controller = new \App\Http\Controllers\SupportTeam\PromotionController(
            $myClassRepo,
            $studentRepo
        );
        
        // Call the reset_all method
        $response = $controller->reset_all();
        
        // Check that the promotion record has been deleted
        $this->assertDatabaseMissing('promotions', ['id' => $promotion->id]);
        
        // Check that the student record in the next session has been deleted
        $this->assertDatabaseMissing('student_records', [
            'user_id' => 1,
            'session' => $nextSession
        ]);
        
        // Check that the original student record still exists and is not marked as graduated
        $this->assertDatabaseHas('student_records', [
            'user_id' => 1,
            'session' => $currentSession,
            'grad' => 0
        ]);
    }
}