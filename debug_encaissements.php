<?php
/**
 * Script de débogage pour vérifier les encaissements
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Encaissement;
use App\Repositories\EncaissementRepo;
use App\Helpers\Qs;

echo "🔍 Débogage des encaissements\n";
echo "============================\n\n";

// 1. Vérifier si la table encaissements existe
echo "1. Vérification de la table encaissements...\n";
if (Schema::hasTable('encaissements')) {
    echo "   ✅ Table 'encaissements' existe\n";
    
    // Vérifier les colonnes
    $columns = Schema::getColumnListing('encaissements');
    echo "   📋 Colonnes trouvées: " . implode(', ', $columns) . "\n";
    
    // Compter les enregistrements
    $count = DB::table('encaissements')->count();
    echo "   📊 Nombre d'enregistrements: {$count}\n";
    
} else {
    echo "   ❌ Table 'encaissements' n'existe pas\n";
    echo "   🔧 Tentative de création de la table...\n";
    
    try {
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_08_25_100000_create_encaissements_table.php']);
        echo "   ✅ Migration exécutée avec succès\n";
    } catch (Exception $e) {
        echo "   ❌ Erreur lors de la migration: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 2. Vérifier les données d'encaissement
echo "2. Vérification des données d'encaissement...\n";
try {
    $year = Qs::getCurrentSession();
    echo "   📅 Année académique actuelle: {$year}\n";
    
    $encaissementRepo = new EncaissementRepo();
    $encaissements = $encaissementRepo->getByYear($year);
    
    echo "   📊 Encaissements trouvés pour {$year}: " . $encaissements->count() . "\n";
    
    if ($encaissements->count() > 0) {
        echo "   📝 Détails des 5 premiers encaissements:\n";
        foreach ($encaissements->take(5) as $enc) {
            echo "      - ID: {$enc->id}, Ref: {$enc->reference_encaissement}, Type: {$enc->type_encaissement}, Montant: {$enc->montant_encaisse} Ar\n";
        }
    }
    
    // Vérifier les statistiques
    $stats = $encaissementRepo->getStatistics($year);
    echo "   📈 Statistiques:\n";
    echo "      - Total encaissements: {$stats['total_encaissements']}\n";
    echo "      - Montant total: " . number_format($stats['total_montant'], 0, ',', ' ') . " Ar\n";
    echo "      - ADRA: {$stats['adra_count']} encaissements\n";
    echo "      - TEAM3: {$stats['team3_count']} encaissements\n";
    
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la récupération des données: " . $e->getMessage() . "\n";
    echo "   🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";

// 3. Vérifier la structure du contrôleur
echo "3. Vérification du contrôleur EncaissementController...\n";
try {
    $controller = new \App\Http\Controllers\SupportTeam\EncaissementController(
        new EncaissementRepo(),
        new \App\Repositories\MyClassRepo(),
        new \App\Repositories\PaymentRepo(),
        new \App\Repositories\StudentRepo()
    );
    echo "   ✅ Contrôleur instantié avec succès\n";
    
} catch (Exception $e) {
    echo "   ❌ Erreur lors de l'instantiation du contrôleur: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Tester les requêtes SQL directement
echo "4. Test des requêtes SQL directes...\n";
try {
    $year = Qs::getCurrentSession();
    
    // Requête directe avec les relations
    $directQuery = DB::table('encaissements')
        ->leftJoin('users as students', 'encaissements.student_id', '=', 'students.id')
        ->leftJoin('payments', 'encaissements.payment_id', '=', 'payments.id')
        ->leftJoin('my_classes', 'encaissements.class_id', '=', 'my_classes.id')
        ->leftJoin('users as creators', 'encaissements.created_by', '=', 'creators.id')
        ->where('encaissements.year', $year)
        ->whereNull('encaissements.deleted_at')
        ->select([
            'encaissements.*',
            'students.name as student_name',
            'payments.title as payment_title',
            'my_classes.name as class_name',
            'creators.name as creator_name'
        ])
        ->orderBy('encaissements.date_encaissement', 'desc')
        ->get();
    
    echo "   📊 Résultats de la requête directe: " . $directQuery->count() . " enregistrements\n";
    
    if ($directQuery->count() > 0) {
        echo "   📝 Premier enregistrement:\n";
        $first = $directQuery->first();
        echo "      - ID: {$first->id}\n";
        echo "      - Étudiant: {$first->student_name}\n";
        echo "      - Paiement: {$first->payment_title}\n";
        echo "      - Classe: {$first->class_name}\n";
        echo "      - Référence: {$first->reference_encaissement}\n";
        echo "      - Montant: {$first->montant_encaisse} Ar\n";
        echo "      - Type: {$first->type_encaissement}\n";
        echo "      - Date: {$first->date_encaissement}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la requête SQL directe: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Vérifier les relations du modèle
echo "5. Test des relations du modèle Encaissement...\n";
try {
    if (DB::table('encaissements')->count() > 0) {
        $encaissement = Encaissement::with(['student', 'payment', 'myClass', 'creator'])->first();
        
        if ($encaissement) {
            echo "   ✅ Modèle chargé avec succès\n";
            echo "   🔗 Relations:\n";
            echo "      - Étudiant: " . ($encaissement->student ? $encaissement->student->name : 'Non trouvé') . "\n";
            echo "      - Paiement: " . ($encaissement->payment ? $encaissement->payment->title : 'Non trouvé') . "\n";
            echo "      - Classe: " . ($encaissement->myClass ? $encaissement->myClass->name : 'Non trouvé') . "\n";
            echo "      - Créateur: " . ($encaissement->creator ? $encaissement->creator->name : 'Non trouvé') . "\n";
        }
    } else {
        echo "   ℹ️  Aucun encaissement à tester\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur lors du test des relations: " . $e->getMessage() . "\n";
}

echo "\n🏁 Débogage terminé.\n";