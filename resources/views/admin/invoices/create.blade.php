<x-app-layout>
    <div class="max-w-5xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            🧾 New Invoice
        </h2>

        <form method="POST" action="{{ route('invoices.store') }}" 
              x-data="invoiceForm()" 
              @submit.prevent="$el.submit()">
            @csrf

            <!-- Customer Selection -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700">👤 Customer</label>
                <select name="customer_id" class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-indigo-200">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone1 }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700">📝 Notes</label>
                <textarea name="notes" class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-indigo-200"></textarea>
            </div>

            <!-- Medicines Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-3 text-gray-800 flex items-center">💊 Medicines</h3>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-3 mb-3 p-4 border rounded-lg bg-gray-50 relative shadow-sm">
                        
                        <!-- Search / Select Medicine -->
                        <div class="col-span-5 relative">
                            <label class="text-xs text-gray-500">Medicine</label>
                            <input type="text" 
                                   class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-indigo-200"
                                   placeholder="Search medicine..."
                                   x-model="item.search"
                                   @input="filterMedicines(index)">
                            
                            <!-- Search results -->
                            <div class="bg-white shadow border rounded mt-1 absolute z-50 w-full max-h-48 overflow-y-auto"
                                 x-show="item.results.length > 0">
                                <template x-for="m in item.results" :key="m.id">
                                    <div @click="selectMedicine(index, m)"
                                         class="px-3 py-2 hover:bg-indigo-100 cursor-pointer flex justify-between">
                                        <span x-text="m.name"></span>
                                        <span class="text-gray-600 text-sm">$<span x-text="m.price"></span></span>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" :name="'items['+index+'][medicine_id]'" x-model="item.medicine_id">
                        </div>

                        <!-- Quantity -->
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500">Qty</label>
                            <input type="number" min="1" 
                                   class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-indigo-200"
                                   x-model.number="item.qty" 
                                   :name="'items['+index+'][quantity]'"
                                   @input="calculateSubtotal(index)">
                        </div>

                        <!-- Price -->
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500">Price</label>
                            <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-100" 
                                   x-model="item.price" readonly>
                        </div>

                        <!-- Subtotal -->
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500">Subtotal</label>
                            <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-100 font-semibold text-right" 
                                   x-model="item.subtotal" readonly>
                        </div>

                        <!-- Remove Button -->
                        <div class="col-span-1 flex items-end">
                            <button type="button" @click="removeItem(index)" 
                                    class="px-3 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600">
                                ✖
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Add Item Button -->
                <button type="button" @click="addItem()" 
                        class="mt-3 px-5 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600">
                    ➕ Add Medicine
                </button>
            </div>

            <!-- Payment Method -->
            <div class="mb-4">
                <label class="block text-sm font-medium">Payment Method</label>
                <select name="payment_method" class="border rounded w-full p-2">
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="Mobile Banking">Mobile Banking</option>
                    <option value="Due">Due</option>
                </select>
            </div>

            <!-- Paid Amount (auto-filled from grandTotal) -->
            <div class="mb-4">
                <label class="block text-sm font-medium">Paid Amount</label>
                <input type="number" step="0.01" name="paid_amount" 
                       x-model="paidAmount"
                       class="border rounded w-full p-2 bg-gray-50">
            </div>

            <!-- Discount & Tax -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">💲 Discount (%)</label>
                    <input type="number" step="0.01" x-model.number="discount" name="discount" 
                           class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-indigo-200" 
                           @input="calculateTotal" value="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">📈 Tax (%)</label>
                    <input type="number" step="0.01" x-model.number="tax" name="tax" 
                           class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring focus:ring-indigo-200" 
                           @input="calculateTotal" value="0">
                </div>
            </div>

            <!-- Totals Summary -->
            <div class="bg-gray-50 border rounded-lg p-4 mb-6 shadow-sm">
                <h4 class="font-semibold text-gray-800 mb-3">💵 Invoice Summary</h4>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium text-gray-700">$<span x-text="subtotal.toFixed(2)"></span></span>
                </div>
                <template x-if="discount > 0">
                    <div class="flex justify-between text-sm mb-1 text-red-600">
                        <span>Discount (<span x-text="discount"></span>%)</span>
                        <span>- $<span x-text="discountAmount"></span></span>
                    </div>
                </template>
                <template x-if="tax > 0">
                    <div class="flex justify-between text-sm mb-1 text-green-600">
                        <span>Tax (<span x-text="tax"></span>%)</span>
                        <span>+ $<span x-text="taxAmount"></span></span>
                    </div>
                </template>
                <div class="flex justify-between font-bold text-lg text-indigo-700 border-t pt-2 mt-2">
                    <span>Grand Total:</span>
                    <span>$<span x-text="grandTotal"></span></span>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-6 text-right">
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700">
                    💾 Save Invoice
                </button>
            </div>
        </form>
    </div>

    <!-- Alpine.js Script -->
    <script>
        function invoiceForm() {
            return {
                items: [],
                medicines: @json($medicines),
                subtotal: 0,
                discount: 0,
                tax: 0,
                discountAmount: 0,
                taxAmount: 0,
                grandTotal: 0,
                paidAmount: 0, // 🔹 auto-filled

                addItem() {
                    this.items.push({ search: '', results: [], medicine_id: '', qty: 1, price: 0, subtotal: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },
                filterMedicines(index) {
                    let query = this.items[index].search.toLowerCase();
                    this.items[index].results = this.medicines.filter(m => m.name.toLowerCase().includes(query));
                },
                selectMedicine(index, medicine) {
                    this.items[index].medicine_id = medicine.id;
                    this.items[index].search = medicine.name;
                    this.items[index].price = medicine.price;
                    this.items[index].subtotal = medicine.price * this.items[index].qty;
                    this.items[index].results = [];
                    this.calculateTotal();
                },
                calculateSubtotal(index) {
                    let item = this.items[index];
                    item.subtotal = item.price * item.qty;
                    this.calculateTotal();
                },
                calculateTotal() {
                    this.subtotal = this.items.reduce((sum, item) => sum + item.subtotal, 0);
                    this.discountAmount = (this.subtotal * this.discount / 100).toFixed(2);
                    this.taxAmount = ((this.subtotal - this.discountAmount) * this.tax / 100).toFixed(2);
                    this.grandTotal = (this.subtotal - this.discountAmount + parseFloat(this.taxAmount)).toFixed(2);

                    // 🔹 Auto-update Paid Amount if empty or 0
                    if (!this.paidAmount || this.paidAmount == 0) {
                        this.paidAmount = this.grandTotal;
                    }
                }
            }
        }
    </script>
</x-app-layout>
