<?php

namespace App\Imports;

use App\Models\StudentRecord;
use App\Helpers\Qs;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $class_id;
    protected $section_id;
    protected $rowCount = 0;

    public function __construct($class_id, $section_id)
    {
        $this->class_id = $class_id;
        $this->section_id = $section_id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $current_year = Qs::getSetting('current_session');
        $student_id = $row['id'];

        // Vérifier si l'élève existe déjà dans l'année courante
        $exists = StudentRecord::where('user_id', $student_id)
            ->where('session', $current_year)
            ->exists();

        if (!$exists) {
            // Récupérer les informations de l'élève de l'année précédente
            $current_year_parts = explode('-', $current_year);
            $previous_year = ($current_year_parts[0]-1) . '-' . ($current_year_parts[1]-1);
            
            $student_record = StudentRecord::where('user_id', $student_id)
                ->where('session', $previous_year)
                ->first();
            
            if ($student_record) {
                $this->rowCount++;
                
                // Créer un nouvel enregistrement pour l'élève dans l'année courante
                $record = new StudentRecord([
                    'user_id' => $student_id,
                    'my_class_id' => $this->class_id,
                    'section_id' => $this->section_id,
                    'my_parent_id' => $student_record->my_parent_id,
                    'adm_no' => $student_record->adm_no,
                    'year_admitted' => $student_record->year_admitted,
                    'house' => $student_record->house,
                    'age' => $student_record->age,
                    'session' => $current_year,
                ]);
                
                // Créer les enregistrements de paiement pour cet élève
                $this->createPaymentRecords($student_id, $this->class_id);
                
                return $record;
            }
        }
        
        return null;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
        ];
    }

    /**
     * Création des enregistrements de paiement pour un élève
     */
    protected function createPaymentRecords($student_id, $class_id)
    {
        $current_year = Qs::getSetting('current_session');
        
        // Récupérer les paiements pour cette classe et les paiements généraux
        $pay1 = DB::table('payments')->where('my_class_id', $class_id)->where('year', $current_year)->get();
        $pay2 = DB::table('payments')->whereNull('my_class_id')->where('year', $current_year)->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        
        if($payments->count()){
            $payment_records = [];
            
            foreach($payments as $p){
                $payment_records[] = [
                    'student_id' => $student_id,
                    'payment_id' => $p->id,
                    'year' => $current_year,
                    'ref_no' => mt_rand(100000, 99999999),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Insertion en masse pour optimiser les performances
            if(count($payment_records) > 0){
                DB::table('payment_records')->insert($payment_records);
            }
        }
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}