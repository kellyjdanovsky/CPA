<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Payment;
use App\Models\User;
use App\Models\MyClass;

class PaymentDuplicatePreventionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $myClass;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur de test
        $this->user = User::factory()->create([
            'user_type' => 'admin'
        ]);
        
        // Créer une classe de test
        $this->myClass = MyClass::factory()->create([
            'name' => 'Test Class'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_payment_creation()
    {
        $this->actingAs($this->user);

        $paymentData = [
            'title' => 'Frais de scolarité',
            'amount' => 50000,
            'my_class_id' => $this->myClass->id,
            'description' => 'Test payment'
        ];

        // Première création - doit réussir
        $response1 = $this->postJson(route('payments.store'), $paymentData);
        $response1->assertStatus(200);
        $response1->assertJson(['ok' => true]);

        // Deuxième création avec les mêmes données - doit échouer
        $response2 = $this->postJson(route('payments.store'), $paymentData);
        $response2->assertStatus(422);
        $response2->assertJson([
            'ok' => false,
            'msg' => 'Un paiement identique existe déjà pour cette année scolaire.'
        ]);

        // Vérifier qu'il n'y a qu'un seul paiement dans la base
        $this->assertEquals(1, Payment::count());
    }

    /** @test */
    public function it_allows_different_payments()
    {
        $this->actingAs($this->user);

        $paymentData1 = [
            'title' => 'Frais de scolarité',
            'amount' => 50000,
            'my_class_id' => $this->myClass->id,
            'description' => 'Test payment 1'
        ];

        $paymentData2 = [
            'title' => 'Frais de cantine',
            'amount' => 30000,
            'my_class_id' => $this->myClass->id,
            'description' => 'Test payment 2'
        ];

        // Les deux créations doivent réussir car les paiements sont différents
        $response1 = $this->postJson(route('payments.store'), $paymentData1);
        $response1->assertStatus(200);

        $response2 = $this->postJson(route('payments.store'), $paymentData2);
        $response2->assertStatus(200);

        // Vérifier qu'il y a deux paiements dans la base
        $this->assertEquals(2, Payment::count());
    }

    /** @test */
    public function it_generates_unique_reference_codes()
    {
        $this->actingAs($this->user);

        $paymentData1 = [
            'title' => 'Frais de scolarité',
            'amount' => 50000,
            'my_class_id' => $this->myClass->id,
        ];

        $paymentData2 = [
            'title' => 'Frais de cantine',
            'amount' => 30000,
            'my_class_id' => $this->myClass->id,
        ];

        // Créer deux paiements
        $this->postJson(route('payments.store'), $paymentData1);
        $this->postJson(route('payments.store'), $paymentData2);

        $payments = Payment::all();
        
        // Vérifier que les codes de référence sont différents
        $this->assertNotEquals($payments[0]->ref_no, $payments[1]->ref_no);
        
        // Vérifier que les codes de référence ne sont pas vides
        $this->assertNotEmpty($payments[0]->ref_no);
        $this->assertNotEmpty($payments[1]->ref_no);
    }
}
