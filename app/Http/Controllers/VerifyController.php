<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function show()
    {
        return view('verify.show');
    }

    public function check(Request $request)
    {
        $request->validate([
            'registration_no' => 'required|string',
            'certificate_number' => 'required|string',
        ]);

        $certificate = Certificate::whereHas('student', function ($q) use ($request) {
                $q->where('registration_no', $request->registration_no);
            })
            ->where('certificate_number', $request->certificate_number)
            ->first();

        if (!$certificate) {
            return back()->withInput()->with('error', 'No matching certificate found. Please check the details and try again.');
        }

        return view('verify.result', compact('certificate'));
    }
}