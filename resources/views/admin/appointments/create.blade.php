<x-app-layout>
    <div class="container mx-auto py-8">
        <h2 class="text-2xl font-semibold mb-6">Add Appointment</h2>

        <form action="{{ route('appointments.store') }}" method="POST" class="bg-white p-6 rounded shadow-md">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Patient</label>
                <select name="patient_id" class="w-full border-gray-300 rounded">
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                    @endforeach
                </select>
                @error('patient_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Doctor</label>
                <select name="doctor_id" class="w-full border-gray-300 rounded">
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                    @endforeach
                </select>
                @error('doctor_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Scheduled At</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full border-gray-300 rounded"/>
                @error('scheduled_at') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Notes</label>
                <textarea name="notes" class="w-full border-gray-300 rounded">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 mr-2 bg-gray-200 rounded">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</x-app-layout>