<x-app-layout>
    <div class="max-w-5xl mx-auto p-8 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Invoice #{{ $invoice->id }}</h2>

        <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" x-data="invoiceForm()">
            @csrf
            @method('PUT')

            <!-- Customer -->
            <div class="mb-4">
                <label class="block font-semibold">Customer</label>
                <select name="customer_id" class="w-full border rounded px-3 py-2">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $invoice->customer_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->phone1 }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label class="block font-semibold">Notes</label>
                <textarea name="notes" class="w-full border rounded px-3 py-2">{{ $invoice->notes }}</textarea>
            </div>

            <!-- Medicines -->
            <h3 class="font-semibold mb-2">💊 Medicines</h3>
            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-12 gap-3 mb-2 p-2 border rounded bg-gray-50">
                    <input type="hidden" :name="'items['+index+'][medicine_id]'" x-model="item.medicine_id">

                    <div class="col-span-5">
                        <input type="text" class="w-full border rounded px-2 py-1 bg-gray-100" x-model="item.name" readonly>
                    </div>

                    <div class="col-span-2">
                        <input type="number" min="1" class="w-full border rounded px-2 py-1" 
                               :name="'items['+index+'][quantity]'" 
                               x-model.number="item.quantity" 
                               @input="calculateSubtotal(index)">
                    </div>

                    <div class="col-span-2">
                        <input type="text" class="w-full border rounded px-2 py-1" 
                               :name="'items['+index+'][price]'" 
                               x-model="item.price" readonly>
                    </div>

                    <div class="col-span-2">
                        <input type="text" class="w-full border rounded px-2 py-1 bg-gray-100" 
                               x-model="item.subtotal" readonly>
                    </div>

                    <div class="col-span-1 text-right">
                        <button type="button" class="px-2 py-1 bg-red-500 text-white rounded" @click="removeItem(index)">✖</button>
                    </div>
                </div>
            </template>

            <!-- Add Medicine -->
            <button type="button" @click="addItem()" class="px-3 py-1 mt-2 bg-green-500 text-white rounded">
                ➕ Add Medicine
            </button>

            <!-- Payment + Discount/Tax -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded px-3 py-2">
                        <option value="Cash" {{ $invoice->payment_method=='Cash'?'selected':'' }}>Cash</option>
                        <option value="Card" {{ $invoice->payment_method=='Card'?'selected':'' }}>Card</option>
                        <option value="Mobile Banking" {{ $invoice->payment_method=='Mobile Banking'?'selected':'' }}>Mobile Banking</option>
                        <option value="Due" {{ $invoice->payment_method=='Due'?'selected':'' }}>Due</option>
                    </select>
                </div>
                <div>
                    <label class="block">Paid Amount</label>
                    <input type="number" name="paid_amount" value="{{ $invoice->paid_amount }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label>Discount (%)</label>
                    <input type="number" name="discount" value="{{ $invoice->discount }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label>Tax (%)</label>
                    <input type="number" name="tax" value="{{ $invoice->tax }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <!-- Totals -->
            <div class="mt-6 border-t pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Grand Total:</span>
                    <span>$<span x-text="grandTotal"></span></span>
                </div>
            </div>

            <!-- Save -->
            <div class="mt-6 text-right">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded">💾 Update Invoice</button>
            </div>
        </form>
    </div>

    <script>
    function invoiceForm() {
        return {
            items: @json($invoiceItems), // clean JSON, no PHP here
            grandTotal: {{ $invoice->grand_total }},
            
            calculateSubtotal(index) {
                let item = this.items[index];
                item.subtotal = item.price * item.quantity;
                this.calculateTotal();
            },
            calculateTotal() {
                this.grandTotal = this.items.reduce((s, i) => s + Number(i.subtotal), 0);
            },
            addItem() {
                this.items.push({ medicine_id:'', name:'', quantity:1, price:0, subtotal:0 });
            },
            removeItem(i) {
                this.items.splice(i,1);
                this.calculateTotal();
            }
        }
    }
</script>

</x-app-layout>
