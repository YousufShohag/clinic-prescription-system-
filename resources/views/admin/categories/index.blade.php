<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-700">Categories</h2>
                <a href="{{ route('categories.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                   + Add Category
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 border">Image</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Description</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border text-center">
                                    @if($category->image)
                                        <img src="{{ asset('storage/'.$category->image) }}"
                                             class="w-12 h-12 object-cover rounded-md mx-auto">
                                    @else
                                        <span class="text-gray-400">No Image</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border font-medium">{{ $category->name }}</td>
                                <td class="px-4 py-2 border">{{ $category->description }}</td>
                                <td class="px-4 py-2 border text-center">
                                    @if($category->status)
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border text-center">
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                       class="inline-block px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                       Edit
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Delete this category?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
