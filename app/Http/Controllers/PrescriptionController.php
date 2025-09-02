<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['doctor'])
            ->withCount('prescriptions')
            ->latest()
            // ->orderBy('name')
            ->paginate(10);

        $prescriptions = Prescription::with(['doctor','patient'])
            ->latest()
            ->paginate(15);

        return view('admin.prescriptions.index', compact('prescriptions','patients'));
    }

    public function create()
    {
        $patients  = Patient::orderBy('name')->get();
        $doctors   = Doctor::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();
        $tests     = Test::orderBy('name')->get();

        return view('admin.prescriptions.create', compact('patients', 'doctors', 'medicines', 'tests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'           => ['required', 'exists:doctors,id'],
            'patient_id'          => ['nullable'], // "__new" or existing id
            'problem_description' => ['nullable', 'string'],
            'doctor_advice'       => ['nullable', 'string'],
            'return_date'         => ['nullable', 'string'], // accept dd/mm/yyyy; will normalize below

            // Clinical findings
            'oe'               => ['nullable', 'string'],
            'bp'               => ['nullable', 'string', 'max:50'],
            'pulse'            => ['nullable', 'integer', 'min:0', 'max:300'],
            'temperature_c'    => ['nullable', 'numeric', 'min:20', 'max:45'],
            'spo2'             => ['nullable', 'integer', 'min:0', 'max:100'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:80'],
            'weight_kg'        => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height_cm'        => ['nullable', 'numeric', 'min:0', 'max:300'],
            'bmi'              => ['nullable', 'numeric', 'min:0', 'max:200'],

            // New patient (if used)
            'new_patient.name'      => ['nullable', 'string', 'max:255'],
            'new_patient.phone'     => ['nullable', 'string', 'max:50'],
            'new_patient.email'     => ['nullable', 'email', 'max:255'],
            'new_patient.notes'     => ['nullable', 'string'],
            'new_patient.age'       => ['nullable', 'integer', 'min:0'],
            'new_patient.sex'       => ['nullable', 'in:male,female,others'],
            'new_patient.images.*'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp'],
            'new_patient.documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx,webp'],
        ]);

        return DB::transaction(function () use ($request) {
            // Resolve/create patient
            $patientId = $request->input('patient_id');
            if ($patientId === '__new') {
                $new = $request->input('new_patient', []);
                if (empty($new['name'])) {
                    return back()->withInput()
                        ->withErrors(['patient_id' => 'Please provide a name for the new patient.']);
                }
                $images = [];
                if ($request->hasFile('new_patient.images')) {
                    foreach ($request->file('new_patient.images') as $img) {
                        $images[] = $img->store('patients/images', 'public');
                    }
                }

                $documents = [];
                if ($request->hasFile('new_patient.documents')) {
                    foreach ($request->file('new_patient.documents') as $doc) {
                        $documents[] = $doc->store('patients/documents', 'public');
                    }
                }

                $patient = Patient::create([
                    'name'      => $new['name'],
                    'phone'     => $new['phone'] ?? null,
                    'email'     => $new['email'] ?? null,
                    'notes'     => $new['notes'] ?? null,
                    'age'       => $new['age'] ?? null,
                    'sex'       => $new['sex'] ?? null,
                    'images'    => $images,
                    'documents' => $documents,
                    'doctor_id' => $request->input('doctor_id'),
                ]);
                $patientId = $patient->id;
            }

            // Normalize return_date (accepts dd/mm/yyyy)
            $normalizedReturn = $this->normalizeReturnDate($request->input('return_date'));

            // Prepare data for create()
            $data = $request->only([
                'doctor_id',
                'problem_description',
                'doctor_advice',
                'oe',
                'bp',
                'pulse',
                'temperature_c',
                'spo2',
                'respiratory_rate',
                'weight_kg',
                'height_cm',
                'bmi',
            ]);
            $data['patient_id']  = $patientId ?: null;
            $data['return_date'] = $normalizedReturn;

            // Compute BMI server-side
            $data['bmi'] = $this->computeBmi($data['weight_kg'] ?? null, $data['height_cm'] ?? null, $data['bmi'] ?? null);

            // Create the prescription
            $prescription = Prescription::create($data);

            // Attach medicines with pivot fields
            $medPivot = $this->extractMedicinePivot($request->input('medicines', []));
            if (!empty($medPivot)) {
                $prescription->medicines()->attach($medPivot);
            }

            // Attach tests
            $testIds = array_filter((array) $request->input('tests', []), fn($v) => !empty($v));
            if (!empty($testIds)) {
                $prescription->tests()->attach($testIds);
            }

            // Re-prescribe rule
            if ($normalizedReturn) {
                $this->setPatientNextReturnDate($patientId, $normalizedReturn);
            } else {
                $this->refreshPatientNextReturnDate($patientId);
            }

            return redirect()
                ->route('prescriptions.show', $prescription->id)
                ->with('success', 'Prescription created successfully!');
        });
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['doctor','patient','medicines','tests']);
        return view('admin.prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $prescription->load(['doctor','patient','medicines','tests']); // include pivots
        $patients  = Patient::orderBy('name')->get();
        $doctors   = Doctor::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();
        $tests     = Test::orderBy('name')->get();

        // For quick lookups in Blade
        $selectedMedicinePivot = $prescription->medicines->keyBy('id'); // each has ->pivot
        $selectedTestIds = $prescription->tests->pluck('id')->toArray();

        return view('admin.prescriptions.edit', compact(
            'prescription','patients','doctors','medicines','tests','selectedMedicinePivot','selectedTestIds'
        ));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'doctor_id'           => ['required','exists:doctors,id'],
            'patient_id'          => ['nullable'], // existing id or "__new"
            'problem_description' => ['nullable','string'],
            'doctor_advice'       => ['nullable','string'],
            'return_date'         => ['nullable','string'], // normalize below

            // Clinical
            'oe'               => ['nullable','string'],
            'bp'               => ['nullable','string','max:50'],
            'pulse'            => ['nullable','integer','min:0','max:300'],
            'temperature_c'    => ['nullable','numeric','min:20','max:45'],
            'spo2'             => ['nullable','integer','min:0','max:100'],
            'respiratory_rate' => ['nullable','integer','min:0','max:80'],
            'weight_kg'        => ['nullable','numeric','min:0','max:500'],
            'height_cm'        => ['nullable','numeric','min:0','max:300'],
            'bmi'              => ['nullable','numeric','min:0','max:200'],

            // Optional new patient on edit
           'new_patient.name'      => ['nullable','string','max:255'],
            'new_patient.phone'     => ['nullable','string','max:50'],
            'new_patient.email'     => ['nullable','email','max:255'],
            'new_patient.notes'     => ['nullable','string'],
            'new_patient.age'       => ['nullable','integer','min:0'],
            'new_patient.sex'       => ['nullable','in:male,female,others'],
            'new_patient.images.*'  => ['nullable','image','mimes:jpg,jpeg,png,gif,webp'],
            'new_patient.documents.*' => ['nullable','file','mimes:pdf,jpg,jpeg,png,gif,doc,docx,webp'],
        ]);

        return DB::transaction(function () use ($request, $prescription) {
            // Patient switch
            $patientId = $request->input('patient_id');
            if ($patientId === '__new') {
                $new = $request->input('new_patient', []);
                if (empty($new['name'])) {
                    return back()->withInput()->withErrors(['patient_id' => 'Please provide a name for the new patient.']);
                }
                 $images = [];
                if ($request->hasFile('new_patient.images')) {
                    foreach ($request->file('new_patient.images') as $img) {
                        $images[] = $img->store('patients/images', 'public');
                    }
                }

                $documents = [];
                if ($request->hasFile('new_patient.documents')) {
                    foreach ($request->file('new_patient.documents') as $doc) {
                        $documents[] = $doc->store('patients/documents', 'public');
                    }
                }
                $patient = Patient::create([
                    'name'  => $new['name'],
                    'phone' => $new['phone'] ?? null,
                    'email' => $new['email'] ?? null,
                    'notes' => $new['notes'] ?? null,
                ]);
                $patientId = $patient->id;
            }

            // Normalize return_date
            $normalizedReturn = $this->normalizeReturnDate($request->input('return_date'));

            $data = $request->only([
                'doctor_id','problem_description','doctor_advice',
                'oe','bp','pulse','temperature_c','spo2','respiratory_rate','weight_kg','height_cm','bmi',
            ]);
            $data['patient_id']  = $patientId ?: $prescription->patient_id;
            $data['return_date'] = $normalizedReturn;
            $data['bmi'] = $this->computeBmi($data['weight_kg'] ?? null, $data['height_cm'] ?? null, $data['bmi'] ?? null);

            $prescription->update($data);

            // sync pivots
            $medPivot = $this->extractMedicinePivot($request->input('medicines', []));
            $prescription->medicines()->sync($medPivot);
            $testIds = array_filter((array) $request->input('tests', []), fn($v) => !empty($v));
            $prescription->tests()->sync($testIds);

            // Re-prescribe rule
            if ($normalizedReturn) {
                $this->setPatientNextReturnDate($prescription->patient_id, $normalizedReturn);
            } else {
                $this->refreshPatientNextReturnDate($prescription->patient_id);
            }

            return redirect()
                ->route('prescriptions.show', $prescription)
                ->with('success', 'Prescription updated successfully.');
        });
    }

    public function destroy(Prescription $prescription)
    {
        $patientId = $prescription->patient_id;
        $prescription->delete();

        // Recompute after deletion
        if ($patientId) {
            $this->refreshPatientNextReturnDate($patientId);
        }

        return redirect()->route('prescriptions.index')->with('success','Prescription deleted.');
    }

    public function searchTests(Request $request)
    {
        $query = $request->get('q', '');
        $tests = Test::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'note')
            ->get();

        return response()->json($tests);
    }

    /** JSON: all prescriptions for a given patient (for sidebar/modal) */
    public function byPatient(Patient $patient)
    {
        $prescriptions = Prescription::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('created_at')
            ->get(['id','doctor_id','created_at','return_date']);

        $data = $prescriptions->map(function ($p) {
            return [
                'id'       => $p->id,
                'date'     => optional($p->created_at)->format('d M Y, h:i A'),
                'doctor'   => optional($p->doctor)->name ?? '—',
                'show_url' => route('prescriptions.show', ['prescription' => $p->id]),
                'return'   => $p->return_date ? Carbon::parse($p->return_date)->format('d/m/Y') : null,
            ];
        });

        return response()->json([
            'patient' => ['id' => $patient->id, 'name' => $patient->name],
            'count'   => $data->count(),
            'data'    => $data,
        ]);
    }

    // ----------------- Helpers -----------------

    /** Accept dd/mm/yyyy or any Carbon-parsable date; returns Y-m-d or null */
    private function normalizeReturnDate(?string $raw): ?string
    {
        if (!$raw) return null;

        // dd/mm/yyyy
        if (preg_match('~^\d{2}/\d{2}/\d{4}$~', $raw)) {
            try {
                return Carbon::createFromFormat('d/m/Y', $raw)->toDateString();
            } catch (\Throwable $e) {
                return null;
            }
        }

        // other formats
        try {
            return Carbon::parse($raw)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Force-set the patient's next_return_date to the given date (Y-m-d or null). */
    protected function setPatientNextReturnDate(int $patientId, ?string $date): void
    {
        Patient::where('id', $patientId)->update(['next_return_date' => $date]);
    }

    /** Recalculate next_return_date from prescriptions (nearest future, else latest past). */
    protected function refreshPatientNextReturnDate(int $patientId): void
    {
        $today = Carbon::today();

        // nearest upcoming (>= today)
        $next = Prescription::where('patient_id', $patientId)
            ->whereNotNull('return_date')
            ->whereDate('return_date', '>=', $today)
            ->orderBy('return_date', 'asc')
            ->value('return_date');

        // fallback to latest past if no future
        if (!$next) {
            $next = Prescription::where('patient_id', $patientId)
                ->whereNotNull('return_date')
                ->orderBy('return_date', 'desc')
                ->value('return_date');
        }

        Patient::where('id', $patientId)->update([
            'next_return_date' => $next,
        ]);
    }

    /** Build [id => ['duration'=>..., 'times_per_day'=>...], ...] for attach/sync */
    private function extractMedicinePivot(array $medicines): array
    {
        $out = [];
        foreach ($medicines as $id => $row) {
            if (!isset($row['selected']) || !$row['selected']) continue;
            $out[(int) $id] = [
                'duration'      => $row['duration']      ?? null,
                'times_per_day' => $row['times_per_day'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        return $out;
    }

    /** Compute BMI; fallback to provided value if not computable. */
    private function computeBmi($weightKg, $heightCm, $fallback = null)
    {
        $w = is_numeric($weightKg) ? (float) $weightKg : null;
        $h = is_numeric($heightCm) ? (float) $heightCm : null;

        if ($w && $h && $h > 0) {
            $hm = $h / 100.0;
            $bmi = $w / ($hm * $hm);
            return round($bmi, 1);
        }
        return is_numeric($fallback) ? round((float) $fallback, 1) : null;
    }
}
