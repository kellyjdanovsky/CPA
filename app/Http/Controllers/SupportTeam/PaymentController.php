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

class PaymentController extends Controller
{
    protected $my_class, $pay, $student, $year;

    public function __construct(MyClassRepo $my_class, PaymentRepo $pay, StudentRepo $student)
    {
        $this->my_class = $my_class;
        $this->pay = $pay;
        $this->year = Qs::getCurrentSession();
        $this->student = $student;

        $this->middleware('teamAccount');
    }

    public function index()
    {
        $d['selected'] = false;
        $d['years'] = $this->pay->getPaymentYears();

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

            // Vérifier si le montant payé est supérieur au solde restant
            if ($req->amt_paid > ($payment->amount - $pr->amt_paid)) {
                return response()->json(['message' => 'Le montant payé ne peut pas être supérieur au solde restant'], 422);
            }

            // Mettre à jour l'enregistrement de paiement
            $d['amt_paid'] = $amt_p = $pr->amt_paid + $req->amt_paid;
            $d['balance'] = $bal = $payment->amount - $amt_p;
            $d['paid'] = $bal < 1 ? 1 : 0;

            $this->pay->updateRecord($pr_id, $d);

            // Créer un reçu
            $d2['amt_paid'] = $req->amt_paid;
            $d2['balance'] = $bal;
            $d2['pr_id'] = $pr_id;
            $d2['year'] = $this->year;
            $d2['methode'] = $req->methode;
            $d2['payment_method'] = $req->payment_method ?? $req->methode;
            $d2['reference_number'] = $req->reference_number ?? null;
            $d2['observations'] = $req->observations ?? null;
            $d2['created_by'] = auth()->user()->name;

            $receipt = $this->pay->createReceipt($d2);

            if (!$receipt) {
                throw new \Exception('Impossible de créer le reçu');
            }

            return Qs::jsonUpdateOk();
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors du paiement: ' . $e->getMessage());

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
            $d['students'] = $st = $this->student->getRecord(['my_class_id' => $class_id])->get()->sortBy('user.name');
            if($st->count() < 1){
                return Qs::goWithDanger('payments.manage');
            }
            $d['selected'] = true;
            $d['my_class_id'] = $class_id;
        }

        return view('pages.support_team.payments.manage', $d);
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
        $data = $req->all();
        $data['year'] = $this->year;
        $data['ref_no'] = Pay::genRefCode();
        $this->pay->create($data);

        return Qs::jsonStoreOk();
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

    public function reset_record($id)
    {
        $pr['amt_paid'] = $pr['paid'] = $pr['balance'] = 0;
        $this->pay->updateRecord($id, $pr);
        $this->pay->deleteReceipts(['pr_id' => $id]);

        return back()->with('flash_success', __('msg.update_ok'));
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

    // Préparer les données pour la vue
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

        // Pour chaque paiement sélectionné
        foreach ($payments as $payment) {
            // Récupérer l'enregistrement de paiement avec les reçus
            $paymentRecord = PaymentRecord::with('receipt')
                ->where('student_id', $student->user_id)
                ->where('payment_id', $payment->id)
                ->first();

            if (!$paymentRecord) {
                continue; // Ignorer si aucun enregistrement de paiement n'existe
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

            // S'assurer que le montant payé dans l'enregistrement est à jour
            if ($totalReceiptAmount > 0 && $paymentRecord->amt_paid != $totalReceiptAmount) {
                $paymentRecord->amt_paid = $totalReceiptAmount;
                $paymentRecord->save();
            }

            // Calculer le montant dû selon le statut
            $amountDue = 0;
            if ($status === 'ADRA') {
                // Pour les élèves ADRA, le montant total à payer est 25% du montant total
                $minimumRequired = $payment->amount * 0.25;
                $totalAmountToPay += $minimumRequired;

                // Si l'élève a déjà payé au moins 25%, le montant dû est 0
                if ($paidAmount >= $minimumRequired) {
                    $amountDue = 0;
                    
                    // Mettre à jour le statut de paiement pour refléter la règle ADRA
                    if (!$paymentRecord->paid) {
                        $paymentRecord->paid = 1;
                        $paymentRecord->balance = 0;
                        $paymentRecord->save();
                    }
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

        // Si aucun paiement n'a été trouvé pour cet étudiant, passer au suivant
        if (empty($paymentRecords)) {
            continue;
        }

        // Pour les étudiants ADRA, vérifier s'ils ont payé au moins 25% du montant total
        // Si oui, ne pas les inclure dans la liste
        if ($status === 'ADRA' && $totalAmountDue === 0) {
            continue;
        }

        // Pour les étudiants normaux, vérifier s'ils ont payé complètement
        // Si oui, ne pas les inclure dans la liste
        if ($status === 'Normal' && $totalAmountDue === 0) {
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

    return view('pages.support_team.payments.unpaid', [
        'unpaid_students' => $unpaidStudents,
        'nom_classe' => $nom_classe,
        'payments' => $payments,
        'payment_ids' => $payment_ids,
        'id_class' => $id_class,
        'statuses' => $statuses
    ]);
}

public function exportUnpaidExcel(Request $request)
{
    $payment_ids = $request->payment_ids ? explode(',', $request->payment_ids) : [];
    $id_class = $request->id_class;
    $statuses = $request->statuses ? explode(',', $request->statuses) : ['Normal', 'ADRA'];

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

    // Préparer les données pour l'export
    $data = [];

    // En-tête du tableau
    $data[] = [
        'Nom de l\'élève', 
        'Classe', 
        'Statut', 
        'Motifs de paiement', 
        'Montant total à payer', 
        'Montant déjà payé', 
        'Date du paiement', 
        'Montant restant à payer'
    ];

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
        $paymentTitles = [];
        $includeStudent = false;

        // Pour chaque paiement sélectionné
        foreach ($payments as $payment) {
            // Récupérer l'enregistrement de paiement avec les reçus
            $paymentRecord = PaymentRecord::with('receipt')
                ->where('student_id', $student->user_id)
                ->where('payment_id', $payment->id)
                ->first();

            if (!$paymentRecord) {
                continue; // Ignorer si aucun enregistrement de paiement n'existe
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
                    $includeStudent = true;
                }
            } else {
                // Pour les autres statuts, le montant total à payer est le montant total
                $totalAmountToPay += $payment->amount;
                
                // Le montant dû est le montant total moins le montant payé
                $amountDue = $payment->amount - $paidAmount;
                
                // Si le paiement est marqué comme payé, le montant dû est 0
                if ($paymentRecord->paid) {
                    $amountDue = 0;
                } else {
                    $includeStudent = true;
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
        }

        // Si aucun paiement n'a été trouvé pour cet étudiant ou si l'étudiant a tout payé, passer au suivant
        if (empty($paymentTitles) || !$includeStudent) {
            continue;
        }

        // Formater la date du dernier paiement
        $formattedLastPaymentDate = $lastPaymentDate ? date('d/m/Y', strtotime($lastPaymentDate)) : '-';

        // Ajouter les données de l'étudiant au tableau
        $data[] = [
            $student->user->name,
            $nom_classe->name . ' ' . $student->section->name,
            $status,
            implode(', ', $paymentTitles),
            number_format($totalAmountToPay, 0, ',', ' ') . ' Ar',
            number_format($totalAmountPaid, 0, ',', ' ') . ' Ar',
            $formattedLastPaymentDate,
            number_format($totalAmountDue, 0, ',', ' ') . ' Ar'
        ];
    }

    // Générer le fichier Excel
    $fileName = 'Impayés_' . $nom_classe->name . '_' . date('Y-m-d') . '.xlsx';

    // Créer un fichier temporaire
    $tempFile = tempnam(sys_get_temp_dir(), 'excel');

    // Créer le fichier Excel
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter les données au fichier Excel
    $sheet->fromArray($data, null, 'A1');

    // Mettre en forme le fichier Excel
    $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');

    // Ajuster la largeur des colonnes
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(25);

    // Enregistrer le fichier Excel
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Télécharger le fichier Excel
    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
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

public function journal()
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
    ->whereDate('created_at', $today)
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

public function journalFilter(Request $request)
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

public function journalExportExcel(Request $request)
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
     * Affiche la page de gestion des reçus ADRA & TEAM 3
     */
    public function adraTeam3()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['selected'] = false;
        $d['my_class_id'] = null; // Initialiser my_class_id à null
        $d['payments'] = $this->pay->getActivePayments()->get();
        $d['payment_records'] = collect([]); // Collection vide pour éviter les erreurs
        $d['students'] = collect([]); // Collection vide pour éviter les erreurs
        
        return view('pages.support_team.payments.adra_team3', $d);
    }

    /**
     * Filtre les élèves ADRA & TEAM 3 par classe
     */
    public function adraTeam3Filter(Request $request)
    {
        $class_id = $request->class_id;
        $selected_status = $request->status ?? ['ADRA', 'TEAM3'];
        $selected_payment_ids = $request->payments ?? [];
        
        $d['my_classes'] = $this->my_class->all();
        $d['selected'] = true;
        $d['my_class_id'] = $class_id;
        
        // Récupérer tous les paiements actifs
        $all_payments = $this->pay->getActivePayments()->get();
        $d['payments'] = $all_payments;
        
        // Si des paiements spécifiques sont sélectionnés
        if (!empty($selected_payment_ids)) {
            $selected_payments = $all_payments->whereIn('id', $selected_payment_ids);
            $d['selected_payments'] = $selected_payments;
        } else {
            $d['selected_payments'] = collect([]);
        }
        
        // Récupérer les élèves ADRA et TEAM 3 de la classe sélectionnée
        try {
            // Récupérer tous les élèves de la classe
            $allStudents = $this->student->getRecord(['my_class_id' => $class_id])->with(['user', 'my_class'])->get();
            
            // Filtrer pour ne garder que les élèves avec les statuts sélectionnés
            $students = $allStudents->filter(function($student) use ($selected_status) {
                if (!$student->user) {
                    return false;
                }
                $status = $student->user->status ?? 'Normal';
                return in_array($status, (array)$selected_status);
            });
            
            // Log pour débogage
            \Log::info('Classe ID: ' . $class_id);
            \Log::info('Statuts sélectionnés: ' . implode(', ', (array)$selected_status));
            \Log::info('Paiements sélectionnés: ' . implode(', ', (array)$selected_payment_ids));
            \Log::info('Nombre total d\'élèves dans la classe: ' . $allStudents->count());
            \Log::info('Nombre d\'élèves filtrés: ' . $students->count());
            
            $d['students'] = $students;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des élèves: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            $d['students'] = collect([]);
        }
        
        // Récupérer tous les enregistrements de paiement pour ces élèves
        $student_ids = $students->pluck('user_id')->toArray();
        $d['payment_records'] = $this->pay->getAllPRForStudents($student_ids)->get();
        
        return view('pages.support_team.payments.adra_team3', $d);
    }
    
    /**
     * Génère un reçu pour les élèves ADRA & TEAM 3
     */
    public function generateAdraTeam3Receipt(Request $request)
    {
        try {
            // Log les données reçues pour le débogage
            \Log::info('Données reçues pour la génération de reçu:', $request->all());
            
            // Valider les données du formulaire avec des messages personnalisés
            $validator = \Validator::make($request->all(), [
                'student_id' => 'required|exists:users,id',
                'payments' => 'required|array',
                'payments.*' => 'exists:payments,id',
                'reference_code' => 'nullable|string|max:50',
            ], [
                'student_id.required' => 'L\'identifiant de l\'élève est requis',
                'student_id.exists' => 'L\'élève spécifié n\'existe pas',
                'payments.required' => 'Veuillez sélectionner au moins un paiement',
                'payments.array' => 'Le format des paiements est invalide',
                'payments.*.exists' => 'Un des paiements sélectionnés n\'existe pas',
            ]);
            
            if ($validator->fails()) {
                \Log::error('Erreurs de validation:', $validator->errors()->toArray());
                return back()->withErrors($validator)->withInput();
            }
            
            $student_id = $request->student_id;
            $payment_ids = $request->payments;
            $reference_code = $request->reference_code;
            
            // Log les données validées
            \Log::info('Données validées:', [
                'student_id' => $student_id,
                'payment_ids' => $payment_ids,
                'reference_code' => $reference_code
            ]);
            
            // Récupérer l'élève
            $student = $this->student->findByUserId($student_id)->with('user')->first();
            if (!$student) {
                return back()->with('flash_danger', 'Élève introuvable');
            }
            
            // Vérifier le statut de l'élève
            $status = $student->user->status ?? 'Normal';
            if (!in_array($status, ['ADRA', 'TEAM3'])) {
                return back()->with('flash_danger', 'L\'élève n\'a pas le statut ADRA ou TEAM3');
            }
            
            // Calculer le pourcentage à appliquer selon le statut
            $percentage = ($status === 'ADRA') ? 0.75 : 1.0;
            
            $totalAmount = 0;
            $paymentDetails = [];
            $receiptIds = [];
            
            // Vérifier que des paiements ont été sélectionnés
            if (empty($payment_ids)) {
                \Log::error('Aucun paiement sélectionné pour l\'élève ID: ' . $student_id);
                return back()->with('flash_danger', 'Veuillez sélectionner au moins un paiement')->withInput();
            }
            
            \Log::info('Traitement des paiements pour l\'élève ID: ' . $student_id . ', Nombre de paiements: ' . count($payment_ids));
            
            // Pour chaque paiement sélectionné
            foreach ($payment_ids as $payment_id) {
                // Récupérer le paiement
                $payment = $this->pay->find($payment_id);
                if (!$payment) {
                    \Log::warning('Paiement non trouvé, ID: ' . $payment_id);
                    continue;
                }
                
                \Log::info('Traitement du paiement: ' . $payment->title . ' (ID: ' . $payment_id . ')');
                
                // Trouver ou créer l'enregistrement de paiement
                $pr = $this->pay->findMyPR($student_id, $payment_id)->first();
                
                // Générer un numéro de référence unique
                $baseRefNo = $reference_code ?: 'CPA/' . $student->my_class->class_type->code . $student->my_class->id . '/' . $this->year;
                $uniqueRefNo = $this->generateUniqueRefNo($baseRefNo, $payment_id);
                
                if (!$pr) {
                    \Log::info('Création d\'un nouvel enregistrement de paiement pour l\'élève ID: ' . $student_id . ', Paiement ID: ' . $payment_id);
                    
                    $pr_data = [
                        'student_id' => $student_id,
                        'payment_id' => $payment_id,
                        'year' => $this->year,
                        'ref_no' => $uniqueRefNo
                    ];
                    $pr = $this->pay->createRecord($pr_data);
                } else {
                    \Log::info('Mise à jour de l\'enregistrement de paiement existant, ID: ' . $pr->id);
                    
                    // Mettre à jour le code de référence
                    $pr->ref_no = $uniqueRefNo;
                    $pr->save();
                }
                
                try {
                    // Calculer le montant à facturer
                    $originalAmount = $payment->amount;
                    $amountToBill = $originalAmount * $percentage;
                    
                    \Log::info('Création du reçu pour le paiement: ' . $payment->title . ', Montant: ' . $amountToBill);
                    
                    // Créer un reçu
                    $receipt_data = [
                        'pr_id' => $pr->id,
                        'amt_paid' => $amountToBill,
                        'balance' => ($status === 'ADRA') ? ($originalAmount - $amountToBill) : 0,
                        'year' => $this->year,
                        'methode' => $status,
                        'payment_method' => $status,
                        'created_by' => auth()->user()->name,
                        'reference_number' => $pr->ref_no, // Utiliser le numéro de référence unique généré
                        'observations' => "Reçu {$status} généré automatiquement",
                    ];
                    
                    $receipt = $this->pay->createReceipt($receipt_data);
                    
                    if ($receipt) {
                        \Log::info('Reçu créé avec succès, ID: ' . $receipt->id);
                        
                        // Mettre à jour l'enregistrement de paiement
                        $pr->amt_paid = $amountToBill;
                        
                        // Pour les élèves ADRA, le solde est de 25%
                        if ($status === 'ADRA') {
                            $pr->balance = $originalAmount - $amountToBill;
                            $pr->paid = 0; // Pas encore payé complètement
                        } else {
                            // Pour les élèves TEAM3, marquer comme payé
                            $pr->balance = 0;
                            $pr->paid = 1;
                        }
                        
                        $pr->methode = $status;
                        $pr->save();
                        
                        $totalAmount += $amountToBill;
                        $receiptIds[] = $pr->id;
                        
                        // Ajouter les détails du paiement
                        $paymentDetails[] = [
                            'title' => $payment->title,
                            'original_amount' => $originalAmount,
                            'paid_amount' => $amountToBill,
                            'percentage' => $percentage * 100
                        ];
                    } else {
                        \Log::error('Échec de la création du reçu pour le paiement ID: ' . $payment_id);
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de la création du reçu pour le paiement ID: ' . $payment_id . ', Erreur: ' . $e->getMessage());
                    \Log::error('Trace: ' . $e->getTraceAsString());
                }
            }
            
            // Vérifier que des reçus ont été générés
            if (empty($receiptIds)) {
                \Log::error('Aucun reçu généré pour l\'élève ID: ' . $student_id);
                return back()->with('flash_danger', 'Aucun reçu n\'a pu être généré. Veuillez réessayer.')->withInput();
            }
            
            \Log::info('Reçus générés avec succès, IDs: ' . implode(', ', $receiptIds));
            
            // Stocker les données dans la session pour l'impression
            session([
                'adra_team3_receipt' => [
                    'student' => $student,
                    'status' => $status,
                    'reference_code' => $reference_code ?: 'Multiple', // Indiquer qu'il y a plusieurs références
                    'payment_details' => $paymentDetails,
                    'total_amount' => $totalAmount,
                    'receipt_ids' => $receiptIds,
                    'receipt_date' => now()->format('d/m/Y'),
                ]
            ]);
            
            \Log::info('Redirection vers la page d\'impression du reçu');
            
            return redirect()->route('payments.adra_team3.print_receipt', ['id' => implode(',', $receiptIds)])
                ->with('flash_success', 'Reçu généré avec succès');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log l'erreur de validation
            \Log::error('Erreur de validation lors de la génération du reçu ADRA/TEAM3:', $e->errors());
            
            // Retourner les erreurs de validation
            return back()->withErrors($e->errors())->withInput()->with('flash_danger', 'Veuillez corriger les erreurs de validation.');
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la génération du reçu ADRA/TEAM3: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            // Retourner un message d'erreur convivial
            return back()->with('flash_danger', 'Une erreur est survenue lors de la génération du reçu: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Imprime un reçu ADRA & TEAM 3
     */
    public function printAdraTeam3Receipt($id)
    {
        // Récupérer les données de la session
        $receiptData = session('adra_team3_receipt');
        
        if (!$receiptData) {
            // Si les données ne sont pas dans la session, essayer de les récupérer à partir de l'ID
            $ids = explode(',', $id);
            $firstId = $ids[0] ?? null;
            
            if (!$firstId) {
                return redirect()->route('payments.adra_team3')
                    ->with('flash_danger', 'Données du reçu introuvables');
            }
            
            try {
                $pr = $this->pay->findRecord($firstId);
                $student = $this->student->findByUserId($pr->student_id)->with('user')->first();
                $status = $student->user->status ?? 'Normal';
                
                // Récupérer tous les reçus associés
                $receipts = [];
                $paymentDetails = [];
                $totalAmount = 0;
                
                foreach ($ids as $receiptId) {
                    $record = $this->pay->findRecord($receiptId);
                    if ($record) {
                        $payment = $this->pay->find($record->payment_id);
                        $receipt = $record->receipt->first();
                        
                        if ($payment && $receipt) {
                            $paymentDetails[] = [
                                'title' => $payment->title,
                                'original_amount' => $payment->amount,
                                'paid_amount' => $receipt->amt_paid,
                                'percentage' => ($status === 'ADRA') ? 75 : 100
                            ];
                            
                            $totalAmount += $receipt->amt_paid;
                        }
                    }
                }
                
                // Collecter tous les numéros de référence
                $refNos = [];
                foreach ($ids as $receiptId) {
                    $record = $this->pay->findRecord($receiptId);
                    if ($record && $record->ref_no) {
                        $refNos[] = $record->ref_no;
                    }
                }
                
                // Déterminer le code de référence à afficher
                $referenceCode = count($refNos) === 1 ? $refNos[0] : 'Multiple';
                
                $receiptData = [
                    'student' => $student,
                    'status' => $status,
                    'reference_code' => $referenceCode,
                    'payment_details' => $paymentDetails,
                    'total_amount' => $totalAmount,
                    'receipt_ids' => $ids,
                    'receipt_date' => now()->format('d/m/Y'),
                    'reference_codes' => $refNos, // Stocker tous les codes de référence
                ];
            } catch (\Exception $e) {
                return redirect()->route('payments.adra_team3')
                    ->with('flash_danger', 'Erreur lors de la récupération des données du reçu: ' . $e->getMessage());
            }
        }
        
        // Préparer les données pour la vue
        $d['student'] = $receiptData['student'];
        $d['status'] = $receiptData['status'];
        $d['reference_code'] = $receiptData['reference_code'];
        $d['payment_details'] = $receiptData['payment_details'];
        $d['total_amount'] = $receiptData['total_amount'];
        $d['receipt_ids'] = $receiptData['receipt_ids'];
        
        // Ajouter les codes de référence et la date si disponibles
        if (isset($receiptData['reference_codes'])) {
            $d['reference_codes'] = $receiptData['reference_codes'];
        }
        if (isset($receiptData['receipt_date'])) {
            $d['receipt_date'] = $receiptData['receipt_date'];
        }
        
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });
        
        // Utiliser le template de reçu thermique 58mm
        return view('pages.support_team.payments.adra_team3_thermal_receipt', $d);
    }
    
    /**
     * Génère des reçus en masse pour les élèves ADRA & TEAM 3 d'une classe
     */
    public function bulkGenerateAdraTeam3Receipts(Request $request)
    {
        try {
            // Valider les données du formulaire
            $request->validate([
                'class_id' => 'required|exists:my_classes,id',
                'payments' => 'required|array',
                'payments.*' => 'exists:payments,id',
            ]);
            
            $class_id = $request->class_id;
            $payment_ids = $request->payments;
            $status_filter = $request->status ?? ['ADRA', 'TEAM3'];
            
            // Rediriger vers la page d'impression en masse
            return redirect()->route('payments.adra_team3.bulk_print_receipts', [
                'class_id' => $class_id,
                'payment_ids' => implode(',', $payment_ids),
                'status' => is_array($status_filter) ? implode(',', $status_filter) : $status_filter
            ]);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la génération en masse des reçus: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            // Retourner un message d'erreur convivial
            return back()->with('flash_danger', 'Une erreur est survenue lors de la génération en masse des reçus: ' . $e->getMessage());
        }
    }
    
    /**
     * Imprime en masse les reçus des élèves ADRA & TEAM 3 d'une classe
     */
    public function bulkPrintAdraTeam3Receipts($class_id, $payment_ids, $status = null)
    {
        try {
            // Convertir les IDs de paiement en tableau
            $payment_ids = explode(',', $payment_ids);
            $status_filter = $status ? explode(',', $status) : ['ADRA', 'TEAM3'];
            
            // Récupérer les élèves avec les statuts sélectionnés de la classe sélectionnée
            $students = $this->student->getRecord(['my_class_id' => $class_id])->with(['user', 'my_class'])->get()
                ->filter(function($student) use ($status_filter) {
                    if (!$student->user) {
                        return false;
                    }
                    $status = $student->user->status ?? 'Normal';
                    return in_array($status, $status_filter);
                });
            
            // Si aucun élève n'est trouvé, rediriger avec un message d'erreur
            if ($students->isEmpty()) {
                return redirect()->route('payments.adra_team3')
                    ->with('flash_danger', 'Aucun élève avec les statuts sélectionnés trouvé dans cette classe');
            }
            
            // Récupérer les paiements
            $payments = $this->pay->findMany($payment_ids);
            
            // Si aucun paiement n'est trouvé, rediriger avec un message d'erreur
            if ($payments->isEmpty()) {
                return redirect()->route('payments.adra_team3')
                    ->with('flash_danger', 'Aucun paiement sélectionné trouvé');
            }
            
            // Préparer les données pour la vue
            $receipts = [];
            
            foreach ($students as $student) {
                $status = $student->user->status;
                $percentage = ($status === 'ADRA') ? 0.75 : 1.0;
                
                $totalAmount = 0;
                $paymentDetails = [];
                $receiptIds = [];
                $refNos = [];
                
                // Utiliser le code de référence existant ou en générer un nouveau
                $baseRefNo = $student->adm_no ? $student->adm_no : 'CPA/' . $student->my_class->class_type->code . $student->my_class->id . '/' . $this->year;
                
                foreach ($payments as $payment) {
                    // Calculer le montant à facturer
                    $originalAmount = $payment->amount;
                    $amountToBill = $originalAmount * $percentage;
                    
                    // Générer un numéro de référence unique pour ce paiement
                    $uniqueRefNo = $this->generateUniqueRefNo($baseRefNo, $payment->id);
                    $refNos[] = $uniqueRefNo;
                    
                    // Créer ou mettre à jour l'enregistrement de paiement
                    $pr = $this->pay->findMyPR($student->user_id, $payment->id)->first();
                    if (!$pr) {
                        $pr_data = [
                            'student_id' => $student->user_id,
                            'payment_id' => $payment->id,
                            'year' => $this->year,
                            'ref_no' => $uniqueRefNo,
                            'amt_paid' => $amountToBill,
                            'balance' => ($status === 'ADRA') ? ($originalAmount - $amountToBill) : 0,
                            'paid' => ($status === 'TEAM3') ? 1 : 0,
                            'methode' => $status
                        ];
                        $pr = $this->pay->createRecord($pr_data);
                    } else {
                        $pr->ref_no = $uniqueRefNo;
                        $pr->amt_paid = $amountToBill;
                        $pr->balance = ($status === 'ADRA') ? ($originalAmount - $amountToBill) : 0;
                        $pr->paid = ($status === 'TEAM3') ? 1 : 0;
                        $pr->methode = $status;
                        $pr->save();
                    }
                    
                    // Créer un reçu
                    $receipt_data = [
                        'pr_id' => $pr->id,
                        'amt_paid' => $amountToBill,
                        'balance' => ($status === 'ADRA') ? ($originalAmount - $amountToBill) : 0,
                        'year' => $this->year,
                        'methode' => $status,
                        'payment_method' => $status,
                        'created_by' => auth()->user()->name,
                        'reference_number' => $uniqueRefNo,
                        'observations' => "Reçu {$status} généré automatiquement en masse"
                    ];
                    
                    $receipt = $this->pay->createReceipt($receipt_data);
                    if ($receipt) {
                        $receiptIds[] = $receipt->id;
                    }
                    
                    // Ajouter les détails du paiement
                    $paymentDetails[] = [
                        'title' => $payment->title,
                        'original_amount' => $originalAmount,
                        'paid_amount' => $amountToBill,
                        'percentage' => $percentage * 100
                    ];
                    
                    $totalAmount += $amountToBill;
                }
                
                // Stocker les données du reçu pour cet élève
                $receipts[] = [
                    'student' => $student,
                    'status' => $status,
                    'reference_code' => $student->adm_no ?? (count($refNos) === 1 ? $refNos[0] : 'Multiple'),
                    'reference_codes' => $refNos,
                    'payment_details' => $paymentDetails,
                    'total_amount' => $totalAmount,
                    'receipt_date' => now()->format('d/m/Y'),
                    'receipt_ids' => $receiptIds
                ];
            }
            
            // Préparer les données pour la vue
            $d['receipts'] = $receipts;
            $d['payments'] = $payments;
            $d['class_id'] = $class_id;
            $d['s'] = Setting::all()->flatMap(function($s){
                return [$s->type => $s->description];
            });
            
            return view('pages.support_team.payments.adra_team3_bulk_receipts', $d);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de l\'impression en masse des reçus: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            // Retourner un message d'erreur convivial
            return redirect()->route('payments.adra_team3')
                ->with('flash_danger', 'Une erreur est survenue lors de l\'impression en masse des reçus: ' . $e->getMessage());
        }
    }
    
    /**
     * Exporte les données des élèves ADRA & TEAM 3 au format Excel
     */
    public function exportAdraTeam3Excel(Request $request)
    {
        try {
            $class_id = $request->class_id;
            $payment_ids = $request->payments ? explode(',', $request->payments) : [];
            $status_filter = $request->status ? explode(',', $request->status) : ['ADRA', 'TEAM3'];
            
            // Si aucune classe n'est sélectionnée, rediriger avec un message d'erreur
            if (!$class_id) {
                return back()->with('flash_danger', 'Veuillez sélectionner une classe');
            }
            
            // Récupérer les élèves avec les statuts sélectionnés de la classe sélectionnée
            $students = $this->student->getRecord(['my_class_id' => $class_id])->with(['user', 'my_class'])->get()
                ->filter(function($student) use ($status_filter) {
                    if (!$student->user) {
                        return false;
                    }
                    $status = $student->user->status ?? 'Normal';
                    return in_array($status, $status_filter);
                });
                
            if ($students->isEmpty()) {
                return back()->with('flash_danger', 'Aucun élève avec les statuts sélectionnés trouvé dans cette classe');
            }
            
            // Récupérer tous les paiements actifs
            $all_payments = $this->pay->getActivePayments()->get();
            
            // Filtrer les paiements si des IDs spécifiques sont fournis
            $payments = !empty($payment_ids) 
                ? $all_payments->whereIn('id', $payment_ids) 
                : $all_payments;
            
            // Préparer les données pour l'export Excel
            $exportData = [];
            
            // Ajouter l'en-tête
            $headers = [
                'Nom de l\'élève', 
                'Classe', 
                'Statut', 
                'Code de référence', 
                'Paiements sélectionnés', 
                'Montant total payé', 
                'Méthode de paiement'
            ];
            
            $exportData[] = $headers;
            
            // Ajouter les données des élèves
            foreach ($students as $student) {
                $status = $student->user->status ?? 'Normal';
                $percentage = ($status === 'ADRA') ? 0.75 : 1.0;
                
                $totalAmount = 0;
                $paymentsList = [];
                
                // Calculer le montant total et préparer la liste des paiements
                foreach ($payments as $payment) {
                    $amountToBill = $payment->amount * $percentage;
                    $totalAmount += $amountToBill;
                    $paymentsList[] = $payment->title . ' (' . number_format($amountToBill, 0, ',', ' ') . ' Ar)';
                }
                
                // Méthode de paiement selon le statut
                $paymentMethod = ($status === 'ADRA') ? 'ADRA (75%)' : (($status === 'TEAM3') ? 'TEAM 3 (100%)' : 'Normal');
                
                // Préparer la ligne pour cet élève
                $row = [
                    $student->user->name,
                    $student->my_class->name,
                    $status,
                    $student->adm_no ?? '',
                    implode(', ', $paymentsList),
                    number_format($totalAmount, 0, ',', ' ') . ' Ar',
                    $paymentMethod
                ];
                
                $exportData[] = $row;
            }
            
            // Générer le fichier Excel
            $filename = 'Eleves_' . implode('_', $status_filter) . '_' . date('Y-m-d') . '.csv';
            $tempFile = tempnam(sys_get_temp_dir(), 'csv');
            
            $file = fopen($tempFile, 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // Ajouter BOM pour UTF-8
            
            // Écrire les données dans le fichier CSV
            foreach ($exportData as $row) {
                fputcsv($file, $row, ';'); // Utiliser le point-virgule comme séparateur pour Excel
            }
            
            fclose($file);
            
            // Télécharger le fichier CSV
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de l\'export Excel ADRA/TEAM3: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            // Retourner un message d'erreur convivial
            return back()->with('flash_danger', 'Une erreur est survenue lors de l\'export Excel: ' . $e->getMessage());
        }
    }
    
    /**
     * Génère un numéro de référence unique pour un paiement
     * 
     * @param string $baseRefNo Le numéro de référence de base
     * @param int $paymentId L'ID du paiement
     * @return string Le numéro de référence unique
     */
    private function generateUniqueRefNo($baseRefNo, $paymentId)
    {
        // Ajouter l'ID du paiement pour rendre le numéro unique par paiement
        $refNo = $baseRefNo . '/' . $paymentId;
        
        // Vérifier si ce numéro existe déjà
        $exists = \DB::table('payment_records')->where('ref_no', $refNo)->exists();
        
        // Si le numéro existe déjà, ajouter un suffixe aléatoire
        if ($exists) {
            $suffix = mt_rand(1000, 9999);
            $refNo = $refNo . '-' . $suffix;
            
            // Vérifier à nouveau (au cas où)
            while (\DB::table('payment_records')->where('ref_no', $refNo)->exists()) {
                $suffix = mt_rand(1000, 9999);
                $refNo = $baseRefNo . '/' . $paymentId . '-' . $suffix;
            }
        }
        
        return $refNo;
    }
    
    /**
     * Sauvegarde le code de référence d'un élève ADRA ou TEAM 3
     */
    public function saveAdraTeam3Reference(Request $request)
    {
        try {
            // Valider les données
            $request->validate([
                'student_id' => 'required|exists:student_records,id',
                'reference_code' => 'required|string|max:50',
            ]);
            
            $student_id = $request->student_id;
            $reference_code = $request->reference_code;
            
            // Récupérer l'élève
            $student = $this->student->getRecord(['id' => $student_id])->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Élève introuvable'
                ], 404);
            }
            
            // Mettre à jour le code de référence (adm_no)
            $student->adm_no = $reference_code;
            $student->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Code de référence enregistré avec succès',
                'reference_code' => $reference_code
            ]);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de la sauvegarde du code de référence: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }
}
