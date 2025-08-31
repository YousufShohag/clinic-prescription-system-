<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;


use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;  
use Illuminate\Support\Facades\Schema; 

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient','doctor'])->latest()->paginate(15);
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('admin.appointments.create', compact('patients','doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Appointment::create($data);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('admin.appointments.edit', compact('appointment','patients','doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($data);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }


public function calendarData(Request $req)
{
    $req->validate([
        'start'     => ['required','date'],
        'end'       => ['required','date'],
        'doctor_id' => ['nullable','exists:doctors,id'],
    ]);

    $start    = Carbon::parse($req->query('start'))->startOfDay();
    $end      = Carbon::parse($req->query('end'))->endOfDay();
    $doctorId = $req->query('doctor_id');

    $hasDateCol = Schema::hasColumn('appointments', 'date');
    $hasSchedAt = Schema::hasColumn('appointments', 'scheduled_at');

    $q = DB::table('appointments');

    if ($hasDateCol) {
        $q->selectRaw('date as d, COUNT(*) as c')
          ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
          ->groupBy('date');
    } elseif ($hasSchedAt) {
        $q->selectRaw('DATE(scheduled_at) as d, COUNT(*) as c')
          ->whereBetween('scheduled_at', [$start, $end])
          ->groupBy(DB::raw('DATE(scheduled_at)'));
    } else {
        return response()->json([]); // no known date columns
    }

    if ($doctorId) $q->where('doctor_id', $doctorId);

    $rows = $q->orderBy('d')->get();

    return response()->json($rows->map(fn($r) => [
        'start' => (string) $r->d,
        'title' => (int)$r->c . ' appt' . ((int)$r->c === 1 ? '' : 's'),
        'allDay' => true,
        'extendedProps' => ['date' => (string)$r->d, 'count' => (int)$r->c],
    ]));
}

public function dayList(Request $req)
{
    $req->validate([
        'date'      => ['required','date'],
        'doctor_id' => ['nullable','exists:doctors,id'],
    ]);

    $date = Carbon::parse($req->query('date'))->toDateString();
    $doctorId = $req->query('doctor_id');

    $hasDateCol = Schema::hasColumn('appointments', 'date');
    $hasSchedAt = Schema::hasColumn('appointments', 'scheduled_at');

    $base = \App\Models\Appointment::with(['patient:id,name,phone','doctor:id,name']);

    if ($hasDateCol) {
        $base->whereDate('date', $date);
    } elseif ($hasSchedAt) {
        $base->whereBetween('scheduled_at', [
            Carbon::parse($date.' 00:00:00'),
            Carbon::parse($date.' 23:59:59'),
        ]);
    } else {
        return response()->json(['date'=>$date,'count'=>0,'items'=>[]]);
    }

    if ($doctorId) $base->where('doctor_id', $doctorId);

    $items = $base
        ->orderBy($hasDateCol ? 'start_time' : 'scheduled_at')
        ->get()
        ->map(function ($a) use ($hasDateCol, $hasSchedAt) {
            // Build time label safely
            $timeLabel = '—';
            try {
                if ($hasDateCol && !empty($a->start_time)) {
                    $timeLabel = Carbon::parse($a->start_time)->format('h:i A');
                } elseif ($hasSchedAt && !empty($a->scheduled_at)) {
                    $timeLabel = Carbon::parse($a->scheduled_at)->format('h:i A');
                }
                if (!empty($a->duration_min)) $timeLabel .= ' ('.$a->duration_min.'m)';
            } catch (\Throwable $e) { /* ignore */ }

            // Only include URLs if those routes exist
            $showUrl = Route::has('appointments.show') ? route('appointments.show', $a->id) : null;
            $editUrl = Route::has('appointments.edit') ? route('appointments.edit', $a->id) : null;

            return [
                'id'       => $a->id,
                'time'     => $timeLabel,
                'patient'  => $a->patient->name ?? '—',
                'phone'    => $a->patient->phone ?? '',
                'doctor'   => $a->doctor->name ?? '—',
                'status'   => ucfirst(str_replace('_',' ', $a->status ?? 'scheduled')),
                'notes'    => (string) \Illuminate\Support\Str::limit($a->notes ?? '', 120),
                'show_url' => $showUrl,
                'edit_url' => $editUrl,
            ];
        });

    return response()->json([
        'date'  => $date,
        'count' => $items->count(),
        'items' => $items,
    ]);
}


    
}