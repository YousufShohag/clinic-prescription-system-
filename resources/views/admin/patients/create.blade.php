{{-- resources/views/admin/patients/form.blade.php --}}
<x-app-layout>
<div class="container mx-auto py-8">
  @php
    $isEdit = isset($patient);
    $title  = $isEdit ? 'Edit Patient' : 'Add New Patient';
    $action = $isEdit ? route('patients.update', $patient->id) : route('patients.store');

    // helpers for existing files (avoid calling Storage here)
    $mkUrl = function ($p) {
      if (!$p) return null;
      return preg_match('/^(https?:\/\/|\/)/i', $p) ? $p : '/storage/' . ltrim($p, '/');
    };
    $images = $isEdit && is_array($patient->images ?? null) ? $patient->images : [];
    $docs   = $isEdit && is_array($patient->documents ?? null) ? $patient->documents : [];
    $avatar = count($images) ? $mkUrl($images[0]) : null;

    $statusVal = old('status', $isEdit ? ($patient->status ?? 'active') : 'active');
    $sexVal    = old('sex',    $isEdit ? ($patient->sex ?? '') : '');
  @endphp

  <div class="max-w-6xl mx-auto">
    {{-- header --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-semibold">{{ $title }}</h2>
        <p class="text-sm text-gray-500">Update demographics, doctor assignment, and manage files.</p>
      </div>
      <a href="{{ route('patients.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Patients</a>
    </div>

    {{-- errors --}}
    @if ($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- left column: profile + quick fields --}}
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center gap-4">
              @if ($avatar)
                <img src="{{ $avatar }}" alt="{{ $patient->name ?? 'Patient' }}" class="w-20 h-20 rounded-full object-cover ring-1 ring-gray-200">
              @else
                <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200 text-2xl font-semibold text-gray-600">
                  {{ strtoupper(substr(old('name', $patient->name ?? 'P'),0,1)) }}
                </div>
              @endif
              <div>
                <div class="text-lg font-semibold">{{ old('name', $patient->name ?? '') ?: '—' }}</div>
                @if($isEdit)
                  <div class="text-xs text-gray-500">ID #{{ $patient->id }}</div>
                @endif
              </div>
            </div>

            <div class="mt-6 space-y-5">
              {{-- Status segmented (pure Tailwind with `peer`) --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="inline-flex rounded-md shadow-sm overflow-hidden border border-gray-200">
                  <input id="status-active" type="radio" name="status" value="active" class="peer sr-only" {{ $statusVal==='active' ? 'checked' : '' }}>
                  <label for="status-active"
                         class="px-3 py-1.5 text-sm cursor-pointer bg-white text-gray-700 peer-checked:bg-blue-600 peer-checked:text-white">
                    Active
                  </label>

                  <input id="status-inactive" type="radio" name="status" value="inactive" class="peer sr-only" {{ $statusVal==='inactive' ? 'checked' : '' }}>
                  <label for="status-inactive"
                         class="px-3 py-1.5 text-sm cursor-pointer bg-white text-gray-700 border-l border-gray-200 peer-checked:bg-blue-600 peer-checked:text-white">
                    Inactive
                  </label>
                </div>
              </div>

              {{-- Sex segmented --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                <div class="inline-flex rounded-md shadow-sm overflow-hidden border border-gray-200">
                  @foreach (['male'=>'Male','female'=>'Female','others'=>'Others'] as $val => $label)
                    <input id="sex-{{ $val }}" type="radio" name="sex" value="{{ $val }}" class="peer sr-only" {{ $sexVal===$val ? 'checked' : '' }}>
                    <label for="sex-{{ $val }}"
                           class="px-3 py-1.5 text-sm cursor-pointer bg-white text-gray-700 {{ !$loop->first ? 'border-l border-gray-200' : '' }}
                                  peer-checked:bg-gray-900 peer-checked:text-white">
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </div>

              {{-- Next return date (optional) --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Next Return Date</label>
                <input type="date" name="next_return_date"
                  value="{{ old('next_return_date', $patient->next_return_date ?? '') }}"
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>

              {{-- Quick meta when editing --}}
              @if($isEdit)
              <div class="text-xs text-gray-500 pt-2 border-t">
                <div>Created: {{ optional($patient->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                <div>Updated: {{ optional($patient->updated_at)->format('d/m/Y H:i') ?? '-' }}</div>
              </div>
              @endif
            </div>
          </div>
        </div>

        {{-- right column: full form --}}
        <div class="lg:col-span-2 space-y-6">
          {{-- Identity --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 font-medium mb-1">Patient Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $patient->name ?? '') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Assign Doctor (optional)</label>
                <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                  <option value="">-- No Doctor --</option>
                  @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ (old('doctor_id', $patient->doctor_id ?? '') == $doctor->id) ? 'selected' : '' }}>
                      {{ $doctor->name }} {{ $doctor->specialization ? '('.$doctor->specialization.')' : '' }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Age</label>
                <input type="number" name="age" value="{{ old('age', $patient->age ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $patient->dob ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
               <div>
                <label class="block text-gray-700 font-medium mb-1">Blood Group</label>
                <select name="blood_group" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                  <option value="">-- Select --</option>
                  @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" @selected(old('blood_group', $patient->blood_group ?? '') === $bg)>{{ $bg }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Guardian Name</label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $patient->guardian_name ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $patient->phone ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" placeholder="+8801XXXXXXXXX">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $patient->email ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400" placeholder="name@example.com">
              </div>
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address', $patient->address ?? '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400"
                          placeholder="Any relevant clinical notes, history, etc.">{{ old('notes', $patient->notes ?? '') }}</textarea>
              </div>
            </div>
          </div>

          {{-- Images --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold">Images</h3>
              <p class="text-xs text-gray-500">JPG, PNG, WEBP, GIF — you can select multiple</p>
            </div>

            {{-- Existing images (edit mode) --}}
            @if($isEdit && count($images))
              <div class="mb-4">
                <div class="text-sm text-gray-600 mb-2">Existing</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                  @foreach($images as $idx => $img)
                    @php $url = $mkUrl($img); @endphp
                    <div class="border rounded overflow-hidden">
                      <img src="{{ $url }}" alt="img-{{ $idx }}" class="w-full h-32 object-cover">
                      <div class="flex items-center justify-between px-2 py-1 text-xs">
                        <span class="truncate" title="{{ $img }}">{{ basename($img) }}</span>
                        <label class="inline-flex items-center gap-1">
                          <input type="checkbox" name="remove_images[]" value="{{ $img }}" class="rounded">
                          <span>Remove</span>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- Upload new images --}}
            <div id="imgDrop" class="relative border-2 border-dashed rounded p-6 text-center hover:bg-gray-50">
              <input id="images" type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              <div class="text-gray-600">
                <div class="font-medium">Drag & drop or click to upload</div>
                <div class="text-xs">Files will be uploaded on submit</div>
              </div>
            </div>
            <div id="imgPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-3 hidden"></div>
          </div>

          {{-- Documents --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold">Documents</h3>
              <p class="text-xs text-gray-500">PDF, DOCX, images, etc. — multiple supported</p>
            </div>

            {{-- Existing documents (edit mode) --}}
            @if($isEdit && count($docs))
              <div class="mb-4">
                <div class="text-sm text-gray-600 mb-2">Existing</div>
                <div class="space-y-2">
                  @foreach($docs as $doc)
                    @php $url = $mkUrl($doc); @endphp
                    <div class="flex items-center justify-between border rounded px-3 py-2">
                      <a href="{{ $url }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline truncate" title="{{ $doc }}">
                        {{ basename($doc) }}
                      </a>
                      <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remove_documents[]" value="{{ $doc }}" class="rounded">
                        <span>Remove</span>
                      </label>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

            {{-- Upload new documents --}}
            <div id="docDrop" class="relative border-2 border-dashed rounded p-6 text-center hover:bg-gray-50">
              <input id="documents" type="file" name="documents[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              <div class="text-gray-600">
                <div class="font-medium">Drag & drop or click to upload</div>
                <div class="text-xs">Files will be uploaded on submit</div>
              </div>
            </div>
            <div id="docPreview" class="mt-3 space-y-2 hidden"></div>
          </div>

          {{-- actions --}}
          <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
            <div class="text-xs text-gray-500">Make sure everything looks right before saving.</div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
              {{ $isEdit ? 'Update Patient' : 'Add Patient' }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- vanilla JS for live previews & drag states (no frameworks) --}}
<script>
(function() {
  function byId(id){ return document.getElementById(id); }
  function readableSize(bytes){
    if(bytes==null) return '';
    const units=['B','KB','MB','GB']; let i=0;
    while(bytes>=1024 && i<units.length-1){ bytes/=1024; i++; }
    return bytes.toFixed(bytes>=1024?1:0)+' '+units[i];
  }

  // images preview
  const imgInput = byId('images');
  const imgDrop  = byId('imgDrop');
  const imgPrev  = byId('imgPreview');
  if (imgInput && imgDrop && imgPrev) {
    const renderImgs = (files) => {
      if (!files || !files.length) { imgPrev.classList.add('hidden'); imgPrev.innerHTML=''; return; }
      const frag = document.createDocumentFragment();
      Array.from(files).forEach((f) => {
        const url = URL.createObjectURL(f);
        const card = document.createElement('div');
        card.className = 'border rounded overflow-hidden';
        card.innerHTML = `
          <img src="${url}" class="w-full h-32 object-cover" alt="${f.name}">
          <div class="px-2 py-1 text-xs flex justify-between">
            <span class="truncate" title="${f.name}">${f.name}</span>
            <span class="text-gray-500">${readableSize(f.size)}</span>
          </div>`;
        frag.appendChild(card);
      });
      imgPrev.innerHTML = ''; imgPrev.appendChild(frag); imgPrev.classList.remove('hidden');
    };
    imgInput.addEventListener('change', (e)=> renderImgs(e.target.files));
    ;['dragenter','dragover'].forEach(ev => imgDrop.addEventListener(ev, ()=> imgDrop.classList.add('ring-2','ring-blue-400')));
    ;['dragleave','drop'].forEach(ev => imgDrop.addEventListener(ev, ()=> imgDrop.classList.remove('ring-2','ring-blue-400')));
  }

  // documents preview
  const docInput = byId('documents');
  const docDrop  = byId('docDrop');
  const docPrev  = byId('docPreview');
  if (docInput && docDrop && docPrev) {
    const icon = (name) => {
      const ext = (name || '').toLowerCase().split('.').pop();
      if (['png','jpg','jpeg','webp','gif','svg'].includes(ext)) return '🖼️';
      if (ext === 'pdf') return '📄';
      if (['doc','docx'].includes(ext)) return '📝';
      if (['xls','xlsx','csv'].includes(ext)) return '📊';
      if (['zip','rar','7z'].includes(ext)) return '🗜️';
      return '📁';
    };
    const renderDocs = (files) => {
      if (!files || !files.length) { docPrev.classList.add('hidden'); docPrev.innerHTML=''; return; }
      const frag = document.createDocumentFragment();
      Array.from(files).forEach((f) => {
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between border rounded px-3 py-2';
        row.innerHTML = `
          <div class="flex items-center gap-2 truncate">
            <span>${icon(f.name)}</span>
            <span class="truncate" title="${f.name}">${f.name}</span>
          </div>
          <span class="text-xs text-gray-500">${readableSize(f.size)}</span>`;
        frag.appendChild(row);
      });
      docPrev.innerHTML = ''; docPrev.appendChild(frag); docPrev.classList.remove('hidden');
    };
    docInput.addEventListener('change', (e)=> renderDocs(e.target.files));
    ;['dragenter','dragover'].forEach(ev => docDrop.addEventListener(ev, ()=> docDrop.classList.add('ring-2','ring-blue-400')));
    ;['dragleave','drop'].forEach(ev => docDrop.addEventListener(ev, ()=> docDrop.classList.remove('ring-2','ring-blue-400')));
  }
})();
</script>
</x-app-layout>
