<?php

namespace App\Http\Controllers\SupportTeam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\User;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['student', 'generator']);

        if ($request->filled('type')) {
            $query->type($request->type);
        }
        if ($request->filled('academic_year')) {
            $query->year($request->academic_year);
        }
        if ($request->filled('student_id')) {
            $query->student($request->student_id);
        }

        $certificates = $query->latest()->get();
        $types = [
            'scolarite' => 'Certificat de Scolarité',
            'frequentation' => 'Certificat de Fréquentation',
            'reussite' => 'Attestation de Réussite',
            'fin_etudes' => 'Certificat de Fin d\'Études',
            'paiement' => 'Attestation de Paiement',
            'transfert' => 'Lettre de Transfert',
        ];
        $years = Certificate::select('academic_year')->distinct()->pluck('academic_year');

        return view('pages.support_team.certificates.index', compact('certificates', 'types', 'years'));
    }

    public function create()
    {
        $types = [
            'scolarite' => 'Certificat de Scolarité',
            'frequentation' => 'Certificat de Fréquentation',
            'reussite' => 'Attestation de Réussite',
            'fin_etudes' => 'Certificat de Fin d\'Études',
            'paiement' => 'Attestation de Paiement',
            'transfert' => 'Lettre de Transfert',
        ];
        return view('pages.support_team.certificates.create', compact('types'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'type' => 'required|in:scolarite,frequentation,reussite,fin_etudes,paiement,transfert',
            'date_issued' => 'required|date',
            'academic_year' => 'required|string',
            'details' => 'nullable|array',
        ]);

        $validated['reference_no'] = Certificate::generateReferenceNo($validated['type'], $validated['academic_year']);
        $validated['generated_by'] = Auth::id() ?? 1;

        $certificate = Certificate::create($validated);

        return redirect()->route('certificates.print', Qs::hash($certificate->id));
    }

    public function print($id)
    {
        $cert_id = Qs::decodeHash($id);
        $certificate = Certificate::with('student')->findOrFail($cert_id);

        return view('pages.support_team.certificates.print', compact('certificate'));
    }

    public function batchGenerate(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'type' => 'required|in:scolarite,frequentation,reussite,fin_etudes,paiement,transfert',
                'date_issued' => 'required|date',
                'academic_year' => 'required|string',
                'student_ids' => 'required|array',
            ]);

            foreach ($request->student_ids as $student_id) {
                Certificate::create([
                    'student_id' => $student_id,
                    'type' => $request->type,
                    'reference_no' => Certificate::generateReferenceNo($request->type, $request->academic_year),
                    'date_issued' => $request->date_issued,
                    'academic_year' => $request->academic_year,
                    'generated_by' => Auth::id() ?? 1,
                    'details' => $request->details ?? null,
                ]);
            }
            
            return Qs::goWithSuccess('Certificats générés avec succès.');
        }

        $types = [
            'scolarite' => 'Certificat de Scolarité',
            'frequentation' => 'Certificat de Fréquentation',
            'reussite' => 'Attestation de Réussite',
            'fin_etudes' => 'Certificat de Fin d\'Études',
            'paiement' => 'Attestation de Paiement',
            'transfert' => 'Lettre de Transfert',
        ];
        return view('pages.support_team.certificates.batch_generate', compact('types'));
    }

    public function exportList(Request $request)
    {
        // Simple export placeholder
        return redirect()->back()->with('flash_success', 'Export functionality requires further setup with Maatwebsite or similar.');
    }

    public function destroy($id)
    {
        $cert_id = Qs::decodeHash($id);
        $certificate = Certificate::findOrFail($cert_id);
        $certificate->delete();

        return back()->with('flash_success', 'Certificat supprimé avec succès.');
    }
}
