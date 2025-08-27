<x-app-layout>
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-6">Edit Category</h2>

            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ $category->name }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $category->description }}</textarea>
                </div>

                <!-- Current Image + Upload New -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category Image</label>

                    <!-- Current image OR new preview -->
                    <div class="mt-2 mb-3">
                        <img id="preview-image"
                             src="{{ $category->image ? asset('storage/'.$category->image) : 'https://via.placeholder.com/100x100?text=No+Image' }}"
                             class="w-24 h-24 object-cover rounded-md border border-gray-300">
                    </div>

                    <!-- File input -->
                    <input type="file" name="image" id="image-input"
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1" {{ $category->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$category->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('categories.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition">
                       Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
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
