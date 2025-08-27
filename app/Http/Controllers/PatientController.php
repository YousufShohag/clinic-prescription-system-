<?php
namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {

        $patients = Patient::with('doctor')      // keep doctor name
        ->withCount('prescriptions')                    // adds prescriptions_count
        ->orderBy('name')
        ->paginate(10);

        // $patients = Patient::with('doctor')->get();
        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        $doctors = Doctor::all();
        return view('admin.patients.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'doctor_id' => 'nullable|exists:doctors,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        Patient::create($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    public function edit(Patient $patient)
    {
        $doctors = Doctor::all();
        return view('admin.patients.edit', compact('patient', 'doctors'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'doctor_id' => 'nullable|exists:doctors,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}
