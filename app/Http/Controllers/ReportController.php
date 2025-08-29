<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Prescription;
use App\Models\Doctor;
use App\Models\Patient;

class ReportController extends Controller
{
   public function doctorPatients(Request $request)
{
    $from = $request->query('from');
    $to   = $request->query('to');
    try { $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay(); }
    catch (\Throwable $e) { $start = now()->subDays(30)->startOfDay(); }
    try { $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : now()->endOfDay(); }
    catch (\Throwable $e) { $end = now()->endOfDay(); }

    $doctorId = $request->query('doctor_id') ?: (auth()->user()->doctor_id ?? \App\Models\Doctor::value('id'));
    $q = trim((string)$request->query('q', ''));

    // Qualify all columns with the table name
    $base = \App\Models\Prescription::query()
        ->where('prescriptions.doctor_id', $doctorId)
        ->whereBetween('prescriptions.created_at', [$start, $end]);

    if ($q !== '') {
        $base->whereIn('prescriptions.patient_id', function ($sub) use ($q) {
            $sub->select('id')->from('patients')
                ->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
        });
    }

    // Stats (qualify distinct column)
    $totalPrescriptions = (clone $base)->count();
    $uniquePatients     = (clone $base)->distinct('prescriptions.patient_id')->count('prescriptions.patient_id');

    // Rows
    $rows = (clone $base)
        ->leftJoin('patients', 'patients.id', '=', 'prescriptions.patient_id')
        ->groupBy('prescriptions.patient_id', 'patients.name', 'patients.phone', 'patients.email')
        ->selectRaw('
            prescriptions.patient_id as patient_id,
            patients.name  as patient_name,
            patients.phone as patient_phone,
            patients.email as patient_email,
            COUNT(*)       as rx_count,
            MIN(prescriptions.created_at) as first_at,
            MAX(prescriptions.created_at) as last_at
        ')
        ->orderByDesc('rx_count')
        ->orderBy('patient_name')
        ->paginate(50)
        ->appends($request->query());

    $doctors = \App\Models\Doctor::orderBy('name')->get(['id','name']);
    $doctor  = \App\Models\Doctor::find($doctorId);

    return view('admin.reports.doctor_patients', [
        'rows'               => $rows,
        'totalPrescriptions' => $totalPrescriptions,
        'uniquePatients'     => $uniquePatients,
        'from'               => $start->toDateString(),
        'to'                 => $end->toDateString(),
        'doctors'            => $doctors,
        'doctor'             => $doctor,
        'filters'            => ['doctor_id' => $doctorId, 'q' => $q],
    ]);
}

public function doctorPatientsExport(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
{
    $from = $request->query('from');
    $to   = $request->query('to');
    try { $start = $from ? \Carbon\Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay(); }
    catch (\Throwable $e) { $start = now()->subDays(30)->startOfDay(); }
    try { $end = $to ? \Carbon\Carbon::parse($to)->endOfDay() : now()->endOfDay(); }
    catch (\Throwable $e) { $end = now()->endOfDay(); }

    $doctorId = $request->query('doctor_id') ?: (auth()->user()->doctor_id ?? \App\Models\Doctor::value('id'));
    $q = trim((string)$request->query('q', ''));

    $base = \App\Models\Prescription::query()
        ->where('prescriptions.doctor_id', $doctorId)
        ->whereBetween('prescriptions.created_at', [$start, $end]);

    if ($q !== '') {
        $base->whereIn('prescriptions.patient_id', function ($sub) use ($q) {
            $sub->select('id')->from('patients')
                ->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
        });
    }

    $query = (clone $base)
        ->leftJoin('patients', 'patients.id', '=', 'prescriptions.patient_id')
        ->groupBy('prescriptions.patient_id', 'patients.name', 'patients.phone', 'patients.email')
        ->selectRaw('
            prescriptions.patient_id as patient_id,
            patients.name  as patient_name,
            patients.phone as patient_phone,
            patients.email as patient_email,
            COUNT(*)       as rx_count,
            MIN(prescriptions.created_at) as first_at,
            MAX(prescriptions.created_at) as last_at
        ')
        ->orderByDesc('rx_count')
        ->orderBy('patient_name');

    $filename = 'doctor_patients_' . now()->format('Ymd_His') . '.csv';

    return response()->streamDownload(function () use ($query) {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Patient ID','Patient','Phone','Email','Rx Count','First Visit','Last Visit']);
        $query->chunk(1000, function ($chunk) use ($out) {
            foreach ($chunk as $row) {
                fputcsv($out, [
                    $row->patient_id,
                    $row->patient_name,
                    $row->patient_phone,
                    $row->patient_email,
                    $row->rx_count,
                    optional($row->first_at)->format('Y-m-d'),
                    optional($row->last_at)->format('Y-m-d'),
                ]);
            }
        });
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
}




public function prescriptions(Request $request)
    {
        // Date range (default last 30 days)
        $from = $request->query('from');
        $to   = $request->query('to');
        try { $start = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay(); }
        catch (\Throwable $e) { $start = now()->subDays(30)->startOfDay(); }
        try { $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay(); }
        catch (\Throwable $e) { $end = now()->endOfDay(); }

        $doctorId  = $request->query('doctor_id');     // optional
        $patientId = $request->query('patient_id');    // optional
        $q         = trim((string)$request->query('q', ''));

        $base = Prescription::query()
            ->with([
                'doctor:id,name,specialization',
                'patient:id,name,phone,email',
            ])
            ->withCount(['medicines', 'tests'])
            ->whereBetween('prescriptions.created_at', [$start, $end]);

        if ($doctorId) {
            $base->where('prescriptions.doctor_id', $doctorId);
        }
        if ($patientId) {
            $base->where('prescriptions.patient_id', $patientId);
        }
        if ($q !== '') {
            // Simple search on problem / advice / O/E
            $base->where(function ($w) use ($q) {
                $w->where('prescriptions.problem_description', 'like', "%{$q}%")
                  ->orWhere('prescriptions.doctor_advice', 'like', "%{$q}%")
                  ->orWhere('prescriptions.oe', 'like', "%{$q}%");
            });
        }

        $prescriptions = (clone $base)
            ->orderByDesc('prescriptions.created_at')
            ->paginate(20)
            ->appends($request->query());

        // Useful bits for header
        $doctor  = $doctorId  ? Doctor::find($doctorId, ['id','name','specialization']) : null;
        $patient = $patientId ? Patient::find($patientId, ['id','name','phone','email']) : null;

        $total = (clone $base)->count();

        return view('admin.reports.prescriptions', [  // <- place Blade here
            'prescriptions' => $prescriptions,
            'doctor'        => $doctor,
            'patient'       => $patient,
            'from'          => $start->toDateString(),
            'to'            => $end->toDateString(),
            'total'         => $total,
            'filters'       => [
                'doctor_id'  => $doctorId,
                'patient_id' => $patientId,
                'q'          => $q,
            ],
        ]);
    }

    /**
     * CSV export for the prescriptions “story” view
     */
    public function prescriptionsExport(Request $request): StreamedResponse
    {
        $from = $request->query('from');
        $to   = $request->query('to');
        try { $start = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay(); }
        catch (\Throwable $e) { $start = now()->subDays(30)->startOfDay(); }
        try { $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay(); }
        catch (\Throwable $e) { $end = now()->endOfDay(); }

        $doctorId  = $request->query('doctor_id');
        $patientId = $request->query('patient_id');
        $q         = trim((string)$request->query('q', ''));

        $base = Prescription::query()
            ->with(['doctor:id,name', 'patient:id,name,phone,email'])
            ->withCount(['medicines', 'tests'])
            ->whereBetween('prescriptions.created_at', [$start, $end]);

        if ($doctorId) {
            $base->where('prescriptions.doctor_id', $doctorId);
        }
        if ($patientId) {
            $base->where('prescriptions.patient_id', $patientId);
        }
        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('prescriptions.problem_description', 'like', "%{$q}%")
                  ->orWhere('prescriptions.doctor_advice', 'like', "%{$q}%")
                  ->orWhere('prescriptions.oe', 'like', "%{$q}%");
            });
        }

        $query = (clone $base)->orderByDesc('prescriptions.created_at');

        $filename = 'prescriptions_story_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['RX ID','Date','Doctor','Patient','Phone','Problem','Medicines','Tests']);
            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $rx) {
                    fputcsv($out, [
                        $rx->id,
                        optional($rx->created_at)->format('Y-m-d H:i'),
                        $rx->doctor->name ?? '',
                        $rx->patient->name ?? '',
                        $rx->patient->phone ?? '',
                        $rx->problem_description ?? '',
                        $rx->medicines_count,
                        $rx->tests_count,
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }


}
