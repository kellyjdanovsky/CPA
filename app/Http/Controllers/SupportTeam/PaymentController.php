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
        } catch (ModelNotFoundException $ex) {
            return back()->with('flash_danger', __('msg.rnf'));
        }
        $d['receipts'] = $pr->receipt;
        $d['payment'] = $pr->payment;
        $d['sr'] = $this->student->findByUserId($pr->student_id)->first();
        $d['s'] = Setting::all()->flatMap(function($s){
            return [$s->type => $s->description];
        });

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

    public function printSpecialReceipts(Request $request)
    {
        try {
            // Valider les données de la requête
            $request->validate([
                'receipt_ids' => 'required|string',
            ]);

            // Récupérer les IDs des reçus
            $receiptIds = explode(',', $request->receipt_ids);

            if (empty($receiptIds)) {
                return back()->with('flash_danger', 'Aucun reçu à imprimer');
            }

            // Récupérer les enregistrements de paiement
            $paymentRecords = [];
            foreach ($receiptIds as $prId) {
                try {
                    $pr = $this->pay->getRecord(['id' => $prId])->with('receipt')->first();
                    if ($pr) {
                        $pr->student = $this->student->findByUserId($pr->student_id)->first();
                        $pr->payment = $this->pay->find($pr->payment_id);
                        $paymentRecords[] = $pr;
                    }
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de la récupération du reçu ID {$prId}: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($paymentRecords)) {
                return back()->with('flash_danger', 'Aucun reçu valide trouvé');
            }

            // Récupérer les paramètres du système
            $settings = Setting::all()->flatMap(function($s){
                return [$s->type => $s->description];
            });

            // Passer les données à la vue
            return view('pages.support_team.payments.print_special_receipts', [
                'payment_records' => $paymentRecords,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de l\'impression des reçus: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            // Retourner un message d'erreur convivial
            return back()->with('flash_danger', 'Une erreur est survenue lors de l\'impression des reçus: ' . $e->getMessage());
        }
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
    $id_pay = $request->my_paymets_id;
    $id_class = $request->my_class_id;

    $nom_classe = MyClass::where('id', $id_class)->first();
    $nom_payment = Payment::where('id', $id_pay)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->first();

    if (!$nom_classe) {
        return back()->with('flash_danger', 'Classe introuvable. Veuillez sélectionner une classe valide.');
    }

    if (!$nom_payment) {
        return back()->with('flash_danger', 'Motif de paiement introuvable. Veuillez sélectionner un motif de paiement valide pour cette classe.');
    }

    // Récupérer tous les étudiants de la classe
    $students = StudentRecord::where('my_class_id', $id_class)->get();

    // Préparer les données pour la vue
    $unpaidStudents = [];

    foreach ($students as $student) {
        // Récupérer l'enregistrement de paiement avec les reçus
        $paymentRecord = PaymentRecord::with('receipt')
            ->where('student_id', $student->user_id)
            ->where('payment_id', $id_pay)
            ->first();

        if (!$paymentRecord) {
            continue; // Ignorer si aucun enregistrement de paiement n'existe
        }

        // Récupérer le statut de l'étudiant
        $status = $student->user->status ?? 'Normal';

        // Ignorer les étudiants avec le statut TEAM3
        if ($status === 'TEAM3') {
            continue;
        }

        // Pour les étudiants ADRA, vérifier s'ils ont payé au moins 25% du montant
        if ($status === 'ADRA') {
            $totalAmount = $nom_payment->amount;

            // Récupérer le montant total des reçus pour cet enregistrement
            $totalReceiptAmount = 0;
            $receipts = $paymentRecord->receipt;
            if ($receipts && $receipts->count() > 0) {
                $totalReceiptAmount = $receipts->sum('amt_paid');
            }

            // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
            $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

            // S'assurer que le montant payé dans l'enregistrement est à jour
            if ($totalReceiptAmount > 0 && $paymentRecord->amt_paid != $totalReceiptAmount) {
                $paymentRecord->amt_paid = $totalReceiptAmount;
                $paymentRecord->save();
            }

            // Éviter la division par zéro
            if ($totalAmount > 0) {
                $paidPercentage = ($paidAmount / $totalAmount) * 100;

                // Si l'étudiant ADRA a payé au moins 25%, l'ignorer
                if ($paidPercentage >= 25) {
                    // Mettre à jour le statut de paiement pour refléter la règle ADRA
                    if (!$paymentRecord->paid) {
                        $paymentRecord->paid = 1;
                        $paymentRecord->balance = 0;
                        $paymentRecord->save();
                    }
                    continue;
                }
            } else {
                // Si le montant total est 0, considérer comme payé
                if (!$paymentRecord->paid) {
                    $paymentRecord->paid = 1;
                    $paymentRecord->balance = 0;
                    $paymentRecord->save();
                }
                continue;
            }
        } else {
            // Pour les étudiants normaux, vérifier s'ils ont payé complètement
            if ($paymentRecord->paid) {
                continue;
            }
        }

        // Récupérer la date du dernier paiement
        $lastPaymentDate = null;
        $receipts = $paymentRecord->receipt;

        // Récupérer le montant total des reçus pour cet enregistrement
        $totalReceiptAmount = 0;
        if ($receipts && $receipts->count() > 0) {
            $lastPaymentDate = $receipts->sortByDesc('created_at')->first()->created_at;
            $totalReceiptAmount = $receipts->sum('amt_paid');
        }

        // S'assurer que le montant payé dans l'enregistrement est à jour
        if ($totalReceiptAmount > 0 && $paymentRecord->amt_paid != $totalReceiptAmount) {
            $paymentRecord->amt_paid = $totalReceiptAmount;
            $paymentRecord->save();
        }

        // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
        $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

        // Calculer le montant dû correctement selon le statut
        if ($status === 'ADRA') {
            // Pour les élèves ADRA, le montant dû est la différence pour atteindre 25% du montant total
            $minimumRequired = $nom_payment->amount * 0.25;

            if ($paidAmount >= $minimumRequired) {
                // Si l'élève a déjà payé au moins 25%, le montant dû est 0
                $amountDue = 0;
            } else {
                // Sinon, le montant dû est ce qui reste à payer pour atteindre 25%
                $amountDue = $minimumRequired - $paidAmount;
            }
        } else {
            // Pour les autres statuts, le montant dû est le montant total moins le montant payé
            $amountDue = $nom_payment->amount - $paidAmount;
        }

        // Si le montant dû est négatif, le mettre à 0
        if ($amountDue < 0) {
            $amountDue = 0;
        }

        // Si le montant dû est 0 mais que le montant payé est aussi 0, alors le montant dû est le montant total
        // ou 25% du montant total pour les élèves ADRA
        if ($amountDue == 0 && $paidAmount == 0) {
            if ($status === 'ADRA') {
                $amountDue = $nom_payment->amount * 0.25;
            } else {
                $amountDue = $nom_payment->amount;
            }
        }

        // Déterminer l'état du paiement
        $paymentState = 'Impayé';
        if ($paymentRecord->paid) {
            $paymentState = 'Acquitté';
        }

        // Ajouter l'étudiant à la liste des impayés
        $unpaidStudents[] = [
            'student' => $student,
            'payment_record' => $paymentRecord,
            'status' => $status,
            'amount_due' => $amountDue,
            'total_amount' => $nom_payment->amount,
            'amount_paid' => $paymentRecord->amt_paid,
            'last_payment_date' => $lastPaymentDate,
            'payment_state' => $paymentState
        ];
    }

    return view('pages.support_team.payments.unpaid', [
        'unpaid_students' => $unpaidStudents,
        'nom_classe' => $nom_classe,
        'nom_payment' => $nom_payment,
        'id_pay' => $id_pay,
        'id_class' => $id_class
    ]);
}

public function exportUnpaidExcel(Request $request)
{
    $id_pay = $request->id_pay;
    $id_class = $request->id_class;

    $nom_classe = MyClass::where('id', $id_class)->first();
    $nom_payment = Payment::where('id', $id_pay)
        ->where(function($query) use ($id_class) {
            $query->where('my_class_id', $id_class)
                  ->orWhereNull('my_class_id');
        })
        ->first();

    if (!$nom_classe || !$nom_payment) {
        return back()->with('flash_danger', 'Classe ou motif de paiement introuvable');
    }

    // Récupérer tous les étudiants de la classe
    $students = StudentRecord::where('my_class_id', $id_class)->get();

    // Préparer les données pour l'export
    $data = [];

    // En-tête du tableau
    $data[] = ['Nom', 'Classe', 'Objet impayé', 'Statut', 'Montant total', 'Montant payé', 'Montant dû', 'Date de paiement'];

    foreach ($students as $student) {
        // Récupérer l'enregistrement de paiement avec les reçus
        $paymentRecord = PaymentRecord::with('receipt')
            ->where('student_id', $student->user_id)
            ->where('payment_id', $id_pay)
            ->first();

        if (!$paymentRecord) {
            continue; // Ignorer si aucun enregistrement de paiement n'existe
        }

        // Récupérer le statut de l'étudiant
        $status = $student->user->status ?? 'Normal';

        // Ignorer les étudiants avec le statut TEAM3
        if ($status === 'TEAM3') {
            continue;
        }

        // Pour les étudiants ADRA, vérifier s'ils ont payé au moins 25% du montant
        if ($status === 'ADRA') {
            $totalAmount = $nom_payment->amount;

            // Récupérer le montant total des reçus pour cet enregistrement
            $totalReceiptAmount = 0;
            $receipts = $paymentRecord->receipt;
            if ($receipts && $receipts->count() > 0) {
                $totalReceiptAmount = $receipts->sum('amt_paid');
            }

            // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
            $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

            // S'assurer que le montant payé dans l'enregistrement est à jour
            if ($totalReceiptAmount > 0 && $paymentRecord->amt_paid != $totalReceiptAmount) {
                $paymentRecord->amt_paid = $totalReceiptAmount;
                $paymentRecord->save();
            }

            // Éviter la division par zéro
            if ($totalAmount > 0) {
                $paidPercentage = ($paidAmount / $totalAmount) * 100;

                // Si l'étudiant ADRA a payé au moins 25%, l'ignorer
                if ($paidPercentage >= 25) {
                    // Mettre à jour le statut de paiement pour refléter la règle ADRA
                    if (!$paymentRecord->paid) {
                        $paymentRecord->paid = 1;
                        $paymentRecord->balance = 0;
                        $paymentRecord->save();
                    }
                    continue;
                }
            } else {
                // Si le montant total est 0, considérer comme payé
                if (!$paymentRecord->paid) {
                    $paymentRecord->paid = 1;
                    $paymentRecord->balance = 0;
                    $paymentRecord->save();
                }
                continue;
            }
        } else {
            // Pour les étudiants normaux, vérifier s'ils ont payé complètement
            if ($paymentRecord->paid) {
                continue;
            }
        }

        // Récupérer la date du dernier paiement
        $lastPaymentDate = '-';
        $receipts = $paymentRecord->receipt;

        // Récupérer le montant total des reçus pour cet enregistrement
        $totalReceiptAmount = 0;
        if ($receipts && $receipts->count() > 0) {
            $lastPaymentDate = date('d/m/Y', strtotime($receipts->sortByDesc('created_at')->first()->created_at));
            $totalReceiptAmount = $receipts->sum('amt_paid');
        }

        // S'assurer que le montant payé dans l'enregistrement est à jour
        if ($totalReceiptAmount > 0 && $paymentRecord->amt_paid != $totalReceiptAmount) {
            $paymentRecord->amt_paid = $totalReceiptAmount;
            $paymentRecord->save();
        }

        // Utiliser le montant des reçus si disponible, sinon utiliser amt_paid de l'enregistrement
        $paidAmount = $totalReceiptAmount > 0 ? $totalReceiptAmount : ($paymentRecord->amt_paid ?: 0);

        // Calculer le montant dû correctement selon le statut
        if ($status === 'ADRA') {
            // Pour les élèves ADRA, le montant dû est la différence pour atteindre 25% du montant total
            $minimumRequired = $nom_payment->amount * 0.25;

            if ($paidAmount >= $minimumRequired) {
                // Si l'élève a déjà payé au moins 25%, le montant dû est 0
                $amountDue = 0;
            } else {
                // Sinon, le montant dû est ce qui reste à payer pour atteindre 25%
                $amountDue = $minimumRequired - $paidAmount;
            }
        } else {
            // Pour les autres statuts, le montant dû est le montant total moins le montant payé
            $amountDue = $nom_payment->amount - $paidAmount;
        }

        // Si le montant dû est négatif, le mettre à 0
        if ($amountDue < 0) {
            $amountDue = 0;
        }

        // Si le montant dû est 0 mais que le montant payé est aussi 0, alors le montant dû est le montant total
        // ou 25% du montant total pour les élèves ADRA
        if ($amountDue == 0 && $paidAmount == 0) {
            if ($status === 'ADRA') {
                $amountDue = $nom_payment->amount * 0.25;
            } else {
                $amountDue = $nom_payment->amount;
            }
        }

        // Déterminer l'état du paiement
        $paymentState = 'Impayé';
        if ($paymentRecord->paid) {
            $paymentState = 'Acquitté';
        }

        // Ajouter les données de l'étudiant au tableau
        $data[] = [
            $student->user->name,
            $nom_classe->name . ' ' . $student->section->name,
            $nom_payment->title,
            $status,
            number_format($nom_payment->amount, 0, ',', ' ') . ' Ar',
            number_format($paymentRecord->amt_paid, 0, ',', ' ') . ' Ar',
            number_format($amountDue, 0, ',', ' ') . ' Ar',
            $lastPaymentDate
        ];
    }

    // Générer le fichier Excel
    $fileName = 'Impayés_' . $nom_classe->name . '_' . $nom_payment->title . '_' . date('Y-m-d') . '.xlsx';

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
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(20);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(20);

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

}
