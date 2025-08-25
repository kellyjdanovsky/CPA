<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\DuplicateDetectionLog;
use Illuminate\Database\QueryException;

trait DuplicateDetection
{
    /**
     * Boot the trait
     */
    protected static function bootDuplicateDetection()
    {
        // Before creating a record, check for duplicates and generate UUID
        static::creating(function ($model) {
            $model->checkForDuplicates('create');
            $model->generateOperationUuid();
        });

        // Before updating a record, check for duplicates
        static::updating(function ($model) {
            $model->checkForDuplicates('update');
        });

        // Log successful operations
        static::created(function ($model) {
            $model->logOperation('create', 'allowed');
        });

        static::updated(function ($model) {
            $model->logOperation('update', 'allowed');
        });
    }

    /**
     * Generate a unique operation UUID for idempotency
     */
    public function generateOperationUuid()
    {
        if (empty($this->operation_uuid)) {
            $this->operation_uuid = Str::uuid()->toString();
        }
    }

    /**
     * Check for duplicate operations based on UUID
     * 
     * @param string $operationUuid
     * @return mixed|null
     */
    public static function findByOperationUuid($operationUuid)
    {
        return static::where('operation_uuid', $operationUuid)->first();
    }

    /**
     * Check for duplicates before performing operation
     * 
     * @param string $operation
     * @throws \Exception
     */
    protected function checkForDuplicates($operation)
    {
        try {
            // Check if operation UUID already exists
            if (!empty($this->operation_uuid)) {
                $existing = static::where('operation_uuid', $this->operation_uuid)
                    ->where('id', '!=', $this->id ?? 0)
                    ->first();
                
                if ($existing) {
                    $this->logOperation($operation, 'blocked', 'Operation UUID already exists');
                    throw new \Exception("Duplicate operation detected. Operation UUID: {$this->operation_uuid}");
                }
            }

            // Check for content-based duplicates
            $duplicateFields = $this->getDuplicateCheckFields();
            if (!empty($duplicateFields)) {
                $query = static::query();
                
                foreach ($duplicateFields as $field) {
                    if (isset($this->attributes[$field])) {
                        $query->where($field, $this->attributes[$field]);
                    }
                }
                
                if ($this->exists) {
                    $query->where('id', '!=', $this->id);
                }
                
                $duplicate = $query->first();
                
                if ($duplicate) {
                    $fingerprint = $this->generateDataFingerprint();
                    $this->logOperation($operation, 'blocked', 'Content-based duplicate detected', $fingerprint);
                    
                    $fieldsList = implode(', ', $duplicateFields);
                    throw new \Exception("Duplicate record detected based on fields: {$fieldsList}");
                }
            }
            
        } catch (QueryException $e) {
            // Handle database constraint violations
            if ($this->isDuplicateKeyError($e)) {
                $this->logOperation($operation, 'blocked', 'Database constraint violation: ' . $e->getMessage());
                throw new \Exception("Duplicate entry detected at database level. Please check your data and try again.");
            }
            throw $e;
        }
    }

    /**
     * Get fields to check for duplicates (override in models)
     * 
     * @return array
     */
    protected function getDuplicateCheckFields()
    {
        return [];
    }

    /**
     * Generate a fingerprint of the data for duplicate detection
     * 
     * @return string
     */
    protected function generateDataFingerprint()
    {
        $criticalFields = $this->getDuplicateCheckFields();
        $data = [];
        
        foreach ($criticalFields as $field) {
            if (isset($this->attributes[$field])) {
                $data[$field] = $this->attributes[$field];
            }
        }
        
        return hash('sha256', json_encode($data));
    }

    /**
     * Log duplicate detection operation
     * 
     * @param string $operation
     * @param string $status
     * @param string|null $reason
     * @param string|null $fingerprint
     */
    protected function logOperation($operation, $status, $reason = null, $fingerprint = null)
    {
        try {
            $user = Auth::user();
            
            DB::table('duplicate_detection_logs')->insert([
                'table_name' => $this->getTable(),
                'operation_type' => $operation,
                'operation_uuid' => $this->operation_uuid ?? null,
                'data_fingerprint' => json_encode([
                    'fingerprint' => $fingerprint ?? $this->generateDataFingerprint(),
                    'model_id' => $this->id ?? null,
                    'critical_fields' => $this->getDuplicateCheckFields()
                ]),
                'user_id' => $user ? $user->id : null,
                'session_id' => session()->getId(),
                'ip_address' => Request::ip(),
                'attempted_at' => now(),
                'status' => $status,
                'reason' => $reason,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the main operation
            \Log::error('Failed to log duplicate detection operation: ' . $e->getMessage());
        }
    }

    /**
     * Check if the exception is a duplicate key error
     * 
     * @param QueryException $e
     * @return bool
     */
    protected function isDuplicateKeyError(QueryException $e)
    {
        $errorCode = $e->errorInfo[1] ?? null;
        $errorMessage = $e->getMessage();
        
        // MySQL duplicate entry errors
        return $errorCode === 1062 || 
               strpos($errorMessage, 'Duplicate entry') !== false ||
               strpos($errorMessage, 'Integrity constraint violation') !== false;
    }

    /**
     * Safely create a record with duplicate detection
     * 
     * @param array $attributes
     * @param string|null $operationUuid
     * @return static|null
     */
    public static function safeCreate(array $attributes, $operationUuid = null)
    {
        try {
            // Check if operation already exists
            if ($operationUuid) {
                $existing = static::findByOperationUuid($operationUuid);
                if ($existing) {
                    return $existing; // Return existing record instead of creating duplicate
                }
                $attributes['operation_uuid'] = $operationUuid;
            }

            return static::create($attributes);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                // Try to find and return the existing record
                $model = new static();
                $duplicateFields = $model->getDuplicateCheckFields();
                
                $query = static::query();
                foreach ($duplicateFields as $field) {
                    if (isset($attributes[$field])) {
                        $query->where($field, $attributes[$field]);
                    }
                }
                
                return $query->first();
            }
            throw $e;
        }
    }

    /**
     * Get duplicate detection logs for this model
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function getDuplicateLogs()
    {
        return DB::table('duplicate_detection_logs')
            ->where('table_name', $this->getTable())
            ->where(function($query) {
                $query->where('operation_uuid', $this->operation_uuid)
                      ->orWhere('data_fingerprint->model_id', $this->id);
            })
            ->orderBy('attempted_at', 'desc');
    }

    /**
     * Clean up old duplicate detection logs
     * 
     * @param int $daysToKeep
     */
    public static function cleanupDuplicateLogs($daysToKeep = 30)
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        DB::table('duplicate_detection_logs')
            ->where('attempted_at', '<', $cutoffDate)
            ->delete();
    }
}