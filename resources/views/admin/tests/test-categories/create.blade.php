<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">New Test Category</h1>
            <a href="{{ route('test-categories.index') }}" class="text-gray-700 hover:text-gray-900">Back</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3 border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form class="bg-white rounded-xl shadow ring-1 ring-black/5 p-6 space-y-4"
              method="POST" action="{{ route('test-categories.store') }}">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input name="name" type="text" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug (optional)</label>
                <input name="slug" type="text" value="{{ old('slug') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from name.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Active</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Create</button>
                <a href="{{ route('test-categories.index') }}" class="text-gray-700 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
