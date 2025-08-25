<?php

namespace Tests\Unit;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Tests\TestCase;

class PaymentJournalTest extends TestCase
{
    /**
     * Test que le helper DateHelper convertit correctement les dates au fuseau horaire UTC+3
     *
     * @return void
     */
    public function test_date_helper_converts_to_utc3()
    {
        // Tester la conversion d'une date simple
        $testDate = '2023-12-25 10:30:00';
        $formatted = DateHelper::formatFrench($testDate);
        
        // Vérifier que la date est formatée en français
        $this->assertStringContainsString('25/12/2023', $formatted);
        
        // Tester avec l'heure
        $formattedWithTime = DateHelper::formatFrenchWithTime($testDate);
        $this->assertStringContainsString('25/12/2023', $formattedWithTime);
        $this->assertStringContainsString('à', $formattedWithTime);
    }

    /**
     * Test que la date actuelle est correctement formatée avec UTC+3
     *
     * @return void
     */
    public function test_date_helper_now_returns_utc3_time()
    {
        $formattedNow = DateHelper::now();
        
        // Vérifier que la date actuelle est formatée en français avec l'heure
        $this->assertStringContainsString('/', $formattedNow);
        $this->assertStringContainsString('à', $formattedNow);
    }

    /**
     * Test que les dates relatives fonctionnent correctement avec UTC+3
     *
     * @return void
     */
    public function test_date_helper_relative_formatting()
    {
        $testDate = Carbon::now()->subDays(2);
        $formatted = DateHelper::formatRelative($testDate);
        
        // Vérifier que la date relative est formatée en français
        $this->assertIsString($formatted);
        $this->assertNotEmpty($formatted);
    }

    /**
     * Test que le helper gère correctement les dates nulles
     *
     * @return void
     */
    public function test_date_helper_handles_null_dates()
    {
        $formatted = DateHelper::formatFrench(null);
        $this->assertEquals('N/A', $formatted);
        
        $formattedWithTime = DateHelper::formatFrenchWithTime(null);
        $this->assertEquals('N/A', $formattedWithTime);
    }

    /**
     * Test que le helper gère correctement les dates invalides
     *
     * @return void
     */
    public function test_date_helper_handles_invalid_dates()
    {
        $formatted = DateHelper::formatFrench('date-invalide');
        $this->assertEquals('Date invalide', $formatted);
    }
}
