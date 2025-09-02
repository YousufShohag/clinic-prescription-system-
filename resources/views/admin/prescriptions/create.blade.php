{{-- resources/views/prescriptions/create.blade.php --}}
<x-app-layout>
  {{-- Select2 assets (load once) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

  <div class="w-full min-h-screen bg-white p-6 md:p-8">
    <h2 class="text-3xl font-semibold mb-6">New Prescription</h2>

    @if($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('prescriptions.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6" enctype="multipart/form-data">
      @csrf

      {{-- LEFT --}}
      <aside class="xl:col-span-3 space-y-6">
        
        {{-- ================= Clinical Findings (collapsible) ================= --}}
        <section id="cf-card" class="border rounded-lg">
          {{-- clickable header --}}
          <button type="button"
                  id="cf-toggle"
                  class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
                  aria-controls="cf-body"
                  aria-expanded="false">
            <span class="text-lg font-semibold">Clinical Findings</span>
            <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
            </svg>
          </button>

          {{-- contents (start hidden) --}}
          <div id="cf-body" class="p-5 border-t hidden">
            {{-- O/E with bullet helpers --}}
            

            {{-- Vitals --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-sm text-gray-700">BP</label>
                <input type="text" name="bp" value="{{ old('bp') }}" placeholder="120/80" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Pulse (bpm)</label>
                <input type="number" step="1" min="0" name="pulse" value="{{ old('pulse') }}" placeholder="78" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature_c" value="{{ old('temperature_c') }}" placeholder="37.0" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">SpO₂ (%)</label>
                <input type="number" step="1" min="0" max="100" name="spo2" value="{{ old('spo2') }}" placeholder="98" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Respiratory Rate (/min)</label>
                <input type="number" step="1" min="0" name="respiratory_rate" value="{{ old('respiratory_rate') }}" placeholder="16" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Weight (kg)</label>
                <input type="number" step="0.1" min="0" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="70.5" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Height (cm)</label>
                <input type="number" step="0.1" min="0" id="height_cm" name="height_cm" value="{{ old('height_cm') }}" placeholder="170" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">BMI (kg/m²)</label>
                <input type="text" id="bmi" name="bmi" value="{{ old('bmi') }}" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
              </div>
            </div>
          </div>
          
        </section>
        <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-medium text-gray-700">O/E (On Examination)</label>
              <div class="flex items-center gap-2">
                <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                        data-bullets-toggle="#oe">• Bullets: OFF</button>
                <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                        data-bullets-clear="#oe" title="Remove leading • from each line">Clear bullets</button>
              </div>
            </div>
            <textarea id="oe" name="oe" rows="4" class="w-full border rounded px-3 py-2 mb-1" data-bullets
                      placeholder="General appearance, system findings, etc.">{{ old('oe') }}</textarea>
            <p class="text-xs text-gray-500">Tip: with bullets ON, press Enter for a new “•” item.</p>
      </aside>

      {{-- MIDDLE --}}
      <section class="xl:col-span-6 space-y-6">
        {{-- Doctor & Patient --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Doctor <span class="text-red-500">*</span></label>
            @php
              $preselectDoctorId = (string) old(
                'doctor_id',
                $prescription->doctor_id ?? ($defaultDoctorId ?? (auth()->user()->doctor_id ?? optional($doctors->first())->id))
              );
            @endphp
            <select name="doctor_id" id="doctor_id" required class="w-full border rounded px-3 py-2">
              <option value="" {{ $preselectDoctorId ? '' : 'selected' }} disabled>-- Select Doctor --</option>
              @foreach($doctors as $doc)
                <option value="{{ $doc->id }}" @selected((string)$doc->id === $preselectDoctorId)>
                  {{ $doc->name }} {{ $doc->specialization ? "({$doc->specialization})" : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Patient</label>
            {{-- AJAX Select2 for patient search (with +New) --}}
            <select
              name="patient_id"
              id="patient_select"
              class="w-full border rounded px-3 py-2"
              data-search-url="{{ route('patients.search') }}"
              data-history-url-template="{{ route('patients.history', ['patient' => '__ID__']) }}"
            >
              <option value="">-- Select existing patient --</option>
              <option value="__new">+ Add new patient</option>
            </select>
            <div id="patient_age_display" class="mt-2 text-sm text-gray-600"></div>
          </div>
        </div>

        {{-- New patient --}}
        <div id="new_patient_block" class="hidden bg-gray-50 border rounded p-5">
          <h3 class="text-md font-semibold mb-4">New Patient Details</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="new_patient[name]" value="{{ old('new_patient.name') }}" placeholder="Patient name" class="border rounded px-3 py-2">
            <input type="number" name="new_patient[age]" value="{{ old('new_patient.age') }}" placeholder="Age" class="border rounded px-3 py-2">
            <select name="new_patient[sex]" class="border rounded px-3 py-2">
              <option value="">Sex</option>
              <option value="male" @selected(old('new_patient.sex')==='male')>Male</option>
              <option value="female" @selected(old('new_patient.sex')==='female')>Female</option>
              <option value="others" @selected(old('new_patient.sex')==='others')>Others</option>
            </select>
            <input type="text" name="new_patient[phone]" value="{{ old('new_patient.phone') }}" placeholder="Phone" class="border rounded px-3 py-2">
            <input type="email" name="new_patient[email]" value="{{ old('new_patient.email') }}" placeholder="Email" class="border rounded px-3 py-2">
            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">Images</label>
                <input type="file" name="new_patient[images][]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Documents</label>
                <input type="file" name="new_patient[documents][]" multiple class="w-full border rounded px-3 py-2">
              </div>
            </div>
            <textarea name="new_patient[notes]" placeholder="Notes (optional)" class="md:col-span-3 border rounded px-3 py-2">{{ old('new_patient.notes') }}</textarea>
          </div>
        </div>

        {{-- Problem (with bullets) --}}
        <div class="flex items-center justify-between mt-6 mb-2">
          <label class="block text-sm font-medium text-gray-700">Problem Description (brief)</label>
          <div class="flex items-center gap-2">
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                    data-bullets-toggle="#problem_description">• Bullets: OFF</button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                    data-bullets-clear="#problem_description">Clear bullets</button>
          </div>
        </div>
        <textarea id="problem_description" name="problem_description" rows="3"
                  class="w-full border rounded px-3 py-2" data-bullets>{{ old('problem_description') }}</textarea>

        {{-- Medicines (Select2 AJAX) --}}
        <div class="space-y-3">
          <div class="flex flex-col md:flex-row md:items-center gap-2">
            <h3 class="text-xl font-semibold">Medicines</h3>
            <div class="flex-1"></div>
            <select id="medicine_picker" multiple class="w-full md:w-96"></select>
            <button type="button" id="medicine_clear" class="px-3 py-2 border rounded">Clear</button>
          </div>

          <pre id="med_debug" class="text-xs text-gray-500 bg-gray-50 p-2 rounded border"></pre>

          <div id="medicine_selected_wrap" class="hidden">
            <div class="text-sm text-gray-600 mb-1">Selected medicines</div>
            <div id="medicine_selected" class="space-y-2"></div>
          </div>
        </div>

        {{-- Tests (Select2 AJAX multi) --}}
        <div class="space-y-3">
          <div class="flex flex-col md:flex-row md:items-center gap-2">
            <h3 class="text-xl font-semibold">Tests</h3>
            <div class="flex-1"></div>
            <select id="test_picker" multiple class="w-full md:w-96"></select>
            <button type="button" id="test_clear" class="px-3 py-2 border rounded">Clear</button>
          </div>

          <div id="test_selected_wrap" class="hidden">
            <div class="text-sm text-gray-600 mb-1">Selected tests</div>
            <div id="test_selected" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
          </div>
        </div>

        {{-- Advice + Submit --}}
        <div class="flex items-center justify-between mt-6 mb-2">
          <label class="block text-sm font-medium text-gray-700">Doctor Advice</label>
          <div class="flex items-center gap-2">
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                    data-bullets-toggle="#doctor_advice">• Bullets: OFF</button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                    data-bullets-clear="#doctor_advice">Clear bullets</button>
          </div>
        </div>
        <textarea id="doctor_advice" name="doctor_advice" rows="3"
                  class="w-full border rounded px-3 py-2" data-bullets>{{ old('doctor_advice') }}</textarea>

        {{-- Return Date --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Return Date</label>
          <input type="date" name="return_date" value="{{ old('return_date', isset($prescription)? optional($prescription->return_date)->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500">The patient should revisit on this date.</p>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
            Create Prescription
          </button>
        </div>
      </section>

      {{-- RIGHT (optional area) --}}
      <aside class="xl:col-span-3 space-y-6">
        {{-- Patient history panel (loads after a patient is selected) --}}
        <section id="prev_rx_panel" class="hidden border rounded-lg p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">
              Previous Prescriptions<br>
              <span id="prev_rx_patient_name" class="text-sm font-normal text-gray-600"></span>
            </h3>
            <span id="prev_rx_count" class="text-sm text-gray-500"></span>
          </div>

          <div id="prev_rx_loading" class="text-sm text-gray-500">Loading...</div>
          <div id="prev_rx_empty" class="hidden text-sm text-gray-500">No previous prescriptions.</div>
          <div id="prev_rx_error" class="hidden text-sm text-red-600">Could not load prescriptions.</div>

          <div id="prev_rx_list" class="hidden">
            <ul id="prev_rx_ul" class="space-y-2"></ul>
          </div>
        </section>
      </aside>
    </form>
  </div>

  {{-- ===== Small helpers (BMI + New Patient toggle initial) ===== --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const patientSelect   = document.getElementById('patient_select');
      const newPatientBlock = document.getElementById('new_patient_block');

      function toggleNewPatient(){
        if (!patientSelect) return;
        if (patientSelect.value === '__new') newPatientBlock.classList.remove('hidden');
        else newPatientBlock.classList.add('hidden');
      }
      patientSelect?.addEventListener('change', toggleNewPatient);

      const weightEl = document.getElementById('weight_kg');
      const heightEl = document.getElementById('height_cm');
      const bmiEl    = document.getElementById('bmi');
      function calcBMI(){
        const w = parseFloat(weightEl?.value || '');
        const h = parseFloat(heightEl?.value || '');
        if (!isFinite(w) || !isFinite(h) || h <= 0) { if (bmiEl) bmiEl.value=''; return; }
        const hm = h/100; const bmi = w/(hm*hm); bmiEl.value = bmi.toFixed(1);
      }
      weightEl?.addEventListener('input', calcBMI);
      heightEl?.addEventListener('input', calcBMI);
      calcBMI();
    });
  </script>

  {{-- ===== Select2: Patient picker (AJAX) ===== --}}
  <script>
  $(function () {
    const $patient  = $('#patient_select');
    const newBlock  = document.getElementById('new_patient_block');
    const $ageInfo  = $('#patient_age_display');

    function computeAgeFromDob(dobStr) {
      if (!dobStr) return null;
      const d = new Date(dobStr);
      if (isNaN(d)) return null;
      const today = new Date();
      let age = today.getFullYear() - d.getFullYear();
      const m = today.getMonth() - d.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
      return age >= 0 ? age : null;
    }

    $patient.select2({
      placeholder: 'Search patients…',
      width: 'resolve',
      allowClear: true,
      minimumInputLength: 1,
      dropdownParent: $patient.parent(),
      ajax: {
        url: $patient.data('search-url') || "{{ route('patients.search') }}",
        dataType: 'json',
        delay: 200,
        data: params => ({ term: params.term, page: params.page || 1 }),
        processResults: (data, params) => {
          params.page = params.page || 1;
          const base = [
            { id: '', text: '-- Select existing patient --' },
            { id: '__new', text: '+ Add new patient' }
          ];
          return {
            results: base.concat((data?.results || [])),
            pagination: { more: !!data?.pagination?.more }
          };
        }
      },

      // Row in dropdown
      templateResult: item => {
        if (!item.id || item.id === '__new') return item.text;
        const age = item.age ?? computeAgeFromDob(item.dob);
        const idBadge = item.id ? `#${item.id}` : '';
        const meta = [
          idBadge,
          (age != null ? `${age}y` : ''),
          (item.sex ? (item.sex[0].toUpperCase() + item.sex.slice(1)) : ''),
          (item.phone || '')
        ].filter(Boolean).join(' • ');
        return $(`
          <div class="flex flex-col">
            <div class="font-medium">${item.name || item.text}</div>
            ${meta ? `<div class="text-xs text-gray-600">${meta}</div>` : ''}
          </div>
        `);
      },

      // What shows in the selected chip (input)
      templateSelection: function (item) {
        if (!item.id || item.id === '__new') return item.text || '+ Add new patient';
        const name = item.name || item.text || '';
        const el = document.createElement('span');
        el.textContent = `#${item.id} - ${name}`;
        return $(el);
      }
    });

    // Toggle new-patient block
    $patient.on('change', function () {
      if (this.value === '__new') {
        newBlock.classList.remove('hidden');
        $ageInfo.text('');
      } else if (!this.value) {
        newBlock.classList.add('hidden');
        $ageInfo.text('');
      } else {
        newBlock.classList.add('hidden');
      }
    });

    // Show age (and gender) below the field
    $patient.on('select2:select', function(e){
      const data = e.params.data || {};
      if (data.id && data.id !== '__new') {
        const age = (data.age ?? computeAgeFromDob(data.dob));
        const sex = data.sex ? data.sex.charAt(0).toUpperCase() + data.sex.slice(1) : '';
        const bits = [];
        if (age != null) bits.push('Age: ' + age);
        if (sex) bits.push('Gender: ' + sex);
        $ageInfo.text(bits.join(' • '));
      } else {
        $ageInfo.text('');
      }
    });

    $patient.on('select2:clear', function(){
      $ageInfo.text('');
    });
  });
  </script>

  {{-- ===== Patient History: load into right panel on selection ===== --}}
  <script>
  $(function () {
    const $patient  = $('#patient_select');
    const tpl       = $patient.data('history-url-template') || "{{ route('patients.history', ['patient' => '__ID__']) }}";

    const $panel    = $('#prev_rx_panel');
    const $name     = $('#prev_rx_patient_name');
    const $count    = $('#prev_rx_count');
    const $loading  = $('#prev_rx_loading');
    const $empty    = $('#prev_rx_empty');
    const $error    = $('#prev_rx_error');
    const $listWrap = $('#prev_rx_list');
    const $ul       = $('#prev_rx_ul');

    function showPanel(){ $panel.removeClass('hidden'); }
    function hidePanel(){ $panel.addClass('hidden'); }
    function stateLoading(){ $loading.removeClass('hidden'); $empty.addClass('hidden'); $error.addClass('hidden'); $listWrap.addClass('hidden'); }
    function stateEmpty(){ $loading.addClass('hidden'); $empty.removeClass('hidden'); $error.addClass('hidden'); $listWrap.addClass('hidden'); }
    function stateError(msg){ $loading.addClass('hidden'); $empty.addClass('hidden'); $error.removeClass('hidden').text(msg || 'Could not load prescriptions.'); $listWrap.addClass('hidden'); }
    function stateList(){ $loading.addClass('hidden'); $empty.addClass('hidden'); $error.addClass('hidden'); $listWrap.removeClass('hidden'); }

    function renderItems(items) {
      $ul.empty();
      items.forEach(it => {
        const title = [it.date, it.doctor_name].filter(Boolean).join(' • ') || 'Prescription';
        const line2 = it.problem ? it.problem : '';
        const href  = it.url || '#';
        $ul.append(`
          <li class="border rounded p-2">
            <div class="flex items-center justify-between gap-2">
              <div class="text-sm">
                <div class="font-medium">${title}</div>
                ${line2 ? `<div class="text-xs text-gray-600">${line2}</div>` : ''}
              </div>
              ${href !== '#' ? `<a href="${href}" target="_blank" class="text-blue-600 text-sm">Open</a>` : ''}
            </div>
          </li>
        `);
      });
    }

    function loadHistory(id, label){
      if (!id) return hidePanel();
      const url = (tpl || '').replace('__ID__', encodeURIComponent(id));
      showPanel(); stateLoading(); $name.text(label || '');

      $.ajax({ url, method: 'GET', dataType: 'json', data: { limit: 10 } })
        .done(res => {
          $count.text(res.count + ' total');
          if (!res.items || res.items.length === 0) return stateEmpty();
          renderItems(res.items); stateList();
        })
        .fail(xhr => {
          const msg = `Could not load prescriptions (${xhr.status}). ${
            (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message :
            (xhr.responseText || '').slice(0, 180)
          }`;
          stateError(msg);
          console.error('History load failed:', xhr);
        });
    }

    $patient.on('select2:select', (e) => {
      const it = e.params.data;
      if (!it || !it.id || it.id === '__new') return hidePanel();
      loadHistory(it.id, it.text || it.name);
    });

    $patient.on('change', function(){
      const val = $(this).val();
      if (!val || val === '__new') hidePanel();
    });
  });
  </script>

  {{-- ===== Select2: Medicine picker (AJAX) ===== --}}
  <script>
  $(function () {
    const $picker   = $('#medicine_picker');
    const $selWrap  = $('#medicine_selected_wrap');
    const $selList  = $('#medicine_selected');
    const $clearBtn = $('#medicine_clear');

    const AJAX_URL = "{{ route('medicines.search') }}";
    function ensureWrap(){ $selWrap.toggleClass('hidden', $selList.children().length === 0); }

    function selectedRow(item) {
      const id = item.id;
      const $row = $(`
        <div class="border rounded p-2" data-id="${id}">
          <div class="flex items-center justify-between gap-2">
            <div class="text-sm">
              <div class="font-medium">
                ${item.name ?? item.text ?? ''}
                ${item.generic ? ' — ' + item.generic : ''}
                ${item.strength ? ' (' + item.strength + ')' : ''}
              </div>
            </div>
            <button type="button" class="text-red-600 text-sm remove-btn">Remove</button>
          </div>
          <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
            <input type="hidden" name="medicines[${id}][selected]" value="1">
            <input type="text" class="border rounded px-2 py-1" name="medicines[${id}][duration]" placeholder="Duration (e.g., 5 days)">
            <input type="text" class="border rounded px-2 py-1" name="medicines[${id}][times_per_day]" placeholder="Times/day (e.g., 3x)">
          </div>
        </div>
      `);
      $row.find('.remove-btn').on('click', function () {
        const remaining = $picker.select2('data').filter(d => d.id !== id).map(d => d.id);
        $picker.val(remaining).trigger('change');
        $row.remove(); ensureWrap();
      });
      return $row;
    }

    const dropdownParent = $('#medicine_picker').parent();

    $picker.select2({
      placeholder: 'Search medicines…',
      width: 'resolve',
      minimumInputLength: 2,
      dropdownParent: dropdownParent,
      ajax: {
        url: AJAX_URL,
        type: 'GET',
        dataType: 'json',
        delay: 200,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: params => ({ term: params.term, page: params.page || 1 }),
        processResults: (data, params) => {
          params.page = params.page || 1;
          const results = Array.isArray(data) ? data : (Array.isArray(data?.results) ? data.results : []);
          const more    = Array.isArray(data) ? false : !!(data?.pagination && data.pagination.more);
          return { results, pagination: { more } };
        },
        cache: true,
        transport: function (params, success, failure) {
          const req = $.ajax(params);
          req.then(success);
          req.fail(function (xhr) {
            $('#med_debug').text('ERROR ' + xhr.status + ':\n' + (xhr.responseText || '').slice(0,1000));
            console.error('Select2 AJAX error:', xhr.status, xhr.responseText);
            failure(xhr);
          });
          return req;
        }
      },
      templateResult: (item) => {
        if (!item.id) return item.text;
        const extra = [item.generic, item.strength, item.manufacturer].filter(Boolean).join(' • ');
        return $(`
          <div class="flex flex-col">
            <div class="font-medium">${item.name || item.text}</div>
            <div class="text-xs text-gray-600">${extra}}</div>
          </div>
        `);
      },
      templateSelection: (item) => item.text || item.name || ''
    });

    $picker.on('select2:select', function (e) {
      const item = e.params.data;
      if ($selList.find(`[data-id="${item.id}"]`).length) return;
      $selList.append(selectedRow(item)); ensureWrap();
    });

    $picker.on('select2:unselect', function (e) {
      const id = e.params.data.id;
      $selList.find(`[data-id="${id}"]`).remove(); ensureWrap();
    });

    $clearBtn.on('click', function () {
      $picker.val(null).trigger('change');
      $selList.empty(); ensureWrap();
    });

    ensureWrap();
  });
  </script>

  {{-- ===== Select2: Test picker (AJAX) ===== --}}
  <script>
  $(function () {
    const $picker   = $('#test_picker');
    const $wrap     = $('#test_selected_wrap');
    const $list     = $('#test_selected');
    const $clearBtn = $('#test_clear');

    const AJAX_URL = "{{ route('tests.search') }}";
    function ensureWrap(){ $wrap.toggleClass('hidden', $list.children().length === 0); }

    function selectedRow(item) {
      const id = item.id;
      const $row = $(`
        <div class="border rounded p-2" data-id="${id}">
          <div class="flex items-center justify-between gap-2">
            <div class="text-sm">
              <div class="font-medium">${item.name ?? item.text ?? ''}</div>
            </div>
            <button type="button" class="text-red-600 text-sm remove-btn">Remove</button>
          </div>
          <input type="hidden" name="tests[]" value="${id}">
        </div>
      `);
      $row.find('.remove-btn').on('click', function(){
        const remaining = $picker.select2('data').filter(d => d.id !== id).map(d => d.id);
        $picker.val(remaining).trigger('change');
        $row.remove(); ensureWrap();
      });
      return $row;
    }

    const dropdownParent = $picker.parent();

    $picker.select2({
      placeholder: 'Search tests…',
      width: 'resolve',
      minimumInputLength: 1,
      dropdownParent,
      ajax: {
        url: AJAX_URL,
        dataType: 'json',
        delay: 200,
        data: params => ({ term: params.term, page: params.page || 1 }),
        processResults: (data, params) => {
          params.page = params.page || 1;
          const results = Array.isArray(data) ? data : (data.results || []);
          const more    = Array.isArray(data) ? false : !!(data.pagination && data.pagination.more);
          return { results, pagination: { more } };
        }
      },
      templateResult: (item) => {
        if (!item.id) return item.text;
        const priced = (item.price ?? '') !== '' ? `৳${item.price}` : '';
        return $(`
          <div class="flex flex-col">
            <div class="font-medium">${item.name || item.text}</div>
            <div class="text-xs text-gray-600">${[priced, item.note].filter(Boolean).join(' • ')}</div>
          </div>
        `);
      },
      templateSelection: (item) => item.text || item.name || ''
    });

    $picker.on('select2:select', function (e) {
      const item = e.params.data;
      if ($list.find(`[data-id="${item.id}"]`).length) return;
      $list.append(selectedRow(item)); ensureWrap();
    });

    $picker.on('select2:unselect', function (e) {
      const id = e.params.data.id;
      $list.find(`[data-id="${id}"]`).remove(); ensureWrap();
    });

    $clearBtn.on('click', function () {
      $picker.val(null).trigger('change');
      $list.empty(); ensureWrap();
    });

    ensureWrap();
  });
  </script>

  {{-- ===== Bulleted textareas (default ON for OE/Problem/Advice) ===== --}}
  <script>
  (function () {
    const BULLET = '• ';

    function setCaretToEnd(el){ try{ const L=el.value.length; el.setSelectionRange(L,L); el.focus(); }catch{} }
    function isOn(el){ return el.dataset.bulletsOn === '1'; }
    function toggle(el,on){ el.dataset.bulletsOn = on ? '1' : '0'; }
    function ensureBullets(text){
      return String(text||'')
        .split(/\r?\n/)
        .map(l => l.trim()==='' ? '' : (l.startsWith(BULLET)? l : BULLET+l))
        .join('\n');
    }
    function stripBullets(el){ el.value = el.value.replace(new RegExp('^'+BULLET,'gm'), ''); }

    function onKeydown(e){
      if (!isOn(e.target)) return;
      const el = e.target;
      if (e.key === 'Enter'){
        e.preventDefault();
        const s=el.selectionStart, epos=el.selectionEnd;
        const before=el.value.slice(0,s), after=el.value.slice(epos);
        const insert = '\n' + BULLET;
        el.value = before + insert + after;
        const pos = before.length + insert.length;
        el.setSelectionRange(pos,pos);
      } else if (e.key === 'Backspace'){
        const s=el.selectionStart, epos=el.selectionEnd;
        if (s===epos){
          const lineStart = el.value.lastIndexOf('\n', s-1)+1;
          if (s - lineStart <= BULLET.length &&
              el.value.slice(lineStart, lineStart+BULLET.length) === BULLET){
            e.preventDefault();
            const before = el.value.slice(0,lineStart), after = el.value.slice(s);
            el.value = before + after;
            el.setSelectionRange(lineStart, lineStart);
          }
        }
      }
    }

    function onPaste(e){
      if (!isOn(e.target)) return;
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text') || '';
      const el=e.target, s=el.selectionStart, epos=el.selectionEnd;
      const before=el.value.slice(0,s), after=el.value.slice(epos);
      const bulletized = ensureBullets(paste);
      el.value = before + bulletized + after;
      const pos = before.length + bulletized.length;
      el.setSelectionRange(pos,pos);
    }

    function enable(el){ toggle(el,true); if (el.value.trim()!=='') el.value = ensureBullets(el.value); }
    function disable(el){ toggle(el,false); }

    function wireTextareas(){
      document.querySelectorAll('textarea[data-bullets]').forEach(el=>{
        el.addEventListener('keydown', onKeydown);
        el.addEventListener('paste', onPaste);
      });
    }

    function wireButtons(){
      document.querySelectorAll('[data-bullets-toggle]').forEach(btn=>{
        const sel = btn.getAttribute('data-bullets-toggle');
        const target = document.querySelector(sel);
        if (!target) return;

        // read initial state from target
        let on = target.dataset.bulletsOn === '1';
        const paint = ()=>{
          btn.textContent = on ? '• Bullets: ON' : '• Bullets: OFF';
          btn.classList.toggle('bg-blue-600', on);
          btn.classList.toggle('text-white', on);
          btn.classList.toggle('bg-gray-100', !on);
          btn.classList.toggle('text-gray-700', !on);
        };
        paint();

        btn.addEventListener('click', ()=>{
          on = !on;
          if (on) enable(target); else disable(target);
          paint(); setCaretToEnd(target);
        });
      });

      document.querySelectorAll('[data-bullets-clear]').forEach(btn=>{
        const sel = btn.getAttribute('data-bullets-clear');
        const target = document.querySelector(sel);
        if (!target) return;
        btn.addEventListener('click', ()=> stripBullets(target));
      });
    }

    document.addEventListener('DOMContentLoaded', ()=>{
      wireTextareas();
      wireButtons();

      // Default ON for these fields
      ['#oe', '#problem_description', '#doctor_advice'].forEach(sel=>{
        const el = document.querySelector(sel);
        if (!el) return;
        el.dataset.bulletsOn = '1';
        if (el.value.trim()!=='') el.value = ensureBullets(el.value);
      });
    });
  })();
  </script>

  {{-- ===== Clinical Findings collapse toggle (with remember state) ===== --}}
  <script>
  (function(){
    const key = 'cf-open';
    function setOpen(open){
      const body = document.getElementById('cf-body');
      const btn  = document.getElementById('cf-toggle');
      const icon = btn?.querySelector('svg');
      if (!body || !btn) return;
      body.classList.toggle('hidden', !open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (icon) icon.classList.toggle('rotate-180', open);
      try { localStorage.setItem(key, open ? '1' : '0'); } catch {}
    }
    document.addEventListener('DOMContentLoaded', () => {
      const remembered = (localStorage.getItem(key) ?? '0') === '1';
      setOpen(remembered); // default collapsed
      document.getElementById('cf-toggle')?.addEventListener('click', () => {
        const body = document.getElementById('cf-body');
        setOpen(body?.classList.contains('hidden'));
      });
    });
  })();
  </script>
</x-app-layout>
