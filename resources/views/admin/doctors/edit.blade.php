{{-- resources/views/doctors/edit.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Doctor</h1>
            <a href="{{ route('doctors.index') }}"
               class="px-4 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">
                ← Back
            </a>
        </div>

        {{-- Flash / Errors --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                <div class="font-semibold mb-1">Please fix the following:</div>
                <ul class="list-disc list-inside text-sm space-y-0.5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            {{-- ===== UPDATE FORM (no other form inside!) ===== --}}
            <form id="doctor-update-form"
                  method="POST"
                  action="{{ route('doctors.update', $doctor->id) }}"
                  enctype="multipart/form-data"
                  x-data="{ preview: null }">
                @csrf
                @method('PUT')

                {{-- Photo --}}
                @php
                    $img = $doctor->image;
                    if ($img && !str_contains($img, 'doctors/')) { $img = 'doctors/'.$img; }
                    $hasImg = $img && \Illuminate\Support\Facades\Storage::disk('public')->exists($img);
                    $imgUrl = $hasImg
                        ? \Illuminate\Support\Facades\Storage::url($img)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($doctor->name) . '&background=E5E7EB&color=374151&size=256&rounded=true';
                @endphp

                <div class="flex items-start gap-4 mb-6">
                    <img :src="preview || '{{ $imgUrl }}'"
                         class="h-20 w-20 rounded-full object-cover ring-2 ring-gray-100"
                         alt="Doctor Photo">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change Photo</label>
                        <input type="file" name="image" accept="image/*"
                               class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                               @change="
                                   const [f] = $event.target.files;
                                   if (!f) { preview = null; return; }
                                   const r = new FileReader();
                                   r.onload = e => preview = e.target.result;
                                   r.readAsDataURL(f);
                               ">
                        <p class="text-xs text-gray-500 mt-1">JPEG/PNG up to ~2MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               value="{{ old('name', $doctor->name) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Specialization</label>
                        <input type="text" name="specialization"
                               value="{{ old('specialization', $doctor->specialization) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Degree</label>
                        <input type="text" name="degree"
                               value="{{ old('degree', $doctor->degree) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Chamber</label>
                        <input type="text" name="chamber"
                               value="{{ old('chamber', $doctor->chamber) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required
                               value="{{ old('email', $doctor->email) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $doctor->phone) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fee (৳)</label>
                        <input type="number" name="consultation_fee" min="0" step="1"
                               value="{{ old('consultation_fee', $doctor->consultation_fee) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Available Time</label>
                        <input type="text" name="available_time"
                               placeholder="e.g. Sun–Thu 6–9 PM"
                               value="{{ old('available_time', $doctor->available_time) }}"
                               class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="mt-1 w-full rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">{{ old('notes', $doctor->notes) }}</textarea>
                    </div>
                </div>
            </form>

            {{-- ===== DELETE FORM (separate, not nested) ===== --}}
            <form id="doctor-delete-form"
                  action="{{ route('doctors.destroy', $doctor->id) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this doctor?')">
                @csrf
                @method('DELETE')
            </form>

            {{-- Footer actions using the form attribute to target the right form --}}
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" form="doctor-delete-form"
                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    Delete
                </button>

                <div class="flex gap-2">
                    <a href="{{ route('doctors.index') }}"
                       class="px-4 py-2 rounded border hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" form="doctor-update-form"
                            class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-500 mt-4">
            Tip: Run <code>php artisan storage:link</code> to serve files from the <code>public</code> disk.
        </p>
    </div>
</x-app-layout>
