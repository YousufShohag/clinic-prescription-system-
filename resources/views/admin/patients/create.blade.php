<x-app-layout>
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6">
            {{ isset($patient) ? 'Edit Patient' : 'Add New Patient' }}
        </h2>

        <form action="{{ isset($patient) ? route('patients.update', $patient->id) : route('patients.store') }}" method="POST" class="space-y-4">
            @csrf
            @if(isset($patient)) @method('PUT') @endif

            <div>
                <label class="block text-gray-700">Patient Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $patient->name ?? '') }}" required>
            </div>

            <div>
                <label class="block text-gray-700">Assign Doctor (optional)</label>
                <select name="doctor_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- No Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ (old('doctor_id', $patient->doctor_id ?? '') == $doctor->id) ? 'selected' : '' }}>
                            {{ $doctor->name }} ({{ $doctor->specialization }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700">Phone</label>
                <input type="text" name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', $patient->phone ?? '') }}">
            </div>

            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" value="{{ old('email', $patient->email ?? '') }}">
            </div>

            <div>
                <label class="block text-gray-700">Notes</label>
                <textarea name="notes" class="w-full border rounded px-3 py-2">{{ old('notes', $patient->notes ?? '') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                {{ isset($patient) ? 'Update Patient' : 'Add Patient' }}
            </button>
        </form>
    </div>
</div>
</x-app-layout>
