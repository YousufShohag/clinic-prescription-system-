<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 flex items-center gap-2">
            📑 Due Invoices
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            
            <!-- Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg text-sm">
                    <thead class="bg-indigo-50 text-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="border px-4 py-3 text-left"># Invoice</th>
                            <th class="border px-4 py-3 text-left">👤 Customer</th>
                            <th class="border px-4 py-3 text-left">📅 Date</th>
                            <th class="border px-4 py-3 text-right">💰 Grand Total</th>
                            <th class="border px-4 py-3 text-right">✅ Paid</th>
                            <th class="border px-4 py-3 text-right">⚠️ Due</th>
                            <th class="border px-4 py-3 text-center">📌 Status</th>
                            <th class="border px-4 py-3 text-center">⚙️ Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- Invoice ID -->
                                <td class="px-4 py-2 font-medium text-gray-800">
                                    #{{ $invoice->id }}
                                </td>

                                <!-- Customer -->
                                <td class="px-4 py-2">
                                    <div class="font-semibold text-gray-700">{{ $invoice->customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $invoice->customer->phone ?? '' }}</div>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-2 text-gray-600">
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                                </td>

                                <!-- Grand Total -->
                                <td class="px-4 py-2 text-right font-bold text-indigo-700">
                                    ${{ number_format($invoice->grand_total, 2) }}
                                </td>

                                <!-- Paid -->
                                <td class="px-4 py-2 text-right text-green-600 font-medium">
                                    ${{ number_format($invoice->paid_amount, 2) }}
                                </td>

                                <!-- Due -->
                                <td class="px-4 py-2 text-right text-red-600 font-bold">
                                    ${{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-2 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        🔴 Unpaid
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-2 text-center space-x-2">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" 
                                       class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                                       title="View Invoice">👁️ View</a>

                                    {{-- <a href="{{ route('invoices.download', $invoice->id) }}" 
                                       class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                       title="Download PDF">⬇️ PDF</a> --}}

                                    <a href="{{ route('invoices.edit', $invoice->id) }}" 
                                       class="px-2 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600"
                                       title="Edit Invoice">✏️ Edit</a>

                                    <!-- Quick Pay -->
                                    <button 
                                        class="px-2 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700"
                                        onclick="openQuickPay({{ $invoice->id }}, {{ $invoice->grand_total - $invoice->paid_amount }})">
                                        💳 Quick Pay
                                    </button>

                                    <!-- Mark Fully Paid -->
                                    <form method="POST" 
                                          action="{{ route('invoices.quickPay', $invoice->id) }}" 
                                          class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="paid_amount" value="{{ $invoice->grand_total - $invoice->paid_amount }}">
                                        <input type="hidden" name="payment_method" value="Cash">
                                        <button type="submit" 
                                                class="px-2 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-700"
                                                title="Mark as Fully Paid">
                                            ✅ Full Pay
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-gray-500">
                                    🎉 All invoices are paid! No due invoices.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Quick Pay Modal -->
                <div id="quickPayModal" 
                     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                        <h3 class="text-lg font-bold mb-4">💳 Quick Pay Invoice</h3>
                        <form method="POST" action="" id="quickPayForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="invoice_id" id="invoiceId">

                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700">Amount to Pay</label>
                                <input type="number" step="0.01" name="paid_amount" id="paidAmountInput" 
                                       class="w-full border rounded px-3 py-2 mt-1" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700">Payment Method</label>
                                <select name="payment_method" class="w-full border rounded px-3 py-2">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Mobile Banking">Mobile Banking</option>
                                </select>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="closeQuickPay()" 
                                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                    ✅ Pay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    <script>
        function openQuickPay(invoiceId, dueAmount) {
    let form = document.getElementById('quickPayForm');
    form.action = `/invoices/${invoiceId}/quick-pay`; 
    document.getElementById('paidAmountInput').value = dueAmount;
    document.getElementById('quickPayModal').classList.remove('hidden');
}

        function closeQuickPay() {
            document.getElementById('quickPayModal').classList.add('hidden');
            document.getElementById('quickPayModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
