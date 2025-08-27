<x-app-layout>
    <div class="max-w-7xl mx-auto p-6" 
         x-data="{ openWeather: false, showModal: false, medicine: {} }">

        <!-- 🌤️ Floating Weather Widget -->
        <div class="fixed bottom-4 right-6 z-50">
            <!-- Minimized Button -->
            <button x-show="!openWeather"
                    @click="openWeather = true"
                    x-transition
                    class="flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2 rounded-full shadow-lg hover:bg-indigo-700">
                <span>🌤️ Weather</span>
            </button>

            <!-- Expanded Weather Card -->
            <div x-show="openWeather"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-400"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-2"
                 class="relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white w-80 rounded-lg shadow-2xl p-6">

                <!-- Close/Minimize Button -->
                <button @click="openWeather = false"
                        class="absolute top-3 right-3 text-white bg-white/20 hover:bg-white/30 rounded-full p-1.5">
                    ✖
                </button>

                <h2 class="text-lg font-semibold mb-2">🌤️ Today's Weather</h2>
                <p class="text-sm mb-4">Chattogram, Bangladesh</p>

                <!-- Current -->
                <div class="flex items-center space-x-4">
                    <div class="text-5xl">☁️</div>
                    <div>
                        <p class="text-lg font-medium">Cloudy, 80°F (27°C)</p>
                        <p class="text-xs text-blue-100">Humidity: 78% | Wind: 12 km/h</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 💊 Medicines Table -->
        <div class="bg-white shadow rounded-lg p-6 mt-5">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">Medicines</h3>
                <a href="{{ route('medicines.create') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + Add Medicine
                </a>
            </div>

            <!-- 🔍 Filter Section -->
            <form method="GET" action="{{ route('medicines.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <!-- Search by Name -->
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name..."
                       class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                <!-- Category Filter -->
                <select name="category" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                <!-- Expiry Filter -->
                <select name="expiry" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Expiry</option>
                    <option value="valid" {{ request('expiry') == 'valid' ? 'selected' : '' }}>Not Expired</option>
                    <option value="expired" {{ request('expiry') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <!-- Buttons -->
                <div class="col-span-1 sm:col-span-4 flex justify-end space-x-3">
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Apply
                    </button>
                    <a href="{{ route('medicines.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Reset
                    </a>
                </div>
            </form>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <table class="w-full border border-gray-200 text-sm text-gray-600">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Image</th>
                        <th class="px-4 py-2 border">Name</th>
                        <th class="px-4 py-2 border">Category</th>
                        <th class="px-4 py-2 border">Type</th>
                        <th class="px-4 py-2 border">Stock</th>
                        <th class="px-4 py-2 border">Price</th>
                        <th class="px-4 py-2 border">Expiry</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $medicine)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border text-center">
                                @if($medicine->image)
                                    <img src="{{ asset('storage/'.$medicine->image) }}" class="w-12 h-12 object-cover rounded-md mx-auto">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>

                            <!-- Clickable Name -->
                            <td class="px-4 py-2 border">
                                <button 
                                    @click="medicine = {
                                        id: '{{ $medicine->id }}',
                                        name: '{{ $medicine->name }}',
                                        category: '{{ $medicine->category->name }}',
                                        type: '{{ $medicine->type }}',
                                        stock: '{{ $medicine->stock }}',
                                        price: '{{ $medicine->price }}',
                                        expiry: '{{ $medicine->expiry_date }}',
                                        status: '{{ $medicine->status ? 'Active' : 'Inactive' }}',
                                        notes: '{{ $medicine->notes }}',
                                        image: '{{ $medicine->image ? asset("storage/".$medicine->image) : "" }}'
                                    }; showModal = true"
                                    class="text-indigo-600 hover:underline font-semibold">
                                    {{ $medicine->name }}
                                </button>
                            </td>

                            <td class="px-4 py-2 border">{{ $medicine->category->name }}</td>
                            <td class="px-4 py-2 border">{{ $medicine->type }}</td>
                            <td class="px-4 py-2 border">{{ $medicine->stock }}</td>
                            <td class="px-4 py-2 border">${{ $medicine->price }}</td>
                            {{-- <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('m/d/Y') }}</td> --}}
                            <td class="px-4 py-2 border text-center">
    @php
        $expiry = \Carbon\Carbon::parse($medicine->expiry_date);
        $daysLeft = now()->diffInDays($expiry, false);
    @endphp

    @if($daysLeft < 0)
        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
            {{ $expiry->format('m/d/Y') }} ❌ Expired
        </span>
    @elseif($daysLeft <= 30)
        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-200 text-red-800 animate-pulse">
            {{ $expiry->format('m/d/Y') }} ⚠️ 1 Month Left
        </span>
    @elseif($daysLeft <= 60)
        <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-200 text-purple-800">
            {{ $expiry->format('m/d/Y') }} ⏳ 2 Months Left
        </span>
    @elseif($daysLeft <= 90)
        <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-200 text-yellow-800">
            {{ $expiry->format('m/d/Y') }} ⚠️ 3 Months Left
        </span>
    @else
        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
            {{ $expiry->format('m/d/Y') }}
        </span>
    @endif
</td>

                            <td class="px-4 py-2 border text-center">
                                @php
                                    $isExpired = \Carbon\Carbon::parse($medicine->expiry_date)->isPast();
                                @endphp
                                @if($isExpired)
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded">Inactive (Expired)</span>
                                @else
                                    @if($medicine->status)
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded">Inactive (Disabled)</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-2 border text-center space-x-2">
    <!-- Edit Button -->
    <a href="{{ route('medicines.edit', $medicine->id) }}" 
       class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 text-white rounded-full shadow hover:bg-yellow-600 transition"
       title="Edit Medicine">
        <!-- Heroicon Pencil -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                     a2 2 0 112.828 2.828L12 20l-4 1 1-4 9.586-9.586z" />
        </svg>
    </a>
<a href="{{ route('medicines.history', $medicine->id) }}" 
   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
   📜 History
</a>

    <!-- Delete Button -->
    <form action="{{ route('medicines.destroy', $medicine->id) }}" method="POST" 
          class="inline-block" onsubmit="return confirm('Delete this medicine?');">
        @csrf @method('DELETE')
        <button type="submit" 
                class="inline-flex items-center justify-center w-8 h-8 bg-red-600 text-white rounded-full shadow hover:bg-red-700 transition"
                title="Delete Medicine">
            <!-- Heroicon Trash -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                         m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    </form>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">
                                No medicines found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $medicines->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- 🔥 Medicine Detail Modal -->
        <div x-show="showModal" 
             x-transition 
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
                <!-- Close -->
                <button @click="showModal = false" 
                        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900">
                    ✖
                </button>

                <!-- Medicine Info -->
                <div class="text-center">
                    <template x-if="medicine.image">
                        <img :src="medicine.image" class="w-24 h-24 object-cover mx-auto rounded mb-3">
                    </template>

                    <h2 class="text-xl font-semibold text-gray-800 text-left" x-text="medicine.name"></h2>
                    <p class="mt-2 text-gray-600 text-left"><b>Category:</b> <span x-text="medicine.category"></span></p>
                    <p class="mt-2 text-gray-600 text-left"><b>Type:</b> <span x-text="medicine.type"></span></p>
                    <p class="text-gray-600 text-left"><b>Stock:</b> <span x-text="medicine.stock"></span></p>
                    <p class="text-gray-600 text-left"><b>Price:</b> $<span x-text="medicine.price"></span></p>
                    <p class="text-gray-600 text-left"><b>Expiry:</b> <span x-text="new Date(medicine.expiry).toLocaleDateString('en-US')"></span></p>
                    <p class="text-gray-600 text-left">
                        <b>Expiry:</b>
                        <span 
                            x-text="(() => {
                                let expiryDate = new Date(medicine.expiry);
                                let today = new Date();
                                let daysLeft = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

                                if (daysLeft < 0) {
                                    return expiryDate.toLocaleDateString('en-US') + ' ❌ Expired';
                                } else if (daysLeft <= 30) {
                                    return expiryDate.toLocaleDateString('en-US') + ' ⚠️ 1 Month Left';
                                } else if (daysLeft <= 60) {
                                    return expiryDate.toLocaleDateString('en-US') + ' ⏳ 2 Months Left';
                                } else if (daysLeft <= 90) {
                                    return expiryDate.toLocaleDateString('en-US') + ' ⚠️ 3 Months Left';
                                } else {
                                    return expiryDate.toLocaleDateString('en-US');
                                }
                            })()"
                            :class="(() => {
                                let expiryDate = new Date(medicine.expiry);
                                let today = new Date();
                                let daysLeft = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

                                if (daysLeft < 0) return 'px-2 py-1 text-xs font-semibold rounded bg-red-200 text-red-800';
                                if (daysLeft <= 30) return 'px-2 py-1 text-xs font-semibold rounded bg-red-200 text-red-800 animate-pulse';
                                if (daysLeft <= 60) return 'px-2 py-1 text-xs font-semibold rounded bg-purple-200 text-purple-800';
                                if (daysLeft <= 90) return 'px-2 py-1 text-xs font-semibold rounded bg-yellow-200 text-yellow-800';
                                return 'px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700';
                            })()"
                        ></span>
                    </p>

                    <p class="text-gray-600 mt-2 text-left"><b>Notes:</b> <span x-text="medicine.notes"></span></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
