<?php

/**
 * Safe Migration Script for Duplicate Prevention System
 * 
 * This script safely runs the duplicate prevention migrations
 * Run with: php migrate_duplicate_prevention.php
 */

echo "🚀 Safe Migration for Duplicate Prevention System\n";
echo str_repeat('=', 60) . "\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from your Laravel project root directory\n";
    exit(1);
}

function runCommand($command, $description) {
    echo "🔄 $description...\n";
    echo "   Command: $command\n";
    
    $output = [];
    $returnCode = 0;
    exec("$command 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Success!\n";
        if (!empty($output)) {
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
    } else {
        echo "❌ Failed with return code: $returnCode\n";
        if (!empty($output)) {
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
        return false;
    }
    echo "\n";
    return true;
}

function checkMigrationStatus() {
    echo "📋 Checking migration status...\n";
    
    $output = [];
    exec("php artisan migrate:status", $output);
    
    $cleanupMigration = false;
    $constraintMigration = false;
    $uuidMigration = false;
    
    foreach ($output as $line) {
        if (strpos($line, 'cleanup_existing_duplicates_before_constraints') !== false) {
            $cleanupMigration = strpos($line, 'Ran') !== false;
        }
        if (strpos($line, 'add_unique_constraints_for_duplicate_prevention') !== false) {
            $constraintMigration = strpos($line, 'Ran') !== false;
        }
        if (strpos($line, 'add_uuid_columns_for_idempotency') !== false) {
            $uuidMigration = strpos($line, 'Ran') !== false;
        }
    }
    
    echo "   Cleanup migration: " . ($cleanupMigration ? "✅ Ran" : "⏳ Pending") . "\n";
    echo "   Constraint migration: " . ($constraintMigration ? "✅ Ran" : "⏳ Pending") . "\n";
    echo "   UUID migration: " . ($uuidMigration ? "✅ Ran" : "⏳ Pending") . "\n\n";
    
    return [
        'cleanup' => $cleanupMigration,
        'constraints' => $constraintMigration,
        'uuid' => $uuidMigration
    ];
}

try {
    // Step 1: Check current migration status
    $status = checkMigrationStatus();
    
    // Step 2: Create backup recommendation
    echo "💾 IMPORTANT: Backup Recommendation\n";
    echo "   Before proceeding, it's highly recommended to backup your database:\n";
    echo "   - mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql\n";
    echo "   - Or use your preferred backup method\n\n";
    
    echo "Do you want to continue? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) !== 'y') {
        echo "❌ Migration cancelled by user\n";
        exit(0);
    }
    
    // Step 3: Run cleanup migration first if not already run
    if (!$status['cleanup']) {
        echo "🧹 Step 1: Running duplicate cleanup migration...\n";
        if (!runCommand("php artisan migrate --path=database/migrations/2025_08_25_000000_cleanup_existing_duplicates_before_constraints.php", "Cleaning up existing duplicates")) {
            echo "❌ Cleanup migration failed. Please check the errors above.\n";
            exit(1);
        }
    } else {
        echo "✅ Cleanup migration already completed\n\n";
    }
    
    // Step 4: Run constraint migration if not already run
    if (!$status['constraints']) {
        echo "🔒 Step 2: Adding unique constraints...\n";
        if (!runCommand("php artisan migrate --path=database/migrations/2025_08_25_000001_add_unique_constraints_for_duplicate_prevention.php", "Adding unique constraints")) {
            echo "❌ Constraint migration failed. Please check the errors above.\n";
            exit(1);
        }
    } else {
        echo "✅ Constraint migration already completed\n\n";
    }
    
    // Step 5: Run UUID migration if not already run
    if (!$status['uuid']) {
        echo "🆔 Step 3: Adding UUID columns for idempotency...\n";
        if (!runCommand("php artisan migrate --path=database/migrations/2025_08_25_000002_add_uuid_columns_for_idempotency.php", "Adding UUID columns")) {
            echo "❌ UUID migration failed. Please check the errors above.\n";
            exit(1);
        }
    } else {
        echo "✅ UUID migration already completed\n\n";
    }
    
    // Step 6: Final verification
    echo "🔍 Final verification...\n";
    $finalStatus = checkMigrationStatus();
    
    if ($finalStatus['cleanup'] && $finalStatus['constraints'] && $finalStatus['uuid']) {
        echo "🎉 All migrations completed successfully!\n\n";
        
        echo "📋 Next Steps:\n";
        echo "1. Test the duplicate prevention system:\n";
        echo "   php test_duplicate_prevention.php\n\n";
        echo "2. Access the admin interface:\n";
        echo "   URL: /super_admin/duplicate_management/dashboard\n\n";
        echo "3. Include JavaScript files in your layout:\n";
        echo "   <script src=\"{{ asset('js/duplicate-prevention.js') }}\"></script>\n\n";
        echo "4. Set up automatic cleanup (optional):\n";
        echo "   Add to your scheduler: php artisan duplicate:cleanup\n\n";
        
    } else {
        echo "⚠️ Some migrations may not have completed successfully.\n";
        echo "Please check the migration status above.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during migration process: " . $e->getMessage() . "\n";
    exit(1);
}

echo "✅ Migration process completed!\n";