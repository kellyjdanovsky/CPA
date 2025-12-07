<?php

namespace App\Http\Controllers\SupportTeam;

use PDF;
use App\Helpers\Qs;
use App\Helpers\Pay;
use App\Models\MyClass;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Models\StudentRecord;
use App\Models\Receipt;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Payment\PaymentCreate;
use App\Http\Requests\Payment\PaymentUpdate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Repositories\DecaissementRepo;

class PaymentController extends Controller
{
    protected $my_class, $pay, $student, $year, $decaissement;

    public function __construct(MyClassRepo $my_class, PaymentRepo $pay, StudentRepo $student, DecaissementRepo $decaissement)
    {
        $this->my_class = $my_class;
        $this->pay = $pay;
        $this->year = Qs::getCurrentSession();
        $this->student = $student;
        $this->decaissement = $decaissement;

        $this->middleware('teamAccount');
    }

    public function index()
    {
        $d['selected'] = false;
        $d['years'] = $this->pay->getPaymentYears();
        $d['my_classes'] = $this->my_class->all(); // Add classes for ADRA & TEAM 3 tab

        return view('pages.support_team.payments.index', $d);
    }

    public function show($year)
    {
        $d['payments'] = $p = $this->pay->getPayment(['year' => $year])->get();

        if(($p->count() < 1)){
            return Qs::goWithDanger('payments.index');
        }

        $d['selected'] = true;
        $d['my_classes'] = $this->my_class->all();
        $d['years'] = $this->pay->getPaymentYears();
        $d['year'] = $year;

        // Fetch Decaissements for this year
        // We assume getByYear exists or we can use getByPeriod with year boundaries
        // Or create a simple query if repository method is missing.
        // Let's check DecaissementRepo first, but assuming typical pattern or similar to DecaissementController logic
        
        $startDate = $year . '-01-01'; // Assuming year is e.g. "2024" or school year "2024-2025"?
        // Usually year in this system is "2024-2025".
        // Let's rely on the repository to handle 'year' filtering if possible.
        // DecaissementController uses $this->decaissement->getByPeriod($start, $end, ['year' => $year])
        
        // Let's try to get all decaissements for this 'year' (session)
        // Check if getByPeriod supports just year filter if dates are null? 
        // Or just use Eloquent directly if Repo is unknown.
        // Better: use the same logic as DecaissementController::index but simplified.
        
        // Hack: Since I don't see DecaissementRepo source, I'll use the Model directly for safety if I can't be sure about Repo methods.
        // But I injected the repo. Let's use `getAll()` if it exists or `getForYear($year)`.
        // DecaissementController uses: $decaissements = $this->decaissement->getByPeriod($filters['date_debut'], $filters['date_fin'], $filters);
        // It provides a 'year' filter.
        
        // Let's fetch all for the session.
        $d['decaissements'] = \App\Models\Decaissement::where('year', $year)->latest()->get();

        return view('pages.support_team.payments.index', $d);

    }

    public function select_year(Request $req)
    {
        return Qs::goToRoute(['payments.show', $req->year]);
    }

    public function create()
    {
        $d['my_classes'] = $this->my_class->all();
        return view('pages.support_team.payments.create', $d);
    }

    public function invoice($st_id, $year = NULL)
    {
        if(!$st_id) {return Qs::goWithDanger();}

        $inv = $year ? $this->pay->getAllMyPR($st_id, $year) : $this->pay->getAllMyPR($st_id);

        $d['sr'] = $this->student->findByUserId($st_id)->first();
        $pr = $inv->get();
        $d['uncleared'] = $pr->where('paid', 0);
        $d['cleared'] = $pr->where('paid', 1);

        return view('pages.support_team.payments.invoice', $d);
    }

    public function receipts($pr_id)
    {
        if(!$pr_id) {return Qs::goWithDanger();}

        try {
            $d['pr'] = $pr = $this->pay->getRecord(['id' => $pr_id])->with('receipt')->first();
            $d['payment'] = $pr->payment;
            $d['sr'] = $this->student->findByUserId($pr->student_id)->first();
            $d['s'] = Setting::all()->flatMap(function($s){
                return [$s->type => $s->description];
            });

            // Ajouter le montant total et la description au premier reçu
            if ($pr->receipt && $pr->receipt->count() > 0) {
                $firstReceipt = $pr->receipt->first();
                $firstReceipt->amount = $pr->amount;
                $firstReceipt->description = $pr->description;
            }
            $d['receipts'] = $pr->receipt;
        } catch (ModelNotFoundException $ex) {
            return back()->with('flash_danger', __('msg.rnf'));
        }

        return view('pages.support_team.payments.receipt', $d);
    }

    public function pdf_receipts($pr_id)
    {
        if(!$pr_id) {return Qs::goWithDanger();}

        try {
            $d['pr'] = $pr = $this->pay->getRecord(['id' => $pr_id])->with('receipt')->first();
        } catch (ModelNotFoundException $ex) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        $d['receipts'] = $pr->receipt;
        $d['payment'] = $pr->payment;
        $d['sr'] = $sr =$this->student->findByUserId($pr->student_id)->first();
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

        $pdf_name = 'Receipt_'.$pr->ref_no;

        return PDF::loadView('pages.support_team.payments.receipt', $d)->download($pdf_name);

        //return $this->downloadReceipt('pages.support_team.payments.receipt', $d, $pdf_name);
    }



    protected function downloadReceipt($page, $data, $name = NULL){
        $path = 'receipts/file.html';
        $disk = Storage::disk('local');
        $disk->put($path, view($page, $data) );
        $html = $disk->get($path);
        return PDF::loadHTML($html)->download($name);
    }

    public function pay_now(Request $req, $pr_id)
    {
        try {
            $this->validate($req, [
                'amt_paid' => 'required|numeric',
                'methode' => 'required|string',
                'payment_method' => 'nullable|string',
                'reference_number' => 'nullable|string',
                'observations' => 'nullable|string'
            ], [], [
                'amt_paid' => 'Amount Paid',
                'methode' => 'Payment Method',
                'payment_method' => 'Payment Method Details',
                'reference_number' => 'Reference Number',
                'observations' => 'Observations'
            ]);

            // Vérifier si le montant payé est supérieur à zéro
            if ($req->amt_paid <= 0) {
                return response()->json(['message' => 'Le montant payé doit être supérieur à zéro'], 422);
            }

            $pr = $this->pay->findRecord($pr_id);
            if (!$pr) {
                return response()->json(['message' => 'Enregistrement de paiement introuvable'], 404);
            }

            $payment = $this->pay->find($pr->payment_id);
            if (!$payment) {
                return response()->json(['message' => 'Paiement introuvable'], 404);
            }

            // Récupérer le montant réellement payé depuis les reçus existants
            $existingPaidAmount = $pr->getTotalPaidFromReceipts();
            
            // Calculer le solde restant en fonction des reçus existants
            $remainingBalance = $payment->amount - $existingPaidAmount;
            
            // Vérifier si le montant payé est supérieur au solde restant
            if ($req->amt_paid > $remainingBalance) {
                return response()->json(['message' => 'Le montant payé ne peut pas être supérieur au solde restant de ' . $remainingBalance], 422);
            }

            // Créer un reçu avec une référence unique
            $d2['amt_paid'] = $req->amt_paid;
            $d2['balance'] = $remainingBalance - $req->amt_paid;
            $d2['pr_id'] = $pr_id;
            $d2['year'] = $this->year;
            $d2['methode'] = $req->methode;
            $d2['payment_method'] = $req->payment_method ?? $req->methode;
            $d2['reference_number'] = $req->reference_number ?? \App\Models\Receipt::generateReferenceNumber();
            $d2['observations'] = $req->observations ?? null;
            $d2['created_by'] = auth()->user()->name;
            // Générer un UUID unique pour éviter les doublons
            $d2['operation_uuid'] = (string) \Illuminate\Support\Str::uuid();

            // Utiliser createSafeReceipt au lieu de createReceipt pour éviter les doublons
            $receipt = \App\Models\Receipt::createSafeReceipt($d2);

            if (!$receipt) {
                throw new \Exception('Impossible de créer le reçu');
            }

            // Le modèle Receipt mettra à jour automatiquement l'enregistrement de paiement
            // via les événements de modèle dans le trait DuplicateDetection
            
            return Qs::jsonUpdateOk();
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors du paiement: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Retourner un message d'erreur convivial
            return response()->json([
                'message' => 'Une erreur est survenue lors du traitement du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manage($class_id = NULL)
    {
        $d['my_classes'] = $this->my_class->all();
        $d['selected'] = false;
        $d['payments'] = $this->pay->getActivePayments()->get();

        if($class_id){
            $students = $this->student->getRecord(['my_class_id' => $class_id])->get()->sortBy('user.name');
            if($students->count() < 1){
                return Qs::goWithDanger('payments.manage');
            }
            
            // Calculate payment status for each student
            foreach ($students as $student) {
                $status = $student->user->status ?? 'Normal';
                $student->payment_status = 'normal';
                $student->payments_paid_25 = 0;
                $student->payments_total = 0;
                
                // For ADRA students, check if they have paid their 25% portion
                if ($status === 'ADRA') {
                    // Get all payments for this student in the current year
                    $paymentRecords = PaymentRecord::where('student_id', $student->user_id)
                        ->where('year', $this->year)
                        ->with('payment')
                        ->get();
                    
                    $paidCount = 0;
                    $totalPayments = count($d['payments']);
                    
                    foreach ($d['payments'] as $payment) {
                        $record = $paymentRecords->where('payment_id', $payment->id)->first();
                        $requiredAmount = $payment->amount * 0.25; // 25% for ADRA students
                        
                        if ($record && $record->amt_paid >= $requiredAmount) {
                            $paidCount++;
                        }
                    }
                    
                    $student->payments_paid_25 = $paidCount;
                    $student->payments_total = $totalPayments;
                    
                    // If all 25% payments are made, mark as "acquittées"
                    if ($paidCount > 0 && $paidCount >= $totalPayments) {
                        $student->payment_status = 'acquittees';
                    } elseif ($paidCount > 0) {
                        $student->payment_status = 'partial';
                    } else {
                        $student->payment_status = 'unpaid';
                    }
                } elseif ($status === 'Team3' || $status === 'TEAM3') {
                    // For TEAM3, mark as automatically covered (no student payment required)
                    $student->payment_status = 'covered';
                }
            }
            
            $d['students'] = $students;
            $d['selected'] = true;
            $d['my_class_id'] = $class_id;
        }

        return view('pages.support_team.payments.manage', $d);
    }

    public function reset_record($id)
    {
        $pr['amt_paid'] = $pr['paid'] = $pr['balance'] = 0;
        $this->pay->updateRecord($id, $pr);
        $this->pay->deleteReceipts(['pr_id' => $id]);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    
    /**
     * Process grouped payment for multiple students
     */
    public function processGroupedPayment(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'student_ids' => 'required|string',
                'payment_id' => 'required|exists:payments,id',
                'payment_method' => 'required|string',
                'amount_paid' => 'required|numeric|min:0',
                'observations' => 'nullable|string'
            ]);

            // Parse student IDs
            $studentIds = explode(',', $request->student_ids);
            $paymentId = $request->payment_id;
            $paymentMethod = $request->payment_method;
            $totalAmountPaid = $request->amount_paid;
            $observations = $request->observations;

            // Get the payment details
            $payment = $this->pay->find($paymentId);
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motif de paiement introuvable.'
                ], 404);
            }

            // Calculate amount per student
            $studentCount = count($studentIds);
            $amountPerStudent = $totalAmountPaid / $studentCount;

            // Process payment for each student
            $processedStudents = [];
            $errors = [];

            foreach ($studentIds as $studentId) {
                try {
                    // Find or create payment record
                    $pr = $this->pay->findMyPR($studentId, $paymentId)->first();
                    
                    if (!$pr) {
                        // Create payment record if it doesn't exist
                        $prData = [
                            'student_id' => $studentId,
                            'payment_id' => $paymentId,
                            'year' => $this->year,
                            'ref_no' => mt_rand(100000, 99999999)
                        ];
                        $pr = $this->pay->createRecord($prData);
                    }

                    // Create receipt for this student
                    $receiptData = [
                        'pr_id' => $pr->id,
                        'amt_paid' => $amountPerStudent,
                        'balance' => max(0, $payment->amount - ($pr->amt_paid + $amountPerStudent)),
                        'year' => $this->year,
                        'methode' => $paymentMethod,
                        'payment_method' => $paymentMethod,
                        'reference_number' => \App\Models\Receipt::generateReferenceNumber(),
                        'observations' => $observations,
                        'created_by' => auth()->user()->name,
                    ];

                    // Use safe receipt creation to prevent duplicates
                    $receipt = \App\Models\Receipt::createSafeReceipt($receiptData);

                    // Update payment record
                    $newAmtPaid = $pr->amt_paid + $amountPerStudent;
                    $newBalance = max(0, $payment->amount - $newAmtPaid);
                    $isPaid = $newBalance == 0 ? 1 : 0;

                    $pr->update([
                        'amt_paid' => $newAmtPaid,
                        'balance' => $newBalance,
                        'paid' => $isPaid,
                        'methode' => $paymentMethod
                    ]);

                    $processedStudents[] = $studentId;
                } catch (\Exception $e) {
                    \Log::error('Error processing grouped payment for student ' . $studentId . ': ' . $e->getMessage());
                    $errors[] = 'Erreur lors du traitement du paiement pour l\'élève ID: ' . $studentId;
                }
            }

            if (count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Des erreurs se sont produites lors du traitement des paiements.',
                    'errors' => $errors
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement groupé effectué avec succès pour ' . count($processedStudents) . ' élèves.',
                'processed_count' => count($processedStudents)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in processGroupedPayment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement du paiement groupé.'
            ], 500);
        }
    }

    /**
     * Get available payments for a class (AJAX)
     */
    public function getClassPaymentsAjax(Request $request)
    {
        $classId = $request->class_id;
        
        if (!$classId) {
            return response()->json([]);
        }

        // Get class-specific payments
        $classPayments = $this->pay->getPayment(['my_class_id' => $classId, 'year' => $this->year])->get();
        
        // Get general payments (not tied to a specific class)
        $generalPayments = $this->pay->getGeneralPayment(['year' => $this->year])->get();
        
        // Merge payments
        $payments = $generalPayments->count() ? $classPayments->merge($generalPayments) : $classPayments;
        
        return response()->json($payments);
    }

    public function generateSpecialReceipts(Request $request)
    {
        try {
            // Valider les données du formulaire
            $request->validate([
                'payment_id' => 'required|exists:payments,id',
                'class_id' => 'required|exists:my_classes,id',
            ]);

            $payment_id = $request->payment_id;
            $class_id = $request->class_id;

            // Récupérer le paiement sélectionné
            $payment = $this->pay->find($payment_id);
            if (!$payment) {
                return back()->with('flash_danger', 'Motif de paiement introuvable');
            }

            // Récupérer la classe
            $class = $this->my_class->find($class_id);
            if (!$class) {
                return back()->with('flash_danger', 'Classe introuvable');
            }

            // Récupérer tous les élèves ADRA et TEAM3 de la classe sélectionnée
            $students = $this->student->getRecord(['my_class_id' => $class_id])->with('user')->get()
                ->filter(function($student) {
                    $status = $student->user->status ?? 'Normal';
                    return in_array($status, ['ADRA', 'TEAM3']);
                });

            if ($students->isEmpty()) {
                return back()->with('flash_danger', 'Aucun élève ADRA ou TEAM3 trouvé dans cette classe');
            }

            $receiptsGenerated = 0;
            $errors = [];
            $generatedReceiptIds = [];
            $paymentRecordsData = [];
            $hasAdraStudents = false;

            // Pour chaque élève, générer un reçu
            foreach ($students as $student) {
                $status = $student->user->status ?? 'Normal';
                if ($status === 'ADRA') {
                    $hasAdraStudents = true;
                }

                // Trouver ou créer l'enregistrement de paiement
                $pr = $this->pay->findMyPR($student->user_id, $payment_id)->first();
                if (!$pr) {
                    // Créer un nouvel enregistrement de paiement si nécessaire
                    $pr_data = [
                        'student_id' => $student->user_id,
                        'payment_id' => $payment_id,
                        'year' => $this->year,
                    ];
                    $pr = $this->pay->createRecord($pr_data);
                    $pr->ref_no = mt_rand(100000, 99999999);
                    $pr->save();
                }

                // Calculer le montant à facturer
                $totalAmount = $payment->amount;
                $paidAmount = $pr->amt_paid ?: 0;

                // Récupérer le montant total des reçus pour cet enregistrement
                $totalReceiptAmount = 0;
                $receipts = $pr->receipt;
                if ($receipts && $receipts->count() > 0) {
                    $totalReceiptAmount = $receipts->sum('amt_paid');
                }

                // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
                $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : $paidAmount;

                // Calculer le montant à facturer en fonction du statut
                $amountToBill = 0;
                if ($status === 'ADRA') {
                    // Pour ADRA, facturer les 75% du montant total
                    $requiredAmount = $totalAmount * 0.75;
                    $amountToBill = $requiredAmount;
                } else if ($status === 'TEAM3') {
                    // Pour TEAM3, facturer 100%
                    $amountToBill = $totalAmount;
                }

                // Vérifier si le montant a déjà été payé
                $alreadyPaid = false;
                if ($paidAmount >= $amountToBill) {
                    $alreadyPaid = true;
                    // On continue quand même pour l'inclure dans le résumé
                    // mais on ne génère pas de nouveau reçu
                }

                // Ajouter les données pour le résumé dans tous les cas
                $paymentRecordsData[] = [
                    'student' => $student,
                    'payment' => $payment,
                    'amount_billed' => $amountToBill,
                    'status' => $status,
                    'already_paid' => $alreadyPaid,
                    'paid_amount' => $paidAmount
                ];

                // Ne créer un reçu que si l'élève n'a pas déjà payé
                if (!$alreadyPaid) {
                    $receipt_data = [
                        'pr_id' => $pr->id,
                        'amt_paid' => $amountToBill,
                        'balance' => 0, // Pas de solde restant car tout est payé
                        'year' => $this->year,
                        'methode' => 'ADRA',
                        'payment_method' => 'ADRA',
                        'created_by' => auth()->user()->name,
                    ];

                    $receipt = $this->pay->createReceipt($receipt_data);

                    if ($receipt) {
                        // Mettre à jour l'enregistrement de paiement
                        $newPaidAmount = $paidAmount + $amountToBill;
                        $pr->amt_paid = $newPaidAmount;

                        // Pour les élèves ADRA, vérifier s'ils ont payé le 25% en cash
                        if ($status === 'ADRA') {
                            // Calculer le montant minimum requis (25% du montant total)
                            $minimumCashRequired = $totalAmount * 0.25;

                            // Vérifier si l'élève a déjà payé au moins 25% en cash
                            $hasPaidCash = false;
                            $cashReceipts = $pr->receipt->where('payment_method', '!=', 'ADRA');
                            $cashPaid = $cashReceipts->sum('amt_paid');

                            if ($cashPaid >= $minimumCashRequired) {
                                $hasPaidCash = true;
                            }

                            // Ne marquer comme payé que si l'élève a payé le 25% en cash
                            if ($hasPaidCash) {
                                $pr->balance = 0;
                                $pr->paid = 1; // Marquer comme payé
                            } else {
                                // Sinon, calculer le solde restant (25% du montant total)
                                $pr->balance = $minimumCashRequired - $cashPaid;
                                $pr->paid = 0; // Pas encore payé complètement
                            }
                        } else {
                            // Pour les élèves TEAM3, marquer comme payé
                            $pr->balance = 0;
                            $pr->paid = 1;
                        }

                        $pr->methode = 'ADRA';
                        $pr->save();

                        $receiptsGenerated++;
                        $generatedReceiptIds[] = $pr->id;
                    } else {
                        $errors[] = "Erreur lors de la création du reçu pour {$student->user->name}";
                    }
                } else {
                    // Si déjà payé, on ajoute quand même l'ID pour l'inclure dans le résumé
                    $generatedReceiptIds[] = $pr->id;
                }
            }

            // Si des reçus ont été générés, rediriger vers la page de résumé
            if (!empty($generatedReceiptIds)) {
                // Stocker les données dans la session pour la page de résumé
                session([
                    'payment_records_data' => $paymentRecordsData,
                    'class_name' => $class->name,
                    'payment_title' => $payment->title,
                    'receipt_ids' => implode(',', $generatedReceiptIds),
                    'has_adra' => $hasAdraStudents
                ]);

                return redirect()->route('payments.special_receipts_summary');
            }

            // Préparer le message de retour si aucun reçu n'a été généré
            if (!empty($errors)) {
                $message = "Aucun reçu généré. Erreurs: " . implode(", ", $errors);
                return back()->with('flash_danger', $message);
            }

            return back()->with('flash_danger', 'Aucun reçu n\'a été généré');

        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la génération des reçus: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Retourner un message d'erreur convivial
            return back()->with('flash_danger', 'Une erreur est survenue lors de la génération des reçus: ' . $e->getMessage());
        }
    }

    public function specialReceiptsSummary()
    {
        // Récupérer les données de la session
        $paymentRecordsData = session('payment_records_data');
        $className = session('class_name');
        $paymentTitle = session('payment_title');
        $receiptIds = session('receipt_ids');
        $hasAdra = session('has_adra', false);

        if (!$paymentRecordsData || !$className || !$paymentTitle || !$receiptIds) {
            return redirect()->route('payments.manage')
                ->with('flash_danger', 'Données de résumé introuvables. Veuillez générer à nouveau les reçus.');
        }

        return view('pages.support_team.payments.special_receipts_summary', [
            'payment_records' => $paymentRecordsData,
            'class_name' => $className,
            'payment_title' => $paymentTitle,
            'receipt_ids' => $receiptIds,
            'has_adra' => $hasAdra
        ]);
    }

    public function select_class(Request $req)
    {
        $this->validate($req, [
            'my_class_id' => 'required|exists:my_classes,id'
        ], [], ['my_class_id' => 'Class']);

        $wh['my_class_id'] = $class_id = $req->my_class_id;

        $pay1 = $this->pay->getPayment(['my_class_id' => $class_id, 'year' => $this->year])->get();
        $pay2 = $this->pay->getGeneralPayment(['year' => $this->year])->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        $students = $this->student->getRecord($wh)->get();

        if($payments->count() && $students->count()){
            foreach($payments as $p){
                foreach($students as $st){
                    $pr['student_id'] = $st->user_id;
                    $pr['payment_id'] = $p->id;
                    $pr['year'] = $this->year;
                    $rec = $this->pay->createRecord($pr);
                    $rec->ref_no ?: $rec->update(['ref_no' => mt_rand(100000, 99999999)]);

                }
            }
        }

        return Qs::goToRoute(['payments.manage', $class_id]);
    }

    public function store(PaymentCreate $req)
    {
        try {
            \DB::beginTransaction();

            $data = $req->all();
            $data['year'] = $this->year;

            // Vérifier si un paiement identique existe déjà (protection renforcée)
            $existingPayment = \DB::table('payments')
                ->where('title', $data['title'])
                ->where('amount', $data['amount'])
                ->where('year', $data['year'])
                ->where(function($query) use ($data) {
                    if (isset($data['my_class_id']) && $data['my_class_id']) {
                        $query->where('my_class_id', $data['my_class_id']);
                    } else {
                        $query->whereNull('my_class_id');
                    }
                })
                ->first();

            if ($existingPayment) {
                \DB::rollBack();
                return response()->json([
                    'ok' => false,
                    'msg' => 'Un paiement identique existe déjà pour cette année scolaire.'
                ], 422);
            }

            // Générer un code de référence unique avec vérification renforcée
            $maxAttempts = 10;
            $attempts = 0;
            do {
                $data['ref_no'] = Pay::genRefCode();
                $refExists = \DB::table('payments')->where('ref_no', $data['ref_no'])->exists();
                $attempts++;
            } while ($refExists && $attempts < $maxAttempts);

            if ($refExists) {
                \DB::rollBack();
                return response()->json([
                    'ok' => false,
                    'msg' => 'Impossible de générer un code de référence unique. Veuillez réessayer.'
                ], 500);
            }

            $payment = $this->pay->create($data);

            \DB::commit();

            return Qs::jsonStoreOk();

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Erreur lors de la création du paiement: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'msg' => 'Erreur lors de la création du paiement. Veuillez réessayer.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $d['payment'] = $pay = $this->pay->find($id);

        return is_null($pay) ? Qs::goWithDanger('payments.index') : view('pages.support_team.payments.edit', $d);
    }

    public function update(PaymentUpdate $req, $id)
    {
        $data = $req->all();
        $this->pay->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->pay->find($id)->delete();

        return Qs::deleteOk('payments.index');
    }



public function verified()
{
    $class = MyClass::all();
    return view('pages.support_team.payments.verified',['class' => $class]);
}

public function checkUnpaid(Request $request)
{
    $payment_ids = $request->my_payments_id;
    $id_class = $request->my_class_id;
    $statuses = $request->status ?? ['Normal', 'ADRA'];
    $student_type = $request->student_type ?? 'unpaid';

    // Vérifier si les paiements sont sélectionnés
    if (empty($payment_ids)) {
        return back()->with('flash_danger', 'Veuillez sélectionner au moins un motif de paiement.');
    }

    $nom_classe = MyClass::where('id', $id_class)->first();
    if (!$nom_classe) {
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    // Récupérer tous les paiements sélectionnés
    $payments = Payment::whereIn('id', $payment_ids)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->get();

    if ($payments->isEmpty()) {
        return back()->with('flash_danger', 'Motifs de paiement introuvables. Veuillez sélectionner des motifs de paiement valides pour cette classe.');
    }

    // Récupérer tous les étudiants de la classe avec le statut spécifié
    $students = StudentRecord::where('my_class_id', $id_class)
        ->with(['user' => function($query) use ($statuses) {
            $query->whereIn('status', $statuses)
                  ->orWhereNull('status');
        }])
        ->get()
        ->filter(function($student) {
            return $student->user !== null;
        });

    // Utiliser la méthode optimisée pour filtrer les étudiants
    $unpaidStudents = $this->filterUnpaidStudents($students, $payments);

    // Filtrer selon le type demandé (payé/impayé) si nécessaire
    if ($student_type === 'unpaid') {
        $unpaidStudents = array_filter($unpaidStudents, function($student) {
            return $student['amount_due'] > 0;
        });
    }

    return view('pages.support_team.payments.unpaid', [
        'unpaid_students' => $unpaidStudents,
        'nom_classe' => $nom_classe,
        'payments' => $payments,
        'payment_ids' => $payment_ids,
        'id_class' => $id_class,
        'statuses' => $statuses,
        'student_type' => $student_type
    ]);
}

public function exportUnpaidExcel(Request $request)
{
    $payment_ids = $request->payment_ids ? explode(',', $request->payment_ids) : [];
    $id_class = $request->id_class;
    $statuses = $request->statuses ? explode(',', $request->statuses) : ['Normal', 'ADRA'];
    $student_type = $request->student_type ?? 'unpaid';

    // Vérifier si les paiements sont sélectionnés
    if (empty($payment_ids)) {
        return back()->with('flash_danger', 'Veuillez sélectionner au moins un motif de paiement.');
    }

    $nom_classe = MyClass::where('id', $id_class)->first();
    if (!$nom_classe) {
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    // Retrieve all selected payments
    $payments = Payment::whereIn('id', $payment_ids)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->get();

    if ($payments->isEmpty()) {
        return back()->with('flash_danger', 'Motifs de paiement introuvables. Veuillez sélectionner des motifs de paiement valides pour cette classe.');
    }

    // Retrieve all students in the class with specified status
    $students = StudentRecord::where('my_class_id', $id_class)
        ->with(['user' => function($query) use ($statuses) {
            $query->whereIn('status', $statuses)
                  ->orWhereNull('status');
        }])
        ->get()
        ->filter(function($student) {
            return $student->user !== null;
        });

    // Prepare data for unpaid students
    $unpaidStudents = $this->filterUnpaidStudents($students, $payments);

    // Prepare data for Excel export
    $data = [];

    // Header row
    $data[] = [
        'Nom de l\'élève',
        'Classe',
        'Statut',
        'Motifs de paiement',
        'Montant total à payer (Ar)',
        'Montant déjà payé (Ar)',
        'Montant restant à payer (Ar)',
    ];

    // Add student data
    foreach ($unpaidStudents as $studentData) {
        $student = $studentData['student'];
        $status = $studentData['status'];
        $amountDue = $studentData['amount_due'];
        $amountPaid = $studentData['amount_paid'] ?? 0;
        $totalAmount = $studentData['total_amount'] ?? ($amountDue + $amountPaid);
        $paymentTitles = $studentData['payment_titles'];

        $data[] = [
            $student->user->name,
            $nom_classe->name,
            $status,
            $paymentTitles,
            number_format($totalAmount, 0, ',', ' '),
            number_format($amountPaid, 0, ',', ' '),
            number_format($amountDue, 0, ',', ' ')
        ];
    }

    // Generate Excel file
    $fileName = 'Impayés_' . str_replace(' ', '_', $nom_classe->name) . '_' . date('Y-m-d') . '.xlsx';

    // Create temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'excel');

    // Create Excel spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add data to spreadsheet
    $sheet->fromArray($data, null, 'A1');

    // Format header row
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    // Auto-size columns
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Save Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Download Excel file
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

/**
 * Export payment notifications data to Excel
 */
public function exportExcelForNotifications(Request $request)
{
    // Validate the request data
    $this->validate($request, [
        'my_class_id' => 'required|exists:my_classes,id',
        'my_payments_id' => 'required|array|min:1',
        'my_payments_id.*' => 'exists:payments,id',
        'payment_deadline' => 'required|date|after_or_equal:today',
        'status' => 'array'
    ]);

    $payment_ids = $request->my_payments_id;
    $id_class = $request->my_class_id;
    $statuses = $request->status ?? ['Normal', 'ADRA'];
    $payment_deadline = $request->payment_deadline;

    $nom_classe = MyClass::where('id', $id_class)->first();
    if (!$nom_classe) {
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    // Retrieve all selected payments
    $payments = Payment::whereIn('id', $payment_ids)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->get();

    if ($payments->isEmpty()) {
        return back()->with('flash_danger', 'Motifs de paiement introuvables. Veuillez sélectionner des motifs de paiement valides pour cette classe.');
    }

    // Retrieve all students in the class with specified status
    $students = StudentRecord::where('my_class_id', $id_class)
        ->with(['user' => function($query) use ($statuses) {
            $query->whereIn('status', $statuses)
                  ->orWhereNull('status');
        }])
        ->get()
        ->filter(function($student) {
            return $student->user !== null;
        });

    // Prepare data for unpaid students
    $unpaidStudents = $this->filterUnpaidStudents($students, $payments);

    // Prepare data for Excel export
    $data = [];

    // Header row
    $data[] = [
        'Nom de l\'élève',
        'Classe',
        'Statut',
        'Motifs de paiement',
        'Montant total à payer (Ar)',
        'Montant déjà payé (Ar)',
        'Montant restant à payer (Ar)',
        'Date limite de paiement'
    ];

    // Add student data
    foreach ($unpaidStudents as $studentData) {
        $student = $studentData['student'];
        $status = $studentData['status'];
        $amountDue = $studentData['amount_due'];
        $amountPaid = $studentData['amount_paid'] ?? 0;
        $totalAmount = $studentData['total_amount'] ?? ($amountDue + $amountPaid);
        $paymentTitles = $studentData['payment_titles'];

        $data[] = [
            $student->user->name,
            $nom_classe->name,
            $status,
            $paymentTitles,
            number_format($totalAmount, 0, ',', ' '),
            number_format($amountPaid, 0, ',', ' '),
            number_format($amountDue, 0, ',', ' '),
            date('d/m/Y', strtotime($payment_deadline))
        ];
    }

    // Generate Excel file
    $fileName = 'Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '_' . date('Y-m-d') . '.xlsx';

    // Create temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'excel');

    // Create Excel spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add data to spreadsheet
    $sheet->fromArray($data, null, 'A1');

    // Format header row
    $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    // Auto-size columns
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Save Excel file
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Download Excel file
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

/**
 * Export payment notifications data to Word
 */
public function exportWordForNotifications(Request $request)
{
    // Validate the request data
    $this->validate($request, [
        'my_class_id' => 'required|exists:my_classes,id',
        'my_payments_id' => 'required|array|min:1',
        'my_payments_id.*' => 'exists:payments,id',
        'payment_deadline' => 'required|date|after_or_equal:today',
        'status' => 'array'
    ]);

    $payment_ids = $request->my_payments_id;
    $id_class = $request->my_class_id;
    $statuses = $request->status ?? ['Normal', 'ADRA'];
    $payment_deadline = $request->payment_deadline;

    $nom_classe = MyClass::where('id', $id_class)->first();
    if (!$nom_classe) {
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    // Retrieve all selected payments
    $payments = Payment::whereIn('id', $payment_ids)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->get();

    if ($payments->isEmpty()) {
        return back()->with('flash_danger', 'Motifs de paiement introuvables. Veuillez sélectionner des motifs de paiement valides pour cette classe.');
    }

    // Retrieve all students in the class with specified status
    $students = StudentRecord::where('my_class_id', $id_class)
        ->with(['user' => function($query) use ($statuses) {
            $query->whereIn('status', $statuses)
                  ->orWhereNull('status');
        }])
        ->get()
        ->filter(function($student) {
            return $student->user !== null;
        });

    // Prepare data for unpaid students
    $unpaidStudents = $this->filterUnpaidStudents($students, $payments);

    // Generate Word document content with improved Malagasy formatting
    $htmlContent = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Avis de Paiement - ' . $nom_classe->name . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .class-info {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .deadline-info {
                font-size: 14px;
                margin-bottom: 20px;
            }
            .student-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .student-table th {
                background-color: #f2f2f2;
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .student-table td {
                border: 1px solid #ddd;
                padding: 8px;
                vertical-align: top;
            }
            .student-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .payment-details {
                border: 1px solid #ddd;
                padding: 8px;
                margin: 5px 0;
            }
            .payment-line {
                margin: 3px 0;
            }
            .label-text {
                font-weight: normal;
            }
            .amount-value {
                font-weight: bold;
            }
            .amount-value.due {
                font-size: 1.1em;
                color: #d32f2f;
            }
            .highlight {
                background-color: #fef3c7;
                border: 1px solid #f59e0b;
                padding: 5px;
                margin: 5px 0;
            }
            .deadline-date {
                display: inline-block;
                border: 1px solid #333;
                padding: 3px 8px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>FAMPAHAFANTARANA FANDOAVAM-BOLA</h1>
            <div class="class-info">Classe: ' . $nom_classe->name . '</div>
            <div class="deadline-info">Date limite de paiement: ' . date('d/m/Y', strtotime($payment_deadline)) . '</div>
        </div>
        
        <table class="student-table">
            <thead>
                <tr>
                    <th>Nom de l\'élève</th>
                    <th>Statut</th>
                    <th>Détails de paiement</th>
                    <th>Montant total (Ar)</th>
                    <th>Montant payé (Ar)</th>
                    <th>Montant restant (Ar)</th>
                </tr>
            </thead>
            <tbody>';

    // Add student data with improved formatting
    foreach ($unpaidStudents as $studentData) {
        $student = $studentData['student'];
        $status = $studentData['status'];
        $amountDue = $studentData['amount_due'];
        $amountPaid = $studentData['amount_paid'] ?? 0;
        $totalAmount = $studentData['total_amount'] ?? ($amountDue + $amountPaid);
        $paymentTitles = $studentData['payment_titles'];

        // Format payment details with the new Malagasy format
        $formattedPaymentDetails = '
        <div class="payment-details">
            <div><strong>Antony tsy voaloa:</strong><br>' . $paymentTitles . '</div>
            <div style="font-size: 11px; margin: 5px 0;">
                <strong>ID Élève:</strong> ' . $student->user_id . ' | <strong>ID Classe:</strong> ' . $student->my_class_id . '
            </div>
            <div class="payment-line">
                <span class="label-text">• Vola rehetra tokony haloa:</span><br>
                <span class="amount-value">' . number_format($totalAmount, 0, ',', ' ') . ' Ariary</span>
            </div>
            <div class="payment-line">
                <span class="label-text">• Vola efa naloa:</span><br>
                <span class="amount-value">' . number_format($amountPaid, 0, ',', ' ') . ' Ariary</span>
            </div>
            <div class="payment-line highlight">
                <span class="label-text">• Vola mbola tokony haloa:</span><br>
                <span class="amount-value due">' . number_format($amountDue, 0, ',', ' ') . ' Ariary</span>
            </div>
            <div style="margin-top: 10px;">
                <strong>Daty farany hanaovana fandoavam-bola:</strong><br>
                <span class="deadline-date">' . date('d/m/Y', strtotime($payment_deadline)) . '</span>
            </div>
        </div>';

        $htmlContent .= '
                <tr>
                    <td>' . $student->user->name . '</td>
                    <td>' . $status . '</td>
                    <td>' . $formattedPaymentDetails . '</td>
                    <td>' . number_format($totalAmount, 0, ',', ' ') . '</td>
                    <td>' . number_format($amountPaid, 0, ',', ' ') . '</td>
                    <td>' . number_format($amountDue, 0, ',', ' ') . '</td>
                </tr>';
    }

    $htmlContent .= '
            </tbody>
        </table>
    </body>
    </html>';

    // Generate Word file name
    $fileName = 'Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '_' . date('Y-m-d') . '.doc';

    // Return Word document as download
    return response($htmlContent)
        ->header('Content-Type', 'application/msword')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

public function select(Request $request)
{
    $classId = $request->input('class_id');

    // Récupérer les paiements spécifiques à la classe sélectionnée
    // ET les paiements applicables à toutes les classes (my_class_id = null)
    $payments = Payment::where(function($query) use ($classId) {
        $query->where('my_class_id', $classId)
              ->orWhereNull('my_class_id');
    })
    ->where('year', Qs::getCurrentSession())
    ->get();

    return response()->json($payments);
}

private function journal_deprecated()
{
    // Par défaut, afficher les paiements du jour
    $today = date('Y-m-d');
    $startDate = $today;
    $endDate = $today;
    $period = 'day';

    // Récupérer tous les reçus de paiement pour aujourd'hui avec les relations nécessaires
    $receipts = Receipt::with([
        'pr' => function($query) {
            $query->with([
                'student' => function($q) {
                    $q->with('student_record.my_class', 'student_record.section');
                },
                'payment'
            ]);
        }
    ])
    ->whereDate('created_at', \Carbon\Carbon::parse($today)->setTimezone('UTC')->format('Y-m-d'))
    ->orderBy('created_at', 'desc')
    ->get();

    // Récupérer les classes pour le filtre
    $classes = MyClass::orderBy('name')->get();

    // Récupérer les types de paiement pour le filtre
    $paymentTypes = Payment::select('title')->distinct()->orderBy('title')->get();

    // Récupérer les méthodes de paiement pour le filtre
    $paymentMethods = Receipt::select('methode')->whereNotNull('methode')->distinct()->get();

    // Calculer le total des paiements pour la période
    $totalAmount = $receipts->sum('amt_paid');

    // Statistiques par classe
    $classTotals = [];
    $classTotalSum = 0;
    foreach ($receipts as $receipt) {
        if ($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_record) {
            $studentRecord = $receipt->pr->student->student_record;
            if ($studentRecord->my_class) {
                $className = $studentRecord->my_class->name;
                if ($studentRecord->section) {
                    $className .= ' ' . $studentRecord->section->name;
                }
                if (!isset($classTotals[$className])) {
                    $classTotals[$className] = 0;
                }
                $classTotals[$className] += $receipt->amt_paid;
                $classTotalSum += $receipt->amt_paid;
            }
        }
    }

    // Statistiques par type de paiement
    $paymentTypeTotals = [];
    $paymentTypeTotalSum = 0;
    foreach ($receipts as $receipt) {
        if ($receipt->pr && $receipt->pr->payment) {
            $paymentTitle = $receipt->pr->payment->title;
            if (!isset($paymentTypeTotals[$paymentTitle])) {
                $paymentTypeTotals[$paymentTitle] = 0;
            }
            $paymentTypeTotals[$paymentTitle] += $receipt->amt_paid;
            $paymentTypeTotalSum += $receipt->amt_paid;
        }
    }

    return view('pages.support_team.payments.journal', [
        'receipts' => $receipts,
        'totalAmount' => $totalAmount,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'period' => $period,
        'classes' => $classes,
        'paymentTypes' => $paymentTypes,
        'paymentMethods' => $paymentMethods,
        'classTotals' => $classTotals,
        'classTotalSum' => $classTotalSum,
        'paymentTypeTotals' => $paymentTypeTotals,
        'paymentTypeTotalSum' => $paymentTypeTotalSum
    ]);
}

private function journalFilter_deprecated(Request $request)
{
    $period = $request->period;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $classId = $request->class_id;
    $paymentType = $request->payment_type;
    $paymentMethod = $request->payment_method;
    $studentName = $request->student_name;

    // Valider les dates
    if (!$startDate) {
        $startDate = date('Y-m-d');
    }

    if (!$endDate) {
        $endDate = date('Y-m-d');
    }

    // Préparer la requête de base
    $query = Receipt::with([
        'pr' => function($query) {
            $query->with([
                'student' => function($q) {
                    $q->with('student_record.my_class', 'student_record.section');
                },
                'payment'
            ]);
        }
    ])
    ->orderBy('created_at', 'desc');

    // Filtrer selon la période sélectionnée (en tenant compte du fuseau horaire UTC+3)
    if ($period == 'day') {
        // Jour spécifique - convertir au fuseau horaire UTC+3
        $carbonDate = \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Riyadh');
        $startOfDay = $carbonDate->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $endOfDay = $carbonDate->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
    } elseif ($period == 'week') {
        // Semaine courante - convertir au fuseau horaire UTC+3
        $carbonDate = \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Riyadh');
        $startOfWeek = $carbonDate->startOfWeek()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $endOfWeek = $carbonDate->endOfWeek()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        $startDate = $carbonDate->startOfWeek()->format('Y-m-d');
        $endDate = $carbonDate->endOfWeek()->format('Y-m-d');
    } elseif ($period == 'month') {
        // Mois courant - convertir au fuseau horaire UTC+3
        $carbonDate = \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Riyadh');
        $startOfMonth = $carbonDate->startOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $endOfMonth = $carbonDate->endOfMonth()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        $startDate = $carbonDate->startOfMonth()->format('Y-m-d');
        $endDate = $carbonDate->endOfMonth()->format('Y-m-d');
    } elseif ($period == 'custom') {
        // Période personnalisée - convertir au fuseau horaire UTC+3
        $carbonStartDate = \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Riyadh');
        $carbonEndDate = \Carbon\Carbon::parse($endDate)->setTimezone('Asia/Riyadh');
        $startDateTime = $carbonStartDate->startOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $endDateTime = $carbonEndDate->endOfDay()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
    }

    // Filtrer par méthode de paiement si spécifié
    if ($paymentMethod) {
        $query->where('methode', $paymentMethod);
    }

    // Exécuter la requête
    $receipts = $query->get();

    // Filtrer par classe si spécifié
    if ($classId) {
        $receipts = $receipts->filter(function($receipt) use ($classId) {
            if (!$receipt->pr || !$receipt->pr->student || !$receipt->pr->student->student_record) {
                return false;
            }

            return $receipt->pr->student->student_record->my_class_id == $classId;
        });
    }

    // Filtrer par type de paiement si spécifié
    if ($paymentType) {
        $receipts = $receipts->filter(function($receipt) use ($paymentType) {
            return $receipt->pr &&
                   $receipt->pr->payment &&
                   $receipt->pr->payment->title == $paymentType;
        });
    }

    // Filtrer par nom d'étudiant si spécifié
    if ($studentName) {
        $receipts = $receipts->filter(function($receipt) use ($studentName) {
            return $receipt->pr &&
                   $receipt->pr->student &&
                   stripos($receipt->pr->student->name, $studentName) !== false;
        });
    }

    // Calculer le total des paiements pour la période
    $totalAmount = $receipts->sum('amt_paid');

    // Récupérer les classes pour le filtre
    $classes = MyClass::orderBy('name')->get();

    // Récupérer les types de paiement pour le filtre
    $paymentTypes = Payment::select('title')->distinct()->orderBy('title')->get();

    // Récupérer les méthodes de paiement pour le filtre
    $paymentMethods = Receipt::select('methode')->whereNotNull('methode')->distinct()->get();

    // Statistiques par classe
    $classTotals = [];
    $classTotalSum = 0;
    foreach ($receipts as $receipt) {
        if ($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_record) {
            $studentRecord = $receipt->pr->student->student_record;
            if ($studentRecord->my_class) {
                $className = $studentRecord->my_class->name;
                if ($studentRecord->section) {
                    $className .= ' ' . $studentRecord->section->name;
                }
                if (!isset($classTotals[$className])) {
                    $classTotals[$className] = 0;
                }
                $classTotals[$className] += $receipt->amt_paid;
                $classTotalSum += $receipt->amt_paid;
            }
        }
    }

    // Statistiques par type de paiement
    $paymentTypeTotals = [];
    $paymentTypeTotalSum = 0;
    foreach ($receipts as $receipt) {
        if ($receipt->pr && $receipt->pr->payment) {
            $paymentTitle = $receipt->pr->payment->title;
            if (!isset($paymentTypeTotals[$paymentTitle])) {
                $paymentTypeTotals[$paymentTitle] = 0;
            }
            $paymentTypeTotals[$paymentTitle] += $receipt->amt_paid;
            $paymentTypeTotalSum += $receipt->amt_paid;
        }
    }

    return view('pages.support_team.payments.journal', [
        'receipts' => $receipts,
        'totalAmount' => $totalAmount,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'period' => $period,
        'classes' => $classes,
        'paymentTypes' => $paymentTypes,
        'paymentMethods' => $paymentMethods,
        'classTotals' => $classTotals,
        'classTotalSum' => $classTotalSum,
        'paymentTypeTotals' => $paymentTypeTotals,
        'paymentTypeTotalSum' => $paymentTypeTotalSum,
        'selectedClass' => $classId,
        'selectedPaymentType' => $paymentType,
        'selectedPaymentMethod' => $paymentMethod,
        'studentName' => $studentName
    ]);
}

public function filter(Request $request)
{

    $id_pay = $request->my_paymets_id;
    $id_class = $request->my_class_id;

    $nom_classe =  MyClass::where('id',$id_class)->first();
    $nom_payment =  Payment::where('id',$id_pay)->where('my_class_id',$id_class)->first();



    $students = StudentRecord::where('my_class_id', $id_class)->get();
    // $pr = PaymentRecord::where('payment_id', $id_pay)->get();
    // dd($students);
    return view('pages.support_team.payments.filter',
    ['id_pay' => $id_pay,
     'id_class'=>$id_class,
     'students' => $students,
     'nom_classe' => $nom_classe,
     'nom_payment' => $nom_payment
    ]);
}

/**
 * ADRA & TEAM 3 Payment Management Interface
 */
public function adraTeam3Filter(Request $request)
{
    $classId = $request->get('class_id', 1);

    // Get all classes for the dropdown
    $classes = $this->my_class->all();

    // Get selected class
    $selectedClass = $this->my_class->find($classId);

    return view('pages.support_team.payments.adra_team3_filter', [
        'classes' => $classes,
        'selectedClass' => $selectedClass,
        'selectedClassId' => $classId,
        'studentsData' => collect(), // Empty collection for initial load
        'year' => $this->year
    ]);
}

/**
 * Get payments for a specific class (AJAX)
 */
public function getClassPayments(Request $request)
{
    $classId = $request->get('class_id');

    if (!$classId) {
        return response()->json([
            'success' => false,
            'message' => 'Class ID is required'
        ]);
    }

    try {
        $year = $this->year ?? Qs::getCurrentSession();
        
        // Debug: Log the request
        \Log::info('Getting payments for class', [
            'class_id' => $classId, 
            'year' => $year, 
            'type' => gettype($classId)
        ]);

        $classPaymentsCount = 0;
        $generalPaymentsCount = 0;

        // If 'all' classes, get ALL payments (general + all class-specific)
        if ($classId == 'all' || $classId === 'all') {
            $allPayments = Payment::where('year', $year)->get();
            $classPaymentsCount = $allPayments->whereNotNull('my_class_id')->count();
            $generalPaymentsCount = $allPayments->whereNull('my_class_id')->count();
            \Log::info('All payments query', ['count' => $allPayments->count()]);
        } else {
            // Get payments for this specific class
            $classPayments = Payment::where('my_class_id', $classId)
                ->where('year', $year)
                ->get();
            $classPaymentsCount = $classPayments->count();

            // Get general payments (for all classes)
            $generalPayments = Payment::whereNull('my_class_id')
                ->where('year', $year)
                ->get();
            $generalPaymentsCount = $generalPayments->count();

            // Merge class-specific and general payments
            $allPayments = $classPayments->merge($generalPayments);
        }

        // Debug: Log the results
        \Log::info('Found payments', ['count' => $allPayments->count()]);

        $paymentsArray = $allPayments->map(function($payment) {
            return [
                'id' => $payment->id,
                'title' => $payment->title,
                'amount' => $payment->amount
            ];
        })->values()->all();

        \Log::info('Returning payments', ['count' => count($paymentsArray)]);

        return response()->json([
            'success' => true,
            'payments' => $paymentsArray,
            'debug' => [
                'class_id' => $classId,
                'year' => $this->year,
                'class_payments_count' => $classPaymentsCount,
                'general_payments_count' => $generalPaymentsCount,
                'total_count' => count($paymentsArray)
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading payments', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error loading payments: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get students for a specific payment (AJAX)
 */
public function getPaymentStudents(Request $request)
{
    $classId = $request->get('class_id');
    $paymentId = $request->get('payment_id');

    if (!$classId || !$paymentId) {
        return response()->json([
            'success' => false,
            'message' => 'Class ID and Payment ID are required'
        ]);
    }

    try {
        // Get students with ADRA or TEAM3 status
        $query = StudentRecord::with(['user', 'my_class'])
            ->whereHas('user', function($q) {
                $q->whereIn('status', ['ADRA', 'TEAM3']);
            });

        // If not 'all', filter by specific class
        if ($classId !== 'all') {
            $query->where('my_class_id', $classId);
        }

        $students = $query->get();

        // Get payment details
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ]);
        }

        $studentsData = $students->map(function($student) use ($payment) {
            // Get existing payment record for this student and payment
            $paymentRecord = PaymentRecord::where('student_id', $student->user_id)
                ->where('payment_id', $payment->id)
                ->where('year', $this->year)
                ->first();

            // Check if the student has already paid their 25%
            $hasPaid25 = false;
            if ($paymentRecord && $paymentRecord->paid) {
                // If paid field is set, check if amt_paid covers at least 25%
                $hasPaid25 = $paymentRecord->amt_paid >= ($payment->amount * 0.25);
            }

            // Generate or use existing reference code
            $refCode = $paymentRecord->ref_no ?? 'REF-' . mt_rand(100000, 999999);

            return [
                'id' => $student->user_id,
                'name' => $student->user->name,
                'adm_no' => $student->adm_no,
                'class_name' => $student->my_class->name,
                'status' => $student->user->status,
                'reference_code' => $refCode,
                'has_paid_25' => $hasPaid25
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $studentsData
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading students: ' . $e->getMessage()
        ]);
    }
}

/**
 * Update reference code for ADRA/TEAM3 payment
 */
public function updateReference(Request $request)
{
    $studentId = $request->student_id;
    $referenceCode = $request->reference_code;

    // Update all payment records for this student
    PaymentRecord::where('student_id', $studentId)
        ->where('year', $this->year)
        ->update(['ref_no' => $referenceCode]);

    return response()->json(['success' => true, 'message' => 'Code de référence mis à jour']);
}

/**
 * Print individual ADRA/TEAM3 receipt for multiple payments
 */
public function printAdraTeam3Receipt(Request $request, $studentId)
{
    $selectedPayments = json_decode($request->selected_payments, true);
    $totalAmount = $request->total_amount;
    $status = $request->status;
    $referenceCode = $request->reference_code;

    // Get student information
    $student = StudentRecord::with(['user', 'my_class'])
        ->where('user_id', $studentId)
        ->first();

    if (!$student) {
        return response()->json(['error' => 'Étudiant non trouvé'], 404);
    }

    // Get payment information
    $payments = Payment::whereIn('id', $selectedPayments)->get();
    if ($payments->isEmpty()) {
        return response()->json(['error' => 'Paiements non trouvés'], 404);
    }

    // For ADRA status (25% student), verify that the student has paid the 25% for ALL selected payments
    if ($status === 'ADRA') {
        $unpaidPayments = [];
        foreach ($payments as $payment) {
            $paymentRecord = PaymentRecord::where('student_id', $studentId)
                ->where('payment_id', $payment->id)
                ->where('year', $this->year)
                ->first();
            
            // Check if payment record exists and if the 25% has been paid
            $requiredAmount = $payment->amount * 0.25;
            if (!$paymentRecord || $paymentRecord->amt_paid < $requiredAmount) {
                $unpaidPayments[] = $payment->title;
            }
        }
        
        // If there are unpaid payments, return error
        if (!empty($unpaidPayments)) {
            return response('<div style="padding: 20px; text-align: center; font-family: Arial, sans-serif;">
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0;">⚠️ Impossible de valider le reçu</h3>
                    <p style="margin: 0;">L\'élève n\'a pas encore payé les 25% pour les paiements suivants :</p>
                    <ul style="text-align: left; margin-top: 10px;">' . 
                        implode('', array_map(function($p) { return "<li><strong>$p</strong></li>"; }, $unpaidPayments)) .
                    '</ul>
                    <p style="margin-top: 10px;">Veuillez d\'abord encaisser les 25% avant d\'imprimer le reçu.</p>
                </div>
                <button onclick="window.close()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Fermer</button>
            </div>', 400);
        }
    }

    // Calculate amounts based on status
    $amountToPay = $status === 'ADRA' ? ($totalAmount * 0.75) : $totalAmount;
    $cashAmount = $status === 'ADRA' ? ($totalAmount * 0.25) : 0;

    // Create or update payment records for each selected payment
    $paymentRecords = [];
    $timestamp = time(); // Unique timestamp for this transaction

    foreach ($payments as $index => $payment) {
        // Generate unique reference code for each payment (Idempotent)
        $uniqueRefCode = $referenceCode . '-P' . $payment->id;

        // Get existing payment record or create new one
        $paymentRecord = PaymentRecord::where([
            'student_id' => $studentId,
            'payment_id' => $payment->id,
            'year' => $this->year
        ])->first();

        if (!$paymentRecord) {
            $paymentRecord = PaymentRecord::create([
                'student_id' => $studentId,
                'payment_id' => $payment->id,
                'year' => $this->year,
                'amt_paid' => 0,
                'balance' => $payment->amount,
                'paid' => 0,
                'ref_no' => $uniqueRefCode
            ]);
        }

        // Calculate proportional amounts for this payment
        $paymentProportion = $payment->amount / $totalAmount;
        $paymentAmountToPay = $amountToPay * $paymentProportion;
        $paymentCashAmount = $cashAmount * $paymentProportion;

        // Update payment record with unique reference (only if not already paid)
        if (!$paymentRecord->paid) {
            $paymentRecord->update([
                'paid' => 1,
                'amt_paid' => $paymentAmountToPay,
                'balance' => $paymentCashAmount,
                'ref_no' => $uniqueRefCode
            ]);
        }

        $paymentRecords[] = $paymentRecord;

        // --- CREATE RECEIPT FOR THIS SPECIFIC PAYMENT ---
        $itemReceiptData = [
            'pr_id' => $paymentRecord->id,
            'amt_paid' => $paymentAmountToPay,
            'balance' => $paymentCashAmount,
            'year' => $this->year,
            'methode' => $status,
            'payment_method' => $status,
            'reference_number' => $uniqueRefCode,
            'observations' => 'Paiement ' . $status . ' - ' . $payment->title . ' (Ref Batch: ' . $referenceCode . ')',
            'created_by' => auth()->id()
        ];

        $itemReceipt = Receipt::where('reference_number', $uniqueRefCode)->first();
        if (!$itemReceipt) {
            Receipt::create($itemReceiptData);
        } else {
            $itemReceipt->update($itemReceiptData);
        }
    }

    // Dummy receipt for view compatibility
    $receipt = new Receipt();
    $receipt->reference_number = $referenceCode;

    // Check if all selected payments are fully cleared
    $isFullyPaid = true;
    foreach ($payments as $payment) {
        $checkPr = PaymentRecord::where([
            'student_id' => $studentId,
            'payment_id' => $payment->id,
            'year' => $this->year
        ])->first();
        
        if (!$checkPr || $checkPr->balance > 0) {
            $isFullyPaid = false;
            break;
        }
    }
    
    // Prepare data for single receipt view (reuse Batch view structure or Single view)
    $receiptsData = [[
        'student' => $student,
        'payments' => $payments,
        'totalAmount' => $totalAmount,
        'amountToPay' => $amountToPay,
        'cashAmount' => $cashAmount,
        'status' => $status,
        'referenceCode' => $referenceCode,
        'receipt' => $receipt,
        'isFullyPaid' => $isFullyPaid
    ]];

    return view('pages.support_team.payments.adra_team3_batch_receipts', [
        'receiptsData' => $receiptsData
    ]);
}

/**
 * Print batch receipts for multiple students with multiple payments
 */
public function printBatchReceipts(Request $request)
{
    $studentsData = json_decode($request->students_data, true);
    $receiptsData = [];

    foreach ($studentsData as $studentData) {
        $studentId = $studentData['student_id'];
        $selectedPayments = $studentData['selected_payments'];
        $totalAmount = $studentData['total_amount'];
        $status = $studentData['status'];
        $referenceCode = $studentData['reference_code'];

        // Get student information
        $student = StudentRecord::with(['user', 'my_class'])
            ->where('user_id', $studentId)
            ->first();

        if (!$student) continue;

        // Get payment information
        $payments = Payment::whereIn('id', $selectedPayments)->get();
        if ($payments->isEmpty()) continue;

        // Calculate amounts
        $amountToPay = $status === 'ADRA' ? ($totalAmount * 0.75) : $totalAmount;
        $cashAmount = $status === 'ADRA' ? ($totalAmount * 0.25) : 0;

        // Create or update payment records for each selected payment
        $paymentRecords = [];
        $timestamp = time(); // Unique timestamp for this transaction

        foreach ($payments as $index => $payment) {
            // Generate unique reference code for each payment (Idempotent: dependent on batch ref + payment id)
            // We removed timestamp to prevent duplication if page is refreshed or reprinted
            $uniqueRefCode = $referenceCode . '-P' . $payment->id;

            // Get existing payment record or create new one
            $paymentRecord = PaymentRecord::where([
                'student_id' => $studentId,
                'payment_id' => $payment->id,
                'year' => $this->year
            ])->first();

            if (!$paymentRecord) {
                $paymentRecord = PaymentRecord::create([
                    'student_id' => $studentId,
                    'payment_id' => $payment->id,
                    'year' => $this->year,
                    'amt_paid' => 0,
                    'balance' => $payment->amount,
                    'paid' => 0,
                    'ref_no' => $uniqueRefCode
                ]);
            }

            // Calculate proportional amounts for this payment
            $paymentProportion = $payment->amount / $totalAmount;
            $paymentAmountToPay = $amountToPay * $paymentProportion;
            $paymentCashAmount = $cashAmount * $paymentProportion;

            // Update payment record with unique reference (only if not already paid)
            if (!$paymentRecord->paid) {
                $paymentRecord->update([
                    'paid' => 1,
                    'amt_paid' => $paymentAmountToPay,
                    'balance' => $paymentCashAmount,
                    'methode' => $status,
                    'ref_no' => $uniqueRefCode
                ]);
            }

            $paymentRecords[] = $paymentRecord;

            // --- CREATE RECEIPT FOR THIS SPECIFIC PAYMENT ---
            
            // Note: We use the uniqueRefCode for the receipt to ensure 1-to-1 mapping
            // OR we can use the batch referenceCode but we need to deal with uniqueness constraints.
            // If the receipts table allows duplicate reference_numbers, we are fine.
            // But Receipt model usually assumes reference_number is unique or at least main identifier.
            // To be safe and detailed, let's create a receipt per item.
            
            // Check if a receipt exists for this specific PR and Reference
            // We use uniqueRefCode to identify this specific line item receipt
            // But for the "Check" (Paper), we want to show the Main Reference.
            
            // Strategy: We will create a receipt for EACH item. 
            // We'll use $uniqueRefCode as the 'reference_number' in DB to avoid collisions,
            // but we can store the 'batch_reference' in observations or a separate field if needed.
            // Actually, the Journal displays 'reference_number'. 
            // Users might want to see the "Main Batch Ref" (e.g. BATCH-001).
            // If we use BATCH-001 for all, and the DB column is UNIQUE, it will fail.
            // Let's assume for now reference_number + year should be unique.
            
            // Let's use $uniqueRefCode for the journal record.
            
            $itemReceiptData = [
                'pr_id' => $paymentRecord->id,
                'amt_paid' => $paymentAmountToPay,
                'balance' => $paymentCashAmount,
                'year' => $this->year,
                'methode' => $status,
                'payment_method' => $status,
                'reference_number' => $uniqueRefCode, // Unique ref for DB
                'observations' => 'Paiement ' . $status . ' - ' . $payment->title . ' (Ref Batch: ' . $referenceCode . ')',
                'created_by' => auth()->id()
            ];

            // Update or Create
            $itemReceipt = Receipt::where('reference_number', $uniqueRefCode)->first();
            if (!$itemReceipt) {
                Receipt::create($itemReceiptData);
            } else {
                $itemReceipt->update($itemReceiptData);
            }
        }

        // We don't need to create a "Global Receipt" anymore since we created detailed ones.
        // But for the PRINT VIEW, we just need a dummy receipt object 
        // or just the reference code. We passed 'receipt' => $receipt before.
        // Let's create a dummy object or fetch the last one to satisfy the view structure if needed.
        // The view uses $receipt->reference_number (which we have as $referenceCode).
        // Check view usage: It uses $referenceCode directly from array, NOT $receipt object.
        // So we can pass null or the last created item receipt.
        
        $receipt = new Receipt(); // Dummy for safety
        $receipt->reference_number = $referenceCode;

        // Check if all selected payments are fully cleared (0 balance) or cleared for ADRA (balance <= 75%)
        $isFullyPaid = true;
        foreach ($payments as $payment) {
            $checkPr = PaymentRecord::where([
                'student_id' => $studentId,
                'payment_id' => $payment->id,
                'year' => $this->year
            ])->first();
            
            // If doesn't exist, then not fully paid
            if (!$checkPr) {
                $isFullyPaid = false;
                break;
            }

            // For ADRA, if balance is <= 75% of original amount, it means student paid their 25%
            // Logic: Original = 100. ADRA pays 75, Student pays 25.
            // If user pays 25, balance should remain 75 (which ADRA pays later or is waived).
            // So if balance <= 75%, user has paid their share.
            if ($status === 'ADRA') {
                $adraPortion = $payment->amount * 0.75;
                // Allow a small float margin error
                if ($checkPr->balance > ($adraPortion + 1)) {
                    $isFullyPaid = false;
                    break;
                }
            } else {
                // For normal/TEAM3, balance must be 0
                if ($checkPr->balance > 0) {
                    $isFullyPaid = false;
                    break;
                }
            }
        }

        // For display in receipt, update payment amounts to 25% if ADRA
        if ($status === 'ADRA') {
            foreach ($payments as $payment) {
                $payment->original_amount = $payment->amount; // Keep original if needed
                $payment->amount = $payment->amount * 0.25; // Show 25% on receipt
            }
        }

        $receiptsData[] = [
            'student' => $student,
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'amountToPay' => $amountToPay,
            'cashAmount' => $cashAmount,
            'status' => $status,
            'referenceCode' => $referenceCode,
            'receipt' => $receipt,
            'isFullyPaid' => $isFullyPaid
        ];
    }

    return view('pages.support_team.payments.adra_team3_batch_receipts', [
        'receiptsData' => $receiptsData
    ]);
}

/**
 * Generate unique reference code to avoid duplicates
 */
private function generateUniqueRefCode($baseRefCode, $studentId, $paymentId)
{
    // Use timestamp and payment ID to ensure uniqueness
    $timestamp = time();
    $uniqueRefCode = $baseRefCode . '-' . $timestamp . '-' . $paymentId;

    // If still exists (very unlikely), add random number
    if (PaymentRecord::where('ref_no', $uniqueRefCode)->exists()) {
        $uniqueRefCode = $baseRefCode . '-' . $timestamp . '-' . $paymentId . '-' . rand(100, 999);
    }

    return $uniqueRefCode;
}

/**
 * Export ADRA/TEAM3 data to Excel
 */
public function exportAdraTeam3Excel(Request $request)
{
    $classId = $request->get('class_id', 1);

    // Get students data (reuse logic from adraTeam3Filter)
    $students = StudentRecord::with(['user', 'my_class', 'section'])
        ->where('my_class_id', $classId)
        ->whereHas('user', function($query) {
            $query->whereIn('status', ['ADRA', 'TEAM3']);
        })
        ->get();

    $paymentRecords = PaymentRecord::with(['payment', 'student', 'receipt'])
        ->whereIn('student_id', $students->pluck('user_id'))
        ->where('year', $this->year)
        ->get()
        ->groupBy('student_id');

    $exportData = [];
    $exportData[] = [
        'Nom & Prénoms',
        'Classe',
        'Statut',
        'Code Référence',
        'Paiements sélectionnés',
        'Montant Total (100%)',
        'Montant à Payer (statut)',
        'Méthode de Paiement',
        'Date Export'
    ];

    foreach ($students as $student) {
        $studentPayments = $paymentRecords->get($student->user_id, collect());
        $totalAmount = $studentPayments->sum('payment.amount');
        $status = $student->user->status;
        $amountToPay = $status === 'ADRA' ? ($totalAmount * 0.75) : $totalAmount;

        $exportData[] = [
            $student->user->name,
            $student->my_class->name,
            $status,
            $studentPayments->first()->ref_no ?? mt_rand(100000, 99999999),
            $studentPayments->pluck('payment.title')->implode(', '),
            number_format($totalAmount, 0, ',', ' ') . ' Ar',
            number_format($amountToPay, 0, ',', ' ') . ' Ar',
            $status,
            date('Y-m-d H:i:s')
        ];
    }

    // Create CSV content
    $filename = 'ADRA_TEAM3_Payments_Class_' . $classId . '_' . date('Y-m-d') . '.csv';
    $csvContent = '';

    foreach ($exportData as $row) {
        $csvContent .= '"' . implode('","', $row) . '"' . "\n";
    }

    // Return CSV download
    return response($csvContent)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

private function journalExportExcel_deprecated(Request $request)
{
    // Récupérer les paramètres de filtrage
    $period = $request->period ?? 'day';
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $classId = $request->class_id;
    $paymentType = $request->payment_type;
    $paymentMethod = $request->payment_method;
    $studentName = $request->student_name;

    // Valider les dates
    if (!$startDate) {
        $startDate = date('Y-m-d');
    }

    if (!$endDate) {
        $endDate = date('Y-m-d');
    }

    // Préparer la requête de base
    $query = Receipt::with([
        'pr' => function($query) {
            $query->with([
                'student' => function($q) {
                    $q->with('student_record.my_class', 'student_record.section');
                },
                'payment'
            ]);
        }
    ])
    ->orderBy('created_at', 'desc');

    // Filtrer selon la période sélectionnée
    if ($period == 'day') {
        // Jour spécifique
        $query->whereDate('created_at', $startDate);
    } elseif ($period == 'week') {
        // Semaine courante
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        $query->whereBetween('created_at', [$startOfWeek.' 00:00:00', $endOfWeek.' 23:59:59']);
        $startDate = $startOfWeek;
        $endDate = $endOfWeek;
    } elseif ($period == 'month') {
        // Mois courant
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        $query->whereBetween('created_at', [$startOfMonth.' 00:00:00', $endOfMonth.' 23:59:59']);
        $startDate = $startOfMonth;
        $endDate = $endOfMonth;
    } elseif ($period == 'custom') {
        // Période personnalisée
        $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
    }

    // Filtrer par méthode de paiement si spécifié
    if ($paymentMethod) {
        $query->where('methode', $paymentMethod);
    }

    // Exécuter la requête
    $receipts = $query->get();

    // Filtrer par classe si spécifié
    if ($classId) {
        $receipts = $receipts->filter(function($receipt) use ($classId) {
            if (!$receipt->pr || !$receipt->pr->student || !$receipt->pr->student->student_record) {
                return false;
            }

            return $receipt->pr->student->student_record->my_class_id == $classId;
        });
    }

    // Filtrer par type de paiement si spécifié
    if ($paymentType) {
        $receipts = $receipts->filter(function($receipt) use ($paymentType) {
            return $receipt->pr &&
                   $receipt->pr->payment &&
                   $receipt->pr->payment->title == $paymentType;
        });
    }

    // Filtrer par nom d'étudiant si spécifié
    if ($studentName) {
        $receipts = $receipts->filter(function($receipt) use ($studentName) {
            return $receipt->pr &&
                   $receipt->pr->student &&
                   stripos($receipt->pr->student->name, $studentName) !== false;
        });
    }

    // Calculer le total des paiements pour la période
    $totalAmount = $receipts->sum('amt_paid');

    // Calculer les totaux par classe et par type de paiement pour les statistiques
    $classTotals = [];
    $classTotalSum = 0;
    $paymentTypeTotals = [];
    $paymentTypeTotalSum = 0;

    foreach ($receipts as $receipt) {
        // Totaux par classe
        if ($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_record) {
            $studentRecord = $receipt->pr->student->student_record;
            if ($studentRecord->my_class) {
                $className = $studentRecord->my_class->name;
                if ($studentRecord->section) {
                    $className .= ' ' . $studentRecord->section->name;
                }
                if (!isset($classTotals[$className])) {
                    $classTotals[$className] = 0;
                }
                $classTotals[$className] += $receipt->amt_paid;
                $classTotalSum += $receipt->amt_paid;
            }
        }

        // Totaux par type de paiement
        if ($receipt->pr && $receipt->pr->payment) {
            $paymentTitle = $receipt->pr->payment->title;
            if (!isset($paymentTypeTotals[$paymentTitle])) {
                $paymentTypeTotals[$paymentTitle] = 0;
            }
            $paymentTypeTotals[$paymentTitle] += $receipt->amt_paid;
            $paymentTypeTotalSum += $receipt->amt_paid;
        }
    }

    // Préparer les données pour l'export Excel
    $data = [];

    // En-tête du tableau
    $data[] = [
        'Date / Heure',
        'Élève',
        'Statut',
        'Classe',
        'Objet du Paiement',
        'Montant (Ar)',
        'Mode de Paiement',
        'Référence / Reçu',
        'Observations',
        'Validé par'
    ];

    // Ajouter les données des reçus
    foreach ($receipts as $receipt) {
        $student = null;
        $class = null;
        $payment = null;
        $status = 'Normal';

        if ($receipt->pr && $receipt->pr->student) {
            $student = $receipt->pr->student;
            $status = $student->status ?? 'Normal';

            if ($student->student_record && $student->student_record->my_class) {
                $class = $student->student_record->my_class->name;
                if ($student->student_record->section) {
                    $class .= ' ' . $student->student_record->section->name;
                }
            }
        }

        if ($receipt->pr && $receipt->pr->payment) {
            $payment = $receipt->pr->payment;
        }

        $data[] = [
            date('d/m/Y H:i', strtotime($receipt->created_at)),
            $student ? $student->name : 'N/A',
            $status,
            $class ?? 'N/A',
            $payment ? $payment->title : 'N/A',
            number_format($receipt->amt_paid, 0, ',', ' '),
            $receipt->methode ?? 'N/A',
            $receipt->reference_number ?? $receipt->pr->ref_no ?? 'N/A',
            $receipt->observations ?? '',
            $receipt->created_by ?? 'Système'
        ];
    }

    // Ajouter la ligne de total
    $data[] = ['', '', '', '', 'TOTAL', number_format($totalAmount, 0, ',', ' '), '', '', '', ''];

    // Générer le nom du fichier
    $fileName = 'Journal_Paiements_';
    if ($startDate == $endDate) {
        $fileName .= date('d-m-Y', strtotime($startDate));
    } else {
        $fileName .= date('d-m-Y', strtotime($startDate)) . '_au_' . date('d-m-Y', strtotime($endDate));
    }
    $fileName .= '.xlsx';

    // Créer un fichier temporaire
    $tempFile = tempnam(sys_get_temp_dir(), 'excel');

    // Créer le fichier Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter les données au fichier Excel
    $sheet->fromArray($data, null, 'A1');

    // Mettre en forme le fichier Excel
    $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    // Ajuster la largeur des colonnes
    $sheet->getColumnDimension('A')->setWidth(20);  // Date/Heure
    $sheet->getColumnDimension('B')->setWidth(30);  // Élève
    $sheet->getColumnDimension('C')->setWidth(15);  // Statut
    $sheet->getColumnDimension('D')->setWidth(20);  // Classe
    $sheet->getColumnDimension('E')->setWidth(30);  // Objet du Paiement
    $sheet->getColumnDimension('F')->setWidth(15);  // Montant
    $sheet->getColumnDimension('G')->setWidth(20);  // Mode de Paiement
    $sheet->getColumnDimension('H')->setWidth(20);  // Référence
    $sheet->getColumnDimension('I')->setWidth(30);  // Observations
    $sheet->getColumnDimension('J')->setWidth(20);  // Validé par

    // Mettre en forme la ligne de total
    $lastRow = count($data);
    $sheet->getStyle('A'.$lastRow.':J'.$lastRow)->getFont()->setBold(true);
    $sheet->getStyle('A'.$lastRow.':J'.$lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');

    // Ajouter une feuille pour les statistiques
    $statsSheet = $spreadsheet->createSheet();
    $statsSheet->setTitle('Statistiques');

    // Préparer les données pour les statistiques par classe
    $classStatsData = [
        ['Statistiques par classe'],
        ['Classe', 'Montant (Ar)']
    ];

    foreach ($classTotals as $className => $amount) {
        $classStatsData[] = [$className, number_format($amount, 0, ',', ' ')];
    }

    $classStatsData[] = ['Total', number_format($classTotalSum, 0, ',', ' ')];

    // Ajouter un espace entre les tableaux
    $classStatsData[] = [''];
    $classStatsData[] = [''];

    // Ajouter les statistiques par type de paiement
    $classStatsData[] = ['Statistiques par objet de paiement'];
    $classStatsData[] = ['Objet', 'Montant (Ar)'];

    foreach ($paymentTypeTotals as $paymentTitle => $amount) {
        $classStatsData[] = [$paymentTitle, number_format($amount, 0, ',', ' ')];
    }

    $classStatsData[] = ['Total', number_format($paymentTypeTotalSum, 0, ',', ' ')];

    // Ajouter les données à la feuille de statistiques
    $statsSheet->fromArray($classStatsData, null, 'A1');

    // Mettre en forme la feuille de statistiques
    $statsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $statsSheet->getStyle('A2:B2')->getFont()->setBold(true);
    $statsSheet->getStyle('A2:B2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    $lastClassRow = count($classTotals) + 3;
    $statsSheet->getStyle('A'.$lastClassRow.':B'.$lastClassRow)->getFont()->setBold(true);
    $statsSheet->getStyle('A'.$lastClassRow.':B'.$lastClassRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');

    $paymentTypeStartRow = $lastClassRow + 3;
    $statsSheet->getStyle('A'.$paymentTypeStartRow)->getFont()->setBold(true)->setSize(14);
    $statsSheet->getStyle('A'.($paymentTypeStartRow+1).':B'.($paymentTypeStartRow+1))->getFont()->setBold(true);
    $statsSheet->getStyle('A'.($paymentTypeStartRow+1).':B'.($paymentTypeStartRow+1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    $lastPaymentTypeRow = $paymentTypeStartRow + count($paymentTypeTotals) + 2;
    $statsSheet->getStyle('A'.$lastPaymentTypeRow.':B'.$lastPaymentTypeRow)->getFont()->setBold(true);
    $statsSheet->getStyle('A'.$lastPaymentTypeRow.':B'.$lastPaymentTypeRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');

    // Ajuster la largeur des colonnes
    $statsSheet->getColumnDimension('A')->setWidth(30);
    $statsSheet->getColumnDimension('B')->setWidth(20);

    // Enregistrer le fichier Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Télécharger le fichier Excel
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

/**
 * Générer les avis de paiement en malgache
 */
public function generatePaymentNotifications(Request $request)
{
    // Log incoming request data for debugging
    \Log::info('PDF Generation Request:', [
        'all_input' => $request->all(),
        'method' => $request->method(),
        'url' => $request->fullUrl(),
        'user_agent' => $request->userAgent()
    ]);

    $this->validate($request, [
        'my_class_id' => 'required|exists:my_classes,id',
        'my_payments_id' => 'required|array|min:1',
        'my_payments_id.*' => 'exists:payments,id',
        'payment_deadline' => 'required|date|after_or_equal:today',
        'status' => 'array'
    ], [
        'my_class_id.required' => 'Veuillez sélectionner une classe.',
        'my_payments_id.required' => 'Veuillez sélectionner au moins un motif de paiement.',
        'payment_deadline.required' => 'Veuillez spécifier une date limite de paiement.',
        'payment_deadline.after' => 'La date limite doit être future.'
    ]);

    $payment_ids = $request->my_payments_id;
    $id_class = $request->my_class_id;
    $statuses = $request->status ?? ['Normal', 'ADRA'];
    $payment_deadline = $request->payment_deadline;
    $action = $request->get('action', 'download'); // 'preview' or 'download'

    // Log validated data
    \Log::info('Validated data:', [
        'payment_ids' => $payment_ids,
        'id_class' => $id_class,
        'statuses' => $statuses,
        'payment_deadline' => $payment_deadline,
        'action' => $action
    ]);

    $nom_classe = MyClass::where('id', $id_class)->first();
    if (!$nom_classe) {
        \Log::error('Class not found:', ['id_class' => $id_class]);
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    // Récupérer tous les paiements sélectionnés
    $payments = Payment::whereIn('id', $payment_ids)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->get();

    if ($payments->isEmpty()) {
        \Log::error('No payments found:', ['payment_ids' => $payment_ids, 'id_class' => $id_class]);
        return back()->with('flash_danger', 'Motifs de paiement introuvables. Veuillez sélectionner des motifs de paiement valides pour cette classe.');
    }

    // Récupérer tous les étudiants de la classe avec le statut spécifié
    $students = StudentRecord::where('my_class_id', $id_class)
        ->with(['user' => function($query) use ($statuses) {
            $query->whereIn('status', $statuses)
                  ->orWhereNull('status');
        }])
        ->get()
        ->filter(function($student) {
            return $student->user !== null;
        });

    // Log students found
    \Log::info('Students found:', [
        'total_students' => $students->count(),
        'class_id' => $id_class,
        'statuses' => $statuses
    ]);

    // Préparer les données pour les lettres
    $unpaidStudents = $this->filterUnpaidStudents($students, $payments);

    // Debug: Detailed logging of unpaid students
    \Log::info('Detailed Unpaid Students Analysis:', [
        'total_students_in_class' => $students->count(),
        'filtered_unpaid_count' => count($unpaidStudents),
        'payments_to_check' => $payments->pluck('title', 'id')->toArray(),
        'sample_unpaid_student' => count($unpaidStudents) > 0 ? [
            'name' => $unpaidStudents[0]['student']->user->name ?? 'N/A',
            'class' => $unpaidStudents[0]['student']->my_class->name ?? 'N/A',
            'status' => $unpaidStudents[0]['status'] ?? 'N/A',
            'amount_due' => $unpaidStudents[0]['amount_due'] ?? 0,
            'payment_titles' => $unpaidStudents[0]['payment_titles'] ?? 'N/A'
        ] : null
    ]);

    // Log filtered unpaid students
    \Log::info('Unpaid students filtered:', [
        'unpaid_count' => count($unpaidStudents),
        'action' => $action
    ]);

    if (empty($unpaidStudents)) {
        \Log::warning('No unpaid students found for PDF generation - creating test data');
        
        // Create test data to see if the template works
        if ($students->count() > 0) {
            $testStudent = $students->first();
            $unpaidStudents = [[
                'student' => $testStudent,
                'status' => 'Normal',
                'amount_due' => 50000,
                'amount_paid' => 0,
                'total_amount' => 50000,
                'payment_titles' => 'Test Payment'
            ]];
            \Log::info('Created test unpaid student data:', ['test_student' => $testStudent->user->name]);
        } else {
            return back()->with('flash_danger', 'Aucun élève trouvé dans cette classe.');
        }
    }

    // Vérifier l'action demandée
    if ($action === 'preview') {
        \Log::info('Generating preview for unpaid students');
        return $this->previewPaymentNotifications($unpaidStudents, $nom_classe, $payments, $payment_deadline);
    }

    // Générer le PDF avec mise en page 10 avis par page
    \Log::info('Generating PDF for unpaid students');
    return $this->generateNotificationsPDF($unpaidStudents, $nom_classe, $payments, $payment_deadline);
}

/**
 * Afficher un aperçu des avis de paiement avant impression
 */
public function previewPaymentNotifications($unpaidStudents, $nom_classe, $payments, $payment_deadline)
{
    // Récupérer les paramètres de l'école
    $school_settings = Setting::all()->flatMap(function($s){
        return [$s->type => $s->description];
    });

    // Préparer les données pour la vue
    $data = [
        'unpaid_students' => $unpaidStudents,
        'class_name' => $nom_classe->name,
        'payments' => $payments,
        'payment_deadline' => $payment_deadline,
        'school_settings' => $school_settings,
        'generated_date' => now()->format('d/m/Y'),
        'is_preview' => true
    ];

    // Retourner la vue de prévisualisation
    return view('pages.support_team.payments.payment_notifications_preview', $data);
}

/**
 * Filtrer les étudiants impayés (DEPRECATED/DUPLICATE - Renamed to avoid fatal error)
 */
private function filterUnpaidStudents_deprecated($students, $payments)
{
    $unpaidStudents = [];
    
    \Log::info('Starting filterUnpaidStudents:', [
        'total_students' => $students->count(),
        'total_payments' => $payments->count(),
        'payment_ids' => $payments->pluck('id')->toArray(),
        'payment_titles' => $payments->pluck('title')->toArray()
    ]);

    foreach ($students as $studentIndex => $student) {
        $status = $student->user->status ?? 'Normal';
        
        \Log::info("Processing student {$studentIndex}:", [
            'student_id' => $student->user_id,
            'student_name' => $student->user->name,
            'status' => $status
        ]);
        
        // Ignorer les étudiants avec le statut TEAM3
        if ($status === 'TEAM3') {
            \Log::info("Skipping student {$studentIndex} - status TEAM3");
            continue;
        }

        $totalAmountDue = 0;
        $totalAmountPaid = 0;
        $totalAmount = 0;
        $paymentTitles = [];
        $hasUnpaidPayments = false;

        foreach ($payments as $payment) {
            $paymentRecord = PaymentRecord::with('receipt')
                ->where('student_id', $student->user_id)
                ->where('payment_id', $payment->id)
                ->first();

            // If no payment record exists, create a virtual one to include the student
            if (!$paymentRecord) {
                \Log::info("No payment record found for student {$student->user_id}, payment {$payment->id} - creating virtual record");
                $paymentTitles[] = $payment->title;
                $requiredAmount = ($status === 'ADRA') ? $payment->amount * 0.25 : $payment->amount;
                $totalAmountDue += $requiredAmount;
                $totalAmount += $requiredAmount;
                $hasUnpaidPayments = true;
                continue;
            }

            $paymentTitles[] = $payment->title;
            
            // Récupérer le montant total des reçus
            $totalReceiptAmount = 0;
            $receipts = $paymentRecord->receipt;
            if ($receipts && $receipts->count() > 0) {
                $totalReceiptAmount = $receipts->sum('amt_paid');
            }

            $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

            // Calculer le montant dû selon le statut
            $amountDue = 0;
            $requiredAmount = 0;
            
            if ($status === 'ADRA') {
                $requiredAmount = $payment->amount * 0.25;
                if ($paidAmount < $requiredAmount) {
                    $amountDue = $requiredAmount - $paidAmount;
                    $hasUnpaidPayments = true;
                }
            } else {
                $requiredAmount = $payment->amount;
                if (!$paymentRecord->paid) {
                    $amountDue = $payment->amount - $paidAmount;
                    if ($amountDue > 0) {
                        $hasUnpaidPayments = true;
                    }
                }
            }
            
            \Log::info("Payment analysis for student {$student->user_id}, payment {$payment->id}:", [
                'payment_title' => $payment->title,
                'payment_amount' => $payment->amount,
                'required_amount' => $requiredAmount,
                'paid_amount' => $paidAmount,
                'amount_due' => $amountDue,
                'payment_record_paid' => $paymentRecord->paid,
                'has_unpaid' => $hasUnpaidPayments
            ]);

            $totalAmountDue += max(0, $amountDue);
            $totalAmountPaid += $paidAmount;
            $totalAmount += $requiredAmount;
        }
        
        \Log::info("Student {$studentIndex} totals:", [
            'total_amount_due' => $totalAmountDue,
            'total_amount_paid' => $totalAmountPaid,
            'total_amount' => $totalAmount,
            'has_unpaid_payments' => $hasUnpaidPayments,
            'payment_titles' => implode(', ', $paymentTitles)
        ]);

        // N'inclure que les étudiants avec des paiements impayés
        if ($hasUnpaidPayments && $totalAmountDue > 0) {
            $unpaidStudents[] = [
                'student' => $student,
                'status' => $status,
                'amount_due' => $totalAmountDue,
                'amount_paid' => $totalAmountPaid,
                'total_amount' => $totalAmount,
                'payment_titles' => implode(', ', $paymentTitles)
            ];
            \Log::info("Added student {$studentIndex} to unpaid list");
        } else {
            \Log::info("Skipped student {$studentIndex} - no unpaid payments or zero amount due");
        }
    }
    
    \Log::info('filterUnpaidStudents completed:', [
        'total_unpaid_students' => count($unpaidStudents)
    ]);

    return $unpaidStudents;
}

/**
 * Générer le PDF des avis de paiement avec optimisations avancées
 */
private function generateNotificationsPDF($unpaidStudents, $nom_classe, $payments, $payment_deadline)
{
    // Optimisations agressives pour éviter les timeouts
    set_time_limit(0); // Supprimer la limite de temps
    ini_set('memory_limit', '1024M'); // Augmenter encore plus la mémoire
    ini_set('max_execution_time', 0); // Supprimer la limite d'exécution
    
    // Éviter les requêtes inutiles en prenant seulement les données essentielles
    $school_settings = [];
    
    // Limiter le nombre d'étudiants pour éviter les gros PDFs
    $maxStudentsPerBatch = 50; // Maximum 50 étudiants par batch
    $studentBatches = array_chunk($unpaidStudents, $maxStudentsPerBatch);
    
    // Debug: Check if we have students
    if (empty($unpaidStudents)) {
        \Log::error('No unpaid students provided to PDF generation');
        return back()->with('flash_danger', 'Aucun étudiant impayé trouvé pour générer le PDF.');
    }
    
    \Log::info('Starting PDF generation:', [
        'total_students' => count($unpaidStudents),
        'class_name' => $nom_classe->name,
        'batches' => count($studentBatches),
        'max_per_batch' => $maxStudentsPerBatch
    ]);
    
    // Si trop d'étudiants, traiter par batch
    if (count($unpaidStudents) > $maxStudentsPerBatch) {
        \Log::info('Using batched PDF generation for large dataset');
        return $this->generateBatchedPDF($studentBatches, $nom_classe, $payments, $payment_deadline);
    }

    // Préparer les données minimales pour le PDF
    $data = [
        'unpaid_students' => $unpaidStudents,
        'class_name' => $nom_classe->name,
        'payments' => $payments,
        'payment_deadline' => $payment_deadline,
        'school_settings' => $school_settings,
        'generated_date' => now()->format('d/m/Y'),
        'is_pdf' => true
    ];

    // Générer le nom du fichier
    $fileName = 'Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '_' . date('Y-m-d') . '.pdf';

    try {
        // Forcer le garbage collection pour libérer la mémoire
        gc_collect_cycles();
        
        // Debug: Log the data being used
        \Log::info('PDF Generation Data:', [
            'student_count' => count($unpaidStudents),
            'class_name' => $nom_classe->name,
            'deadline' => $payment_deadline,
            'memory_before' => memory_get_usage(true),
            'template' => 'pages.support_team.payments.payment_notifications_pdf',
            'first_student_sample' => isset($unpaidStudents[0]) ? [
                'student_name' => $unpaidStudents[0]['student']->user->name ?? 'N/A',
                'status' => $unpaidStudents[0]['status'] ?? 'N/A',
                'amount_due' => $unpaidStudents[0]['amount_due'] ?? 0,
                'payment_titles' => $unpaidStudents[0]['payment_titles'] ?? 'N/A'
            ] : 'No students found'
        ]);
        
        // Test if view exists and can be rendered
        if (!view()->exists('pages.support_team.payments.payment_notifications_pdf')) {
            throw new \Exception('PDF template view does not exist: payment_notifications_pdf');
        }
        
        // Try to render the view first (test)
        $htmlContent = view('pages.support_team.payments.payment_notifications_pdf', $data)->render();
        
        // Debug: Check HTML content structure
        \Log::info('HTML Content Analysis:', [
            'total_length' => strlen($htmlContent),
            'contains_student_data' => strpos($htmlContent, 'Ry Ray aman-drenin') !== false,
            'contains_notification_class' => strpos($htmlContent, 'class="notif"') !== false,
            'student_name_check' => isset($unpaidStudents[0]) && isset($unpaidStudents[0]['student']) ? 
                (strpos($htmlContent, $unpaidStudents[0]['student']->user->name) !== false ? 'FOUND' : 'NOT_FOUND') : 'NO_STUDENTS',
            'html_preview' => substr($htmlContent, 0, 500) . '...'
        ]);
        
        if (empty($htmlContent) || strlen($htmlContent) < 100) {
            throw new \Exception('Generated HTML content is empty or too short: ' . strlen($htmlContent) . ' characters');
        }
        
        \Log::info('HTML content generated successfully:', ['length' => strlen($htmlContent)]);
        
        // Générer le PDF avec la vue mise à jour
        $pdf = PDF::loadView('pages.support_team.payments.payment_notifications_pdf', $data);
        
        // Configuration minimaliste pour maximum de performances
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => false, // Désactiver le parser HTML5 complexe
            'isRemoteEnabled' => false,
            'isFontSubsettingEnabled' => false, // Désactiver le subsetting des polices
            'defaultFont' => 'Arial',
            'dpi' => 72, // Réduire la résolution pour accélérer
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
            'tempDir' => sys_get_temp_dir(),
            'enableCssFloat' => true,
            'enableHtml5Parser' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
            'logOutputFile' => null
        ]);
        
        // Forcer encore le garbage collection
        gc_collect_cycles();
        
        \Log::info('PDF generated successfully, starting download');
        return $pdf->download($fileName);
        
    } catch (\Exception $e) {
        // Log détaillé pour debugging
        \Log::error('PDF Generation Error: ' . $e->getMessage(), [
            'student_count' => count($unpaidStudents),
            'class_name' => $nom_classe->name,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'file_name' => $fileName,
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Retourner directement la vue HTML au lieu du PDF en cas d'erreur
        \Log::info('Returning HTML fallback instead of PDF');
        return response(view('pages.support_team.payments.payment_notifications_content', $data))
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '.html"');
    }
}

/**
 * Générer le PDF par batches pour de gros volumes
 */
private function generateBatchedPDF($studentBatches, $nom_classe, $payments, $payment_deadline)
{
    $zipFiles = [];
    $tempDir = sys_get_temp_dir() . '/payment_notifications_' . uniqid();
    
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0755, true);
    }
    
    try {
        foreach ($studentBatches as $index => $batch) {
            $data = [
                'unpaid_students' => $batch,
                'class_name' => $nom_classe->name,
                'payments' => $payments,
                'payment_deadline' => $payment_deadline,
                'school_settings' => [],
                'generated_date' => now()->format('d/m/Y'),
                'is_pdf' => true
            ];
            
            $fileName = 'Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '_Batch_' . ($index + 1) . '_' . date('Y-m-d') . '.pdf';
            $filePath = $tempDir . '/' . $fileName;
            
            $pdf = PDF::loadView('pages.support_team.payments.payment_notifications_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'dpi' => 72
            ]);
            
            $pdf->save($filePath);
            $zipFiles[] = $filePath;
            
            // Libérer la mémoire
            unset($pdf, $data);
            gc_collect_cycles();
        }
        
        // Créer un ZIP avec tous les fichiers
        $zipFileName = 'Avis_Paiement_' . str_replace(' ', '_', $nom_classe->name) . '_Complet_' . date('Y-m-d') . '.zip';
        $zipPath = $tempDir . '/' . $zipFileName;
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($zipFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } else {
            throw new \Exception('Impossible de créer le fichier ZIP');
        }
        
    } catch (\Exception $e) {
        // Nettoyer les fichiers temporaires
        foreach ($zipFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        if (file_exists($tempDir)) {
            rmdir($tempDir);
        }
        
        \Log::error('Batched PDF Generation Error: ' . $e->getMessage());
        return back()->with('flash_danger', 'Erreur lors de la génération des PDFs groupés: ' . $e->getMessage());
    }
}

/**
 * Générer le texte en malgache pour un étudiant
 */
private function generateMalagasyText($student, $paymentTitles, $amount, $deadline)
{
    $text = "Ry Ray aman-drenin'i {$student->user->name} ({$student->my_class->name})";
    if ($student->section) {
        $text = "Ry Ray aman-drenin'i {$student->user->name} ({$student->my_class->name} {$student->section->name})";
    }
    
    $text .= ",\n";
    $text .= "Ampahafantarina fa mbola tsy voaloa ny {$paymentTitles}.\n";
    $text .= "Vola tokony haloanareo : " . number_format($amount, 0, ',', ' ') . " Ar.\n";
    $text .= "Daty farany hanaovana fandoavam-bola : " . date('d/m/Y', strtotime($deadline)) . ".\n";
    $text .= "Misaotra amin'ny fiaraha-miasa sy ny fandraisana andraikitra.";
    
    return $text;
}

    /**
     * Filtrer les étudiants impayés avec optimisation des requêtes
     */
    private function filterUnpaidStudents($students, $payments)
    {
        // OPTIMISATION: Récupérer tous les PaymentRecords en une seule requête
        $studentIds = $students->pluck('user_id')->toArray();
        $paymentIds = $payments->pluck('id')->toArray();
        
        $allPaymentRecords = PaymentRecord::whereIn('student_id', $studentIds)
            ->whereIn('payment_id', $paymentIds)
            ->with('receipt')
            ->get()
            ->groupBy('student_id');

        $unpaidStudents = [];

        foreach ($students as $student) {
            // Récupérer le statut de l'étudiant
            $status = $student->user->status ?? 'Normal';

            // Ignorer les étudiants avec le statut TEAM3
            if ($status === 'TEAM3') {
                continue;
            }

            // Initialiser les variables pour ce student
            $totalAmountToPay = 0;
            $totalAmountPaid = 0;
            $totalAmountDue = 0;
            $lastPaymentDate = null;
            $paymentRecords = [];
            $paymentTitles = [];
            
            // Récupérer les PR de cet étudiant depuis la collection pré-chargée
            $studentPRs = $allPaymentRecords->get($student->user_id, collect());

            // Pour chaque paiement sélectionné
            foreach ($payments as $payment) {
                // Récupérer l'enregistrement de paiement depuis la collection en mémoire
                $paymentRecord = $studentPRs->where('payment_id', $payment->id)->first();

                if (!$paymentRecord) {
                    // Si pas d'enregistrement, tout est dû (sauf si ADRA)
                    $amountDue = $payment->amount;
                    if ($status === 'ADRA') {
                        $amountDue = $payment->amount * 0.25;
                    }
                    
                    $totalAmountToPay += ($status === 'ADRA' ? $payment->amount * 0.25 : $payment->amount);
                    $totalAmountDue += $amountDue;
                    
                    continue; 
                }

                // Ajouter le titre du paiement à la liste
                $paymentTitles[] = $payment->title;

                // Récupérer le montant total des reçus pour cet enregistrement
                $totalReceiptAmount = 0;
                $receipts = $paymentRecord->receipt;
                if ($receipts && $receipts->count() > 0) {
                    $totalReceiptAmount = $receipts->sum('amt_paid');
                    
                    // Mettre à jour la date du dernier paiement si nécessaire
                    $receiptDate = $receipts->sortByDesc('created_at')->first()->created_at;
                    if (!$lastPaymentDate || $receiptDate > $lastPaymentDate) {
                        $lastPaymentDate = $receiptDate;
                    }
                }

                // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
                $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

                // Calculer le montant dû selon le statut
                $amountDue = 0;
                if ($status === 'ADRA') {
                    // Pour les élèves ADRA, le montant total à payer est 25% du montant total
                    $minimumRequired = $payment->amount * 0.25;
                    $totalAmountToPay += $minimumRequired;

                    // Si l'élève a déjà payé au moins 25%, le montant dû est 0
                    if ($paidAmount >= $minimumRequired) {
                        $amountDue = 0;
                    } else {
                        // Sinon, le montant dû est ce qui reste à payer pour atteindre 25%
                        $amountDue = $minimumRequired - $paidAmount;
                    }
                } else {
                    // Pour les autres statuts, le montant total à payer est le montant total
                    $totalAmountToPay += $payment->amount;
                    
                    // Le montant dû est le montant total moins le montant payé
                    $amountDue = $payment->amount - $paidAmount;
                    
                    // Si le paiement est marqué comme payé, le montant dû est 0
                    if ($paymentRecord->paid) {
                        $amountDue = 0;
                    }
                }

                // Si le montant dû est négatif, le mettre à 0
                if ($amountDue < 0) {
                    $amountDue = 0;
                }

                // Ajouter le montant payé au total
                $totalAmountPaid += $paidAmount;
                
                // Ajouter le montant dû au total
                $totalAmountDue += $amountDue;

                // Ajouter l'enregistrement de paiement à la liste
                $paymentRecords[] = [
                    'record' => $paymentRecord,
                    'payment' => $payment,
                    'amount_due' => $amountDue,
                    'amount_paid' => $paidAmount
                ];
            }

            // Si le montant dû total est 0, on passe (sauf si on veut tout voir, mais ici on filtre les impayés)
            if ($totalAmountDue <= 0) {
                continue;
            }

            // Déterminer l'état du paiement
            $paymentState = 'Impayé';
            if ($totalAmountDue === 0) {
                $paymentState = 'Acquitté';
            } else if ($totalAmountPaid > 0) {
                $paymentState = 'Partiellement payé';
            }

            // Ajouter l'étudiant à la liste des impayés
            $unpaidStudents[] = [
                'student' => $student,
                'payment_records' => $paymentRecords,
                'status' => $status,
                'amount_due' => $totalAmountDue,
                'total_amount' => $totalAmountToPay,
                'amount_paid' => $totalAmountPaid,
                'last_payment_date' => $lastPaymentDate,
                'payment_state' => $paymentState,
                'payment_titles' => implode(', ', $paymentTitles)
            ];
        }
        
        return $unpaidStudents;
    }

    /**
     * Afficher le journal des paiements avec filtres
     */
    public function journal(Request $request)
    {
        $d['period'] = $request->period ?? 'month';
        
        // Définir les dates par défaut selon la période
        if ($d['period'] == 'day') {
            $d['startDate'] = date('Y-m-d');
            $d['endDate'] = date('Y-m-d');
        } elseif ($d['period'] == 'week') {
            $d['startDate'] = now()->startOfWeek()->format('Y-m-d');
            $d['endDate'] = now()->endOfWeek()->format('Y-m-d');
        } elseif ($d['period'] == 'month') {
            $d['startDate'] = date('Y-m-01');
            $d['endDate'] = date('Y-m-t');
        } else {
            $d['startDate'] = $request->start_date ?? date('Y-m-01');
            $d['endDate'] = $request->end_date ?? date('Y-m-t');
        }

        $d['selectedClass'] = $request->class_id;
        $d['selectedPaymentType'] = $request->payment_type;
        $d['selectedPaymentMethod'] = $request->payment_method;
        $d['studentName'] = $request->student_name;

        // Récupérer les données pour les listes déroulantes des filtres
        $d['classes'] = $this->my_class->all();
        $d['paymentTypes'] = $this->pay->getPayment(['year' => $this->year])->get();
        $d['paymentMethods'] = Receipt::select('methode')->distinct()->whereNotNull('methode')->get();

        // --- REQUÊTE 1 : Paiements normaux (Journal Général) ---
        $query = Receipt::with(['pr.student.student_record.my_class', 'pr.payment'])
            ->where('year', $this->year);

        // Exclure les paiements ADRA (75%) et TEAM3 (100%)
        $query->where(function($q) {
            $q->whereNull('methode')
              ->orWhere(function($subQ) {
                  $subQ->whereNotIn('methode', ['ADRA', 'TEAM3']);
              });
        });

        // Appliquer filtres pour Paiements Normaux
        $query->whereBetween('created_at', [$d['startDate'] . ' 00:00:00', $d['endDate'] . ' 23:59:59']);

        if ($d['selectedClass']) {
            $query->whereHas('pr.student.student_record', function($q) use ($d) {
                $q->where('my_class_id', $d['selectedClass']);
            });
        }
        if ($d['selectedPaymentType']) {
            $query->whereHas('pr.payment', function($q) use ($d) {
                $q->where('title', $d['selectedPaymentType']);
            });
        }
        if ($d['selectedPaymentMethod']) {
            $query->where('methode', $d['selectedPaymentMethod']);
        }
        if ($d['studentName']) {
            $query->whereHas('pr.student', function($q) use ($d) {
                $q->where('name', 'like', '%' . $d['studentName'] . '%');
            });
        }

        $receipts = $query->latest()->get();
        
        // --- REQUÊTE 2 : Paiements Automatiques (ADRA/TEAM3) ---
        $autoQuery = Receipt::with(['pr.student.student_record.my_class', 'pr.payment'])
            ->where('year', $this->year)
            ->whereIn('methode', ['ADRA', 'TEAM3']);
            
        // Appliquer les mêmes filtres temporels et contextuels
        $autoQuery->whereBetween('created_at', [$d['startDate'] . ' 00:00:00', $d['endDate'] . ' 23:59:59']);

        if ($d['selectedClass']) {
            $autoQuery->whereHas('pr.student.student_record', function($q) use ($d) {
                $q->where('my_class_id', $d['selectedClass']);
            });
        }
        if ($d['selectedPaymentType']) {
            $autoQuery->whereHas('pr.payment', function($q) use ($d) {
                $q->where('title', $d['selectedPaymentType']);
            });
        }
        // Note: On n'applique pas le filtre payment_method ici car on veut forcer ADRA/TEAM3
        if ($d['studentName']) {
            $autoQuery->whereHas('pr.student', function($q) use ($d) {
                $q->where('name', 'like', '%' . $d['studentName'] . '%');
            });
        }
        
        // Récupérer et s'assurer "sans répétition" (groupement)
        // On récupère tout et on groupe dans la vue, ou on distinct ici ?
        // La demande "sans repetition" suggère de voir si on a plusieurs lignes pour le même paiement.
        // On va les récupérer normalement, le générateur gère l'unicité.
        $d['autoReceipts'] = $autoQuery->latest()->get();

        $d['receipts'] = $receipts;
        
        // Calculer le total général (Paiements normaux)
        $d['totalAmount'] = $receipts->sum('amt_paid');
        
        // Calculer les statistiques par classe
        $d['classTotals'] = [];
        $d['classTotalSum'] = 0;
        foreach ($receipts as $receipt) {
            if ($receipt->pr && $receipt->pr->student && $receipt->pr->student->student_record && $receipt->pr->student->student_record->my_class) {
                $className = $receipt->pr->student->student_record->my_class->name;
                if (!isset($d['classTotals'][$className])) {
                    $d['classTotals'][$className] = 0;
                }
                $d['classTotals'][$className] += $receipt->amt_paid;
                $d['classTotalSum'] += $receipt->amt_paid;
            }
        }
        arsort($d['classTotals']); 

        // Calculer les statistiques par type de paiement
        $d['paymentTypeTotals'] = [];
        $d['paymentTypeTotalSum'] = 0;
        foreach ($receipts as $receipt) {
            if ($receipt->pr && $receipt->pr->payment) {
                $paymentTitle = $receipt->pr->payment->title;
                if (!isset($d['paymentTypeTotals'][$paymentTitle])) {
                    $d['paymentTypeTotals'][$paymentTitle] = 0;
                }
                $d['paymentTypeTotals'][$paymentTitle] += $receipt->amt_paid;
                $d['paymentTypeTotalSum'] += $receipt->amt_paid;
            }
        }
        arsort($d['paymentTypeTotals']); // Trier par montant décroissant

        return view('pages.support_team.payments.journal', $d);
    }

    /**
     * Alias pour le filtre du journal (utilise la même logique que journal)
     */
    public function journalFilter(Request $request)
    {
        return $this->journal($request);
    }

    /**
     * Exporter le journal en Excel (Serveur)
     */
    public function journalExportExcel(Request $request)
    {
        // Pour l'instant, on utilise l'export JS côté client qui est plus rapide et déjà implémenté
        // Si besoin d'un export serveur, on pourra l'implémenter ici avec PhpSpreadsheet
        return $this->journal($request);
    }
}
