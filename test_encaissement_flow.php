<?php
/**
 * Test complet du flux d'encaissement
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Encaissement;
use App\Models\Payment;
use App\Models\MyClass;
use App\User;
use App\Helpers\Qs;
use App\Repositories\EncaissementRepo;
use App\Repositories\PaymentRepo;
use App\Http\Controllers\SupportTeam\EncaissementController;

echo "🧪 Test complet du flux d'encaissement\n";
echo "=====================================\n\n";

try {
    $year = Qs::getCurrentSession();
    echo "📅 Année académique: {$year}\n\n";
    
    // 1. Test des repositories
    echo "1. Test des repositories...\n";
    
    $paymentRepo = new PaymentRepo();
    $encaissementRepo = new EncaissementRepo();
    
    // Test PaymentRepo
    $allPayments = $paymentRepo->getPayment(['year' => $year])->get();
    echo "   💰 Paiements trouvés pour {$year}: " . $allPayments->count() . "\n";
    
    if ($allPayments->count() > 0) {
        $firstPayment = $allPayments->first();
        echo "   📝 Premier paiement: {$firstPayment->title} - {$firstPayment->amount} Ar\n";
        
        // Test getPaymentWithYear
        $testPayments = $paymentRepo->getPaymentWithYear([], $year)->get();
        echo "   📊 Test getPaymentWithYear: " . $testPayments->count() . " paiements\n";
    }
    
    // Test EncaissementRepo
    $allEncaissements = $encaissementRepo->getByYear($year);
    echo "   💳 Encaissements trouvés pour {$year}: " . $allEncaissements->count() . "\n";
    
    echo "\n";
    
    // 2. Test du contrôleur
    echo "2. Test du contrôleur EncaissementController...\n";
    
    try {
        $controller = new EncaissementController(
            $encaissementRepo,
            new \App\Repositories\MyClassRepo(),
            $paymentRepo,
            new \App\Repositories\StudentRepo()
        );
        echo "   ✅ Contrôleur instancié avec succès\n";
        
        // Simuler une requête pour getClassPayments
        if (MyClass::count() > 0) {
            $testClass = MyClass::first();
            echo "   🏫 Test avec la classe: {$testClass->name}\n";
            
            // Créer une fausse requête
            $request = new \Illuminate\Http\Request();
            $request->merge(['class_id' => $testClass->id]);
            
            try {
                $response = $controller->getClassPayments($request);
                $responseData = $response->getData(true);
                
                echo "   📊 Réponse getClassPayments:\n";
                echo "      - Succès: " . ($responseData['success'] ? 'Oui' : 'Non') . "\n";
                echo "      - Nombre de paiements: " . count($responseData['payments'] ?? []) . "\n";
                
                if (isset($responseData['payments']) && !empty($responseData['payments'])) {
                    echo "      - Premier paiement: " . $responseData['payments'][0]['title'] . "\n";
                }
                
            } catch (Exception $e) {
                echo "   ❌ Erreur getClassPayments: " . $e->getMessage() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Erreur instanciation contrôleur: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // 3. Test de la vue
    echo "3. Test des données pour la vue...\n";
    
    $viewData = [
        'my_classes' => (new \App\Repositories\MyClassRepo())->all(),
        'payments' => $paymentRepo->getPayment(['year' => $year])->get(),
        'encaissements' => $encaissementRepo->getByYear($year),
        'statistics' => $encaissementRepo->getStatistics($year)
    ];
    
    echo "   🏫 Classes disponibles: " . $viewData['my_classes']->count() . "\n";
    echo "   💰 Paiements disponibles: " . $viewData['payments']->count() . "\n";
    echo "   💳 Encaissements disponibles: " . $viewData['encaissements']->count() . "\n";
    echo "   📊 Statistiques: " . json_encode($viewData['statistics']) . "\n";
    
    // Vérifier les relations des encaissements
    if ($viewData['encaissements']->count() > 0) {
        echo "\n   🔗 Test des relations du premier encaissement:\n";
        $firstEnc = $viewData['encaissements']->first();
        
        echo "      - ID: {$firstEnc->id}\n";
        echo "      - Référence: {$firstEnc->reference_encaissement}\n";
        echo "      - Étudiant: " . ($firstEnc->student ? $firstEnc->student->name : 'RELATION MANQUANTE') . "\n";
        echo "      - Paiement: " . ($firstEnc->payment ? $firstEnc->payment->title : 'RELATION MANQUANTE') . "\n";
        echo "      - Classe: " . ($firstEnc->myClass ? $firstEnc->myClass->name : 'RELATION MANQUANTE') . "\n";
        echo "      - Créateur: " . ($firstEnc->creator ? $firstEnc->creator->name : 'RELATION MANQUANTE') . "\n";
    }
    
    echo "\n";
    
    // 4. Test de création d'un encaissement
    echo "4. Test de création d'un encaissement...\n";
    
    if ($allPayments->count() > 0 && MyClass::count() > 0 && User::where('user_type', 'student')->count() > 0) {
        $payment = $allPayments->first();
        $class = MyClass::first();
        $student = User::where('user_type', 'student')->first();
        
        // Vérifier si un encaissement existe déjà
        $existing = Encaissement::where('student_id', $student->id)
                                ->where('payment_id', $payment->id)
                                ->where('year', $year)
                                ->first();
        
        if (!$existing) {
            echo "   🔧 Création d'un encaissement de test...\n";
            
            $encaissementData = [
                'student_id' => $student->id,
                'payment_id' => $payment->id,
                'payment_record_id' => 1, // Valeur par défaut
                'class_id' => $class->id,
                'type_encaissement' => 'ADRA',
                'montant_original' => $payment->amount,
                'pourcentage_pris_en_charge' => 75,
                'montant_encaisse' => $payment->amount * 0.75,
                'date_encaissement' => now()->format('Y-m-d'),
                'reference_encaissement' => 'TEST-' . time(),
                'created_by' => 1,
                'year' => $year,
                'observations' => 'Test automatique'
            ];
            
            try {
                $newEncaissement = $encaissementRepo->create($encaissementData);
                echo "   ✅ Encaissement créé (ID: {$newEncaissement->id})\n";
                
                // Vérifier immédiatement la récupération
                $retrieved = $encaissementRepo->getByYear($year);
                echo "   📊 Encaissements après création: " . $retrieved->count() . "\n";
                
            } catch (Exception $e) {
                echo "   ❌ Erreur lors de la création: " . $e->getMessage() . "\n";
                echo "   🔍 Stack trace: " . $e->getTraceAsString() . "\n";
            }
        } else {
            echo "   ℹ️  Encaissement de test existe déjà (ID: {$existing->id})\n";
        }
    } else {
        echo "   ⚠️  Données insuffisantes pour créer un encaissement de test\n";
    }
    
    echo "\n✅ Test complet terminé!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur globale: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}