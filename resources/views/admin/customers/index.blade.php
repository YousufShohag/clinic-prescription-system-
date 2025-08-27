<x-app-layout>
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Customers</h2>
            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                + Add Customer
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Phone 1</th>
                    <th class="px-4 py-2 border">Phone 2</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $customer->name }}</td>
                        <td class="px-4 py-2 border">{{ $customer->phone1 }}</td>
                        <td class="px-4 py-2 border">{{ $customer->phone2 }}</td>
                        <td class="px-4 py-2 border">{{ $customer->email }}</td>
                        <td class="px-4 py-2 border">
                            @if($customer->status)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center space-x-2">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this customer?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
