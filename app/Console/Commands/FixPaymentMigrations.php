<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixPaymentMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:fix-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix payment system migration conflicts by marking existing tables as migrated';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔧 Fixing payment system migration conflicts...');
        
        $migrations = [
            '2025_08_25_100000_create_encaissements_table',
            '2025_08_25_100001_create_recettes_table',
            '2025_08_25_100002_create_decaissements_table'
        ];

        $tables = ['encaissements', 'recettes', 'decaissements'];
        
        foreach ($migrations as $index => $migration) {
            $table = $tables[$index];
            
            // Check if table exists
            if (Schema::hasTable($table)) {
                $this->info("✅ Table '{$table}' already exists");
                
                // Check if migration is already recorded
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();
                
                if (!$exists) {
                    // Insert migration record
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $this->getNextBatch()
                    ]);
                    
                    $this->info("✅ Marked migration '{$migration}' as completed");
                } else {
                    $this->info("ℹ️  Migration '{$migration}' already recorded");
                }
            } else {
                $this->warn("⚠️  Table '{$table}' does not exist");
            }
        }
        
        $this->info('');
        $this->info('🎉 Payment migration conflicts have been resolved!');
        $this->info('You can now run: php artisan migrate');
        
        return 0;
    }

    /**
     * Get the next batch number for migrations
     *
     * @return int
     */
    private function getNextBatch()
    {
        $lastBatch = DB::table('migrations')->max('batch');
        return $lastBatch ? $lastBatch + 1 : 1;
    }
}