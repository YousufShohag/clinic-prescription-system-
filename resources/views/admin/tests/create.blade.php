<x-app-layout>
    <div class="container mx-auto py-8">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Create New Test</h2>

            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

<form action="{{ route('tests.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
    <button type="submit">Upload & Import</button>
</form>


            {{-- Create Test Form --}}
            <form action="{{ route('tests.store') }}" method="POST" class="space-y-4">
                @csrf
<div>
                    <label for="test_category_id" class="block text-gray-700 font-medium mb-1">Category</label>
                    <select name="test_category_id" id="test_category_id"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('test_category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-1">Test Name</label>
                    <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" value="{{ old('name') }}" required>
                </div>

                <div>
                    <label for="price" class="block text-gray-700 font-medium mb-1">Price (৳)</label>
                    <input type="number" name="price" id="price" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" step="0.01" value="{{ old('price') }}" required>
                </div>

                <div>
                    <label for="note" class="block text-gray-700 font-medium mb-1">Note</label>
                    <textarea name="note" id="note" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" rows="3">{{ old('note') }}</textarea>
                </div>

                <div>
                    <label for="status" class="block text-gray-700 font-medium mb-1">Status</label>
                    <select name="status" id="status" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" required>
                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Create Test</button>
            </form>
        </div>

        {{-- Optional: Existing Tests Table --}}
        @if(isset($tests) && $tests->count() > 0)
            <div class="mt-8 max-w-4xl mx-auto bg-white shadow rounded-lg p-6 overflow-x-auto">
                <h4 class="text-xl font-semibold text-gray-800 mb-4">Existing Tests</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (৳)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tests as $test)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium">{{ $test->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $test->price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $test->note ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($test->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
