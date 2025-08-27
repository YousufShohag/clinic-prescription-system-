<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">📄 Invoices</h2>
            <a href="{{ route('invoices.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                ➕ New Invoice
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="p-3 mb-4 bg-green-100 text-green-700 border border-green-300 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Invoice Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 border">#</th>
                        <th class="px-4 py-3 border">Customer</th>
                        <th class="px-4 py-3 border">Date</th>
                        <th class="px-4 py-3 border">Total</th>
                        <th class="px-4 py-3 border">Paid Amount</th> 
                        <th class="px-4 py-3 border">Status</th>
                        <th class="px-4 py-3 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- ID -->
                            <td class="border px-4 py-2 font-medium text-gray-700">
                                #{{ $invoice->id }}
                            </td>

                            <!-- Customer -->
                            <td class="border px-4 py-2">
                                <span class="font-semibold">{{ $invoice->customer->name }}</span><br>
                                <span class="text-xs text-gray-500">{{ $invoice->customer->phone ?? '' }}</span>
                            </td>

                            <!-- Date -->
                            <td class="border px-4 py-2 text-gray-600">
                                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                            </td>

                            <!-- Totals -->
                            <td class="border px-4 py-2">
                                <div>
                                    <span class="text-gray-500 text-xs">Subtotal:</span> 
                                    <span class="text-gray-700">${{ number_format($invoice->total, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">Final Total:</span> 
                                    <span class="font-bold text-indigo-700">
                                        ${{ number_format($invoice->grand_total, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="border px-4 py-2 text-center font-semibold text-green-700">
                                ${{ number_format($invoice->paid_amount, 2) }}
                            </td>

                            <!-- Status -->
                            <td class="border px-4 py-2 text-center">
                                @if($invoice->status)
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                                        ✅ Paid
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                                        ⏳ Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="border px-4 py-2 text-center space-x-2">
                                <!-- View -->
                                <a href="{{ route('invoices.show', $invoice->id) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                   title="View Invoice">
                                   👁️
                                </a>

                                <a href="{{ route('invoices.edit', $invoice->id) }}" 
       class="inline-flex items-center px-2 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600"
       title="Edit Invoice">
       ✏️
    </a>

                                <!-- PDF -->
                                <a href="{{ route('invoices.download', $invoice->id) }}" 
                                   class="inline-flex items-center px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700"
                                   title="Download PDF">
                                   ⬇️
                                </a>

                                <!-- Print -->
                                <a href="{{ route('invoices.show', $invoice->id) }}?print=true" 
                                   class="inline-flex items-center px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                   title="Print Invoice">
                                   🖨️
                                </a>

                                {{-- <div class="flex gap-4 mb-6">
    <a href="{{ route('reports.export.excel', ['month' => now()->month]) }}" 
       class="px-4 py-2 bg-green-600 text-white rounded shadow">⬇️ Export Excel</a>

    <a href="{{ route('reports.export.pdf', ['month' => now()->month]) }}" 
       class="px-4 py-2 bg-red-600 text-white rounded shadow">⬇️ Export PDF</a>
</div> --}}

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
