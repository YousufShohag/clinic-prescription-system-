<x-app-layout>
    <div class="container mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Prescriptions</h2>
            <a href="{{ route('prescriptions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ New Prescription</a>
        </div>

        <div class="bg-white shadow rounded overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($prescriptions as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $p->id }}</td>
                            <td class="px-4 py-3">{{ $p->patient->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->doctor->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->created_at->format('m-d-Y H:i') }}</td>
                            <td class="px-4 py-3 flex items-center space-x-3">
                                <a href="{{ route('prescriptions.show', $p->id) }}" class="text-blue-600 hover:underline">View</a>
                                <a href="{{ route('prescriptions.edit', $p->id) }}" class="text-green-600 hover:underline">Edit</a>
                                <form action="{{ route('prescriptions.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this prescription?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $prescriptions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
