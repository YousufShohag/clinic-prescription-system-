<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.doctors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'bma_registration_number' => 'nullable|string|max:255',
            'chamber' => 'nullable|string|max:255',
            'email' => 'required|email|unique:doctors,email',
            'phone' => 'nullable|string|max:20',
            'consultation_fee' => 'nullable|numeric',
            'available_time' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/doctors', $filename);
            $data['image'] = $filename;
        }

        Doctor::create($data);

        return redirect()->route('doctors.index')->with('success','Doctor added successfully.');
    }
}
