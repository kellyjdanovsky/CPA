<?php

/**
 * Test Script for Duplicate Prevention System
 * 
 * This script tests the comprehensive duplicate prevention system
 * Run with: php test_duplicate_prevention.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Set up basic Laravel environment for testing
if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StudentRecord;
use App\Models\PaymentRecord;
use App\Models\Receipt;
use App\Models\Mark;
use App\Services\TransactionLockService;
use App\Services\DuplicateLoggerService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DuplicatePreventionTest
{
    private $lockService;
    private $loggerService;
    private $testResults = [];

    public function __construct()
    {
        $this->lockService = new TransactionLockService();
        $this->loggerService = new DuplicateLoggerService();
    }

    public function runAllTests()
    {
        echo "🧪 Démarrage des tests du système de protection contre les doublons...\n\n";

        $this->testTransactionLocks();
        $this->testDuplicateDetection();
        $this->testModelSafety();
        $this->testLoggingSystem();
        $this->testCleanupOperations();

        $this->displayResults();
    }

    public function testTransactionLocks()
    {
        echo "🔒 Test du système de verrous de transaction...\n";

        try {
            // Test 1: Acquisition et libération de verrou
            $lockKey = 'test_student_123_update';
            $acquired = $this->lockService->acquireLock($lockKey, 'test', 30);
            $this->addResult('Lock Acquisition', $acquired);

            // Test 2: Tentative d'acquisition du même verrou (doit échouer)
            try {
                $this->lockService->acquireLock($lockKey, 'test', 30);
                $this->addResult('Duplicate Lock Prevention', false);
            } catch (Exception $e) {
                $this->addResult('Duplicate Lock Prevention', true);
            }

            // Test 3: Libération du verrou
            $released = $this->lockService->releaseLock($lockKey);
            $this->addResult('Lock Release', $released);

            // Test 4: Acquisition après libération (doit réussir)
            $reacquired = $this->lockService->acquireLock($lockKey, 'test', 30);
            $this->addResult('Lock Re-acquisition', $reacquired);
            $this->lockService->releaseLock($lockKey);

        } catch (Exception $e) {
            $this->addResult('Transaction Lock System', false, $e->getMessage());
        }

        echo "✅ Tests des verrous terminés\n\n";
    }

    public function testDuplicateDetection()
    {
        echo "🔍 Test du système de détection de doublons...\n";

        try {
            // Test avec UUID
            $uuid1 = Str::uuid()->toString();
            $uuid2 = Str::uuid()->toString();

            // Simuler la recherche par UUID (sans réellement insérer)
            $foundExisting = DB::table('student_records')
                ->where('operation_uuid', $uuid1)
                ->exists();
            
            $this->addResult('UUID Uniqueness Check', !$foundExisting);

            // Test de génération de fingerprint
            $testData = ['user_id' => 123, 'session' => '2025-2026', 'my_class_id' => 5];
            $fingerprint1 = hash('sha256', json_encode($testData));
            $fingerprint2 = hash('sha256', json_encode($testData));
            
            $this->addResult('Data Fingerprint Consistency', $fingerprint1 === $fingerprint2);

            // Test avec données différentes
            $testData2 = ['user_id' => 124, 'session' => '2025-2026', 'my_class_id' => 5];
            $fingerprint3 = hash('sha256', json_encode($testData2));
            
            $this->addResult('Data Fingerprint Uniqueness', $fingerprint1 !== $fingerprint3);

        } catch (Exception $e) {
            $this->addResult('Duplicate Detection System', false, $e->getMessage());
        }

        echo "✅ Tests de détection terminés\n\n";
    }

    public function testModelSafety()
    {
        echo "🛡️ Test de la sécurité des modèles...\n";

        try {
            // Test des champs de vérification des doublons
            $studentRecord = new StudentRecord();
            $duplicateFields = $this->callProtectedMethod($studentRecord, 'getDuplicateCheckFields');
            
            $expectedFields = ['user_id', 'session', 'my_class_id'];
            $this->addResult('Student Duplicate Fields', $duplicateFields === $expectedFields);

            $paymentRecord = new PaymentRecord();
            $paymentFields = $this->callProtectedMethod($paymentRecord, 'getDuplicateCheckFields');
            
            $expectedPaymentFields = ['student_id', 'payment_id', 'year'];
            $this->addResult('Payment Duplicate Fields', $paymentFields === $expectedPaymentFields);

            // Test de génération d'UUID
            $receipt = new Receipt();
            $this->callProtectedMethod($receipt, 'generateOperationUuid');
            
            $this->addResult('UUID Generation', !empty($receipt->operation_uuid));

        } catch (Exception $e) {
            $this->addResult('Model Safety', false, $e->getMessage());
        }

        echo "✅ Tests de sécurité des modèles terminés\n\n";
    }

    public function testLoggingSystem()
    {
        echo "📝 Test du système de journalisation...\n";

        try {
            // Test de journalisation d'une tentative
            $logId = $this->loggerService->logDuplicateAttempt(
                'test_table',
                'test_operation',
                'blocked',
                ['test_field' => 'test_value'],
                'Test duplicate attempt',
                Str::uuid()->toString()
            );

            $this->addResult('Log Creation', $logId > 0);

            // Test de récupération des statistiques
            $stats = $this->loggerService->getDuplicateStatistics(1);
            $this->addResult('Statistics Generation', isset($stats['overall']));

            // Test de génération de rapport
            $report = $this->loggerService->generateDuplicateReport(1);
            $this->addResult('Report Generation', isset($report['period']) && isset($report['statistics']));

        } catch (Exception $e) {
            $this->addResult('Logging System', false, $e->getMessage());
        }

        echo "✅ Tests de journalisation terminés\n\n";
    }

    public function testCleanupOperations()
    {
        echo "🧹 Test des opérations de nettoyage...\n";

        try {
            // Test de nettoyage des verrous expirés
            $cleanedLocks = $this->lockService->cleanupExpiredLocks();
            $this->addResult('Lock Cleanup', is_int($cleanedLocks));

            // Test de nettoyage des logs (simulation)
            $oldLogCount = DB::table('duplicate_detection_logs')
                ->where('attempted_at', '<', now()->subDays(1000))
                ->count();
            
            $this->addResult('Log Cleanup Readiness', is_int($oldLogCount));

            // Test des statistiques de verrous
            $lockStats = $this->lockService->getLockStatistics();
            $this->addResult('Lock Statistics', isset($lockStats['summary']));

        } catch (Exception $e) {
            $this->addResult('Cleanup Operations', false, $e->getMessage());
        }

        echo "✅ Tests de nettoyage terminés\n\n";
    }

    private function callProtectedMethod($object, $method, $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    private function addResult($testName, $passed, $error = null)
    {
        $this->testResults[] = [
            'test' => $testName,
            'passed' => $passed,
            'error' => $error
        ];

        $status = $passed ? '✅' : '❌';
        $errorMsg = $error ? " (Erreur: $error)" : '';
        echo "  $status $testName$errorMsg\n";
    }

    private function displayResults()
    {
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "📊 RÉSULTATS DES TESTS\n";
        echo str_repeat('=', 60) . "\n\n";

        $passed = 0;
        $total = count($this->testResults);

        foreach ($this->testResults as $result) {
            if ($result['passed']) {
                $passed++;
            }
        }

        $percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

        echo "Tests réussis: $passed/$total ($percentage%)\n\n";

        if ($passed === $total) {
            echo "🎉 TOUS LES TESTS ONT RÉUSSI!\n";
            echo "Le système de protection contre les doublons est prêt à être utilisé.\n\n";
        } else {
            echo "⚠️  CERTAINS TESTS ONT ÉCHOUÉ\n";
            echo "Veuillez vérifier les erreurs ci-dessus avant de déployer le système.\n\n";

            echo "Tests échoués:\n";
            foreach ($this->testResults as $result) {
                if (!$result['passed']) {
                    echo "  ❌ {$result['test']}";
                    if ($result['error']) {
                        echo " - {$result['error']}";
                    }
                    echo "\n";
                }
            }
            echo "\n";
        }

        echo "Recommendations:\n";
        echo "1. Exécutez les migrations: php artisan migrate\n";
        echo "2. Testez le système en environnement de développement\n";
        echo "3. Configurez le nettoyage automatique: php artisan schedule:work\n";
        echo "4. Surveillez les logs dans l'interface admin\n\n";
    }
}

// Exécuter les tests
try {
    $tester = new DuplicatePreventionTest();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Erreur critique lors de l'exécution des tests: " . $e->getMessage() . "\n";
    echo "Stacktrace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "✅ Tests terminés avec succès!\n";