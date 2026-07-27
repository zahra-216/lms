<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\PaymentPlan;
use App\Models\StudentPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $students = Student::with(['course.faculty', 'level'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('registration_no', 'like', "%{$search}%");
                });
            })
            ->get();

        $levelOrder = ['Diploma', 'HND', 'Top-up', 'Degree'];
        $grouped = [];

        foreach ($levelOrder as $levelName) {
            $levelStudents = $students->filter(function ($student) use ($levelName) {
                return $student->level && $student->level->name === $levelName;
            });

            $grouped[$levelName] = $levelStudents
                ->groupBy(function ($student) {
                    return optional(optional($student->course)->faculty)->name ?? 'Unassigned Faculty';
                })
                ->map(function ($facultyStudents) {
                    return $facultyStudents->groupBy(function ($student) {
                        return optional($student->course)->name ?? 'Unassigned Course';
                    });
                });
        }

        return view('admin.payments.index', ['grouped' => $grouped, 'search' => $search]);
    }

    public function show($studentId)
    {
        $student = Student::with(['course.faculty', 'level'])->findOrFail($studentId);
        $plan = PaymentPlan::where('student_id', $studentId)->first();
        $payments = StudentPayment::where('student_id', $studentId)->latest('date')->get();
        $totalPaid = $payments->sum('amount');

        return view('admin.payments.show', compact('student', 'plan', 'payments', 'totalPaid'));
    }

    public function storePlan(Request $request, $studentId)
    {
        $request->validate([
            'total_installments' => 'required|integer|min:1',
            'total_fee' => 'required|numeric|min:0',
        ]);

        PaymentPlan::updateOrCreate(
            ['student_id' => $studentId],
            ['total_installments' => $request->total_installments, 'total_fee' => $request->total_fee]
        );

        return back()->with('success', 'Payment plan saved!');
    }

    public function storePayment(Request $request, $studentId)
    {
        $request->validate([
            'type_of_payment' => 'required|in:cash,card,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'invoice_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        StudentPayment::create($request->all() + ['student_id' => $studentId]);

        return back()->with('success', 'Payment added!');
    }

    public function editPayment($id)
    {
        $payment = StudentPayment::findOrFail($id);
        return view('admin.payments.edit', compact('payment'));
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'type_of_payment' => 'required|in:cash,card,bank_transfer',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'invoice_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $payment = StudentPayment::findOrFail($id);
        $payment->update($request->all());

        return redirect()->route('admin.payments.show', $payment->student_id)
            ->with('success', 'Payment updated!');
    }

    public function destroyPayment($id)
    {
        $payment = StudentPayment::findOrFail($id);
        $studentId = $payment->student_id;
        $payment->delete();

        return redirect()->route('admin.payments.show', $studentId)
            ->with('success', 'Payment deleted!');
    }
}