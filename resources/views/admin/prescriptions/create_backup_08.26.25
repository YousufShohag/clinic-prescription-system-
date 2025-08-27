{{-- resources/views/prescriptions/create.blade.php --}}
<x-app-layout>
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

      {{-- LEFT: Clinical Findings (col-span-3) --}}
      <aside class="xl:col-span-3 space-y-6">
        <section class="border rounded-lg p-5 bg-gray-50">
          <h3 class="text-xl font-semibold mb-4">Clinical Findings</h3>

          <label class="block text-sm font-medium text-gray-700 mb-2">O/E (On Examination)</label>
          <textarea name="oe" rows="4" class="w-full border rounded px-3 py-2 mb-5"
                    placeholder="General appearance, system findings, etc.">{{ old('oe') }}</textarea>

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

      {{-- MIDDLE: Everything else (col-span-6) --}}
      <section class="xl:col-span-6 space-y-6">
        {{-- Doctor & Patient --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Doctor <span class="text-red-500">*</span></label>
            <select name="doctor_id" id="doctor_id" required class="w-full border rounded px-3 py-2">
              <option value="">-- Select Doctor --</option>
              @foreach($doctors as $doc)
                <option value="{{ $doc->id }}">{{ $doc->name }} {{ $doc->specialization ? "({$doc->specialization})" : '' }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Patient</label>
            <select name="patient_id" id="patient_select" class="w-full border rounded px-3 py-2" data-url-base="{{ url('/patients') }}">
              <option value="">-- Select existing patient --</option>
              <option value="__new">+ Add new patient</option>
              @foreach($patients as $pat)
                <option value="{{ $pat->id }}">{{ $pat->name }} {{ $pat->phone ? "({$pat->phone})" : '' }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- New patient (reveals when "__new") --}}
        <div id="new_patient_block" class="hidden bg-gray-50 border rounded p-5">
          <h3 class="text-md font-semibold mb-4">New Patient Details</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="new_patient[name]" value="{{ old('new_patient.name') }}" placeholder="Patient name" class="border rounded px-3 py-2">
            <input type="text" name="new_patient[phone]" value="{{ old('new_patient.phone') }}" placeholder="Phone" class="border rounded px-3 py-2">
            <input type="email" name="new_patient[email]" value="{{ old('new_patient.email') }}" placeholder="Email" class="border rounded px-3 py-2">
            <textarea name="new_patient[notes]" placeholder="Notes (optional)" class="md:col-span-3 border rounded px-3 py-2">{{ old('new_patient.notes') }}</textarea>
          </div>
        </div>

        {{-- Problem description --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Problem Description (brief)</label>
          <textarea name="problem_description" rows="3" class="w-full border rounded px-3 py-2">{{ old('problem_description') }}</textarea>
        </div>

        {{-- Medicines --}}
        <div class="space-y-3">
          <div class="flex flex-col md:flex-row md:items-center gap-2">
            <h3 class="text-xl font-semibold">Medicines</h3>
            <div class="flex-1"></div>
            <input type="text" id="medicine_search" placeholder="Search medicines…" class="border rounded px-3 py-2 w-full md:w-80" autocomplete="off">
            <button type="button" id="medicine_clear" class="px-3 py-2 border rounded">Clear</button>
          </div>

          <div id="medicine_selected_wrap" class="hidden">
            <div class="text-sm text-gray-600 mb-1">Selected medicines</div>
            <div id="medicine_selected" class="space-y-2"></div>
          </div>

          <div>
            <div class="text-sm text-gray-600 mb-1">Search results</div>
            <div id="medicine_pool" class="space-y-2">
              @foreach($medicines as $medicine)
                <div id="medrow_{{ $medicine->id }}" class="hidden flex flex-col md:flex-row md:items-center gap-3 p-2 border rounded medicine-row"
                     data-name="{{ strtolower($medicine->name) }}"
                     data-price="{{ $medicine->price }}"
                     data-id="{{ $medicine->id }}"
                     data-type="medicine">
                  <div class="flex items-center gap-2 md:w-2/5">
                    <input type="checkbox" id="med_{{ $medicine->id }}" name="medicines[{{ $medicine->id }}][selected]" value="1"
                           class="medicine-checkbox item-checkbox" data-id="{{ $medicine->id }}" data-kind="medicine">
                    <label for="med_{{ $medicine->id }}" class="font-medium">{{ $medicine->name }}</label>
                    <span class="text-sm text-gray-500">৳{{ $medicine->price }}</span>
                  </div>
                  <div class="flex gap-2 items-center md:w-3/5">
                    <input type="text" name="medicines[{{ $medicine->id }}][duration]" placeholder="Duration (e.g., 5 days)"
                           class="hidden med-input med-duration-{{ $medicine->id }} w-full md:w-1/2 border rounded px-2 py-1">
                    <input type="text" name="medicines[{{ $medicine->id }}][times_per_day]" placeholder="Times/day (e.g., 3x)"
                           class="hidden med-input med-times-{{ $medicine->id }} w-full md:w-1/2 border rounded px-2 py-1">
                  </div>
                </div>
              @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-1">Start typing to see medicine results. Checked items move up.</p>
          </div>
        </div>

        {{-- Tests --}}
        <div class="space-y-3">
          <div class="flex flex-col md:flex-row md:items-center gap-2">
            <h3 class="text-xl font-semibold">Tests</h3>
            <div class="flex-1"></div>
            <input type="text" id="test_search" placeholder="Search tests…" class="border rounded px-3 py-2 w-full md:w-80" autocomplete="off">
            <button type="button" id="test_clear" class="px-3 py-2 border rounded">Clear</button>
          </div>

          <div id="test_selected_wrap" class="hidden">
            <div class="text-sm text-gray-600 mb-1">Selected tests</div>
            <div id="test_selected" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
          </div>

          <div>
            <div class="text-sm text-gray-600 mb-1">Search results</div>
            <div id="test_pool" class="grid grid-cols-1 md:grid-cols-2 gap-2">
              @foreach($tests as $test)
                <label id="testrow_{{ $test->id }}" class="hidden flex items-center gap-2 p-2 border rounded test-row"
                       data-name="{{ strtolower($test->name) }}"
                       data-price="{{ $test->price ?? '' }}"
                       data-id="{{ $test->id }}"
                       data-type="test">
                  <input type="checkbox" class="item-checkbox" name="tests[]" value="{{ $test->id }}" data-id="{{ $test->id }}" data-kind="test">
                  <div class="flex-1">
                    <div class="font-medium">{{ $test->name }}</div>
                    <div class="text-sm text-gray-500">৳{{ $test->price ?? '-' }} @if($test->note) — {{ $test->note }} @endif</div>
                  </div>
                </label>
              @endforeach
            </div>
            <p class="text-xs text-gray-500 mt-1">Start typing to see test results. Checked items move up.</p>
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
          <input type="date" name="return_date" value="{{ old('return_date', isset($prescription)? optional($prescription->return_date)->format('Y-m-d') : '') }}"
                class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500">The patient should revisit on this date.</p>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
            Create Prescription
          </button>
        </div>
      </section>

      {{-- RIGHT: Previous Prescriptions (col-span-3) --}}
      <aside class="xl:col-span-3 space-y-6">
        <section id="prev_rx_panel" class="hidden border rounded-lg p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold">
              Previous Prescriptions<br><span id="prev_rx_patient_name" class="text-sm font-normal text-gray-600"></span>
            </h3>
            <span id="prev_rx_count" class="text-sm text-gray-500"></span>
          </div>

          <div id="prev_rx_loading" class="text-sm text-gray-500">Loading...</div>
          <div id="prev_rx_empty" class="hidden text-sm text-gray-500">No previous prescriptions.</div>
          <div id="prev_rx_error" class="hidden text-sm text-red-600">Could not load prescriptions.</div>

          <div id="prev_rx_list" class="hidden">
            <ul id="prev_rx_ul" class="space-y-2">
              {{-- populated via JS --}}
            </ul>
          </div>
        </section>
      </aside>
    </form>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    // Toggle new patient
    const patientSelect   = document.getElementById('patient_select');
    const newPatientBlock = document.getElementById('new_patient_block');
    function toggleNewPatient(){
      if (!patientSelect) return;
      if (patientSelect.value === '__new') newPatientBlock.classList.remove('hidden');
      else newPatientBlock.classList.add('hidden');
    }
    patientSelect?.addEventListener('change', toggleNewPatient);
    toggleNewPatient();

    // BMI calc
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

    // ===== Previous Prescriptions (RIGHT panel) =====
    const prevPanel   = document.getElementById('prev_rx_panel');
    const prevName    = document.getElementById('prev_rx_patient_name');
    const prevCount   = document.getElementById('prev_rx_count');
    const prevLoading = document.getElementById('prev_rx_loading');
    const prevEmpty   = document.getElementById('prev_rx_empty');
    const prevError   = document.getElementById('prev_rx_error');
    const prevList    = document.getElementById('prev_rx_list');
    const prevUL      = document.getElementById('prev_rx_ul');

    async function loadPrevRx(patientId, label) {
      if (!patientId || patientId === '__new') { prevPanel.classList.add('hidden'); return; }

      const base = patientSelect.dataset.urlBase; // e.g., /patients
      const url  = `${base}/${patientId}/prescriptions`;

      // reset UI
      prevPanel.classList.remove('hidden');
      prevName.textContent = label || '';
      prevCount.textContent = '';
      prevLoading.classList.remove('hidden');
      prevEmpty.classList.add('hidden');
      prevError.classList.add('hidden');
      prevList.classList.add('hidden');
      prevUL.innerHTML = '';

      try {
        const res = await fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          credentials: 'same-origin'
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const payload = await res.json();
        const items = payload.data || [];

        prevLoading.classList.add('hidden');
        prevCount.textContent = `${items.length} item${items.length===1?'':'s'}`;

        if (!items.length) { prevEmpty.classList.remove('hidden'); return; }

        // Compact list: #ID • Date • Doctor • View
        prevUL.innerHTML = items.map(p => `
          <li class="border rounded p-2">
            <div class="flex items-center justify-between gap-2">
              <div class="text-sm">
                <span class="font-semibold">#${p.id}</span>
                <span class="text-gray-500">• ${p.date ?? '-'}</span><br>
                <span class="text-gray-700">${p.doctor ?? '—'}</span>
              </div>
              <a href="${p.show_url}" class="text-blue-600 hover:underline text-sm" target="_blank" rel="noopener">View</a>
            </div>
          </li>
        `).join('');
        prevList.classList.remove('hidden');

      } catch (e) {
        prevLoading.classList.add('hidden');
        prevError.classList.remove('hidden');
        console.error(e);
      }
    }

    // Hook select change
    patientSelect?.addEventListener('change', function(){
      const id = this.value;
      const label = this.options[this.selectedIndex]?.text || '';
      loadPrevRx(id, label);
    });

    // Preload if old value exists
    @if(old('patient_id'))
      loadPrevRx(@json(old('patient_id')), '');
    @endif

    // ===== Pickers (same logic, no inner scroll) =====
    function stopEnterSubmit(el){ el?.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); }); }
    function toggleMedInputs(id, on){
      document.querySelectorAll('.med-duration-'+id+', .med-times-'+id).forEach(i => {
        if(on){ i.classList.remove('hidden'); } else { i.classList.add('hidden'); i.value=''; }
      });
    }
    function setupPicker({ searchInputId, clearBtnId, poolId, selectedWrapId, selectedId, rowClass, kind }) {
      const searchEl = document.getElementById(searchInputId);
      const clearBtn = document.getElementById(clearBtnId);
      const pool     = document.getElementById(poolId);
      const selectedW= document.getElementById(selectedWrapId);
      const selected = document.getElementById(selectedId);

      stopEnterSubmit(searchEl);
      const norm = s => (s||'').toString().trim().toLowerCase();
      const match= (row,q)=> q && ((row.dataset.name||'').includes(q) || (row.dataset.price||'').toString().includes(q));
      const ensureSel = ()=> selectedW.classList.toggle('hidden', selected.children.length===0);
      const refresh = ()=>{
        const q = norm(searchEl.value);
        pool.querySelectorAll('.'+rowClass).forEach(row=>{
          const cb = row.querySelector('.item-checkbox');
          const on = cb && cb.checked;
          if (on) row.classList.add('hidden'); else row.classList.toggle('hidden', !match(row,q));
        });
      };
      const moveSel = row => { row.classList.remove('hidden'); selected.appendChild(row); ensureSel(); };
      const movePool= row => { pool.appendChild(row); refresh(); ensureSel(); };
      const refocus = ()=> setTimeout(()=>{ if(!searchEl) return; const v=searchEl.value; searchEl.focus(); try{searchEl.setSelectionRange(v.length,v.length);}catch(e){} },0);

      function onChange(e){
        const cb = e.target.closest('.item-checkbox'); if(!cb) return; if (cb.dataset.kind!==kind) return;
        const id = cb.dataset.id; const rowId=(kind==='medicine'?'medrow_':'testrow_')+id; const row=document.getElementById(rowId); if(!row) return;
        if (cb.checked){ if(kind==='medicine') toggleMedInputs(id,true); moveSel(row); }
        else { if(kind==='medicine') toggleMedInputs(id,false); movePool(row); }
        refocus();
      }
      pool.addEventListener('change', onChange);
      selected.addEventListener('change', onChange);
      searchEl.addEventListener('input', refresh);
      clearBtn.addEventListener('click', ()=>{ searchEl.value=''; refresh(); refocus(); });

      refresh(); ensureSel();
    }

    setupPicker({ searchInputId:'medicine_search', clearBtnId:'medicine_clear', poolId:'medicine_pool', selectedWrapId:'medicine_selected_wrap', selectedId:'medicine_selected', rowClass:'medicine-row', kind:'medicine' });
    setupPicker({ searchInputId:'test_search',      clearBtnId:'test_clear',      poolId:'test_pool',      selectedWrapId:'test_selected_wrap',      selectedId:'test_selected',      rowClass:'test-row',      kind:'test' });
  });
  </script>
</x-app-layout>
