<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Prescription;
use Illuminate\Support\Str;
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

     public function search(Request $r)
    {
        $q    = trim($r->input('term', $r->input('q', '')));
        $page = max(1, (int) $r->input('page', 1));
        $per  = 20;

        $builder = Patient::query();
        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $total   = $builder->count();
        $results = $builder->orderBy('name')
            ->skip(($page - 1) * $per)
            ->take($per)
            ->get(['id', 'name', 'phone', 'email', 'notes']);

        // For Select2, `text` is what shows after selection
        return response()->json([
            'results' => $results->map(fn ($p) => [
                'id'    => $p->id,
                'text'  => $p->name . ($p->phone ? " ({$p->phone})" : ''),
                'name'  => $p->name,
                'phone' => $p->phone,
                'email' => $p->email,
                'notes' => $p->notes,
            ]),
            'pagination' => [ 'more' => ($page * $per) < $total ],
        ]);
    }

// public function history(Patient $patient, Request $r)
// {
//     $limit = min(50, max(1, (int) $r->input('limit', 10)));

//     // Pull recent prescriptions. If you have a 'doctor' relation, keep with('doctor').
//     $items = Prescription::where('patient_id', $patient->id)
//         ->with('doctor')                // remove if you don't have it
//         ->latest()
//         ->take($limit)
//         ->get();

//     return response()->json([
//         'patient' => [
//             'id'   => $patient->id,
//             'name' => $patient->name,
//         ],
//         'count' => $items->count(),
//         'items' => $items->map(fn ($p) => [
//             'id'            => $p->id,
//             'date'          => optional($p->created_at)->format('Y-m-d'),
//             'doctor_name'   => optional($p->doctor)->name, // null if no relation
//             'problem'       => (string) str($p->problem_description ?? '')->limit(120),
//             // Optional: include a URL if you have a show route like /prescriptions/{id}
//             'url'           => url('/prescriptions/'.$p->id),
//         ]),
//     ]);
// }

// public function history(Patient $patient, Request $request)
// {
//     $limit = min(50, max(1, (int) $request->input('limit', 10)));

//     // Pull recent prescriptions; select only safe fields to avoid unknown columns.
//     $items = Prescription::where('patient_id', $patient->id)
//         ->orderByDesc('id')
//         ->limit($limit)
//         ->get(['id', 'created_at', 'doctor_id', 'problem_description']); // adjust column names if needed

//     // If you have a doctors table but no relation, resolve names cheaply (optional)
//     $doctorNames = [];
//     if (Schema::hasTable('doctors')) {
//         $doctorIds = $items->pluck('doctor_id')->filter()->unique()->values();
//         if ($doctorIds->isNotEmpty()) {
//             $doctorNames = \DB::table('doctors')
//                 ->whereIn('id', $doctorIds)
//                 ->pluck('name', 'id')
//                 ->toArray();
//         }
//     }

//     return response()->json([
//         'patient' => ['id' => $patient->id, 'name' => $patient->name],
//         'count'   => $items->count(),
//         'items'   => $items->map(function ($p) use ($doctorNames) {
//             $date  = optional($p->created_at)->format('Y-m-d');
//             $doc   = $doctorNames[$p->doctor_id] ?? null;        // null-safe
//             $prob  = $p->problem_description ?? null;            // null-safe; rename if your column differs
//             return [
//                 'id'          => $p->id,
//                 'date'        => $date,
//                 'doctor_name' => $doc,
//                 'problem'     => $prob ? \Illuminate\Support\Str::limit($prob, 120) : null,
//                 'url'         => route('prescriptions.show', $p->id), // ensure this route exists or swap to url('/prescriptions/'.$p->id)
//             ];
//         }),
//     ]);
// }

public function history(Patient $patient, Request $request)
{
    $limit = min(50, max(1, (int) $request->input('limit', 10)));

    $items = Prescription::where('patient_id', $patient->id)
        ->orderByDesc('id')
        ->limit($limit)
        ->get(['id', 'created_at', 'doctor_id', 'problem_description']); // rename fields if needed

    // Optional: resolve doctor names (only if doctors table exists)
    $doctorNames = [];
    if (Schema::hasTable('doctors')) {
        $doctorIds = $items->pluck('doctor_id')->filter()->unique();
        if ($doctorIds->isNotEmpty()) {
            $doctorNames = Doctor::whereIn('id', $doctorIds)->pluck('name', 'id')->toArray();
        }
    }

    return response()->json([
        'patient' => ['id' => $patient->id, 'name' => $patient->name],
        'count'   => $items->count(),
        'items'   => $items->map(function ($p) use ($doctorNames) {
            return [
                'id'          => $p->id,
                'date'        => optional($p->created_at)->format('Y-m-d'),
                'doctor_name' => $doctorNames[$p->doctor_id] ?? null,
                'problem'     => $p->problem_description ? Str::limit($p->problem_description, 120) : null,
                'url'         => url('/prescriptions/'.$p->id),
            ];
        }),
    ]);
}


}
