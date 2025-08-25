<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CleanupExistingDuplicatesBeforeConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "🧹 Starting cleanup of existing duplicate records...\n";
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clean up existing duplicates
        $this->cleanupStudentDuplicates();
        $this->cleanupPaymentDuplicates();
        $this->cleanupMarkDuplicates();
        $this->cleanupReceiptDuplicates();
        
        if (Schema::hasTable('exam_records')) {
            $this->cleanupExamRecordDuplicates();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        echo "✅ Cleanup completed successfully!\n";
    }

    /**
     * Clean up duplicate student records
     */
    private function cleanupStudentDuplicates()
    {
        echo "🔍 Checking for duplicate student records...\n";
        
        $duplicates = DB::select("
            SELECT user_id, session, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids, COUNT(*) as count
            FROM student_records 
            GROUP BY user_id, session
            HAVING COUNT(*) > 1
        ");
        
        if (empty($duplicates)) {
            echo "✅ No duplicate student records found\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " groups of duplicate student records\n";
        
        foreach ($duplicates as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });
            
            if (!empty($deleteIds)) {
                // Delete the duplicate records
                $deleted = DB::table('student_records')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                echo "  → Kept record ID {$keepId}, deleted {$deleted} duplicates for user {$duplicate->user_id} in session {$duplicate->session}\n";
            }
        }
    }
    
    /**
     * Clean up duplicate payment records
     */
    private function cleanupPaymentDuplicates()
    {
        echo "🔍 Checking for duplicate payment records...\n";
        
        $duplicates = DB::select("
            SELECT student_id, payment_id, year, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids, COUNT(*) as count
            FROM payment_records 
            GROUP BY student_id, payment_id, year
            HAVING COUNT(*) > 1
        ");
        
        if (empty($duplicates)) {
            echo "✅ No duplicate payment records found\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " groups of duplicate payment records\n";
        
        foreach ($duplicates as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });
            
            if (!empty($deleteIds)) {
                // First, delete related receipts
                $receiptDeleted = DB::table('receipts')
                    ->whereIn('pr_id', $deleteIds)
                    ->delete();
                    
                // Then delete the payment records
                $paymentDeleted = DB::table('payment_records')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                echo "  → Kept record ID {$keepId}, deleted {$paymentDeleted} payment records and {$receiptDeleted} receipts for student {$duplicate->student_id}\n";
            }
        }
    }
    
    /**
     * Clean up duplicate mark records
     */
    private function cleanupMarkDuplicates()
    {
        echo "🔍 Checking for duplicate mark records...\n";
        
        $duplicates = DB::select("
            SELECT student_id, subject_id, exam_id, year, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids, COUNT(*) as count
            FROM marks 
            GROUP BY student_id, subject_id, exam_id, year
            HAVING COUNT(*) > 1
        ");
        
        if (empty($duplicates)) {
            echo "✅ No duplicate mark records found\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " groups of duplicate mark records\n";
        
        foreach ($duplicates as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });
            
            if (!empty($deleteIds)) {
                $deleted = DB::table('marks')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                echo "  → Kept record ID {$keepId}, deleted {$deleted} duplicate marks for student {$duplicate->student_id}\n";
            }
        }
    }
    
    /**
     * Clean up duplicate receipt records
     */
    private function cleanupReceiptDuplicates()
    {
        echo "🔍 Checking for duplicate receipt records...\n";
        
        // Only clean if reference_number column exists
        if (!Schema::hasColumn('receipts', 'reference_number')) {
            echo "✅ No reference_number column, skipping receipt cleanup\n";
            return;
        }
        
        $duplicates = DB::select("
            SELECT reference_number, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids, COUNT(*) as count
            FROM receipts 
            WHERE reference_number IS NOT NULL AND reference_number != ''
            GROUP BY reference_number
            HAVING COUNT(*) > 1
        ");
        
        if (empty($duplicates)) {
            echo "✅ No duplicate receipt records found\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " groups of duplicate receipt records\n";
        
        foreach ($duplicates as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });
            
            if (!empty($deleteIds)) {
                $deleted = DB::table('receipts')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                echo "  → Kept record ID {$keepId}, deleted {$deleted} duplicate receipts with reference {$duplicate->reference_number}\n";
            }
        }
    }
    
    /**
     * Clean up duplicate exam record entries
     */
    private function cleanupExamRecordDuplicates()
    {
        echo "🔍 Checking for duplicate exam records...\n";
        
        $duplicates = DB::select("
            SELECT student_id, exam_id, year, MIN(id) as keep_id, GROUP_CONCAT(id ORDER BY id) as all_ids, COUNT(*) as count
            FROM exam_records 
            GROUP BY student_id, exam_id, year
            HAVING COUNT(*) > 1
        ");
        
        if (empty($duplicates)) {
            echo "✅ No duplicate exam records found\n";
            return;
        }
        
        echo "Found " . count($duplicates) . " groups of duplicate exam records\n";
        
        foreach ($duplicates as $duplicate) {
            $allIds = explode(',', $duplicate->all_ids);
            $keepId = $duplicate->keep_id;
            $deleteIds = array_filter($allIds, function($id) use ($keepId) {
                return $id != $keepId;
            });
            
            if (!empty($deleteIds)) {
                $deleted = DB::table('exam_records')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                    
                echo "  → Kept record ID {$keepId}, deleted {$deleted} duplicate exam records for student {$duplicate->student_id}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse cleanup - duplicates were already removed
        echo "⚠️  Cannot reverse duplicate cleanup - records were permanently removed\n";
    }
}