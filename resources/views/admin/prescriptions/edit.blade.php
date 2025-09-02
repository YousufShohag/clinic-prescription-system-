<x-app-layout>
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Edit Prescription #{{ $prescription->id }}</h2>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Fallbacks if controller didn't pass helpers --}}
        @php
            $selectedMedicinePivot = $selectedMedicinePivot ?? $prescription->medicines->keyBy('id');
            $selectedTestIds = $selectedTestIds ?? $prescription->tests->pluck('id')->toArray();
        @endphp

        <form action="{{ route('prescriptions.update', $prescription) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Doctor & Patient --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Doctor <span class="text-red-500">*</span></label>
                    <select name="doctor_id" id="doctor_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Doctor --</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" @selected($doc->id == old('doctor_id', $prescription->doctor_id))>
                                {{ $doc->name }} {{ $doc->specialization ? "({$doc->specialization})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Patient</label>
                    <select name="patient_id" id="patient_select" class="w-full border rounded px-3 py-2">
                        <option value="">-- Select existing patient --</option>
                        @foreach($patients as $pat)
                            <option value="{{ $pat->id }}" @selected($pat->id == old('patient_id', $prescription->patient_id))>
                                {{ $pat->name }} {{ $pat->phone ? "({$pat->phone})" : '' }}
                            </option>
                        @endforeach
                        <option value="__new">+ Add new patient</option>
                    </select>
                </div>
            </div>

            {{-- New patient block --}}
            <div id="new_patient_block" class="hidden bg-gray-50 p-4 rounded">
                <h3 class="text-sm font-medium mb-2">New Patient Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div><input type="text" name="new_patient[name]" placeholder="Patient name" class="w-full border rounded px-3 py-2"></div>
                    <div><input type="number" name="new_patient[age]" placeholder="Age" class="w-full border rounded px-3 py-2"></div>
                    <div>
                        <select name="new_patient[sex]" class="w-full border rounded px-3 py-2">
                            <option value="">Sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div><input type="text" name="new_patient[phone]" placeholder="Phone" class="w-full border rounded px-3 py-2"></div>
                    <div><input type="email" name="new_patient[email]" placeholder="Email" class="w-full border rounded px-3 py-2"></div>
                    <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm text-gray-700">Images</label>
                            <input type="file" name="new_patient[images][]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Documents</label>
                            <input type="file" name="new_patient[documents][]" multiple class="w-full border rounded px-3 py-2">
                        </div>
                    </div>
                    <div class="md:col-span-3"><textarea name="new_patient[notes]" placeholder="Notes (optional)" class="w-full border rounded px-3 py-2"></textarea></div>
                </div>
            </div>

            {{-- Clinical Findings --}}
            <div class="bg-gray-50 border rounded p-4">
                <h3 class="text-lg font-medium mb-3">Clinical Findings</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">O/E</label>
                    <textarea name="oe" rows="3" class="w-full border rounded px-3 py-2">{{ old('oe', $prescription->oe) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div><label class="block text-sm">BP</label><input type="text" name="bp" class="w-full border rounded px-3 py-2" value="{{ old('bp', $prescription->bp) }}"></div>
                    <div><label class="block text-sm">Pulse (bpm)</label><input type="number" name="pulse" class="w-full border rounded px-3 py-2" value="{{ old('pulse', $prescription->pulse) }}"></div>
                    <div><label class="block text-sm">Temp (°C)</label><input type="number" step="0.1" name="temperature_c" class="w-full border rounded px-3 py-2" value="{{ old('temperature_c', $prescription->temperature_c) }}"></div>
                    <div><label class="block text-sm">SpO₂ (%)</label><input type="number" name="spo2" class="w-full border rounded px-3 py-2" value="{{ old('spo2', $prescription->spo2) }}"></div>
                    <div><label class="block text-sm">RR (/min)</label><input type="number" name="respiratory_rate" class="w-full border rounded px-3 py-2" value="{{ old('respiratory_rate', $prescription->respiratory_rate) }}"></div>
                    <div><label class="block text-sm">Weight (kg)</label><input type="number" step="0.1" id="weight_kg" name="weight_kg" class="w-full border rounded px-3 py-2" value="{{ old('weight_kg', $prescription->weight_kg) }}"></div>
                    <div><label class="block text-sm">Height (cm)</label><input type="number" step="0.1" id="height_cm" name="height_cm" class="w-full border rounded px-3 py-2" value="{{ old('height_cm', $prescription->height_cm) }}"></div>
                    <div><label class="block text-sm">BMI</label><input type="text" id="bmi" name="bmi" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ old('bmi', $prescription->bmi) }}" readonly></div>
                </div>
            </div>

            {{-- Problem --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Problem Description</label>
                <textarea name="problem_description" rows="3" class="w-full border rounded px-3 py-2">{{ old('problem_description', $prescription->problem_description) }}</textarea>
            </div>

            {{-- Medicines --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-medium">Medicines</h3>
                    <input type="text" id="medicine_search" placeholder="Search medicines…" class="border rounded px-3 py-2 w-full md:w-80" autocomplete="off">
                    <button type="button" id="medicine_clear" class="px-3 py-2 border rounded">Clear</button>
                </div>

                {{-- Selected Medicines (start empty; JS moves checked items here) --}}
                <div id="medicine_selected_wrap" class="hidden">
                    <div class="text-sm text-gray-600 mb-1">Selected medicines</div>
                    <div id="medicine_selected" class="space-y-2"></div>
                </div>

                {{-- Pool: render EVERY medicine exactly once --}}
                <div>
                    <div class="text-sm text-gray-600 mb-1">Search results</div>
                    <div id="medicine_pool" class="space-y-2 max-h-60 overflow-auto">
                        @php
                            $selectedMedicineIds = $prescription->medicines->pluck('id')->all();
                            $selectedMedicinePivot = $prescription->medicines->keyBy('id');
                        @endphp
                        @foreach($medicines as $medicine)
                            @php $isSel = in_array($medicine->id, $selectedMedicineIds); @endphp
                            <div id="medrow_{{ $medicine->id }}" class="hidden flex items-center gap-3 p-2 border rounded medicine-row"
                                 data-name="{{ strtolower($medicine->name) }}" data-price="{{ $medicine->price }}" data-id="{{ $medicine->id }}" data-type="medicine">
                                <div class="flex items-center gap-2 w-2/5">
                                    <input type="checkbox" class="medicine-checkbox item-checkbox" id="med_{{ $medicine->id }}"
                                           name="medicines[{{ $medicine->id }}][selected]" value="1"
                                           data-id="{{ $medicine->id }}" data-kind="medicine" {{ $isSel ? 'checked' : '' }}>
                                    <label for="med_{{ $medicine->id }}" class="font-medium">{{ $medicine->name }}</label>
                                    <span class="text-sm text-gray-500">৳{{ $medicine->price }}</span>
                                </div>
                                <div class="flex gap-2 items-center w-3/5">
                                    <input type="text" name="medicines[{{ $medicine->id }}][duration]" placeholder="Duration"
                                           class="hidden med-input med-duration-{{ $medicine->id }} w-1/2 border rounded px-2 py-1"
                                           value="{{ $isSel ? ($selectedMedicinePivot[$medicine->id]->pivot->duration ?? '') : '' }}">
                                    <input type="text" name="medicines[{{ $medicine->id }}][times_per_day]" placeholder="Times/day"
                                           class="hidden med-input med-times-{{ $medicine->id }} w-1/2 border rounded px-2 py-1"
                                           value="{{ $isSel ? ($selectedMedicinePivot[$medicine->id]->pivot->times_per_day ?? '') : '' }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500">Start typing to see medicine results. Checked items stay above.</p>
                </div>
            </div>

            {{-- Tests --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-medium">Tests</h3>
                    <input type="text" id="test_search" placeholder="Search tests…" class="border rounded px-3 py-2 w-full md:w-80" autocomplete="off">
                    <button type="button" id="test_clear" class="px-3 py-2 border rounded">Clear</button>
                </div>

                {{-- Selected Tests (start empty; JS moves checked items here) --}}
                <div id="test_selected_wrap" class="hidden">
                    <div class="text-sm text-gray-600 mb-1">Selected tests</div>
                    <div id="test_selected" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
                </div>

                {{-- Pool: render EVERY test exactly once --}}
                <div>
                    <div class="text-sm text-gray-600 mb-1">Search results</div>
                    <div id="test_pool" class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                        @php $selectedTestIds = $prescription->tests->pluck('id')->all(); @endphp
                        @foreach($tests as $test)
                            @php $isSel = in_array($test->id, $selectedTestIds); @endphp
                            <label id="testrow_{{ $test->id }}" class="hidden flex items-center gap-2 p-2 border rounded test-row"
                                   data-name="{{ strtolower($test->name) }}" data-price="{{ $test->price ?? '' }}" data-id="{{ $test->id }}" data-type="test">
                                <input type="checkbox" class="item-checkbox" name="tests[]" value="{{ $test->id }}"
                                       data-id="{{ $test->id }}" data-kind="test" {{ $isSel ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $test->name }}</div>
                                    <div class="text-sm text-gray-500">@if(!is_null($test->price)) ৳{{ $test->price }} @endif @if($test->note) — {{ $test->note }} @endif</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500">Start typing to see test results. Checked items stay above.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Doctor Advice</label>
                <textarea name="doctor_advice" rows="3" class="w-full border rounded px-3 py-2">{{ old('doctor_advice', $prescription->doctor_advice) }}</textarea>
            </div>

            {{-- Return Date --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Return Date</label>
          <input type="date" name="return_date" value="{{ old('return_date', isset($prescription)? optional($prescription->return_date)->format('Y-m-d') : '') }}"
                class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500">The patient should revisit on this date.</p>
        </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Prescription</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- Patient toggle ---
  const patientSelect = document.getElementById('patient_select');
  const newPatientBlock = document.getElementById('new_patient_block');
  function toggleNewPatient(){ if (patientSelect && patientSelect.value==='__new') newPatientBlock.classList.remove('hidden'); else newPatientBlock.classList.add('hidden'); }
  if (patientSelect){ patientSelect.addEventListener('change', toggleNewPatient); toggleNewPatient(); }

  // --- BMI auto ---
  const weightEl = document.getElementById('weight_kg');
  const heightEl = document.getElementById('height_cm');
  const bmiEl = document.getElementById('bmi');
  function calcBMI(){
    const w = parseFloat(weightEl?.value || ''), hcm = parseFloat(heightEl?.value || '');
    if (!isFinite(w)||!isFinite(hcm)||hcm<=0){ if(bmiEl) bmiEl.value=''; return; }
    const hm = hcm/100; if(bmiEl) bmiEl.value = (w/(hm*hm)).toFixed(1);
  }
  weightEl?.addEventListener('input', calcBMI);
  heightEl?.addEventListener('input', calcBMI);

  // --- Helpers ---
  function stopEnterSubmit(inputEl){ inputEl?.addEventListener('keydown', e => { if(e.key==='Enter') e.preventDefault(); }); }
  function toggleMedInputs(id, on){
    document.querySelectorAll('.med-duration-'+id+', .med-times-'+id).forEach(i=>{
      i.classList.toggle('hidden', !on);
      if(!on) i.value='';
    });
  }

  // --- Generic picker (single-instance rows) ---
  function setupPicker({ searchInputId, clearBtnId, poolId, selectedWrapId, selectedId, rowClass, kind }){
    const searchEl = document.getElementById(searchInputId);
    const clearBtn = document.getElementById(clearBtnId);
    const pool = document.getElementById(poolId);
    const selectedWrap = document.getElementById(selectedWrapId);
    const selected = document.getElementById(selectedId);

    stopEnterSubmit(searchEl);

    function normalize(s){ return (s||'').toString().trim().toLowerCase(); }
    function matches(row,q){ if(!q) return false; const n=row.dataset.name||'', p=(row.dataset.price||'').toString(); return n.includes(q)||p.includes(q); }
    function ensureSelectedWrapVisibility(){ selectedWrap.classList.toggle('hidden', selected.children.length===0); }
    function refreshPool(){
      const q = normalize(searchEl.value);
      pool.querySelectorAll('.'+rowClass).forEach(row=>{
        const cb=row.querySelector('.item-checkbox'); const checked = cb && cb.checked;
        if(checked) row.classList.add('hidden'); else row.classList.toggle('hidden', !matches(row,q));
      });
    }
    function moveToSelected(row){ row.classList.remove('hidden'); selected.appendChild(row); ensureSelectedWrapVisibility(); }
    function moveToPool(row){ pool.appendChild(row); refreshPool(); ensureSelectedWrapVisibility(); }
    function refocus(){ setTimeout(()=>{ searchEl?.focus(); const v=searchEl?.value||''; try{ searchEl?.setSelectionRange(v.length,v.length);}catch(e){} },0); }

    // INITIAL: move any pre-checked rows from pool -> selected (so there is only ONE copy in DOM)
    pool.querySelectorAll('.'+rowClass+' .item-checkbox:checked').forEach(cb=>{
      const row = cb.closest('.'+rowClass);
      if(kind==='medicine') toggleMedInputs(cb.dataset.id, true);
      moveToSelected(row);
    });
    ensureSelectedWrapVisibility();
    refreshPool();

    // Change handling (works in both containers)
    function onChange(e){
      const cb = e.target.closest('.item-checkbox'); if(!cb) return;
      if(cb.dataset.kind !== kind) return;
      const row = cb.closest('.'+rowClass);
      if(cb.checked){ if(kind==='medicine') toggleMedInputs(cb.dataset.id,true); moveToSelected(row); }
      else { if(kind==='medicine') toggleMedInputs(cb.dataset.id,false); moveToPool(row); }
      refocus();
    }
    pool.addEventListener('change', onChange);
    selected.addEventListener('change', onChange);

    // Search + clear
    searchEl.addEventListener('input', refreshPool);
    clearBtn.addEventListener('click', ()=>{ searchEl.value=''; refreshPool(); refocus(); });
  }

  // Wire pickers
  setupPicker({
    searchInputId:'medicine_search', clearBtnId:'medicine_clear',
    poolId:'medicine_pool', selectedWrapId:'medicine_selected_wrap',
    selectedId:'medicine_selected', rowClass:'medicine-row', kind:'medicine'
  });
  setupPicker({
    searchInputId:'test_search', clearBtnId:'test_clear',
    poolId:'test_pool', selectedWrapId:'test_selected_wrap',
    selectedId:'test_selected', rowClass:'test-row', kind:'test'
  });

  // Initialize BMI from current values
  calcBMI();
});
</script>
</x-app-layout>
