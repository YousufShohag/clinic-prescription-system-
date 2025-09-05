<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Prescription;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('doctor')
            ->withCount('prescriptions')
            ->latest()
            // ->orderBy('name')
            ->paginate(10);

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
            'name'         => 'required|string|max:255',
            'doctor_id'    => 'nullable|exists:doctors,id',
            'age'          => 'nullable|integer|min:0',
            'sex'          => 'nullable|in:male,female,others',
            'dob'          => 'nullable|date',
            'next_return_date' => 'nullable|date',
            'blood_group'  => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'guardian_name' => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            'documents.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,doc,docx,webp',
            'status'       => 'required|in:active,inactive',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'notes'        => 'nullable|string',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('patients/images', 'public');
            }
        }

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $documents[] = $doc->store('patients/documents', 'public');
            }
        }

        $data = $request->except(['images', 'documents', 'remove_images', 'remove_documents']);
        $data['images']    = $images;
        $data['documents'] = $documents;

        Patient::create($data);

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
            'name'         => 'required|string|max:255',
            'doctor_id'    => 'nullable|exists:doctors,id',
            'age'          => 'nullable|integer|min:0',
            'sex'          => 'nullable|in:male,female,others',
            'dob'          => 'nullable|date',
            'next_return_date' => 'nullable|date',
            'blood_group'  => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'guardian_name' => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'images.*'     => 'nullable|image|mimes:jpg,jpeg,png,gif,webp',
            'documents.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,doc,docx,webp',
            'status'       => 'required|in:active,inactive',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'notes'        => 'nullable|string',
        ]);

        $data = $request->except(['images', 'documents', 'remove_images', 'remove_documents']);

        // ----- Images: keep -> remove -> add new -----
        $existingImages = is_array($patient->images) ? $patient->images : [];
        $removeImages   = $request->input('remove_images', []);              // array of original paths
        $keepImages     = array_values(array_diff($existingImages, $removeImages));

        $newImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newImages[] = $image->store('patients/images', 'public');
            }
        }
        $finalImages = array_values(array_filter(array_merge($keepImages, $newImages)));

        // delete removed images from disk (safe best-effort)
        foreach ($removeImages as $path) {
            try {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed deleting image: '.$path, ['error'=>$e->getMessage()]);
            }
        }

        // ----- Documents: keep -> remove -> add new -----
        $existingDocs = is_array($patient->documents) ? $patient->documents : [];
        $removeDocs   = $request->input('remove_documents', []);
        $keepDocs     = array_values(array_diff($existingDocs, $removeDocs));

        $newDocs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $newDocs[] = $doc->store('patients/documents', 'public');
            }
        }
        $finalDocs = array_values(array_filter(array_merge($keepDocs, $newDocs)));

        // delete removed docs from disk
        foreach ($removeDocs as $path) {
            try {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed deleting document: '.$path, ['error'=>$e->getMessage()]);
            }
        }

        // assign back
        $data['images']    = $finalImages;
        $data['documents'] = $finalDocs;

        $patient->update($data);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        // optional: delete files too
        foreach ((array) $patient->images as $p) {
            try { if ($p && Storage::disk('public')->exists($p)) Storage::disk('public')->delete($p); } catch (\Throwable $e) {}
        }
        foreach ((array) $patient->documents as $p) {
            try { if ($p && Storage::disk('public')->exists($p)) Storage::disk('public')->delete($p); } catch (\Throwable $e) {}
        }

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
            // ->get(['id', 'name', 'phone', 'email', 'notes', 'age', 'sex']);
            ->get(['id', 'name', 'phone', 'email', 'notes', 'age', 'sex', 'blood_group', 'guardian_name']);

        return response()->json([
            'results' => $results->map(fn ($p) => [
                'id'    => $p->id,
                'text'  => $p->name . ($p->phone ? " ({$p->phone})" : ''),
                'name'  => $p->name,
                'phone' => $p->phone,
                'email' => $p->email,
                'notes' => $p->notes,
                'age'   => $p->age,
                'sex'   => $p->sex,
                'blood_group' => $p->blood_group,
                'guardian_name' => $p->guardian_name,
            ]),
            'pagination' => [ 'more' => ($page * $per) < $total ],
        ]);
    }

    /** JSON for the “Prescriptions” popup (used by patients.index) */
    public function prescriptions(Patient $patient, Request $request): JsonResponse
    {
        $limit = min(100, max(1, (int) $request->input('limit', 50)));

        $items = Prescription::where('patient_id', $patient->id)
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'created_at', 'doctor_id', 'problem_description']);

        $doctorNames = [];
        if (Schema::hasTable('doctors')) {
            $ids = $items->pluck('doctor_id')->filter()->unique()->values();
            if ($ids->isNotEmpty()) {
                $doctorNames = Doctor::whereIn('id', $ids)->pluck('name', 'id')->toArray();
            }
        }

        return response()->json([
            'data' => $items->map(function ($p) use ($doctorNames) {
                return [
                    'id'       => $p->id,
                    'date'     => optional($p->created_at)->format('Y-m-d'),
                    'doctor'   => $doctorNames[$p->doctor_id] ?? null,
                    'problem'  => $p->problem_description ? Str::limit($p->problem_description, 120) : null,
                    'show_url' => route('prescriptions.show', $p->id), // make sure this route exists
                ];
            }),
        ]);
    }

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

    /** JSON for the “Documents” section inside the View popup */
    public function documents(Patient $patient): JsonResponse
    {
        try {
            // Normalize documents: array | JSON string | null
            $raw = $patient->documents;
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $paths = is_array($decoded) ? $decoded : [];
            } elseif (is_array($raw)) {
                $paths = $raw;
            } else {
                $paths = [];
            }

            $paths = collect($paths)
                ->filter(fn($p) => is_string($p) && trim($p) !== '')
                ->values();

            $items = $paths->map(function (string $path) {
                $isAbsolute = Str::startsWith($path, ['http://','https://','/storage/','storage/']);
                $url = $isAbsolute ? $path : Storage::disk('public')->url($path);

                $ext  = strtolower(pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'webp' => 'image/webp',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf',
                    default => 'application/octet-stream',
                };

                return [
                    'id'        => md5($path),
                    'name'      => basename($path),
                    'url'       => $url,
                    'thumb_url' => in_array($ext, ['png','jpg','jpeg','webp','gif','svg']) ? $url : null,
                    'mime'      => $mime,
                    'uploaded_at' => null,
                ];
            })->values();

            return response()->json(['data' => $items], 200);
        } catch (\Throwable $e) {
            Log::error('patients.documents error', [
                'patient_id' => $patient->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'data'  => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
