<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Decaissement;
use App\Helpers\DateHelper;
use Mockery;
use ReflectionClass;
use Illuminate\Foundation\Testing\WithFaker;

class DecaissementVerificationTest extends TestCase
{
    use WithFaker;

    public function test_is_invalid_decaissement()
    {
        $controller = new \App\Http\Controllers\SupportTeam\DecaissementController();

        // Get the protected method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('isInvalidDecaissement');
        $method->setAccessible(true);

        // Test invalid disbursement (missing required fields)
        $decaissement = Mockery::mock(Decaissement::class);
        $decaissement->shouldReceive('getAttribute')->with('montant')->andReturn(null);
        $decaissement->shouldReceive('getAttribute')->with('motif')->andReturn('Test motif');
        $decaissement->shouldReceive('getAttribute')->with('beneficiaire')->andReturn('Test beneficiary');

        $result = $method->invokeArgs($controller, [$decaissement]);
        $this->assertTrue($result);

        // Test invalid disbursement (inconsistent montant_lettres)
        $decaissement = Mockery::mock(Decaissement::class);
        $decaissement->shouldReceive('getAttribute')->with('montant')->andReturn(1000);
        $decaissement->shouldReceive('getAttribute')->with('motif')->andReturn('Test motif');
        $decaissement->shouldReceive('getAttribute')->with('beneficiaire')->andReturn('Test beneficiary');
        $decaissement->shouldReceive('getAttribute')->with('montant_lettres')->andReturn('cinq cent ariary');

        $result = $method->invokeArgs($controller, [$decaissement]);
        $this->assertTrue($result);

        // Test valid disbursement
        $decaissement = Mockery::mock(Decaissement::class);
        $decaissement->shouldReceive('getAttribute')->with('montant')->andReturn(1000);
        $decaissement->shouldReceive('getAttribute')->with('motif')->andReturn('Test motif');
        $decaissement->shouldReceive('getAttribute')->with('beneficiaire')->andReturn('Test beneficiary');
        $decaissement->shouldReceive('getAttribute')->with('montant_lettres')->andReturn(DateHelper::convertirMontantEnLettres(1000));

        $result = $method->invokeArgs($controller, [$decaissement]);
        $this->assertFalse($result);
    }

    public function test_correct_decaissement()
    {
        $controller = new \App\Http\Controllers\SupportTeam\DecaissementController();

        // Get the protected method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('correctDecaissement');
        $method->setAccessible(true);

        // Test correcting montant_lettres
        $decaissement = Mockery::mock(Decaissement::class);
        $decaissement->shouldReceive('getAttribute')->with('montant')->andReturn(1000);
        $decaissement->shouldReceive('getAttribute')->with('montant_lettres')->andReturn('cinq cent ariary');
        $decaissement->shouldReceive('update')->once()->with(['montant_lettres' => 'un mille ariary'])->andReturnTrue();

        $method->invokeArgs($controller, [$decaissement]);

        // Test no changes needed
        $decaissement = Mockery::mock(Decaissement::class);
        $decaissement->shouldReceive('getAttribute')->with('montant')->andReturn(1000);
        $decaissement->shouldReceive('getAttribute')->with('montant_lettres')->andReturn('un mille ariary');
        $decaissement->shouldReceive('update')->never();

        $method->invokeArgs($controller, [$decaissement]);
    }
}