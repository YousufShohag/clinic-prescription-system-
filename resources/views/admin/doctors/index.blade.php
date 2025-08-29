{{-- resources/views/doctors/index.blade.php --}}
<x-app-layout>
    <div class="max-w-7xl mx-auto p-6"
         x-data="{ openWeather:false, showModal:false, doctor:{} }">

        {{-- 🌤️ Floating Weather Widget (same as medicine) --}}
        <div class="fixed bottom-4 right-6 z-50">
            <button x-show="!openWeather"
                    @click="openWeather = true"
                    x-transition
                    class="flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2 rounded-full shadow-lg hover:bg-indigo-700">
                <span>🌤️ Weather</span>
            </button>

            <div x-show="openWeather"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-400"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-2"
                 class="relative bg-gradient-to-r from-blue-500 to-indigo-600 text-white w-80 rounded-lg shadow-2xl p-6">
                <button @click="openWeather = false"
                        class="absolute top-3 right-3 text-white bg-white/20 hover:bg-white/30 rounded-full p-1.5">
                    ✖
                </button>
                <h2 class="text-lg font-semibold mb-2">🌤️ Today's Weather</h2>
                <p class="text-sm mb-4">Chattogram, Bangladesh</p>
                <div class="flex items-center space-x-4">
                    <div class="text-5xl">☁️</div>
                    <div>
                        <p class="text-lg font-medium">Cloudy, 80°F (27°C)</p>
                        <p class="text-xs text-blue-100">Humidity: 78% | Wind: 12 km/h</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 👨‍⚕️ Doctors Table --}}
        <div class="bg-white shadow rounded-lg p-6 mt-5">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">Doctors</h3>
                <a href="{{ route('doctors.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + Add Doctor
                </a>
            </div>

            {{-- 🔍 Filter Section (server-side, like medicines) --}}
            @php
                // Compute specializations from the current page if controller didn't pass it
                $specOptions = isset($specializations) ? collect($specializations)
                    : $doctors->pluck('specialization')->filter()->unique()->values();
            @endphp
            <form method="GET" action="{{ route('doctors.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
                {{-- Search by name/phone/email/chamber --}}
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name/phone/email/chamber…"
                       class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                {{-- Specialization --}}
                <select name="specialization"
                        class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Specializations</option>
                    @foreach($specOptions as $s)
                        <option value="{{ $s }}" {{ request('specialization') == $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>

                {{-- Fee min --}}
                <input type="number" name="fee_min" value="{{ request('fee_min') }}"
                       placeholder="Min Fee"
                       class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                {{-- Fee max --}}
                <input type="number" name="fee_max" value="{{ request('fee_max') }}"
                       placeholder="Max Fee"
                       class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                {{-- Availability (simple text contains) --}}
                <input type="text" name="available"
                       value="{{ request('available') }}"
                       placeholder="Available e.g. Sun-Thu 6-9 PM"
                       class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                {{-- Buttons --}}
                <div class="col-span-1 sm:col-span-5 flex justify-end space-x-3">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Apply
                    </button>
                    <a href="{{ route('doctors.index') }}"
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
                        <th class="px-4 py-2 border">Specialization</th>
                        <th class="px-4 py-2 border">Phone</th>
                        <th class="px-4 py-2 border">Fee</th>
                        <th class="px-4 py-2 border">Available</th>
                        <th class="px-4 py-2 border">Notes</th>
                        <th class="px-4 py-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doc)
                        @php
                            $img = $doc->image;
                            if ($img && !str_contains($img, 'doctors/')) { $img = 'doctors/'.$img; }
                            $hasImg = $img && \Illuminate\Support\Facades\Storage::disk('public')->exists($img);
                            $imgUrl = $hasImg
                                ? \Illuminate\Support\Facades\Storage::url($img)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($doc->name) . '&background=E5E7EB&color=374151&size=128&rounded=true';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border text-center">
                                <img src="{{ $imgUrl }}" class="w-12 h-12 object-cover rounded-full mx-auto ring-2 ring-gray-100">
                            </td>

                            {{-- Clickable Name → opens details modal (like medicines) --}}
                            <td class="px-4 py-2 border">
                                <button
                                    @click="
                                        doctor = {
                                            id:'{{ $doc->id }}',
                                            name:`{{ $doc->name }}`,
                                            specialization:`{{ $doc->specialization ?? '' }}`,
                                            degree:`{{ $doc->degree ?? '' }}`,
                                            chamber:`{{ $doc->chamber ?? '' }}`,
                                            email:`{{ $doc->email }}`,
                                            phone:`{{ $doc->phone ?? '' }}`,
                                            fee:`{{ $doc->consultation_fee ?? '' }}`,
                                            available:`{{ $doc->available_time ?? '' }}`,
                                            notes:`{{ $doc->notes ?? '' }}`,
                                            image:'{{ $hasImg ? \Illuminate\Support\Facades\Storage::url($img) : '' }}',
                                            fallback:'{{ $imgUrl }}',
                                        };
                                        showModal = true;
                                    "
                                    class="text-indigo-600 hover:underline font-semibold">
                                    {{ $doc->name }}
                                </button>
                            </td>

                            <td class="px-4 py-2 border">{{ $doc->specialization ?? '—' }}</td>
                            <td class="px-4 py-2 border">{{ $doc->phone ?? '—' }}</td>
                            <td class="px-4 py-2 border">
                                {{ $doc->consultation_fee ? '৳ '.number_format($doc->consultation_fee) : '—' }}
                            </td>
                            <td class="px-4 py-2 border">
                                @if($doc->available_time)
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-emerald-50 text-emerald-700">
                                        {{ $doc->available_time }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="block max-w-[220px] truncate" title="{{ $doc->notes }}">{{ $doc->notes ?? '—' }}</span>
                            </td>

                            <td class="px-4 py-2 border text-center space-x-2">
                                {{-- Edit --}}
                                <a href="{{ route('doctors.edit', $doc->id) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 bg-yellow-500 text-white rounded-full shadow hover:bg-yellow-600 transition"
                                   title="Edit Doctor">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                                                 a2 2 0 112.828 2.828L12 20l-4 1 1-4 9.586-9.586z"/>
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('doctors.destroy', $doc->id) }}" method="POST"
                                      class="inline-block" onsubmit="return confirm('Delete this doctor?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-600 text-white rounded-full shadow hover:bg-red-700 transition"
                                            title="Delete Doctor">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                     m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">
                                No doctors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $doctors->appends(request()->query())->links() }}
            </div>
        </div>

        {{-- 🪟 Doctor Detail Modal (matches style of medicine modal) --}}
        <div x-show="showModal"
             x-transition
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
             style="display: none;">
            <div class="bg-white rounded-lg shadow-lg w-[28rem] p-6 relative">
                <button @click="showModal = false"
                        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900">
                    ✖
                </button>

                <div class="text-center">
                    <template x-if="doctor.image || doctor.fallback">
                        <img :src="doctor.image || doctor.fallback" class="w-24 h-24 object-cover mx-auto rounded-full mb-3 ring-2 ring-gray-100">
                    </template>

                    <h2 class="text-xl font-semibold text-gray-800 text-left" x-text="doctor.name"></h2>
                    <p class="mt-2 text-gray-600 text-left"><b>Specialization:</b> <span x-text="doctor.specialization || '—'"></span></p>
                    <p class="mt-1 text-gray-600 text-left"><b>Degree:</b> <span x-text="doctor.degree || '—'"></span></p>
                    <p class="mt-1 text-gray-600 text-left"><b>Chamber:</b> <span x-text="doctor.chamber || '—'"></span></p>
                    <p class="mt-1 text-gray-600 text-left"><b>Email:</b>
                        <a :href="'mailto:'+doctor.email" class="text-indigo-600 hover:underline" x-text="doctor.email"></a>
                    </p>
                    <p class="mt-1 text-gray-600 text-left"><b>Phone:</b>
                        <a :href="'tel:'+doctor.phone" class="hover:underline" x-text="doctor.phone || '—'"></a>
                    </p>
                    <p class="mt-1 text-gray-600 text-left"><b>Fee:</b> <span x-text="doctor.fee ? ('৳ ' + Number(doctor.fee).toLocaleString()) : '—'"></span></p>
                    <p class="mt-1 text-gray-600 text-left"><b>Available:</b>
                        <span class="px-2 py-1 text-xs font-semibold rounded"
                              :class="doctor.available ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                              x-text="doctor.available || '—'"></span>
                    </p>
                    <p class="text-gray-600 mt-3 text-left"><b>Notes:</b> <span x-text="doctor.notes || '—'"></span></p>

                    <div class="mt-5 flex justify-end gap-2">
                        <a :href="`{{ url('/doctors') }}/${doctor.id}/edit`"
                           class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            Edit
                        </a>
                        <form :action="`{{ url('/doctors') }}/${doctor.id}`" method="POST"
                              onsubmit="return confirm('Delete this doctor?')" >
                            @csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                        <button @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- /x-data --}}
</x-app-layout>
