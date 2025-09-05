{{-- resources/views/patients/edit.blade.php --}}
<x-app-layout>
<div class="container mx-auto py-8">
  <div class="max-w-6xl mx-auto">
    {{-- header --}}
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-semibold">Edit Patient</h2>
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

    <form action="{{ route('patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PUT')

      @php
        // helpers to show existing file urls without needing Storage facade here
        $mkUrl = function ($p) {
          if (!$p) return null;
          return preg_match('/^(https?:\/\/|\/)/i', $p) ? $p : '/storage/' . ltrim($p, '/');
        };
        $images = is_array($patient->images) ? $patient->images : [];
        $docs   = is_array($patient->documents) ? $patient->documents : [];
        $avatar = count($images) ? $mkUrl($images[0]) : null;
      @endphp

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- left column: profile summary & status --}}
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center gap-4">
              @if ($avatar)
                <img src="{{ $avatar }}" alt="{{ $patient->name }}" class="w-20 h-20 rounded-full object-cover ring-1 ring-gray-200">
              @else
                <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200 text-2xl font-semibold text-gray-600">
                  {{ strtoupper(substr($patient->name,0,1)) }}
                </div>
              @endif
              <div>
                <div class="text-lg font-semibold">{{ $patient->name }}</div>
                <div class="text-sm text-gray-500">#{{ $patient->id }}</div>
              </div>
            </div>

            <div class="mt-6 space-y-4">
              {{-- status segmented --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="inline-flex rounded-md shadow-sm overflow-hidden border border-gray-200">
                  @php $status = old('status', $patient->status ?? 'active'); @endphp
                  <label class="px-3 py-1.5 cursor-pointer text-sm {{ $status==='active' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
                    <input type="radio" name="status" value="active" class="sr-only" {{ $status==='active' ? 'checked' : '' }}>
                    Active
                  </label>
                  <label class="px-3 py-1.5 cursor-pointer text-sm border-l border-gray-200 {{ $status==='inactive' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
                    <input type="radio" name="status" value="inactive" class="sr-only" {{ $status==='inactive' ? 'checked' : '' }}>
                    Inactive
                  </label>
                </div>
              </div>

              {{-- sex segmented --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                @php $sex = old('sex', $patient->sex ?? ''); @endphp
                <div class="inline-flex rounded-md shadow-sm overflow-hidden border border-gray-200">
                  @foreach (['male'=>'Male','female'=>'Female','others'=>'Others'] as $val => $label)
                    <label class="px-3 py-1.5 cursor-pointer text-sm {{ $sex===$val ? 'bg-gray-900 text-white' : 'bg-white text-gray-700' }} {{ !$loop->first ? 'border-l border-gray-200' : '' }}">
                      <input type="radio" name="sex" value="{{ $val }}" class="sr-only" {{ $sex===$val ? 'checked' : '' }}>
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </div>

              {{-- return date --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Next Return Date</label>
                <input type="date" name="next_return_date"
                  value="{{ old('next_return_date', $patient->next_return_date) }}"
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>

              {{-- quick meta --}}
              <div class="text-xs text-gray-500 pt-2 border-t">
                <div>Created: {{ optional($patient->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                <div>Updated: {{ optional($patient->updated_at)->format('d/m/Y H:i') ?? '-' }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- right column: form fields & files --}}
        <div class="lg:col-span-2 space-y-6">
          {{-- identity --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 font-medium mb-1">Patient Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $patient->name) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Assign Doctor (optional)</label>
                <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                  <option value="">-- No Doctor --</option>
                  @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ (old('doctor_id', $patient->doctor_id) == $doctor->id) ? 'selected' : '' }}>
                      {{ $doctor->name }} {{ $doctor->specialization ? '('.$doctor->specialization.')' : '' }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Age</label>
                <input type="number" name="age" value="{{ old('age', $patient->age) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob', $patient->dob) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
               <div>
                <label class="block text-gray-700 font-medium mb-1">Blood Group</label>
                <select name="blood_group" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
                  <option value="">-- Select --</option>
                  @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" @selected(old('blood_group', $patient->blood_group) === $bg)>{{ $bg }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Guardian Name</label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $patient->guardian_name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $patient->email) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-1">Address</label>
                <input type="text" name="address" value="{{ old('address', $patient->address) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">
              </div>
              <div class="md:col-span-2">
                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400">{{ old('notes', $patient->notes) }}</textarea>
              </div>
            </div>
          </div>

          {{-- files: images --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold">Images</h3>
              <p class="text-xs text-gray-500">JPG, PNG, WEBP, GIF</p>
            </div>

            {{-- existing images --}}
            @if(count($images))
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

            {{-- upload images --}}
            <div id="imgDrop" class="relative border-2 border-dashed rounded p-6 text-center hover:bg-gray-50">
              <input id="images" type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              <div class="text-gray-600">
                <div class="font-medium">Drag & drop or click to upload</div>
                <div class="text-xs">You can select multiple files</div>
              </div>
            </div>
            <div id="imgPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mt-3 hidden"></div>
          </div>

          {{-- files: documents --}}
          <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold">Documents</h3>
              <p class="text-xs text-gray-500">PDF, DOCX, images, etc.</p>
            </div>

            {{-- existing docs --}}
            @if(count($docs))
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

            {{-- upload docs --}}
            <div id="docDrop" class="relative border-2 border-dashed rounded p-6 text-center hover:bg-gray-50">
              <input id="documents" type="file" name="documents[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
              <div class="text-gray-600">
                <div class="font-medium">Drag & drop or click to upload</div>
                <div class="text-xs">Multiple files supported</div>
              </div>
            </div>
            <div id="docPreview" class="mt-3 space-y-2 hidden"></div>
          </div>

          {{-- actions --}}
          <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
            <div class="text-xs text-gray-500">Changes are saved to the patient record.</div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
              Update Patient
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- vanilla JS for previews & drag states --}}
<script>
(function() {
  function byId(id){ return document.getElementById(id); }
  function readableSize(bytes){
    if(!bytes && bytes !== 0) return '';
    const units = ['B','KB','MB','GB']; let i=0; while(bytes>=1024 && i<units.length-1){ bytes/=1024; i++; }
    return bytes.toFixed(bytes>=1024?1:0)+' '+units[i];
  }

  // image uploader
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

  // documents uploader
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  setupSegmented('input[name="status"]', {
    active:  ['bg-blue-600','text-white'],
    idle:    ['bg-white','text-gray-700'],
  });

  setupSegmented('input[name="sex"]', {
    active:  ['bg-gray-900','text-white'],
    idle:    ['bg-white','text-gray-700'],
  });

  function setupSegmented(selector, cls) {
    const inputs = Array.from(document.querySelectorAll(selector));
    if (!inputs.length) return;

    const ACTIVE = cls.active, IDLE = cls.idle;
    const allClasses = [...new Set([...ACTIVE, ...IDLE])];

    function refresh() {
      inputs.forEach(input => {
        const label = input.closest('label');
        if (!label) return;
        label.classList.remove(...allClasses);
        label.classList.add(...(input.checked ? ACTIVE : IDLE));
      });
    }

    // init + events
    refresh();
    inputs.forEach(input => {
      input.addEventListener('change', refresh);
      // also click on label area (since input is inside label)
      const label = input.closest('label');
      if (label) label.addEventListener('click', () => {
        // defer until browser marks the radio checked
        setTimeout(refresh, 0);
      });
    });
  }
});
</script>

</x-app-layout>
