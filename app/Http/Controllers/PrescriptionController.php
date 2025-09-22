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
// use Barryvdh\DomPDF\Facade\Pdf;
// use Illuminate\Http\Request;
// TCPDF ships as \TCPDF
use TCPDF;
// use Spatie\Browsershot\Browsershot;



class PrescriptionController extends Controller
{
    // public function index()
    // {
    //     $patients = Patient::with(['doctor'])
    //         ->withCount('prescriptions')
    //         ->latest()
    //         // ->orderBy('name')
    //         ->paginate(10);

    //     $prescriptions = Prescription::with(['doctor','patient'])
    //         ->latest()
    //         ->paginate(15);

    //     return view('admin.prescriptions.index', compact('prescriptions','patients'));
    // }

public function index(Request $request)
{
    $q        = (string) $request->query('q', '');
    $doctorId = $request->integer('doctor_id');
    $status   = (string) $request->query('status', ''); // overdue|today|upcoming|none
    $from     = $request->date('from');
    $to       = $request->date('to');

    // Optional: doctor dropdown in filters
    $doctors = Doctor::orderBy('name')->get(['id','name']);

    // (Left side panel you already had; keep if you need it)
    $patients = Patient::with('doctor')->withCount('prescriptions')->latest()->paginate(10);

    $today = Carbon::today();

    $prescriptions = Prescription::with(['doctor','patient'])
        ->withCount(['medicines','tests'])
        ->when($doctorId, fn($q) => $q->where('doctor_id', $doctorId))
        ->when($q, function ($query) use ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('id', $q) // quick jump by id
                   ->orWhere('problem_description', 'like', "%{$q}%")
                   ->orWhereHas('patient', fn($p) => $p->where('name', 'like', "%{$q}%"))
                   ->orWhereHas('doctor',  fn($d) => $d->where('name',  'like', "%{$q}%"));
            });
        })
        ->when($status, function ($query) use ($status, $today) {
            if ($status === 'overdue') {
                $query->whereNotNull('return_date')->whereDate('return_date', '<',  $today);
            } elseif ($status === 'today') {
                $query->whereDate('return_date', '=', $today);
            } elseif ($status === 'upcoming') {
                $query->whereDate('return_date', '>',  $today);
            } elseif ($status === 'none') {
                $query->whereNull('return_date');
            }
        })
        ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
        ->when($to,   fn($q) => $q->whereDate('created_at', '<=', $to))
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('admin.prescriptions.index', compact(
        'prescriptions','patients','doctors','q','doctorId','status','from','to'
    ));
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

            'ph'               => ['nullable', 'string'],
            'dh'               => ['nullable', 'string'],
            'mh'               => ['nullable', 'string'],
            'oh'               => ['nullable', 'string'],
            'pae'              => ['nullable', 'string'],
            'dx'               => ['nullable', 'string'],
            'previous_investigation' => ['nullable', 'string'],
            'ah'               => ['nullable', 'string'],
            'special_note'     => ['nullable', 'string'],
            'referred_to'      => ['nullable', 'string'],

            // New patient (if used)
            'new_patient.name'      => ['nullable', 'string', 'max:255'],
            'new_patient.phone'     => ['nullable', 'string', 'max:50'],
            'new_patient.email'     => ['nullable', 'email', 'max:255'],
            'new_patient.notes'     => ['nullable', 'string'],
            'new_patient.age'       => ['nullable', 'integer', 'min:0'],
            'new_patient.sex'       => ['nullable', 'in:male,female,others'],
            'new_patient.blood_group' => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'new_patient.guardian_name' => ['nullable','string','max:255'],
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
                    'blood_group' => $new['blood_group'] ?? null,
                    'guardian_name' => $new['guardian_name'] ?? null,
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
                'ph',
                'dh',
                'mh',
                'oh',
                'pae',
                'dx',
                'previous_investigation',
                'ah',
                'special_note',
                'referred_to',
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
    
 /** TCPDF version of the PDF */
    public function pdfTcpdf(Request $request, Prescription $prescription)
    {
         $prescription->load(['doctor', 'patient', 'medicines', 'tests']);

        // --- init tcpdf ---
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name', 'Laravel'));
        $pdf->SetAuthor(optional($prescription->doctor)->name ?? 'Doctor');
        $pdf->SetTitle('Prescription #'.$prescription->id);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // margins (L, T, R)
        $pdf->SetMargins(12, 14, 12);
        $pdf->SetAutoPageBreak(true, 16);

        // Use DejaVu Sans (bundled) for robust Unicode (e.g., Bangla)
        $pdf->SetFont('dejavusans', '', 10);

        $pdf->AddPage();

        // Render a simple, TCPDF-safe HTML view (no Tailwind / grid / flex)
        $html = view('admin.prescriptions.pdf_tcpdf', [
            'prescription' => $prescription,
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        // --- draw barcode (right-aligned near top of patient band) ---
        $barcodeValue = 'RX-' . $prescription->id;

        // Decide a safe spot; tweak Y as you like
        // You can also move this below patient block if your HTML grows taller.
        $x = $pdf->GetPageWidth() - $pdf->getMargins()['right'] - 37; // 60mm width for barcode cell
        $y = 50; // mm from top
        $w = 38;
        $h = 10; // barcode height

        $style = [
            'position'      => '',
            'align'         => 'R',
            'stretch'       => false,
            'fitwidth'      => true,
            'cellfitalign'  => '',
            'border'        => false,
            'hpadding'      => 'auto',
            'vpadding'      => 'auto',
            'fgcolor'       => [0, 0, 0],
            'bgcolor'       => false,
            'text'          => false,     // show human-readable text under barcode
            'font'          => 'dejavusans',
            'fontsize'      => 8,
            'stretchtext'   => 4
        ];

        $pdf->write1DBarcode($barcodeValue, 'C128', $x, $y, $w, $h, 0.4, $style, 'N');

        $filename = 'prescription_' . $prescription->id . '.pdf';

        // /prescriptions/{id}/pdf-tcpdf?download=1 to download, else stream
        if ($request->boolean('download')) {
            return $pdf->Output($filename, 'D'); // Download
        }
        return $pdf->Output($filename, 'I');     // Inline stream
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

            'ph'               => ['nullable','string'],
            'dh'               => ['nullable','string'],
            'mh'               => ['nullable','string'],
            'oh'               => ['nullable','string'],
            'pae'              => ['nullable','string'],
            'dx'               => ['nullable','string'],
            'previous_investigation' => ['nullable','string'],
            'ah'               => ['nullable','string'],
            'special_note'     => ['nullable','string'],
            'referred_to'      => ['nullable','string'],

            // Optional new patient on edit
           'new_patient.name'      => ['nullable','string','max:255'],
            'new_patient.phone'     => ['nullable','string','max:50'],
            'new_patient.email'     => ['nullable','email','max:255'],
            'new_patient.notes'     => ['nullable','string'],
            'new_patient.age'       => ['nullable','integer','min:0'],
            'new_patient.sex'       => ['nullable','in:male,female,others'],
            'new_patient.blood_group' => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'new_patient.guardian_name' => ['nullable','string','max:255'],
            'new_patient.images.*'  => ['nullable','image','mimes:jpg,jpeg,png,gif,webp'],
            'new_patient.documents.*' => ['nullable','file','mimes:pdf,jpg,jpeg,png,gif,doc,docx,webp'],

            'medicines.*.meal_time' => ['nullable','in:খাবারের আগে,খাবারের পরে,খাবারের সাথে,midday,bedtime'],
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
                    'blood_group' => $new['blood_group'] ?? null,
                    'guardian_name' => $new['guardian_name'] ?? null,
                ]);
                $patientId = $patient->id;
            }

            // Normalize return_date
            $normalizedReturn = $this->normalizeReturnDate($request->input('return_date'));

            $data = $request->only([
                'doctor_id','problem_description','doctor_advice',
                'oe','bp','pulse','temperature_c','spo2','respiratory_rate','weight_kg','height_cm','bmi',
                'ph','dh','mh','oh','pae','dx','previous_investigation','ah','special_note','referred_to',
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
    // private function extractMedicinePivot(array $medicines): array
    // {
    //     $out = [];
    //     foreach ($medicines as $id => $row) {
    //         if (!isset($row['selected']) || !$row['selected']) continue;
    //         $out[(int) $id] = [
    //             'duration'      => $row['duration']      ?? null,
    //             'times_per_day' => $row['times_per_day'] ?? null,
    //             'meal_time'     => $row['meal_time']     ?? null,
    //             'created_at'    => now(),
    //             'updated_at'    => now(),
    //         ];
    //     }
    //     return $out;
    // }

    /** Build [id => ['duration'=>..., 'times_per_day'=>..., 'meal_time'=>...], ...] for attach/sync */
private function extractMedicinePivot(array $medicines): array
{
    // English key → Bangla label map
    $mealMap = [
        'before_meal' => 'খাবারের আগে',
        'after_meal'  => 'খাবারের পরে',
        'with_meal'   => 'খাবারের সাথে',
        'midday'      => 'দুপুরে',
        'bedtime'     => 'রাতে শোবার আগে',
    ];

    $out = [];
    foreach ($medicines as $id => $row) {
        if (empty($row['selected'])) continue;

        // Translate to Bangla if value is an English key, otherwise keep as-is
        $mealTime = $row['meal_time'] ?? null;
        $mealTimeBn = $mealTime
            ? ($mealMap[$mealTime] ?? $mealTime)
            : null;

        $out[(int) $id] = [
            'duration'      => $row['duration']      ?? null,
            'times_per_day' => $row['times_per_day'] ?? null,
            'meal_time'     => $mealTimeBn,   // ✅ store Bangla
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

//     public function pdfPublic(Prescription $prescription)
// {
//     // authorize read if needed, then render PDF exactly like your mpdf route
//     return $this->renderMpdf($prescription); // replace with your actual PDF generator
// }
}
