<x-app-layout>
    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-xl p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h13v6M9 17H6a2 2 0 01-2-2v-1h5m0 3h0m13-6h-5m5 0a2 2 0 012 2v1a2 2 0 01-2 2h-3" />
            </svg>
            <span>Edit Medicine</span>
        </h2>

        <form action="{{ route('medicines.update', $medicine->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $medicine->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ $medicine->name }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <input type="text" name="type" value="{{ $medicine->type }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" name="stock" value="{{ $medicine->stock }}" min="0" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" step="0.01" name="price" value="{{ $medicine->price }}" min="0" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Expiry Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ $medicine->expiry_date }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $medicine->description }}</textarea>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $medicine->notes }}</textarea>
            </div>

            <!-- Current Image + Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <div class="mt-1 mb-3">
                    <img id="preview-image" 
                         src="{{ $medicine->image ? asset('storage/'.$medicine->image) : 'https://via.placeholder.com/150x150?text=No+Image' }}"
                         class="w-28 h-28 object-cover rounded-lg border border-gray-300 shadow-sm hover:scale-105 transition">
                </div>
                <input type="file" name="image" id="image-input"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="1" {{ $medicine->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$medicine->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="md:col-span-2 flex justify-end space-x-3 pt-4">
                <a href="{{ route('medicines.index') }}"
                   class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 rounded-lg shadow hover:bg-gray-200 transition">
                   ✖ Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                    💾 Update
                </button>
            </div>
        </form>
    </div>

    <!-- Live Preview Script -->
    <script>
        const input = document.getElementById('image-input');
        const preview = document.getElementById('preview-image');
        input.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    </script>
</x-app-layout>
