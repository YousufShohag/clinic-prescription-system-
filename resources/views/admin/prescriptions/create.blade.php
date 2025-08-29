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

    <form action="{{ route('prescriptions.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      @csrf

      {{-- LEFT --}}
      <aside class="xl:col-span-3 space-y-6">
        <section class="border rounded-lg p-5 bg-gray-50">
          <h3 class="text-xl font-semibold mb-4">Clinical Findings</h3>

          <label class="block text-sm font-medium text-gray-700 mb-2">O/E (On Examination)</label>
          <textarea name="oe" rows="4" class="w-full border rounded px-3 py-2 mb-5" placeholder="General appearance, system findings, etc.">{{ old('oe') }}</textarea>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        </section>

        
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
    <option value="{{ $doc->id }}"
      @selected((string)$doc->id === $preselectDoctorId)>
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
          </div>
        </div>

        {{-- New patient --}}
        <div id="new_patient_block" class="hidden bg-gray-50 border rounded p-5">
          <h3 class="text-md font-semibold mb-4">New Patient Details</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="new_patient[name]" value="{{ old('new_patient.name') }}" placeholder="Patient name" class="border rounded px-3 py-2">
            <input type="text" name="new_patient[phone]" value="{{ old('new_patient.phone') }}" placeholder="Phone" class="border rounded px-3 py-2">
            <input type="email" name="new_patient[email]" value="{{ old('new_patient.email') }}" placeholder="Email" class="border rounded px-3 py-2">
            <textarea name="new_patient[notes]" placeholder="Notes (optional)" class="md:col-span-3 border rounded px-3 py-2">{{ old('new_patient.notes') }}</textarea>
          </div>
        </div>

        {{-- Problem --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Problem Description (brief)</label>
          <textarea name="problem_description" rows="3" class="w-full border rounded px-3 py-2">{{ old('problem_description') }}</textarea>
        </div>

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
        <div>
          <label class="block text-sm font-medium text-gray-700">Doctor Advice</label>
          <textarea name="doctor_advice" rows="3" class="w-full border rounded px-3 py-2">{{ old('doctor_advice') }}</textarea>
        </div>

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
    const $patient = $('#patient_select');
    const newBlock = document.getElementById('new_patient_block');

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
      templateResult: item => {
        if (!item.id || item.id === '__new') return item.text;
        const extra = [item.phone, item.email].filter(Boolean).join(' • ');
        return $(`
          <div class="flex flex-col">
            <div class="font-medium">${item.name || item.text}</div>
            ${extra ? `<div class="text-xs text-gray-600">${extra}</div>` : ''}
          </div>
        `);
      },
      templateSelection: item => item.text || item.name || ''
    });

    $patient.on('change', function () {
      if (this.value === '__new') newBlock.classList.remove('hidden');
      else newBlock.classList.add('hidden');
    });
  });
  </script>

  {{-- ===== Patient History: load into left panel on selection ===== --}}
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
        const price = (item.price ?? '') !== '' ? `৳${item.price}` : '';
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
</x-app-layout>
