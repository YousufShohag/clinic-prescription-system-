<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;


class DoctorController extends Controller
{
    // public function index()
    // {
    //     $doctors = Doctor::latest()->get();
    //     return view('admin.doctors.index', compact('doctors'));
    // }
    public function index(Request $request)
{
    $q = \App\Models\Doctor::query();

    if ($s = $request->get('search')) {
        $q->where(function($w) use ($s){
            $w->where('name','like',"%$s%")
              ->orWhere('email','like',"%$s%")
              ->orWhere('phone','like',"%$s%")
              ->orWhere('chamber','like',"%$s%");
        });
    }
    if ($sp = $request->get('specialization')) {
        $q->where('specialization', $sp);
    }
    if ($min = $request->get('fee_min')) {
        $q->where('consultation_fee','>=',$min);
    }
    if ($max = $request->get('fee_max')) {
        $q->where('consultation_fee','<=',$max);
    }
    if ($av = $request->get('available')) {
        $q->where('available_time','like',"%$av%");
    }

    $doctors = $q->latest()->paginate(15);
    // Optionally: $specializations = \App\Models\Doctor::whereNotNull('specialization')->distinct()->pluck('specialization');
    return view('admin.doctors.index', compact('doctors'));
}


    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        // Validate into $data (avoid $request->all())
        $data = $request->validate([
            'name'                   => 'required|string|max:255',
            'specialization'         => 'nullable|string|max:255',
            'degree'                 => 'nullable|string|max:255',
            'bma_registration_number'=> 'nullable|string|max:255',
            'chamber'                => 'nullable|string|max:255',
            'email'                  => 'required|email|unique:doctors,email',
            'phone'                  => 'nullable|string|max:20',
            'consultation_fee'       => 'nullable|numeric',
            'available_time'         => 'nullable|string|max:255',
            'notes'                  => 'nullable|string',
            'image'                  => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ]);

        // Store to storage/app/public/doctors and save "doctors/xxx.jpg" in DB
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctors', 'public');
            
        }

      

        Doctor::create($data);

        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully.');
    }
public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }
    // public function update(Request $request, Doctor $doctor)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'specialization' => 'nullable|string|max:255',
    //         'degree' => 'nullable|string|max:255',
    //         'chamber' => 'nullable|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'phone' => 'nullable|string|max:255',
    //         'consultation_fee' => 'nullable|numeric|min:0',
    //         'available_time' => 'nullable|string|max:255',
    //         'notes' => 'nullable|string',
    //         'image' => 'nullable|image|max:2048', // jpg/png <= 2MB
    //     ]);

    //     if ($request->hasFile('image')) {
    //         // (Optional) delete old image if stored on public disk
    //         if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
    //             Storage::disk('public')->delete($doctor->image);
    //         }
    //         $path = $request->file('image')->store('doctors', 'public'); // doctors/xxxx.jpg
    //         $data['image'] = $path;
    //     }

    //     $doctor->update($data);

    //     return redirect()->back()->with('success', 'Doctor updated successfully.');
    // }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'chamber' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'consultation_fee' => 'nullable|numeric|min:0',
            'available_time' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // jpg/png <= 2MB
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
                Storage::disk('public')->delete($doctor->image);
            }

            // save new one
            $path = $request->file('image')->store('doctors', 'public');
            $data['image'] = $path;
        }

        $doctor->update($data);

        //return redirect()->back()->with('success', 'Doctor updated successfully.');
        return redirect()->route('doctors.index')->with('success', 'Doctor Updated successfully.');
    }

public function destroy(Doctor $doctor)
    {
        // (Optional) delete file
        if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
            Storage::disk('public')->delete($doctor->image);
        }
        $doctor->delete();

        return redirect()->back()->with('success', 'Doctor deleted.');
    }
}
