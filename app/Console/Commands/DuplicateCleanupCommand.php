<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLockService;
use App\Services\DuplicateLoggerService;
use Carbon\Carbon;

class DuplicateCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicate:cleanup 
                           {--days=30 : Number of days to keep duplicate logs}
                           {--remove-duplicates : Remove actual duplicate records}
                           {--dry-run : Show what would be done without actually doing it}
                           {--table= : Specific table to clean (optional)}
                           {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate detection logs and optionally remove duplicate records';

    /**
     * @var TransactionLockService
     */
    protected $lockService;

    /**
     * @var DuplicateLoggerService
     */
    protected $loggerService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->lockService = new TransactionLockService();
        $this->loggerService = new DuplicateLoggerService();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting duplicate cleanup process...');

        $daysToKeep = $this->option('days');
        $removeDuplicates = $this->option('remove-duplicates');
        $dryRun = $this->option('dry-run');
        $table = $this->option('table');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Clean up expired transaction locks
        $this->cleanupExpiredLocks($dryRun);

        // Clean up old duplicate logs
        $this->cleanupDuplicateLogs($daysToKeep, $dryRun);

        // Remove actual duplicates if requested
        if ($removeDuplicates) {
            if (!$force && !$this->confirm('This will remove duplicate records from your database. Are you sure?')) {
                $this->info('Duplicate removal cancelled.');
                return 0;
            }
            $this->removeDuplicateRecords($table, $dryRun);
        }

        // Generate cleanup report
        $this->generateCleanupReport();

        $this->info('Duplicate cleanup process completed.');
        return 0;
    }

    /**
     * Clean up expired transaction locks
     */
    protected function cleanupExpiredLocks($dryRun = false)
    {
        $this->info('Cleaning up expired transaction locks...');

        $expiredCount = DB::table('transaction_locks')
            ->where('expires_at', '<=', Carbon::now())
            ->count();

        if ($expiredCount > 0) {
            if (!$dryRun) {
                $this->lockService->cleanupExpiredLocks();
            }
            $this->info("Removed {$expiredCount} expired transaction locks.");
        } else {
            $this->info('No expired transaction locks found.');
        }
    }

    /**
     * Clean up old duplicate detection logs
     */
    protected function cleanupDuplicateLogs($daysToKeep, $dryRun = false)
    {
        $this->info("Cleaning up duplicate logs older than {$daysToKeep} days...");

        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        $oldLogsCount = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '<', $cutoffDate)
            ->count();

        if ($oldLogsCount > 0) {
            if (!$dryRun) {
                $deleted = $this->loggerService->cleanupOldLogs($daysToKeep);
                $this->info("Deleted {$deleted} old duplicate detection logs.");
            } else {
                $this->info("Would delete {$oldLogsCount} old duplicate detection logs.");
            }
        } else {
            $this->info('No old duplicate logs found.');
        }
    }

    /**
     * Remove actual duplicate records
     */
    protected function removeDuplicateRecords($table = null, $dryRun = false)
    {
        $this->info('Scanning for duplicate records...');

        $tables = $table ? [$table] : [
            'student_records',
            'payment_records', 
            'receipts',
            'marks',
            'exam_records'
        ];

        foreach ($tables as $tableName) {
            $this->cleanupTableDuplicates($tableName, $dryRun);
        }
    }

    /**
     * Clean up duplicates in a specific table
     */
    protected function cleanupTableDuplicates($tableName, $dryRun = false)
    {
        $this->info("Checking for duplicates in {$tableName}...");

        $duplicateQueries = $this->getDuplicateQueries();
        
        if (!isset($duplicateQueries[$tableName])) {
            $this->warn("No duplicate detection query defined for {$tableName}");
            return;
        }

        $query = $duplicateQueries[$tableName];
        $duplicates = DB::select($query);

        if (empty($duplicates)) {
            $this->info("No duplicates found in {$tableName}");
            return;
        }

        $this->warn("Found " . count($duplicates) . " duplicate groups in {$tableName}");

        foreach ($duplicates as $duplicate) {
            $this->handleDuplicateGroup($tableName, $duplicate, $dryRun);
        }
    }

    /**
     * Handle a group of duplicate records
     */
    protected function handleDuplicateGroup($tableName, $duplicate, $dryRun = false)
    {
        // Get all records in this duplicate group
        $duplicateIds = explode(',', $duplicate->duplicate_ids);
        
        if (count($duplicateIds) <= 1) {
            return;
        }

        // Keep the first record (oldest), remove the rest
        $keepId = $duplicateIds[0];
        $removeIds = array_slice($duplicateIds, 1);

        $this->warn("  Duplicate group: keeping ID {$keepId}, removing IDs: " . implode(', ', $removeIds));

        if (!$dryRun) {
            try {
                DB::beginTransaction();

                // Remove the duplicate records
                DB::table($tableName)->whereIn('id', $removeIds)->delete();

                // Log the cleanup action
                $this->loggerService->logDuplicateAttempt(
                    $tableName,
                    'cleanup',
                    'removed_duplicates',
                    [
                        'kept_id' => $keepId,
                        'removed_ids' => $removeIds,
                        'duplicate_count' => count($removeIds)
                    ],
                    'Automatic duplicate cleanup'
                );

                DB::commit();
                $this->info("    Removed " . count($removeIds) . " duplicate records");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("    Failed to remove duplicates: " . $e->getMessage());
            }
        }
    }

    /**
     * Get duplicate detection queries for each table
     */
    protected function getDuplicateQueries()
    {
        return [
            'student_records' => "
                SELECT 
                    GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                    user_id, session, my_class_id,
                    COUNT(*) as duplicate_count
                FROM student_records 
                GROUP BY user_id, session, my_class_id
                HAVING COUNT(*) > 1
            ",
            'payment_records' => "
                SELECT 
                    GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                    student_id, payment_id, year,
                    COUNT(*) as duplicate_count
                FROM payment_records 
                GROUP BY student_id, payment_id, year
                HAVING COUNT(*) > 1
            ",
            'receipts' => "
                SELECT 
                    GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                    pr_id, reference_number,
                    COUNT(*) as duplicate_count
                FROM receipts 
                WHERE reference_number IS NOT NULL
                GROUP BY pr_id, reference_number
                HAVING COUNT(*) > 1
            ",
            'marks' => "
                SELECT 
                    GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                    student_id, subject_id, exam_id, year,
                    COUNT(*) as duplicate_count
                FROM marks 
                GROUP BY student_id, subject_id, exam_id, year
                HAVING COUNT(*) > 1
            ",
            'exam_records' => "
                SELECT 
                    GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                    student_id, exam_id, year,
                    COUNT(*) as duplicate_count
                FROM exam_records 
                GROUP BY student_id, exam_id, year
                HAVING COUNT(*) > 1
            "
        ];
    }

    /**
     * Generate cleanup report
     */
    protected function generateCleanupReport()
    {
        $this->info('Generating cleanup report...');

        // Get recent statistics
        $stats = $this->loggerService->getDuplicateStatistics(7);
        $patterns = $this->loggerService->detectDuplicatePatterns(7);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Attempts (7 days)', $stats['overall']->total_attempts],
                ['Blocked Attempts', $stats['overall']->blocked_attempts],
                ['Allowed Attempts', $stats['overall']->allowed_attempts],
                ['Unique Users', $stats['overall']->unique_users],
                ['Affected Tables', $stats['overall']->affected_tables],
            ]
        );

        if (count($patterns['frequent_duplicators']) > 0) {
            $this->warn('Users with frequent duplicate attempts:');
            foreach ($patterns['frequent_duplicators'] as $duplicator) {
                $this->line("  User ID {$duplicator->user_id}: {$duplicator->attempt_count} attempts on {$duplicator->table_name}");
            }
        }

        // Active locks status
        $activeLocks = $this->lockService->getActiveLocks();
        $this->info("Active transaction locks: " . count($activeLocks));

        if (count($activeLocks) > 5) {
            $this->warn('High number of active locks detected. Consider reviewing system performance.');
        }
    }
}