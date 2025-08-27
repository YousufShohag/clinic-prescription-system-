<x-app-layout>
    <div class="container mx-auto py-8">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                {{ isset($doctor) ? 'Edit Doctor' : 'Add New Doctor' }}
            </h2>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($doctor) ? route('doctors.update', $doctor->id) : route('doctors.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($doctor))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Name</label>
                        <input type="text" name="name" placeholder="Dr. John Doe" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('name', $doctor->name ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Specialization</label>
                        <input type="text" name="specialization" placeholder="Cardiologist" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('specialization', $doctor->specialization ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Degree</label>
                        <input type="text" name="degree" placeholder="MBBS, MD" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('degree', $doctor->degree ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">BMA Registration Number</label>
                        <input type="text" name="bma_registration_number" placeholder="BMA12345" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('bma_registration_number', $doctor->bma_registration_number ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Chamber</label>
                        <input type="text" name="chamber" placeholder="City Hospital, Room 101" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('chamber', $doctor->chamber ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Email</label>
                        <input type="email" name="email" placeholder="doctor@example.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('email', $doctor->email ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+8801XXXXXXXXX" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('phone', $doctor->phone ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Consultation Fee</label>
                        <input type="number" name="consultation_fee" step="0.01" placeholder="500" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('consultation_fee', $doctor->consultation_fee ?? '') }}" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Available Time (optional)</label>
                        <input type="text" name="available_time" placeholder="10:00 AM - 2:00 PM" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('available_time', $doctor->available_time ?? '') }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Notes (optional)</label>
                        <textarea name="notes" rows="3" placeholder="Additional info about the doctor" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('notes', $doctor->notes ?? '') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Doctor Image (optional)</label>
                        <input type="file" name="image" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                        @if(isset($doctor) && $doctor->image)
                            <img src="{{ asset('storage/doctors/'.$doctor->image) }}" alt="Doctor Image" class="h-24 w-24 mt-2 rounded-full object-cover">
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition font-semibold">
                        {{ isset($doctor) ? 'Update Doctor' : 'Add Doctor' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
