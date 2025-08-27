<x-app-layout>
    <div class="container mx-auto py-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-semibold text-gray-800">Doctors</h2>
            <a href="{{ route('doctors.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Add Doctor
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Degree</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chamber</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee (৳)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($doctors as $doctor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($doctor->image)
                                    <img src="{{ asset('storage/doctors/'.$doctor->image) }}" alt="Doctor Image" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $doctor->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->specialization ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->degree ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->chamber ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->email }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->consultation_fee ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->available_time ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $doctor->notes ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap flex gap-2">
                                <a href="{{ route('doctors.edit', $doctor->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition text-sm">Edit</a>
                                <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($doctors->isEmpty())
                <div class="mt-6 text-center text-gray-500">
                    No doctors found.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
