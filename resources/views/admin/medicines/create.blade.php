<x-app-layout>
<div class="max-w-5xl mx-auto bg-gradient-to-br from-indigo-50 via-white to-indigo-100 shadow-lg rounded-xl p-8 mt-5">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-indigo-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Add New Medicine
        </h2>
        <a href="{{ route('medicines.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md shadow hover:bg-gray-200 transition">
            Back to List
        </a>
    </div>

    <form action="{{ route('medicines.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Category -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
            <select name="category_id" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Grid for inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                <input type="text" name="type" required placeholder="Tablet, Syrup, etc."
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Stock -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                <input type="number" name="stock" min="0" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Price ($)</label>
                <input type="number" step="0.01" name="price" min="0" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Expiry Date -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" required
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="1" selected>Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <!-- Notes -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Medicine Image</label>
            <div class="flex items-center gap-4 mt-2">
                <img id="preview-image" src="https://via.placeholder.com/100x100?text=Preview"
                     class="w-24 h-24 object-cover rounded-md border border-gray-300 shadow-sm">
                <input type="file" name="image" id="image-input"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100">
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:opacity-90 transition">
                💊 Save Medicine
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
