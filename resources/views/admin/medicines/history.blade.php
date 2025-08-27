<x-app-layout>
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-5">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                📜 Stock History – <span class="text-indigo-600">{{ $medicine->name }}</span>
            </h2>
            <a href="{{ route('medicines.index') }}" 
               class="px-4 py-2 bg-gray-700 text-white rounded-lg shadow hover:bg-gray-800 transition">
                ⬅ Back
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg text-sm">
                <thead class="bg-indigo-50 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 border text-left">📅 Date</th>
                        <th class="px-4 py-3 border text-left">🧾 Invoice</th>
                        <th class="px-4 py-3 border text-center">📌 Type</th>
                        <th class="px-4 py-3 border text-center">📦 Qty</th>
                        <th class="px-4 py-3 border text-center">📊 Stock After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2 text-gray-600">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="border px-4 py-2">
                                @if($log->invoice_id)
                                    <a href="{{ route('invoices.show', $log->invoice_id) }}" 
                                       class="text-blue-600 hover:underline">
                                        #{{ $log->invoice_id }}
                                    </a>
                                @else
                                    <span class="text-gray-400">–</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold">
                                @if($log->type === 'add')
                                    <span class="text-green-600">➕ Added</span>
                                @elseif($log->type === 'remove')
                                    <span class="text-red-600">➖ Removed</span>
                                @elseif($log->type === 'sale')
                                    <span class="text-indigo-600">🛒 Sale</span>
                                @else
                                    <span class="text-gray-600">{{ ucfirst($log->type) }}</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-center">
                                {{ $log->quantity }}
                            </td>
                            <td class="border px-4 py-2 text-center font-bold text-gray-800">
                                {{ $log->stock_after }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">
                                🚫 No stock history available for this medicine.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (if needed) -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
