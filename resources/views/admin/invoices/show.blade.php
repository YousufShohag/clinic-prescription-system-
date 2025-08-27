<x-app-layout>
    <div class="mt-5 max-w-4xl mx-auto bg-white p-8 shadow-xl rounded-lg border border-gray-300 print:border-black relative min-h-screen flex flex-col">

        <!-- 🔹 Header -->
        <div class="flex justify-between items-center border-b border-gray-300 print:border-black pb-4 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('logo.png') }}" alt="Pharmacy Logo" class="h-32 w-32 object-contain">
                <div>
                    <h1 class="text-2xl font-bold text-indigo-700">Patwari Model Pharmacy</h1>
                    <p class="text-sm text-gray-600">123 Main Street, Chattogram, Bangladesh</p>
                    <p class="text-sm text-gray-600">📞 +880 123 456 789 | ✉️ info@pharmacy.com</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-semibold text-gray-700">Invoice No </h2>
                <p class="text-sm text-gray-500">#{{ $invoice->id }}</p>
                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
            </div>
        </div>

        <!-- 🔹 Items Table -->
        <table class="w-full mt-4 border border-gray-300 print:border-black text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-4 py-2 border text-left">Medicine</th>
                    <th class="px-4 py-2 border text-center">Qty</th>
                    <th class="px-4 py-2 border text-right">Price</th>
                    <th class="px-4 py-2 border text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                        <td class="px-4 py-2 border">{{ $item->medicine->name }}</td>
                        <td class="px-4 py-2 border text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 border text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-2 border text-right">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            {{-- <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="3" class="px-4 py-2 border text-right">Subtotal</td>
                    <td class="px-4 py-2 border text-right">${{ number_format($invoice->total, 2) }}</td>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <td colspan="3" class="px-4 py-2 border text-right">Discount ({{ $invoice->discount }}%)</td>
                    <td class="px-4 py-2 border text-right">- ${{ number_format(($invoice->total * $invoice->discount) / 100, 2) }}</td>
                </tr>
                @endif
                @if($invoice->tax > 0)
                <tr>
                    <td colspan="3" class="px-4 py-2 border text-right">Tax ({{ $invoice->tax }}%)</td>
                    <td class="px-4 py-2 border text-right">+ ${{ number_format((($invoice->total - ($invoice->total * $invoice->discount / 100)) * $invoice->tax) / 100, 2) }}</td>
                </tr>
                @endif
                <tr class="bg-indigo-100 text-indigo-900 font-bold">
                    <td colspan="3" class="px-4 py-2 border text-right">Grand Total</td>
                    <td class="px-4 py-2 border text-right">${{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
            </tfoot> --}}
            <tfoot class="bg-gray-100 font-semibold">
    <tr>
        <td colspan="3" class="px-4 py-2 border text-right">Subtotal</td>
        <td class="px-4 py-2 border text-right">${{ number_format($invoice->total, 2) }}</td>
    </tr>
    @if($invoice->discount > 0)
    <tr>
        <td colspan="3" class="px-4 py-2 border text-right">Discount ({{ $invoice->discount }}%)</td>
        <td class="px-4 py-2 border text-right">
            - ${{ number_format(($invoice->total * $invoice->discount) / 100, 2) }}
        </td>
    </tr>
    @endif
    @if($invoice->tax > 0)
    <tr>
        <td colspan="3" class="px-4 py-2 border text-right">Tax ({{ $invoice->tax }}%)</td>
        <td class="px-4 py-2 border text-right">
            + ${{ number_format((($invoice->total - ($invoice->total * $invoice->discount / 100)) * $invoice->tax) / 100, 2) }}
        </td>
    </tr>
    @endif
    <tr class="bg-indigo-100 text-indigo-900 font-bold">
        <td colspan="3" class="px-4 py-2 border text-right">Grand Total</td>
        <td class="px-4 py-2 border text-right">${{ number_format($invoice->grand_total, 2) }}</td>
    </tr>
    <!-- 🔹 Paid Amount -->
    <tr class="bg-green-50 text-green-700 font-semibold">
        <td colspan="3" class="px-4 py-2 border text-right">Paid Amount</td>
        <td class="px-4 py-2 border text-right">
            ${{ number_format($invoice->paid_amount, 2) }}
        </td>
    </tr>
    <!-- 🔹 Due Amount -->
    @if($invoice->grand_total > $invoice->paid_amount)
    <tr class="bg-red-50 text-red-700 font-semibold">
        <td colspan="3" class="px-4 py-2 border text-right">Due Amount</td>
        <td class="px-4 py-2 border text-right">
            ${{ number_format($invoice->grand_total - $invoice->paid_amount, 2) }}
        </td>
    </tr>
    @endif
</tfoot>

        </table>


        

        <!-- 🔹 Footer -->
        <div class="invoice-footer mt-10 flex justify-between items-center text-sm text-gray-600 border-t border-gray-300 print:border-black pt-4">

            <!-- Left: QR -->
            <div class="qr-code-container text-left">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(
                    'Invoice #'.$invoice->id.
                    "\nCustomer: ".$invoice->customer->name.
                    "\nTotal: $".number_format($invoice->grand_total,2).
                    "\nStatus: ".($invoice->status ? 'Paid' : 'Unpaid'),
                    'QRCODE'
                ) }}" alt="QR Code" class="h-20 w-20" />
                
            </div>

            <!-- Center: Thank You -->
            <p class="text-center">💊 Thank you for choosing <b>Patwari Model Pharmacy</b>!</p>

            <!-- Right: Signature + Print Button -->
            <div class="text-right">
                <p>____________________ <br> Authorized Signature</p>
                <!-- Print button -->
                <button onclick="window.print()" 
                        class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 print:hidden">
                    🖨️ Print Invoice
                </button>
            </div>
        </div>
    </div>

    <style>
    @media print {
        .print\:hidden { display: none !important; }

        .invoice-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding: 10px 20px;
            border-top: 1px solid black !important;
            background: white;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }
    </style>
</x-app-layout>
