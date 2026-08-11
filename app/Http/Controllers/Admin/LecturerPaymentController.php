<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\LecturerPayment;
use App\Models\Course;
use Illuminate\Http\Request;

class LecturerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $lecturers = Lecturer::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        })->get();

        return view('admin.lecturer-payments.index', compact('lecturers', 'search'));
    }

    public function show($lecturerId)
    {
        $lecturer = Lecturer::findOrFail($lecturerId);
        $courses = Course::all();
        $payments = LecturerPayment::with('courses')
            ->where('lecturer_id', $lecturerId)
            ->latest('date')
            ->get();

        return view('admin.lecturer-payments.show', compact('lecturer', 'courses', 'payments'));
    }

    public function store(Request $request, $lecturerId)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'type_of_lecture' => 'required|in:online,physical',
            'date' => 'required|date',
            'total_hours' => 'nullable|numeric|min:0',
            'payment_type' => 'required|in:per_month,per_hour,per_day',
            'rate_amount' => 'required|numeric|min:0',
            'total_payment' => 'required|numeric|min:0',
            'completed_payment' => 'nullable|numeric|min:0',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $payment = LecturerPayment::create($request->except('course_ids') + ['lecturer_id' => $lecturerId]);
        $payment->courses()->sync($request->course_ids);

        return back()->with('success', 'Payment record added!');
    }

    public function edit($id)
    {
        $payment = LecturerPayment::with('courses')->findOrFail($id);
        $courses = Course::all();
        $selectedCourseIds = $payment->courses->pluck('id')->toArray();

        return view('admin.lecturer-payments.edit', compact('payment', 'courses', 'selectedCourseIds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'type_of_lecture' => 'required|in:online,physical',
            'date' => 'required|date',
            'total_hours' => 'nullable|numeric|min:0',
            'payment_type' => 'required|in:per_month,per_hour,per_day',
            'rate_amount' => 'required|numeric|min:0',
            'total_payment' => 'required|numeric|min:0',
            'completed_payment' => 'nullable|numeric|min:0',
            'paid_date' => 'nullable|date',
            'invoice_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $payment = LecturerPayment::findOrFail($id);
        $payment->update($request->except('course_ids'));
        $payment->courses()->sync($request->course_ids);

        return redirect()->route('admin.lecturer-payments.show', $payment->lecturer_id)
            ->with('success', 'Payment updated!');
    }

    public function destroy($id)
    {
        $payment = LecturerPayment::findOrFail($id);
        $lecturerId = $payment->lecturer_id;
        $payment->delete();

        return redirect()->route('admin.lecturer-payments.show', $lecturerId)
            ->with('success', 'Payment deleted!');
    }
}