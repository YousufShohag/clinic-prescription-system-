<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PublicAppointmentController extends Controller
{
    public function showForm(Request $request)
{
    $doctors = Doctor::orderBy('name')->get(['id','name']);
    return view('frontend.welcome', compact('doctors')); 
}

    public function checkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:6']);
        $p = Patient::where('phone', $request->phone)->first();

        return response()->json([
            'exists'  => (bool) $p,
            'patient' => $p ? ['id'=>$p->id, 'name'=>$p->name, 'dob'=>$p->dob] : null,
        ]);
    }

    // public function bookExisting(Request $request)
    // {
    //     $data = $request->validate([
    //         'phone'      => ['required','string','min:6'],
    //         'dob'        => ['required','date'],
    //         'doctor_id'  => ['required', Rule::exists('doctors','id')],
    //         'date'       => ['required','date'],
    //         'start_time' => ['required','date_format:H:i'],
    //         'notes'      => ['nullable','string','max:1000'],
    //     ]);

    //     $patient = Patient::where('phone', $data['phone'])
    //         ->whereDate('dob', $data['dob'])
    //         ->first();

    //     if (!$patient) {
    //         return back()->withInput()->withErrors([
    //             'phone' => 'No patient found with that phone & DOB.',
    //         ]);
    //     }

    //     $this->ensureNoOverlapOrFail($data['doctor_id'], $data['date'], $data['start_time']);

    //     $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['start_time']);

    //     DB::transaction(function () use ($data, $patient, $start) {
    //         Appointment::create([
    //             'doctor_id'    => $data['doctor_id'],
    //             'patient_id'   => $patient->id,
    //             'scheduled_at' => $start,
    //             'notes'        => $data['notes'] ?? null,
    //             'appointment_number'  => $number,   // ✅ set it
    //         ]);
    //     });

    //     return redirect()->route('public.appointment')
    //         ->with('status', 'Your appointment has been booked. Thank you!');
    // }

//     public function bookExisting(Request $request)
// {
//     $data = $request->validate([
//         'phone'      => ['required','string','min:6'],
//         'dob'        => ['required','date'],
//         'doctor_id'  => ['required', Rule::exists('doctors','id')],
//         'date'       => ['required','date'],
//         'start_time' => ['required','date_format:H:i'],
//         'notes'      => ['nullable','string','max:1000'],
//     ]);

//     $patient = Patient::where('phone', $data['phone'])
//         ->whereDate('dob', $data['dob'])
//         ->first();

//     if (!$patient) {
//         return back()->withInput()->withErrors(['phone' => 'No patient found with that phone & DOB.']);
//     }

//     $this->ensureNoOverlapOrFail($data['doctor_id'], $data['date'], $data['start_time']);

//     $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['start_time']);

//     DB::transaction(function () use ($data, $patient, $start) {
//         // ✅ compute the number *inside* the transaction
//         $number = $this->nextAppointmentNumber((int)$data['doctor_id'], $start);

//         Appointment::create([
//             'doctor_id'           => $data['doctor_id'],
//             'patient_id'          => $patient->id,
//             'scheduled_at'        => $start,
//             'notes'               => $data['notes'] ?? null,
//             'appointment_number'  => $number,
//         ]);
//     });

//     return redirect()->route('public.appointment')
//         ->with('status', 'Your appointment has been booked. Thank you!');
// }
public function bookExisting(Request $request)
{
    // 1) Validate inputs: phone required; dob/patient_id optional, but at least one must be present
    $data = $request->validate([
        'phone'       => ['required','string','min:6'],
        'dob'         => ['nullable','date'],
        'patient_id'  => ['nullable','integer','min:1'],
        'doctor_id'   => ['required', Rule::exists('doctors','id')],
        'date'        => ['required','date'],
        'start_time'  => ['required','date_format:H:i'],
        'notes'       => ['nullable','string','max:1000'],
    ]);

    if (empty($data['dob']) && empty($data['patient_id'])) {
        return back()->withInput()->withErrors([
            'dob' => 'Provide Date of Birth or Patient ID.',
            'patient_id' => 'Provide Date of Birth or Patient ID.',
        ]);
    }

    // 2) Find patient **by phone** (must exist)
    $patient = Patient::where('phone', $data['phone'])->first();

    if (!$patient) {
        return back()->withInput()->withErrors([
            'phone' => 'No patient found with that phone number.',
        ]);
    }

    // 3) Require EITHER matching DOB OR matching Patient ID (against the same record)
    $dobMatches = false;
    if (!empty($data['dob']) && $patient->dob) {
        $dobMatches = \Carbon\Carbon::parse($patient->dob)->isSameDay($data['dob']);
    }

    $idMatches = empty($data['patient_id']) ? false : ((int)$patient->id === (int)$data['patient_id']);

    if (!($dobMatches || $idMatches)) {
        return back()->withInput()->withErrors([
            'dob' => 'DOB or Patient ID must match the profile associated with this phone.',
            'patient_id' => 'DOB or Patient ID must match the profile associated with this phone.',
        ]);
    }

    // 4) Overlap check (fixed 30-min slot)
    $this->ensureNoOverlapOrFail($data['doctor_id'], $data['date'], $data['start_time']);

    // 5) Create appointment
    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['start_time']);

    DB::transaction(function () use ($data, $patient, $start) {
        $number = $this->nextAppointmentNumber((int)$data['doctor_id'], $start); // returns INT

        Appointment::create([
            'doctor_id'          => $data['doctor_id'],
            'patient_id'         => $patient->id,
            'scheduled_at'       => $start,
            'notes'              => $data['notes'] ?? null,
            'appointment_number' => $number,
        ]);
    });

    return redirect()->route('public.appointment')
        ->with('status', 'Your appointment has been booked. Thank you!');
}


    // public function bookNew(Request $request)
    // {
    //     $data = $request->validate([
    //         'name'       => ['required','string','max:120'],
    //         'phone'      => ['required','string','min:6','max:30'],
    //         'email'      => ['nullable','email','max:120'],
    //         'dob'        => ['nullable','date'],
    //         'gender'     => ['nullable', Rule::in(['male','female','other'])],
    //         'doctor_id'  => ['required', Rule::exists('doctors','id')],
    //         'date'       => ['required','date'],
    //         'start_time' => ['required','date_format:H:i'],
    //         'notes'      => ['nullable','string','max:1000'],
    //     ]);

    //     $patient = Patient::firstOrCreate(
    //         ['phone' => $data['phone']],
    //         [
    //             'name'   => $data['name'],
    //             'email'  => $data['email'] ?? null,
    //             'dob'    => $data['dob'] ?? null,
    //             'gender' => $data['gender'] ?? null,
    //         ]
    //     );

    //     if (!$patient->wasRecentlyCreated) {
    //         $patient->fill([
    //             'name'   => $patient->name ?: $data['name'],
    //             'email'  => $patient->email ?: ($data['email'] ?? null),
    //             'dob'    => $patient->dob ?: ($data['dob'] ?? null),
    //             'gender' => $patient->gender ?: ($data['gender'] ?? null),
    //         ])->save();
    //     }

    //     $this->ensureNoOverlapOrFail($data['doctor_id'], $data['date'], $data['start_time']);

    //     $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['start_time']);

    //     DB::transaction(function () use ($data, $patient, $start) {
    //         Appointment::create([
    //             'doctor_id'    => $data['doctor_id'],
    //             'patient_id'   => $patient->id,
    //             'scheduled_at' => $start,
    //             'notes'        => $data['notes'] ?? null,
    //             'appointment_number'  => $number,   // ✅ set it
    //         ]);
    //     });

    //     return redirect()->route('public.appointment')
    //         ->with('status', 'Your account was created and the appointment booked. See you soon!');
    // }
    public function bookNew(Request $request)
{
    $data = $request->validate([
        'name'       => ['required','string','max:120'],
        'phone'      => ['required','string','min:6','max:30'],
        'email'      => ['nullable','email','max:120'],
        'dob'        => ['nullable','date'],
        'gender'     => ['nullable', Rule::in(['male','female','other'])],
        'doctor_id'  => ['required', Rule::exists('doctors','id')],
        'date'       => ['required','date'],
        'start_time' => ['required','date_format:H:i'],
        'notes'      => ['nullable','string','max:1000'],
    ]);

    $patient = Patient::firstOrCreate(
        ['phone' => $data['phone']],
        [
            'name'   => $data['name'],
            'email'  => $data['email'] ?? null,
            'dob'    => $data['dob'] ?? null,
            'gender' => $data['gender'] ?? null,
        ]
    );

    if (!$patient->wasRecentlyCreated) {
        $patient->fill([
            'name'   => $patient->name ?: $data['name'],
            'email'  => $patient->email ?: ($data['email'] ?? null),
            'dob'    => $patient->dob ?: ($data['dob'] ?? null),
            'gender' => $patient->gender ?: ($data['gender'] ?? null),
        ])->save();
    }

    $this->ensureNoOverlapOrFail($data['doctor_id'], $data['date'], $data['start_time']);

    $start = Carbon::createFromFormat('Y-m-d H:i', $data['date'].' '.$data['start_time']);

    DB::transaction(function () use ($data, $patient, $start) {
        // ✅ compute the number *inside* the transaction
        $number = $this->nextAppointmentNumber((int)$data['doctor_id'], $start);
        

        Appointment::create([
            'doctor_id'           => $data['doctor_id'],
            'patient_id'          => $patient->id,
            'scheduled_at'        => $start,
            'notes'               => $data['notes'] ?? null,
            'appointment_number'  => $number,
        ]);
    });

    return redirect()->route('public.appointment')
        ->with('status', 'Your account was created and the appointment booked. See you soon!');
}
    private function nextAppointmentNumber(int $doctorId, \Carbon\Carbon $when): int
{
    $date = $when->toDateString(); // e.g. 2025-09-24

    // Lock rows for the same doctor + date and get the current max integer
    $last = DB::table('appointments')
        ->where('doctor_id', $doctorId)
        ->whereDate('scheduled_at', $date)
        ->lockForUpdate()
        ->max('appointment_number');   // <-- returns int|null

    return ((int) $last) + 1;          // start from 1 if null
}

    private function ensureNoOverlapOrFail($doctorId, $date, $startTime, $durationMin = null)
    {
        $defaultDuration = 30;
        $start    = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}");
        $duration = (int) ($durationMin ?? $defaultDuration);
        $end      = (clone $start)->addMinutes($duration);

        $hasDurationCol = Schema::hasColumn('appointments', 'duration_min');

        $endSql = $hasDurationCol
            ? "TIMESTAMPADD(MINUTE, COALESCE(duration_min,{$defaultDuration}), scheduled_at)"
            : "TIMESTAMPADD(MINUTE, {$duration}, scheduled_at)";

        $overlap = Appointment::where('doctor_id', $doctorId)
            ->where(function ($q) use ($start, $end, $endSql) {
                $q->where('scheduled_at', '<', $end)
                  ->whereRaw("{$endSql} > ?", [$start]);
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_time' => 'Selected time overlaps with another appointment for this doctor.',
            ]);
        }
    }

    public function checkPatient(Request $request)
{
    $data = $request->validate([
        'phone'      => ['required','string','min:6'],
        'dob'        => ['nullable','date'],
        'patient_id' => ['nullable','integer'],
    ]);

    $query = Patient::where('phone', $data['phone']);

    if (!empty($data['dob'])) {
        $query->whereDate('dob', $data['dob']);
    } elseif (!empty($data['patient_id'])) {
        $query->where('id', $data['patient_id']);
    }

    $patient = $query->first();

    return response()->json([
        'exists'  => (bool) $patient,
        'patient' => $patient ? ['id'=>$patient->id, 'name'=>$patient->name, 'dob'=>$patient->dob] : null,
    ]);
}

}
