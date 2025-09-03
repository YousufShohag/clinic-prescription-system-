{{-- resources/views/admin/prescriptions/edit.blade.php --}}
<x-app-layout>
  {{-- Select2 assets (load once) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

  @php
    // Precompute arrays for safe @json usage
    $initialPatientJson = optional($prescription->patient)->only(['id','name','age','sex','dob','phone']);

    $initialMeds = $prescription->medicines->map(function($m){
      return [
        'id'            => $m->id,
        'name'          => $m->name,
        'text'          => $m->name,
        'generic'       => $m->generic,
        'strength'      => $m->strength,
        'manufacturer'  => $m->manufacturer,
        'duration'      => optional($m->pivot)->duration,
        'times_per_day' => optional($m->pivot)->times_per_day,
      ];
    })->values()->all();

    $initialTests = $prescription->tests->map(function($t){
      return [
        'id'    => $t->id,
        'name'  => $t->name,
        'text'  => $t->name,
        'price' => $t->price,
        'note'  => $t->note,
      ];
    })->values()->all();
  @endphp

  <div class="w-full min-h-screen bg-white p-6 md:p-8">
    <div class="flex items-start justify-between gap-4">
      <h2 class="text-3xl font-semibold">Edit Prescription #{{ $prescription->id }}</h2>

      {{-- SMART BAR --}}
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <button type="button" id="tpl_btn" class="px-3 py-2 text-sm border rounded hover:bg-gray-50">Templates</button>
          <div id="tpl_menu" class="hidden absolute right-0 z-20 mt-1 w-56 bg-white border rounded shadow">
            <button type="button" data-template="urti"  class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm">URTI</button>
            <button type="button" data-template="gerd"  class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm">GERD</button>
            <button type="button" data-template="htn"   class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm">Hypertension FU</button>
            <button type="button" data-template="dm2"   class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm">Type-2 DM FU</button>
          </div>
        </div>

        <button type="button" id="btn_normals" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Alt+N">Normal Vitals</button>

        <div class="hidden md:block h-6 w-px bg-gray-300"></div>

        <button type="button" id="btn_preview" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Alt+P">Preview</button>

        <div class="hidden md:block h-6 w-px bg-gray-300"></div>

        <button type="button" id="btn_save_draft" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Alt+D">Save draft</button>
        <button type="button" id="btn_restore_draft" class="px-3 py-2 text-sm border rounded hover:bg-gray-50" title="Alt+R">Restore</button>
        <button type="button" id="btn_clear_draft" class="px-3 py-2 text-sm border rounded hover:bg-gray-50">Clear</button>
        <span id="draft_status" class="text-xs text-gray-500 ml-1 whitespace-nowrap"></span>
      </div>
    </div>

    <p class="text-xs text-gray-500 mt-1">
      Shortcuts: <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">1</kbd> CF Panel •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">N</kbd> Normal Vitals •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">B</kbd> Toggle bullets •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">S</kbd> Submit
    </p>

    @if($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('prescriptions.update', $prescription) }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-4" enctype="multipart/form-data">
      @csrf
      @method('PUT')

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
            {{-- NOTE: O/E moved to segmented panel below. This block only holds vitals. --}}

            {{-- Vitals --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">BP</label>
                <input type="text" name="bp" id="bp" value="{{ old('bp', $prescription->bp) }}" placeholder="120/80" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Pulse (bpm)</label>
                <input type="number" step="1" min="0" name="pulse" id="pulse" value="{{ old('pulse', $prescription->pulse) }}" placeholder="72" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature_c" id="temperature_c" value="{{ old('temperature_c', $prescription->temperature_c) }}" placeholder="37.0" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">SpO₂ (%)</label>
                <input type="number" step="1" min="0" max="100" name="spo2" id="spo2" value="{{ old('spo2', $prescription->spo2) }}" placeholder="98" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Respiratory Rate (/min)</label>
                <input type="number" step="1" min="0" name="respiratory_rate" id="respiratory_rate" value="{{ old('respiratory_rate', $prescription->respiratory_rate) }}" placeholder="16" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Weight (kg)</label>
                <input type="number" step="0.1" min="0" id="weight_kg" name="weight_kg" value="{{ old('weight_kg', $prescription->weight_kg) }}" placeholder="70.0" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Height (cm)</label>
                <input type="number" step="0.1" min="0" id="height_cm" name="height_cm" value="{{ old('height_cm', $prescription->height_cm) }}" placeholder="170" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">BMI (kg/m²)</label>
                <input type="text" id="bmi" name="bmi" value="{{ old('bmi', $prescription->bmi) }}" readonly class="w-full border rounded px-3 py-2 bg-gray-100">
              </div>
            </div>

            <div class="mt-3">
              <button type="button" id="btn_normals_inline" class="px-2 py-1 text-xs border rounded hover:bg-gray-50">Set Normal Vitals</button>
              <button type="button" id="btn_clear_vitals" class="px-2 py-1 text-xs border rounded hover:bg-gray-50">Clear Vitals</button>
            </div>
          </div>
        </section>

        {{-- ===== Segmented History panel (O/E, P/H, D/H, M/H, …) ===== --}}
        @php
          $histTabs = [
            'oe' => 'O/E',
            'ph' => 'P/H',
            'dh' => 'D/H',
            'mh' => 'M/H',
            'oh' => 'OH',
            'pae' => 'P/A/E',
            'dx' => 'DX',
            'previous_investigation' => 'Previous Investigation',
            'ah' => 'A/H',
            'special_note' => 'Special Note',
            'referred_to' => 'Referred To',
          ];
        @endphp

        <section class="border rounded-lg">
          {{-- segmented buttons --}}
          <div id="hist-tabs" class="p-3 border-b flex flex-wrap gap-2">
            @foreach($histTabs as $key => $label)
              <button type="button"
                      class="hist-tab px-3 py-1 text-sm rounded-full border hover:bg-gray-50"
                      data-target="#hist_{{ $key }}">
                <span class="tab-text">{{ $label }}</span>
                <span class="ml-1 hidden tab-dot">•</span>
              </button>
            @endforeach
          </div>

          {{-- panes --}}
          <div class="p-4">
            @foreach($histTabs as $key => $label)
              <div id="hist_{{ $key }}" class="hist-pane hidden">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                  <div class="flex items-center gap-2">
                    <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                            data-bullets-toggle="#{{ $key }}">• Bullets: OFF</button>
                    <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                            data-bullets-clear="#{{ $key }}">Clear</button>
                  </div>
                </div>

                {{-- quick chips row --}}
                <div class="flex flex-wrap gap-2 mb-2" data-chip-row data-target="#{{ $key }}"></div>

                <textarea id="{{ $key }}" name="{{ $key }}" rows="3"
                          class="w-full border rounded px-3 py-2"
                          data-bullets>{{ old($key, $prescription->$key ?? '') }}</textarea>
              </div>
            @endforeach
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
                <option value="{{ $doc->id }}" @selected((string) $doc->id === $preselectDoctorId)>
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
              @if($prescription->patient)
                <option value="{{ $prescription->patient_id }}" selected>
                  {{ $prescription->patient->name }}{{ $prescription->patient->phone ? " ({$prescription->patient->phone})" : '' }}
                </option>
              @endif
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

        {{-- Problem (with bullets + chips) --}}
        <div class="flex items-center justify-between mt-6 mb-2">
          <label class="block text-sm font-medium text-gray-700">Problem Description (brief)</label>
          <div class="flex items-center gap-2">
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700" data-bullets-toggle="#problem_description">• Bullets: OFF</button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700" data-bullets-clear="#problem_description">Clear</button>
          </div>
        </div>
        <div class="flex flex-wrap gap-2 mb-2" data-chip-row data-target="#problem_description"></div>
        <textarea id="problem_description" name="problem_description" rows="3" class="w-full border rounded px-3 py-2" data-bullets>{{ old('problem_description', $prescription->problem_description) }}</textarea>

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
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700" data-bullets-toggle="#doctor_advice">• Bullets: OFF</button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700" data-bullets-clear="#doctor_advice">Clear</button>
          </div>
        </div>
        <div class="flex flex-wrap gap-2 mb-2" data-chip-row data-target="#doctor_advice"></div>
        <textarea id="doctor_advice" name="doctor_advice" rows="3" class="w-full border rounded px-3 py-2" data-bullets>{{ old('doctor_advice', $prescription->doctor_advice) }}</textarea>

        {{-- Return Date --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Return Date</label>
          {{-- Assuming DB stores Y-m-d already --}}
          <input type="date" name="return_date" value="{{ old('return_date', $prescription->return_date) }}" class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500">The patient should revisit on this date.</p>
        </div>

        <div class="flex justify-end">
          <button id="submit_btn" type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700" title="Alt+S">
            Update Prescription
          </button>
        </div>
      </section>

      {{-- RIGHT (optional area) --}}
      <aside class="xl:col-span-3 space-y-6">
        {{-- Patient history panel (loads after a patient is selected) --}}
        <section id="prev_rx_panel" class="hidden border rounded-lg p-5 sticky top-6">
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

      templateResult: item => {
        if (!item.id || item.id === '__new') return item.text;
        const age = item.age ?? computeAgeFromDob(item.dob);
        const idBadge = item.id ? `#${item.id}` : '';
        const meta = [ idBadge, (age != null ? `${age}y` : ''), (item.sex ? (item.sex[0].toUpperCase() + item.sex.slice(1)) : ''), (item.phone || '') ]
          .filter(Boolean).join(' • ');
        return $(`
          <div class="flex flex-col">
            <div class="font-medium">${item.name || item.text}</div>
            ${meta ? `<div class="text-xs text-gray-600">${meta}</div>` : ''}
          </div>
        `);
      },

      templateSelection: function (item) {
        if (!item.id || item.id === '__new') return item.text || '+ Add new patient';
        const name = item.name || item.text || '';
        const el = document.createElement('span');
        el.textContent = `#${item.id} - ${name}`;
        return $(el);
      }
    });

    $patient.on('change', function () {
      if (this.value === '__new') { newBlock.classList.remove('hidden'); $ageInfo.text(''); }
      else if (!this.value)      { newBlock.classList.add('hidden');    $ageInfo.text(''); }
      else                       { newBlock.classList.add('hidden'); }
    });

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

    $patient.on('select2:clear', function(){ $ageInfo.text(''); });

    // Preselect existing patient to populate info panel
    const initialPatient = @json($initialPatientJson);
    if (initialPatient && initialPatient.id) {
      $patient.trigger({
        type: 'select2:select',
        params: { data: initialPatient }
      });
      $patient.trigger('change');
    }
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
                ${item.name ?? item.text ?? ''} ${item.generic ? ' — ' + item.generic : ''} ${item.strength ? ' (' + item.strength + ')' : ''}
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
      dropdownParent,
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
            <div class="text-xs text-gray-600">${extra}</div>
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

    // Preload existing medicines
    const initialMeds = @json($initialMeds);
    if (initialMeds.length) {
      initialMeds.forEach(item => {
        $picker.append(new Option(item.name, item.id, true, true));
        const $row = selectedRow(item);
        $row.find(`input[name="medicines[${item.id}][duration]"]`).val(item.duration || '');
        $row.find(`input[name="medicines[${item.id}][times_per_day]"]`).val(item.times_per_day || '');
        $selList.append($row);
      });
      $picker.trigger('change');
    }
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

    // Preload existing tests
    const initialTests = @json($initialTests);
    if (initialTests.length) {
      initialTests.forEach(item => {
        $picker.append(new Option(item.name, item.id, true, true));
        $list.append(selectedRow(item));
      });
      $picker.trigger('change');
    }
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
          if (s - lineStart <= BULLET.length && el.value.slice(lineStart, lineStart+BULLET.length) === BULLET){
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
      ['#oe', '#problem_description', '#doctor_advice'].forEach(sel=>{
        const el = document.querySelector(sel);
        if (!el) return;
        el.dataset.bulletsOn = '1';
        if (el.value.trim()!=='') el.value = ensureBullets(el.value);
      });
    });

    // expose globally for shortcut toggle
    window.__toggleBulletsFocused = function(){
      const el = document.activeElement;
      if (!el || el.tagName !== 'TEXTAREA' || !el.hasAttribute('data-bullets')) return;
      const on = el.dataset.bulletsOn === '1';
      if (on) disable(el); else enable(el);
      // update paired button via existing handler
      document.querySelectorAll(`[data-bullets-toggle="#${el.id}"]`).forEach(btn=>{ btn.click(); });
    }
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

  {{-- ===== Smart chips, templates, normals, preview, drafts, shortcuts ===== --}}
  <script>
  (function(){
    const DRAFT_KEY = 'rx-draft-v2';

    const CHIP_SETS = {
      '#oe': [
        'GA: NAD','No pallor/icl/jaun/edema','CVS: S1 S2 normal','RS: Clear, no added sound',
        'P/A: Soft, non-tender','CNS: Oriented, normal tone/reflex','ENT: Congested oropharynx',
        'Skin: No rash'
      ],
      '#problem_description': [
        'Fever with sore throat','Cough, runny nose','Epigastric burning','Headache','Follow-up for HTN',
        'Follow-up for DM','Dyspepsia','Dizziness'
      ],
      '#doctor_advice': [
        'Hydration + rest','Salt-water gargle','Paracetamol PRN','Avoid spicy/oily foods',
        'Small frequent meals','Home glucose monitoring','BP log daily','ER if red flags'
      ],
      '#ph': ['HTN','DM','BA','CKD','IHD'],
      '#dh': ['NSAIDs use','PPIs use','Steroid use','Antibiotics recently'],
      '#mh': ['No known chronic illness','Known hypothyroid','Known dyslipidemia'],
      '#oh': ['Non-smoker','Ex-smoker','Occasional alcohol','Sedentary lifestyle'],
      '#pae': ['No organomegaly','No focal neuro deficit','No pedal edema'],
      '#dx': ['URTI','Acute gastritis','Hypertension – controlled','Type-2 DM – controlled'],
      '#previous_investigation': ['CBC normal','CXR clear','RBS 7.2 mmol/L','LFT normal','Creatinine 1.0'],
      '#ah': ['NKDA','Allergy: penicillin'],
      '#special_note': ['Discussed compliance','Explained warning signs','Counseled diet & exercise'],
      '#referred_to': ['ENT OPD','Medicine OPD','Cardiology','Endocrinology']
    };

    const TEMPLATES = {
      urti: {
        problem_description: '• Fever with sore throat\n• Runny nose\n• Dry cough',
        oe: '• GA: NAD\n• ENT: Congested oropharynx, no exudate\n• RS: Clear, no added sound\n• CVS: S1 S2 normal',
        doctor_advice: '• Hydration + rest\n• Salt-water gargle\n• Paracetamol PRN\n• ER if red flags',
        dx: '• URTI'
      },
      gerd: {
        problem_description: '• Epigastric burning\n• Post-meal fullness',
        oe: '• P/A: Soft, mild epigastric tenderness\n• CVS/RS: Normal',
        doctor_advice: '• Avoid spicy/oily foods\n• Small frequent meals\n• Elevate head-end while sleeping',
        dx: '• Acute gastritis / GERD'
      },
      htn: {
        problem_description: '• Follow-up for HTN',
        oe: '• CVS: S1 S2 normal\n• No pedal edema',
        doctor_advice: '• BP log daily\n• Low-salt diet\n• Regular exercise',
        dx: '• Hypertension – controlled'
      },
      dm2: {
        problem_description: '• Follow-up for Type-2 DM',
        oe: '• GA: NAD\n• No neuropathic deficits',
        doctor_advice: '• Home glucose monitoring\n• Diet counseling\n• Foot care advice',
        dx: '• Type-2 DM – controlled'
      }
    };

    function addChips(){
      document.querySelectorAll('[data-chip-row]').forEach(row=>{
        const targetSel = row.getAttribute('data-target');
        const items = CHIP_SETS[targetSel] || [];
        row.innerHTML = '';
        items.forEach(text=>{
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
          btn.textContent = text;
          btn.addEventListener('click', ()=>{
            const ta = document.querySelector(targetSel);
            if (!ta) return;
            ta.dataset.bulletsOn = '1';
            const val = ta.value.trim();
            ta.value = (val ? (val + '\n• ' + text) : ('• ' + text));
            ta.dispatchEvent(new Event('input', {bubbles:true}));
            ta.focus(); ta.selectionStart = ta.selectionEnd = ta.value.length;
          });
          row.appendChild(btn);
        });
      });
    }

    function applyTemplate(key){
      const t = TEMPLATES[key]; if (!t) return;
      Object.entries(t).forEach(([field, value])=>{
        const el = document.getElementById(field);
        if (el){
          el.dataset.bulletsOn = '1';
          el.value = value;
          el.dispatchEvent(new Event('input', {bubbles:true}));
        }
      });
    }

    function setNormals(){
      const v = (id, val)=>{ const el=document.getElementById(id); if (el) el.value = val; };
      v('bp','120/80'); v('pulse','72'); v('temperature_c','37.0'); v('spo2','98'); v('respiratory_rate','16');
    }
    function clearVitals(){
      ['bp','pulse','temperature_c','spo2','respiratory_rate'].forEach(id=>{
        const el=document.getElementById(id); if (el) el.value='';
      });
    }

    // Preview
    function openPreview(){
      const data = {
        Doctor: $('#doctor_id option:selected').text().trim(),
        Patient: $('#patient_select').val() ? $('#patient_select').find(':selected').text().trim() : '',
        OE: $('#oe').val(),
        BP: $('#bp').val(), Pulse: $('#pulse').val(), Temp: $('#temperature_c').val(), SpO2: $('#spo2').val(), RR: $('#respiratory_rate').val(),
        Problem: $('#problem_description').val(),
        Advice: $('#doctor_advice').val(),
        Return: $('[name="return_date"]').val(),
        DX: $('#dx').val()
      };
      const html = `
        <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
          <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg">
            <div class="flex items-center justify-between px-4 py-3 border-b">
              <h3 class="text-lg font-semibold">Quick Preview</h3>
              <button type="button" id="pv_close" class="text-gray-600 hover:text-black">✕</button>
            </div>
            <div class="p-4 max-h-[70vh] overflow-auto space-y-3 text-sm">
              ${Object.entries(data).map(([k,v])=> v ? `<div><span class="font-medium">${k}:</span><pre class="whitespace-pre-wrap mt-1">${v}</pre></div>` : '').join('')}
            </div>
            <div class="px-4 py-3 border-t flex justify-end gap-2">
              <button type="button" id="pv_close2" class="px-3 py-2 border rounded">Close</button>
              <button type="button" id="pv_submit" class="px-3 py-2 bg-blue-600 text-white rounded">Submit</button>
            </div>
          </div>
        </div>`;
      const wrap = document.createElement('div');
      wrap.id='pv_wrap'; wrap.innerHTML=html;
      document.body.appendChild(wrap);
      document.getElementById('pv_close')?.addEventListener('click', ()=>wrap.remove());
      document.getElementById('pv_close2')?.addEventListener('click', ()=>wrap.remove());
      document.getElementById('pv_submit')?.addEventListener('click', ()=>{ wrap.remove(); document.getElementById('submit_btn')?.click(); });
    }

    // Drafts
    function collectForm(){
      const data = {};
      document.querySelectorAll('input, textarea, select').forEach(el=>{
        if (!el.name) return;
        if (el.type === 'file') return;
        if (el.type === 'checkbox' || el.type === 'radio') data[el.name] = el.checked ? el.value : '';
        else data[el.name] = el.value;
      });
      return data;
    }
    function fillForm(data){
      if (!data) return;
      Object.entries(data).forEach(([name, val])=>{
        const els = document.getElementsByName(name);
        if (!els || !els.length) return;
        const el = els[0];
        if (el.tagName==='SELECT'){
          $(el).val(val).trigger('change');
        } else {
          el.value = val ?? '';
          el.dispatchEvent(new Event('input', {bubbles:true}));
        }
      });
    }
    function saveDraft(manual=false){
      const obj = { t: Date.now(), data: collectForm() };
      try { localStorage.setItem(DRAFT_KEY, JSON.stringify(obj)); } catch {}
      setStatus('Saved' + (manual?' (manual)':'' ));
    }
    function restoreDraft(){
      try {
        const raw = localStorage.getItem(DRAFT_KEY);
        if (!raw) { setStatus('No draft'); return; }
        const obj = JSON.parse(raw);
        fillForm(obj.data);
        setStatus('Restored');
      } catch { setStatus('Draft restore failed'); }
    }
    function clearDraft(){
      try { localStorage.removeItem(DRAFT_KEY); setStatus('Draft cleared'); } catch {}
    }
    let statusTimer;
    function setStatus(txt){
      const el = document.getElementById('draft_status');
      if (!el) return;
      el.textContent = txt;
      clearTimeout(statusTimer);
      statusTimer = setTimeout(()=>{ el.textContent=''; }, 2000);
    }

    // Template dropdown + wiring
    document.addEventListener('DOMContentLoaded', ()=>{
      addChips();

      document.getElementById('tpl_btn')?.addEventListener('click', ()=>{
        document.getElementById('tpl_menu')?.classList.toggle('hidden');
      });
      document.querySelectorAll('#tpl_menu [data-template]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          applyTemplate(btn.getAttribute('data-template'));
          document.getElementById('tpl_menu')?.classList.add('hidden');
        });
      });

      const setN = ()=>setNormals();
      document.getElementById('btn_normals')?.addEventListener('click', setN);
      document.getElementById('btn_normals_inline')?.addEventListener('click', setN);
      document.getElementById('btn_clear_vitals')?.addEventListener('click', clearVitals);

      document.getElementById('btn_preview')?.addEventListener('click', openPreview);

      // drafts
      document.getElementById('btn_save_draft')?.addEventListener('click', ()=>saveDraft(true));
      document.getElementById('btn_restore_draft')?.addEventListener('click', restoreDraft);
      document.getElementById('btn_clear_draft')?.addEventListener('click', clearDraft);

      // autosave on change
      ['input','change'].forEach(evt=>{
        document.addEventListener(evt, (e)=>{
          if (!(e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement || e.target instanceof HTMLSelectElement)) return;
          saveDraft(false);
        }, true);
      });
      // also catch select2 select/unselect
      $(document).on('select2:select select2:unselect', ()=>saveDraft(false));

      // keyboard shortcuts
      document.addEventListener('keydown', (e)=>{
        if (!e.altKey) return;
        const k = e.key.toLowerCase();
        if (k==='1'){ e.preventDefault(); document.getElementById('cf-toggle')?.click(); }
        else if (k==='n'){ e.preventDefault(); setNormals(); }
        else if (k==='p'){ e.preventDefault(); openPreview(); }
        else if (k==='d'){ e.preventDefault(); saveDraft(true); }
        else if (k==='r'){ e.preventDefault(); restoreDraft(); }
        else if (k==='b'){ e.preventDefault(); window.__toggleBulletsFocused?.(); }
        else if (k==='s'){ e.preventDefault(); document.getElementById('submit_btn')?.click(); }
      });
    });
  })();
  </script>

  {{-- ===== Segmented history tab switcher (activates O/E by default) ===== --}}
  <script>
  (function(){
    function activate(targetSel){
      document.querySelectorAll('.hist-pane').forEach(p => {
        p.classList.toggle('hidden', ('#'+p.id) !== targetSel);
      });
      document.querySelectorAll('.hist-tab').forEach(b => {
        const active = (b.dataset.target === targetSel);
        b.classList.toggle('bg-blue-600', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('border-blue-600', active);
      });
    }

    function refreshDots(){
      document.querySelectorAll('.hist-tab').forEach(b=>{
        const sel = b.dataset.target;
        const ta  = document.querySelector(sel.replace('#hist_','#'));
        const has = !!(ta && ta.value.trim());
        b.querySelector('.tab-dot')?.classList.toggle('hidden', !has);
      });
    }

    document.addEventListener('DOMContentLoaded', ()=>{
      // click-to-switch
      document.querySelectorAll('.hist-tab').forEach(b=>{
        b.addEventListener('click', ()=> activate(b.dataset.target));
      });

      // default active: O/E if present; otherwise first
      const oeTab = document.querySelector('.hist-tab[data-target="#hist_oe"]');
      const first = oeTab || document.querySelector('.hist-tab');
      if (first) activate(first.dataset.target);

      // live dot update
      document.querySelectorAll('.hist-pane textarea').forEach(ta=>{
        ta.addEventListener('input', refreshDots);
      });
      refreshDots();
    });
  })();
  </script>
</x-app-layout>
