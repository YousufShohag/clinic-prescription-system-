<x-app-layout>
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6">Edit Patient</h2>

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-medium mb-1">Patient Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                    value="{{ old('name', $patient->name) }}" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Assign Doctor (optional)</label>
                <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">-- No Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" 
                            {{ (old('doctor_id', $patient->doctor_id) == $doctor->id) ? 'selected' : '' }}>
                            {{ $doctor->name }} ({{ $doctor->specialization ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Phone</label>
                <input type="text" name="phone" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                    value="{{ old('phone', $patient->phone) }}">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                    value="{{ old('email', $patient->email) }}">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                <textarea name="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" rows="3">{{ old('notes', $patient->notes) }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Update Patient
            </button>
        </form>
    </div>
</div>
</x-app-layout>
