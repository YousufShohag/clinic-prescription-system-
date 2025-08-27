<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Test Categories</h1>
            <a href="{{ route('test-categories.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                + New Category
            </a>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow ring-1 ring-black/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Active</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ $cat->name }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $cat->slug }}</td>
                                <td class="px-6 py-4">
                                    @if($cat->is_active)
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex gap-3">
                                        <a href="{{ route('test-categories.edit', $cat) }}"
                                           class="text-blue-600 hover:text-blue-800">Edit</a>
                                        <form method="POST" action="{{ route('test-categories.destroy', $cat) }}"
                                              onsubmit="return confirm('Delete this category?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    No categories yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($categories, 'links'))
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
