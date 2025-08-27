<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Available Tests</h2>
            <a href="{{ route('tests.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Add New Test
            </a>
        </div>

        {{-- DESKTOP / TABLET (md+) --}}
        <div class="hidden md:block bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (৳)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tests as $test)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $test->name }}
                            </td>

                            {{-- Category (truncate + tooltip) --}}
                            <td class="px-6 py-4 text-gray-700 max-w-[160px] truncate" title="{{ $test->category?->name }}">
                                {{ $test->category?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                ৳{{ number_format($test->price, 2) }}
                            </td>

                            {{-- Note (truncate + tooltip) --}}
                            <td class="px-6 py-4 text-gray-700 max-w-[220px] truncate" title="{{ $test->note }}">
                                {{ $test->note ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($test->status === 'active')
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right text-sm">
                                <div class="inline-flex gap-3">
                                    <a href="{{ route('tests.edit', $test) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <form action="{{ route('tests.destroy', $test) }}" method="POST"
                                          onsubmit="return confirm('Delete this test?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No tests available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($tests,'links'))
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $tests->links() }}
                </div>
            @endif
        </div>

        {{-- MOBILE (< md) — cards, no horizontal scroll --}}
        <div class="md:hidden space-y-3">
            @forelse($tests as $test)
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-gray-900">{{ $test->name }}</h3>
                            <p class="text-sm text-gray-600 max-w-[220px] truncate" title="{{ $test->category?->name }}">
                                {{ $test->category?->name ?? '—' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm font-semibold text-gray-900">
                                ৳{{ number_format($test->price, 2) }}
                            </div>
                            <div class="mt-1">
                                @if($test->status === 'active')
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="mt-2 text-sm text-gray-700 max-w-full truncate" title="{{ $test->note }}">
                        {{ $test->note ?? '—' }}
                    </p>

                    <div class="mt-3 flex justify-end gap-3">
                        <a href="{{ route('tests.edit', $test) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                        <form action="{{ route('tests.destroy', $test) }}" method="POST"
                              onsubmit="return confirm('Delete this test?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                    No tests available yet.
                </div>
            @endforelse

            @if(method_exists($tests,'links'))
                <div class="pt-2">
                    {{ $tests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
