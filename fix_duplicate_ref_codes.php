<?php
/**
 * Script pour corriger les codes de référence dupliqués dans payment_records
 * Exécuter avec: php fix_duplicate_ref_codes.php
 */

require_once 'vendor/autoload.php';

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentRecord;
use Illuminate\Support\Facades\DB;

echo "=== CORRECTION DES CODES DE RÉFÉRENCE DUPLIQUÉS ===\n\n";

try {
    // Étape 1: Identifier les doublons
    echo "1. Identification des doublons...\n";
    
    $duplicates = DB::table('payment_records')
        ->select('ref_no', DB::raw('COUNT(*) as count'))
        ->whereNotNull('ref_no')
        ->groupBy('ref_no')
        ->having('count', '>', 1)
        ->get();
    
    echo "   Trouvé " . $duplicates->count() . " codes de référence dupliqués\n\n";
    
    if ($duplicates->isEmpty()) {
        echo "✅ Aucun doublon trouvé. Base de données propre.\n";
        exit(0);
    }
    
    // Étape 2: Corriger chaque doublon
    echo "2. Correction des doublons...\n";
    
    $correctedCount = 0;
    
    foreach ($duplicates as $duplicate) {
        $refNo = $duplicate->ref_no;
        echo "   Traitement du code: $refNo (utilisé {$duplicate->count} fois)\n";
        
        // Récupérer tous les enregistrements avec ce code
        $records = PaymentRecord::where('ref_no', $refNo)->get();
        
        // Garder le premier, modifier les autres
        foreach ($records as $index => $record) {
            if ($index === 0) {
                echo "     - Enregistrement #{$record->id}: Gardé tel quel\n";
                continue;
            }
            
            // Générer un nouveau code unique
            $newRefNo = $refNo . '-FIX-' . time() . '-' . $record->id;
            
            // Vérifier l'unicité
            while (PaymentRecord::where('ref_no', $newRefNo)->exists()) {
                $newRefNo = $refNo . '-FIX-' . time() . '-' . $record->id . '-' . rand(100, 999);
            }
            
            // Mettre à jour l'enregistrement
            $record->update(['ref_no' => $newRefNo]);
            
            echo "     - Enregistrement #{$record->id}: Nouveau code -> $newRefNo\n";
            $correctedCount++;
        }
        
        echo "\n";
    }
    
    echo "✅ Correction terminée. $correctedCount enregistrements mis à jour.\n\n";
    
    // Étape 3: Vérification finale
    echo "3. Vérification finale...\n";
    
    $remainingDuplicates = DB::table('payment_records')
        ->select('ref_no', DB::raw('COUNT(*) as count'))
        ->whereNotNull('ref_no')
        ->groupBy('ref_no')
        ->having('count', '>', 1)
        ->get();
    
    if ($remainingDuplicates->isEmpty()) {
        echo "✅ Tous les doublons ont été corrigés avec succès!\n";
    } else {
        echo "⚠️  Il reste " . $remainingDuplicates->count() . " doublons à corriger.\n";
        foreach ($remainingDuplicates as $remaining) {
            echo "   - Code: {$remaining->ref_no} (utilisé {$remaining->count} fois)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la correction: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== RECOMMANDATIONS ===\n";
echo "1. Tester l'impression ADRA/TEAM3 pour vérifier que tout fonctionne\n";
echo "2. Surveiller les logs pour d'éventuelles nouvelles erreurs\n";
echo "3. Considérer l'ajout d'un index unique sur (student_id, payment_id, year)\n";

echo "\n=== REQUÊTE SQL POUR VÉRIFIER L'UNICITÉ ===\n";
echo "SELECT ref_no, COUNT(*) as count FROM payment_records \n";
echo "WHERE ref_no IS NOT NULL \n";
echo "GROUP BY ref_no \n";
echo "HAVING count > 1;\n";

echo "\n✅ Script terminé avec succès!\n";

?>
