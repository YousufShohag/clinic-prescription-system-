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
        $appointments = Appointment::with(['patient','doctor'])->latest()->paginate(30);
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        return view('admin.appointments.create', compact('patients','doctors'));
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'patient_id' => 'required|exists:patients,id',
    //         'doctor_id' => 'required|exists:doctors,id',
    //         'scheduled_at' => 'required|date',
    //         'notes' => 'nullable|string',
    //     ]);

    //     $date = Carbon::parse($data['scheduled_at'])->toDateString();

    //     Appointment::create($data);
    //     if (Schema::hasColumn('appointments', 'scheduled_at')) {
    //         $count = Appointment::whereDate('scheduled_at', $date)->count();
    //     } elseif (Schema::hasColumn('appointments', 'date')) {
    //         $count = Appointment::whereDate('date', $date)->count();
    //     } elseif (Schema::hasColumn('appointments', 'appointment_date')) {
    //         $count = Appointment::whereDate('appointment_date', $date)->count();
    //     } else {
    //         $count = 0;
    //     }

    //     $data['appointment_number'] = $count + 1;

    //     $appointment = Appointment::create($data);

    //     if ($request->wantsJson()) {
    //         return response()->json($appointment, 201);
    //     }

    //     return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    // }

    public function store(Request $request)
{
    $data = $request->validate([
        'patient_id'   => 'required|exists:patients,id',
        'doctor_id'    => 'required|exists:doctors,id',
        'scheduled_at' => 'required|date',
        'notes'        => 'nullable|string',
    ]);

    $date = Carbon::parse($data['scheduled_at'])->toDateString();

    // Create exactly ONE row, and compute the next number inside a transaction.
    $appointment = DB::transaction(function () use ($data, $date) {
        // If you want numbering per-doctor-per-day, keep the doctor filter.
        // If you want numbering per-day (all doctors combined), remove the where('doctor_id', ...) line.
        $q = Appointment::whereDate('scheduled_at', $date)
                        ->where('doctor_id', $data['doctor_id']);

        // Lock rows so two users can't grab the same number at the same time
        $max = $q->lockForUpdate()->max('appointment_number');
        $next = ($max ?? 0) + 1;

        return Appointment::create(array_merge($data, [
            'appointment_number' => $next,
        ]));
    });

    if ($request->wantsJson()) {
        return response()->json($appointment, 201);
    }

    return redirect()->route('appointments.index')
        ->with('success', 'Appointment created successfully.');
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
        'patient_id'   => 'required|exists:patients,id',
        'doctor_id'    => 'required|exists:doctors,id',
        'scheduled_at' => 'required|date',
        'notes'        => 'nullable|string',
    ]);

    $newDate = Carbon::parse($data['scheduled_at'])->toDateString();
    $oldDate = optional($appointment->scheduled_at)->toDateString();
    $doctorChanged = (int)$appointment->doctor_id !== (int)$data['doctor_id'];
    $dateChanged   = $oldDate !== $newDate;

    DB::transaction(function () use ($appointment, &$data, $newDate, $dateChanged, $doctorChanged) {
        if ($dateChanged || $doctorChanged) {
            $q = Appointment::whereDate('scheduled_at', $newDate)
                            ->where('doctor_id', $data['doctor_id']);
            $max  = $q->lockForUpdate()->max('appointment_number');
            $data['appointment_number'] = ($max ?? 0) + 1;
        }
        $appointment->update($data);
    });

    if ($request->wantsJson()) {
        return response()->json(['ok' => true]);
    }

    return redirect()->route('appointments.index')
        ->with('success', 'Appointment updated successfully.');
}


// App\Http\Controllers\AppointmentController.php
public function destroy(Request $request, Appointment $appointment)
{
    // capture day + doctor BEFORE delete (for optional resequencing)
    $date     = optional($appointment->scheduled_at)->toDateString();
    $doctorId = (int) $appointment->doctor_id;

    $appointment->delete();

    // OPTIONAL: keep numbers contiguous for that doctor+day
    $this->resequenceNumbers($date, $doctorId);

    if ($request->wantsJson()) {
        return response()->json(['ok' => true]);
    }

    return redirect()
        ->route('appointments.index')
        ->with('success', 'Appointment deleted successfully.');
}

/**
 * Re-number appointment_number for a given date+doctor to 1..N
 * Safe to call even if the column doesn't exist.
 */
private function resequenceNumbers(?string $date, ?int $doctorId): void
{
    if (!$date || !$doctorId) return;
    if (!Schema::hasColumn('appointments', 'appointment_number')) return;

    DB::transaction(function () use ($date, $doctorId) {
        $list = Appointment::whereDate('scheduled_at', $date)
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_number')   // existing numbering first
            ->orderBy('scheduled_at')         // stable fallback
            ->lockForUpdate()
            ->get();

        $i = 1;
        foreach ($list as $a) {
            if ((int) $a->appointment_number !== $i) {
                // update quietly to avoid events/observers, if available in your Laravel
                if (method_exists($a, 'updateQuietly')) {
                    $a->updateQuietly(['appointment_number' => $i]);
                } else {
                    $a->appointment_number = $i;
                    $a->save();
                }
            }
            $i++;
        }
    });
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

// public function dayList(Request $req)
// {
//     $req->validate([
//         'date'      => ['required','date'],
//         'doctor_id' => ['nullable','exists:doctors,id'],
//     ]);

//     $date = Carbon::parse($req->query('date'))->toDateString();
//     $doctorId = $req->query('doctor_id');

//     $hasDateCol = Schema::hasColumn('appointments', 'date');
//     $hasSchedAt = Schema::hasColumn('appointments', 'scheduled_at');
//     $hasNumber  = Schema::hasColumn('appointments', 'appointment_number');

//     $base = \App\Models\Appointment::with(['patient:id,name,phone','doctor:id,name']);

//     if ($hasDateCol) {
//         $base->whereDate('date', $date);
//     } elseif ($hasSchedAt) {
//         $base->whereBetween('scheduled_at', [
//             Carbon::parse($date.' 00:00:00'),
//             Carbon::parse($date.' 23:59:59'),
//         ]);
//     } else {
//         return response()->json(['date'=>$date,'count'=>0,'items'=>[]]);
//     }

//     if ($doctorId) $base->where('doctor_id', $doctorId);

//     $items = $base
//         ->orderBy($hasNumber ? 'appointment_number' : ($hasDateCol ? 'start_time' : 'scheduled_at'))
//         ->get()
//        ->map(function ($a) use ($hasDateCol, $hasSchedAt, $hasNumber) {
//             // Build time label safely
//             $timeLabel = '—';
//             try {
//                 if ($hasDateCol && !empty($a->start_time)) {
//                     $timeLabel = Carbon::parse($a->start_time)->format('h:i A');
//                 } elseif ($hasSchedAt && !empty($a->scheduled_at)) {
//                     $timeLabel = Carbon::parse($a->scheduled_at)->format('h:i A');
//                 }
//                 if (!empty($a->duration_min)) $timeLabel .= ' ('.$a->duration_min.'m)';
//             } catch (\Throwable $e) { /* ignore */ }

//             // Only include URLs if those routes exist
//             $showUrl = Route::has('appointments.show') ? route('appointments.show', $a->id) : null;
//             $editUrl = Route::has('appointments.edit') ? route('appointments.edit', $a->id) : null;

//             return [
//                 'id'       => $a->id,
//                 'number'   => $hasNumber ? (int) $a->appointment_number : null,
//                 'time'     => $timeLabel,
//                 'patient'  => $a->patient->name ?? '—',
//                 'phone'    => $a->patient->phone ?? '',
//                 'doctor'   => $a->doctor->name ?? '—',
//                 'status'   => ucfirst(str_replace('_',' ', $a->status ?? 'scheduled')),
//                 'notes'    => (string) \Illuminate\Support\Str::limit($a->notes ?? '', 120),
//                 'show_url' => $showUrl,
//                 'edit_url' => $editUrl,
//             ];
//         });

//     return response()->json([
//         'date'  => $date,
//         'count' => $items->count(),
//         'items' => $items,
//     ]);
// }

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
    $hasNumber  = Schema::hasColumn('appointments', 'appointment_number');

    $base = Appointment::with(['patient:id,name,phone','doctor:id,name']);

    if ($hasDateCol) {
        $base->whereDate('date', $date);
    } elseif ($hasSchedAt) {
        $base->whereDate('scheduled_at', $date);
    } else {
        return response()->json(['date'=>$date,'count'=>0,'items'=>[]]);
    }

    if ($doctorId) $base->where('doctor_id', $doctorId);

    // Order by ticket if present (nulls last), then by time
    if ($hasNumber) {
        $base->orderByRaw('CASE WHEN appointment_number IS NULL THEN 1 ELSE 0 END, appointment_number ASC');
    }
    $base->when($hasDateCol, fn($q)=>$q->orderBy('start_time'))
         ->when($hasSchedAt, fn($q)=>$q->orderBy('scheduled_at'));

    $rows = $base->get()->values();

    $items = $rows->map(function ($a, $i) use ($hasDateCol, $hasSchedAt, $hasNumber) {
        $timeLabel = '—';
        try {
            if ($hasDateCol && !empty($a->start_time)) {
                $timeLabel = Carbon::parse($a->start_time)->format('h:i A');
            } elseif ($hasSchedAt && !empty($a->scheduled_at)) {
                $timeLabel = Carbon::parse($a->scheduled_at)->format('h:i A');
            }
            if (!empty($a->duration_min)) $timeLabel .= ' ('.$a->duration_min.'m)';
        } catch (\Throwable $e) {}

        $showUrl = Route::has('appointments.show') ? route('appointments.show', $a->id) : null;
        $editUrl = Route::has('appointments.edit') ? route('appointments.edit', $a->id) : null;
        $deleteUrl = Route::has('appointments.destroy') ? route('appointments.destroy', $a->id) : null;

        return [
            'id'       => $a->id,
            'number'   => $hasNumber ? ($a->appointment_number ?? ($i+1)) : ($i+1), // fallback
            'time'     => $timeLabel,
            'patient'  => $a->patient->name ?? '—',
            'phone'    => $a->patient->phone ?? '',
            'doctor'   => $a->doctor->name ?? '—',
            'status'   => ucfirst(str_replace('_',' ', $a->status ?? 'scheduled')),
            'notes'    => (string) \Illuminate\Support\Str::limit($a->notes ?? '', 120),
            'show_url' => $showUrl,
            'edit_url' => $editUrl,
            'delete_url' => $deleteUrl,
        ];
    });

    return response()->json([
        'date'  => $date,
        'count' => $items->count(),
        'items' => $items,
    ]);
}



    
}