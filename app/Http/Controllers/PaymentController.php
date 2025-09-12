<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Helpers\Pay;
use PDF;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['student', 'class'])->orderBy('created_at', 'desc')->get();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string',
            'description' => 'nullable|string|max:255'
        ]);

        $payment = Payment::create($validatedData);
        Pay::createTransaction($payment);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function generateInvoice(Payment $payment)
    {
        $pdf = PDF::loadView('payments.invoice', compact('payment'));
        return $pdf->download('invoice-' . $payment->id . '.pdf');
    }

    public function verified($class_id = NULL)
    {
        $payments = Payment::when($class_id, function($query) use ($class_id) {
            return $query->where('class_id', $class_id);
        })->verified()->get();
        
        return view('payments.verified', compact('payments'));
    }

    public function filter(Request $request)
    {
        $query = Payment::query();
        
        if ($request->has('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        if ($request->has('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        $payments = $query->get();
        return view('payments.index', compact('payments'));
    }
}
