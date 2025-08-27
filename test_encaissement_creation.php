<?php
/**
 * Test creation of sample encaissement
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

echo "🧪 Test de création d'encaissement\n";
echo "=================================\n\n";

try {
    // Get current year
    $year = Qs::getCurrentSession();
    echo "📅 Année académique: {$year}\n";
    
    // Find a class
    $class = MyClass::first();
    if (!$class) {
        echo "❌ Aucune classe trouvée\n";
        exit(1);
    }
    echo "🏫 Classe trouvée: {$class->name}\n";
    
    // Find a payment
    $payment = Payment::where('year', $year)->first();
    if (!$payment) {
        echo "❌ Aucun paiement trouvé pour l'année {$year}\n";
        exit(1);
    }
    echo "💰 Paiement trouvé: {$payment->title} - {$payment->amount} Ar\n";
    
    // Find a student
    $student = User::where('user_type', 'student')->first();
    if (!$student) {
        echo "❌ Aucun étudiant trouvé\n";
        exit(1);
    }
    echo "👨‍🎓 Étudiant trouvé: {$student->name}\n";
    
    // Check if encaissement already exists
    $existing = Encaissement::where('student_id', $student->id)
                           ->where('payment_id', $payment->id)
                           ->where('year', $year)
                           ->first();
    
    if ($existing) {
        echo "ℹ️  Encaissement existe déjà (ID: {$existing->id})\n";
    } else {
        // Create a test encaissement
        echo "\n🔧 Création d'un encaissement de test...\n";
        
        $encaissementData = [
            'student_id' => $student->id,
            'payment_id' => $payment->id,
            'payment_record_id' => 1, // This might need to be adjusted
            'class_id' => $class->id,
            'type_encaissement' => 'ADRA',
            'montant_original' => $payment->amount,
            'pourcentage_pris_en_charge' => 75,
            'montant_encaisse' => $payment->amount * 0.75,
            'date_encaissement' => now()->format('Y-m-d'),
            'reference_encaissement' => 'TEST-' . time(),
            'created_by' => 1, // Assuming admin user has ID 1
            'year' => $year,
            'observations' => 'Test encaissement créé automatiquement'
        ];
        
        $encaissement = Encaissement::create($encaissementData);
        echo "✅ Encaissement créé (ID: {$encaissement->id})\n";
    }
    
    // Test the repository
    echo "\n🔍 Test du repository...\n";
    $repo = new EncaissementRepo();
    
    // Get all encaissements for current year
    $encaissements = $repo->getByYear($year);
    echo "📊 Nombre d'encaissements pour {$year}: " . $encaissements->count() . "\n";
    
    if ($encaissements->count() > 0) {
        echo "📝 Premier encaissement:\n";
        $first = $encaissements->first();
        echo "   - ID: {$first->id}\n";
        echo "   - Référence: {$first->reference_encaissement}\n";
        echo "   - Étudiant: " . ($first->student ? $first->student->name : 'N/A') . "\n";
        echo "   - Paiement: " . ($first->payment ? $first->payment->title : 'N/A') . "\n";
        echo "   - Classe: " . ($first->myClass ? $first->myClass->name : 'N/A') . "\n";
        echo "   - Montant: {$first->montant_encaisse} Ar\n";
        echo "   - Type: {$first->type_encaissement}\n";
    }
    
    // Test statistics
    echo "\n📈 Test des statistiques...\n";
    $stats = $repo->getStatistics($year);
    echo "   - Total encaissements: {$stats['total_encaissements']}\n";
    echo "   - Montant total: " . number_format($stats['total_montant'], 0, ',', ' ') . " Ar\n";
    echo "   - ADRA: {$stats['adra_count']}\n";
    echo "   - TEAM3: {$stats['team3_count']}\n";
    
    echo "\n✅ Test terminé avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}