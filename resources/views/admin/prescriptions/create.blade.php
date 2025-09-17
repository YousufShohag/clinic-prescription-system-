{{-- resources/views/prescriptions/create.blade.php --}}
<x-app-layout>
  {{-- Select2 assets (load once) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<style>
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<style>
  /* Preview-only helpers (won't affect your show blade) */
  #rx_preview_canvas .rx-card { border:1px solid #e5e7eb; border-radius: .5rem; }
  #rx_preview_canvas .rx-left { border-right:1px solid #e5e7eb; }
  #rx_preview_canvas .rx-section-title { font-weight:600; }
  #rx_preview_canvas .rx-dot { display:inline-block; width:.4rem; }
</style>



{{-- <style>
/* -------- Doctor-friendly theme (keeps your Tailwind utilities) -------- */
:root{
  --df-accent:#0ea5e9;       /* sky-500 */
  --df-ink:#0f172a;          /* slate-900 */
  --df-muted:#64748b;        /* slate-500 */
  --df-border:#e5e7eb;       /* gray-200 */
  --df-soft:#f8fafc;         /* slate-50 */
}

/* Card shells */
section.border.rounded-lg{
  border-color: var(--df-border) !important;
  border-radius: 12px !important;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 1px 0 rgba(15,23,42,.03);
}

/* Header buttons */
section.border.rounded-lg > button{
  position: relative;
  padding: .85rem 1rem;
  background: var(--df-soft);
  font-weight: 600;
  color: var(--df-ink);
  border-bottom: 1px solid var(--df-border);
}

/* Accent stripe on header */
section.border.rounded-lg > button::before{
  content:"";
  position:absolute; inset:0 auto 0 0;
  width:4px; background:var(--df-accent); opacity:.7;
  border-top-left-radius:12px; border-bottom-left-radius:12px;
}

/* Chevron rotation on expand */
section.border.rounded-lg > button[aria-expanded="true"] svg{
  transform: rotate(180deg);
}

/* Count badges (auto via data-count attr set by JS) */
#cf-toggle,#hist-toggle,#cc-toggle,#med-toggle,#test-toggle,#advice-toggle{ padding-right: 2.25rem; }
#cf-toggle[data-count]::after,
#hist-toggle[data-count]::after,
#cc-toggle[data-count]::after,
#med-toggle[data-count]::after,
#test-toggle[data-count]::after,
#advice-toggle[data-count]::after{
  content: attr(data-count);
  position:absolute; right:.8rem; top:50%; transform: translateY(-50%);
  min-width: 1.25rem; height: 1.25rem; line-height: 1.25rem; text-align:center;
  font-size: .75rem; color:#0b3b53; background:#e0f2fe; border:1px solid #bae6fd; border-radius:999px;
}

/* Section bodies */
section.border.rounded-lg > .border-t{ border-color: var(--df-border) !important; }

/* Inputs: friendlier focus ring & radius */
input[type="text"], input[type="number"], input[type="date"], input[type="email"],
select, textarea{
  border-color: var(--df-border);
  border-radius: 10px;
}
input:focus, select:focus, textarea:focus{
  outline: none !important;
  box-shadow: 0 0 0 3px rgba(14,165,233,.25);
  border-color:#7dd3fc;
}

/* Little refinements */
label.block.text-sm{ color: #334155; } /* slate-700 */
.text-sm.text-gray-600{ color: var(--df-muted) !important; }

/* Medicine rows: sightline + active highlight is already added by your JS */
#medicine_selected [data-id]{
  border-color: var(--df-border);
  border-radius: 10px;
}
#medicine_selected [data-id].ring{
  box-shadow: 0 0 0 3px rgba(14,165,233,.25);
}

/* Live preview card tweaks */
#rx_live_preview_card .bg-gray-50{ background: var(--df-soft) !important; }
#rx_preview_canvas{ line-height: 1.65; }
#rx_preview_canvas .font-semibold{ color: #111827; }

/* Print: cleaner output (hides buttons/controls) */
@media print{
  #tpl_btn, #btn_normals, #btn_preview, #btn_save_draft, #btn_restore_draft, #btn_clear_draft,
  #rx_preview_print, #rx_preview_toggle, .select2-container{ display:none !important; }
  #rx_preview_canvas{ max-width: none !important; }
}
</style> --}}


  <div class="w-full min-h-screen bg-white p-6 md:p-8">
    <div class="flex items-start justify-between gap-2">
      <h2 class="text-3xl font-semibold">New Prescription</h2>

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

    {{-- <p class="text-xs text-gray-500 mt-1">
      Shortcuts: <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">1</kbd> CF Panel •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">N</kbd> Normal Vitals •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">B</kbd> Toggle bullets •
      <kbd class="px-1 border rounded">Alt</kbd>+<kbd class="px-1 border rounded">S</kbd> Submit
    </p> --}}

    @if($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('prescriptions.store') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-4" enctype="multipart/form-data">
      @csrf

      {{-- LEFT --}}
      <aside class="xl:col-span-3 space-y-6">
        {{-- ================= Clinical Findings (collapsible) ================= --}}
        <section id="cf-card" class="border rounded-lg">
          <button type="button"
                  id="cf-toggle"
                  class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
                  aria-controls="cf-body"
                  aria-expanded="false">
            <span class="text-sm font-semibold">Clinical Findings</span>
            <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
            </svg>
          </button>

          <div id="cf-body" class="p-5 border-t hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700">BP</label>
                <input type="text" name="bp" id="bp" value="{{ old('bp') }}" placeholder="120/80" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Pulse (bpm)</label>
                <input type="number" step="1" min="0" name="pulse" id="pulse" value="{{ old('pulse') }}" placeholder="72" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Temperature (°C)</label>
                <input type="number" step="0.1" name="temperature_c" id="temperature_c" value="{{ old('temperature_c') }}" placeholder="37.0" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">SpO₂ (%)</label>
                <input type="number" step="1" min="0" max="100" name="spo2" id="spo2" value="{{ old('spo2') }}" placeholder="98" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Respiratory Rate (/min)</label>
                <input type="number" step="1" min="0" name="respiratory_rate" id="respiratory_rate" value="{{ old('respiratory_rate') }}" placeholder="16" class="w-full border rounded px-3 py-2">
              </div>
              <div>
                <label class="block text-sm text-gray-700">Weight (kg)</label>
                <input type="number" step="0.1" min="0" id="weight_kg" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="70.0" class="w-full border rounded px-3 py-2">
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

            <div class="mt-3">
              <button type="button" id="btn_normals_inline" class="px-2 py-1 text-xs border rounded hover:bg-gray-50">Set Normal Vitals</button>
              <button type="button" id="btn_clear_vitals" class="px-2 py-1 text-xs border rounded hover:bg-gray-50">Clear Vitals</button>
            </div>
          </div>
        </section>

        {{-- ===== Segmented History panel ===== --}}
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

     <!-- ===== History (O/E, P/H, D/H, M/H, OH, P/A/E, DX, Previous Investigation, A/H, Special Note, Referred To) – Collapsible ===== -->
<section class="border rounded-lg" id="hist-card">
  <button type="button"
          class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
          aria-controls="hist-body" aria-expanded="false" id="hist-toggle">
    <span class="text-sm font-semibold">History (O/E, P/H, D/H, …)</span>
    <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
    </svg>
  </button>

  <div id="hist-body" class="p-0 border-t hidden">
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

    <div class="p-4">
      @foreach($histTabs as $key => $label)
        <div id="hist_{{ $key }}" class="hist-pane hidden">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
            <div class="flex items-center gap-2">
              <!-- (Optional) Suggestions menu is auto-added by your existing script -->
              <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                      data-bullets-toggle="#{{ $key }}">• Bullets: OFF</button>
              <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                      data-bullets-clear="#{{ $key }}">Clear</button>
            </div>
          </div>

          {{-- <div class="flex flex-wrap gap-2 mb-2" data-chip-row data-target="#{{ $key }}"></div> --}}

          <textarea id="{{ $key }}" name="{{ $key }}" rows="3"
                    class="w-full border rounded px-3 py-2"
                    data-bullets>{{ old($key) }}</textarea>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section id="cc-card" class="border rounded-lg mt-4">
  <!-- Header -->
  <button type="button"
          id="cc-toggle"
          class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
          aria-controls="cc-body"
          aria-expanded="true">
    <span class="text-sm font-semibold">Chief Complain (C/C)</span>
    <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path fill-rule="evenodd"
            d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z"
            clip-rule="evenodd" />
    </svg>
  </button>

  <!-- Body -->
  <div id="cc-body" class="p-4 border-t">
    <div class="flex items-center justify-between mb-2">
      <label for="problem_description" class="block text-sm font-medium text-gray-700">Chief Complain (C/C)</label>

      <div class="flex items-center gap-2 relative">
        <!-- Suggestions button + menu (unchanged IDs) -->
        <div class="relative">
          <button type="button" id="btn_cc_suggestions"
                  class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
            Suggestions ▾
          </button>
          <div id="cc_sugg_menu"
               class="hidden absolute right-0 z-30 mt-1 w-80 bg-white border rounded shadow p-2 max-h-64 overflow-auto">
            <div class="text-xs text-gray-500 px-1 mb-1">Click to insert</div>
            <div id="cc_sugg_list" class="flex flex-wrap gap-1"></div>
          </div>
        </div>

        <button type="button"
                class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                data-bullets-toggle="#problem_description">• Bullets: OFF</button>
        <button type="button"
                class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                data-bullets-clear="#problem_description">Clear</button>
      </div>
    </div>

    <textarea id="problem_description"
              name="problem_description"
              rows="3"
              class="w-full border rounded px-2 py-2"
              data-bullets>{{ old('problem_description') }}</textarea>
  </div>
</section>

<section id="med-card" class="border rounded-lg">
  <!-- Header -->
  <button type="button"
          id="med-toggle"
          class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
          aria-controls="med-body"
          aria-expanded="true">
    <span class="text-sm font-semibold">Medicines</span>
    <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path fill-rule="evenodd"
            d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z"
            clip-rule="evenodd" />
    </svg>
  </button>

  <!-- Body -->
  <div id="med-body" class="p-4 border-t">
    <!-- START: MED BODY (your original content) -->
    <div class="space-y-3">
      <div class="flex flex-col md:flex-row md:items-center gap-2">
        {{-- <h3 class="text-sm font-semibold">Medicines</h3> --}}
        <div class="flex-1"></div>
        <select id="medicine_picker" multiple class="w-full md:w-96"></select>
        <button type="button" id="medicine_clear" class="px-1 py-1 border rounded">Clear</button>
      </div>

      <!-- Central times/duration suggestions (applies to the active medicine row) -->
      <div id="med_central_suggestions" class="hidden border rounded p-3">
        <div class="mt-1">
          <div class="relative">
            <button type="button" id="med_sugg_prev"
                    class="hidden md:flex items-center justify-center w-7 h-7 rounded-full border bg-white shadow absolute left-0 top-1/2 -translate-y-1/2 z-10 disabled:opacity-40"
                    aria-label="Scroll left">‹</button>

            <div id="med_sugg_scroller"
                 class="flex items-center gap-2 flex-nowrap overflow-x-auto whitespace-nowrap no-scrollbar py-1 px-8">
              <div id="med_sugg_times" class="inline-flex gap-1 shrink-0"></div>
              <span class="text-gray-300 shrink-0">|</span>
              <div id="med_sugg_duration" class="inline-flex gap-1 shrink-0"></div>
            </div>

            <button type="button" id="med_sugg_next"
                    class="hidden md:flex items-center justify-center w-7 h-7 rounded-full border bg-white shadow absolute right-0 top-1/2 -translate-y-1/2 z-10 disabled:opacity-40"
                    aria-label="Scroll right">›</button>
          </div>
        </div>

        <div id="medicine_selected_wrap" class="hidden">
          <div class="text-sm text-gray-600 mb-1">Selected medicines</div>
          <div id="medicine_selected" class="space-y-2"></div>
        </div>
      </div>
    </div>
    <!-- END: MED BODY -->
  </div>
</section>


<section id="test-card" class="border rounded-lg">
  <!-- Header -->
  <button type="button"
          id="test-toggle"
          class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
          aria-controls="test-body"
          aria-expanded="true">
    <span class="text-sm font-semibold">Investigations</span>
    <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path fill-rule="evenodd"
            d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z"
            clip-rule="evenodd" />
    </svg>
  </button>

  <!-- Body -->
  <div id="test-body" class="p-4 border-t">
    <!-- START: TEST BODY (your original content) -->
    <div class="space-y-3">
      <div class="flex flex-col md:flex-row md:items-center gap-2">
        {{-- <h3 class="text-xl font-semibold">Tests</h3> --}}
        <div class="flex-1"></div>
        <select id="test_picker" multiple class="w-full md:w-96"></select>
        <button type="button" id="test_clear" class="px-1 py-1 border rounded">Clear</button>
      </div>

      <div id="test_selected_wrap" class="hidden">
        <div class="text-sm text-gray-600 mb-1">Selected tests</div>
        <div id="test_selected" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
      </div>
    </div>
    <!-- END: TEST BODY -->
  </div>
</section>
      </aside>


      

      {{-- MIDDLE --}}
      <section class="xl:col-span-6 space-y-6">
        {{-- Doctor & Patient --}}
        <div class="grid grid-cols-1 md:grid-cols-2 ">
          <div class="hidden">
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

          <div class="">
            <label class="block text-sm font-medium text-gray-700">Patient</label>
            {{-- <select
              name="patient_id"
              id="patient_select"
              class="w-full border rounded px-3 py-2"
              data-search-url="{{ route('patients.search') }}"
              data-history-url-template="{{ route('patients.history', ['patient' => '__ID__']) }}">
              <option value="">-- Select existing patient --</option>
              <option value="__new">+ Add new patient</option>
            </select> --}}
            <select
              name="patient_id"
              id="patient_select"
              class="w-full border rounded px-3 py-2"
              data-search-url="{{ route('patients.search') }}"
              data-history-url-template="{{ route('patients.history', ['patient' => '__ID__']) }}"
              data-show-url-template="{{ route('patients.show', ['patient' => '__ID__']) }}" >
              <option value="">-- Select existing patient --</option>
              <option value="__new">+ Add new patient</option>
            </select> 
            
          </div>
          <div id="patient_age_display" class="mt-6 text-sm text-gray-600" style="padding-left: 10px;"> </div>
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

            <select name="new_patient[blood_group]" class="border rounded px-3 py-2">
              <option value="">Blood Group</option>
              @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                <option value="{{ $bg }}" @selected(old('new_patient.blood_group')===$bg)>{{ $bg }}</option>
              @endforeach
            </select>
            <input type="text" name="new_patient[guardian_name]" value="{{ old('new_patient.guardian_name') }}" placeholder="Guardian name" class="border rounded px-3 py-2">

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


        <!-- ===== Live Rx Preview ===== -->
        <section id="rx_live_preview_card" class=" rounded-lg overflow-hidden">
          {{-- <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
            <h3 class="text-sm font-semibold">Prescription Preview (Live)</h3>
            <div class="flex gap-2">
              <button type="button" id="rx_preview_print" class="px-3 py-1 text-sm border rounded hover:bg-gray-100">
                Print
              </button>
              <button type="button" id="rx_preview_toggle" class="px-3 py-1 text-sm border rounded hover:bg-gray-100">
                Collapse
              </button>
            </div>
          </div> --}}

          <div id="rx_preview_body" class="p-2 bg-white">
            <!-- Rendered via JS -->
            <div id="rx_preview_canvas" class="max-w-[680px] mx-auto text-sm leading-6"></div>
          </div>
        </section>





        {{-- Medicines (Select2 AJAX) --}}
        <div class="space-y-3">
          

          <!-- Central times/duration suggestions (applies to the active medicine row) -->
          <div id="med_central_suggestions" class="hidden border rounded p-3">
            {{-- <div class="flex items-center justify-between gap-2">
              <div class="font-medium">
                Suggestions
                <span class="text-gray-500">(active medicine: <span id="med_active_label">none</span>)</span>
              </div>
            </div> --}}
         <!-- Central times/duration suggestions (applies to the active medicine row) -->
         <div class="mt-1">
          
        </div>
            {{-- <div class="mt-1">
              <div class="text-xs text-gray-600 mb-1">Duration</div>
              <div id="med_sugg_duration" class="flex flex-wrap gap-1"></div>
            </div> --}}
          {{-- <pre id="med_debug" class="text-xs text-gray-500 bg-gray-50 p-2 rounded border"></pre> --}}
        </div>
        </div>
        
      </section>

      {{-- RIGHT (optional area) --}}
      <aside class="xl:col-span-3 space-y-6">
        {{-- Advice + Submit --}}
        <!-- ===== Doctor Advice (collapsible) ===== -->
<section id="advice-card" class="border rounded-lg">
  <button type="button"
          id="advice-toggle"
          class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100"
          aria-controls="advice-body"
          aria-expanded="false">
    <span class="text-sm font-semibold">Doctor Advice</span>
    <svg class="w-5 h-5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.232l3.71-3.001a.75.75 0 11.94 1.17l-4.2 3.4a.75.75 0 01-.94 0l-4.2-3.4a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
    </svg>
  </button>

  <div id="advice-body" class="p-5 border-t hidden">
    <div class="flex items-center justify-between mb-2">
      {{-- <label class="block text-sm font-medium text-gray-700">Doctor Advice</label> --}}
      <div class="flex items-center gap-2 relative">
        <!-- Suggestions button + menu (unchanged IDs so your existing script keeps working) -->
        <div class="relative">
          <button type="button" id="btn_advice_suggestions"
                  class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
            Suggestions ▾
          </button>
          <div id="advice_sugg_menu"
               class="hidden absolute right-0 z-30 mt-1 w-72 bg-white border rounded shadow p-2 max-h-64 overflow-auto">
            <div class="text-xs text-gray-500 px-1 mb-1">Click to insert</div>
            <div id="advice_sugg_list" class="flex flex-wrap gap-1"></div>
          </div>
        </div>

        <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                data-bullets-toggle="#doctor_advice">• Bullets: OFF</button>
        <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"
                data-bullets-clear="#doctor_advice">Clear</button>
      </div>
    </div>

    <textarea id="doctor_advice" name="doctor_advice" rows="5"
              class="w-full border rounded px-3 py-2" data-bullets>{{ old('doctor_advice') }}</textarea>
  </div>
</section>


        {{-- Return Date --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Return Date</label>
          <input type="date" name="return_date" value="{{ old('return_date', isset($prescription)? optional($prescription->return_date)->format('Y-m-d') : '') }}" class="w-full border rounded px-3 py-2">
          <p class="text-xs text-gray-500">The patient should revisit on this date.</p>
        </div>

        <div class="flex justify-end">
          <button id="submit_btn" type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700" title="Alt+S">
            Create Prescription
          </button>
        </div>

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
          <div id="prev_rx_more_wrap" class="hidden mt-2 flex items-center justify-end">
            <button type="button" id="prev_rx_toggle" class="text-blue-600 text-sm hover:underline">
              See more
            </button>
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

      // ✅ Robust normalization (array | {results} | {data})
      processResults: (data, params) => {
        params.page = params.page || 1;

        const base = [
          { id: '', text: '-- Select existing patient --' },
          { id: '__new', text: '+ Add new patient' }
        ];

        const arr =
          Array.isArray(data) ? data :
          (Array.isArray(data?.results) ? data.results :
          (Array.isArray(data?.data) ? data.data : []));

        // Map to Select2-friendly shape while preserving extra fields we use in templates
        const mapped = arr.map(it => ({
          id: it.id,
          text: it.text ?? it.name ?? `${(it.first_name ?? '')} ${(it.last_name ?? '')}`.trim(),
          name: it.name ?? it.text ?? null,
          dob: it.dob ?? it.date_of_birth ?? null,
          age: it.age ?? null,
          sex: it.sex ?? it.gender ?? null,
          phone: it.phone ?? it.mobile ?? null
        }));

        const more =
          !Array.isArray(data) && (
            !!data?.pagination?.more ||
            (!!data?.meta?.has_more) // common alt
          );

        return { results: base.concat(mapped), pagination: { more } };
      },

      // ✅ Surface AJAX errors instead of silently failing
      transport: function (params, success, failure) {
        const req = $.ajax(params);
        req.then(success);
        req.fail(function (xhr) {
          console.error('Patient search AJAX error:', xhr.status, xhr.responseText);
          alert('Patient search failed (' + xhr.status + '). Check console for details.');
          failure(xhr);
        });
        return req;
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

  // Show/hide new patient block + little age/sex banner
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
});
</script>


  {{-- ===== Patient History: load into right panel on selection ===== --}}
<script>
  $(function () {
    const $patient  = $('#patient_select');
    const histTpl   = $patient.data('history-url-template') || "{{ route('patients.history', ['patient' => '__ID__']) }}";

    const $panel    = $('#prev_rx_panel');
    const $name     = $('#prev_rx_patient_name');
    const $count    = $('#prev_rx_count');
    const $loading  = $('#prev_rx_loading');
    const $empty    = $('#prev_rx_empty');
    const $error    = $('#prev_rx_error');
    const $listWrap = $('#prev_rx_list');
    const $ul       = $('#prev_rx_ul');

    const $moreWrap = $('#prev_rx_more_wrap');
    const $toggle   = $('#prev_rx_toggle');

    const FIRST_LIMIT = 3;
    let currentPatientId = null;
    let currentPatientLabel = '';
    let expanded = false;        // are we showing “all”?
    let totalAvailable = 0;      // total prescriptions for patient

    const buildUrl = (tpl, id) => (tpl || '').replace('__ID__', encodeURIComponent(id));

    function showPanel(){ $panel.removeClass('hidden'); }
    function hidePanel(){ $panel.addClass('hidden'); }
    function stateLoading(){ $loading.removeClass('hidden'); $empty.addClass('hidden'); $error.addClass('hidden'); $listWrap.addClass('hidden'); $moreWrap.addClass('hidden'); }
    function stateEmpty(){ $loading.addClass('hidden'); $empty.removeClass('hidden'); $error.addClass('hidden'); $listWrap.addClass('hidden'); $moreWrap.addClass('hidden'); }
    function stateError(msg){ $loading.addClass('hidden'); $empty.addClass('hidden'); $error.removeClass('hidden').text(msg || 'Could not load prescriptions.'); $listWrap.addClass('hidden'); $moreWrap.addClass('hidden'); }
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

    function loadHistory(id, label, limit) {
      if (!id) return hidePanel();
      const url = buildUrl(histTpl, id);

      currentPatientId    = id;
      currentPatientLabel = label || '';
      expanded            = !!(limit && limit > FIRST_LIMIT);

      showPanel();
      stateLoading();
      $name.text(currentPatientLabel);

      $.ajax({
        url,
        method: 'GET',
        dataType: 'json',
        data: { limit: limit || FIRST_LIMIT }
      })
      .done(res => {
        const items = res.items || [];
        const total = res.count || res.total || items.length; // fallback if API doesn’t return total
        totalAvailable = total;

        $count.text(total + ' total');

        if (items.length === 0) return stateEmpty();

        renderItems(items);
        stateList();

        // Show toggle only if there’s more than FIRST_LIMIT
        if (total > FIRST_LIMIT) {
          $moreWrap.removeClass('hidden');
          $toggle.text(expanded ? 'Minimize' : 'See more');
        } else {
          $moreWrap.addClass('hidden');
        }
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

    // Toggle button: expand to ALL, or minimize back to 3
    $toggle.on('click', function(){
      if (!currentPatientId) return;
      if (!expanded) {
        // Show all (use totalAvailable if known, else a big number)
        loadHistory(currentPatientId, currentPatientLabel, Math.max(totalAvailable || 0, 200));
      } else {
        // Minimize back to 3
        loadHistory(currentPatientId, currentPatientLabel, FIRST_LIMIT);
      }
    });

    // When a patient is selected: load top 3
    $patient.on('select2:select', (e) => {
      const it = e.params.data;
      if (!it || !it.id || it.id === '__new') return hidePanel();
      loadHistory(it.id, it.text || it.name, FIRST_LIMIT);
    });

    $patient.on('change', function(){
      const val = $(this).val();
      if (!val || val === '__new') hidePanel();
    });
  });
</script>



  {{-- ===== Select2: Medicine picker (AJAX) + TIMES/DURATION CHIP AUTOFILL ===== --}}
    <script>
    $(function () {
      
      const $picker   = $('#medicine_picker');
      const $selWrap  = $('#medicine_selected_wrap');
      const $selList  = $('#medicine_selected');
      const $clearBtn = $('#medicine_clear');
      const $debug    = $('#med_debug');

      // Central panel
      const $panel        = $('#med_central_suggestions');
      const $label        = $('#med_active_label');
      const $timesWrap    = $('#med_sugg_times');
      const $durWrap      = $('#med_sugg_duration');
      const $clearActive  = $('#med_sugg_clear');

      let activeRowId = null;

      const AJAX_URL = "{{ route('medicines.search') }}";
      const COMMON_TIMES    = ['১+১+১','১+০+১','১+১+১','০+০+১','১+১+০','১+০+০'];
      const COMMON_DURATION = ['৩ দিন','৫ দিন','৭ দিন','১০ দিন','১৪ দিন','৩০ দিন'];

      // Centralized suggestions per generic
      const MED_SUGG = {
        // Antibiotics
        amoxicillin:   { times: ['১+০+১'],    duration: ['৫ দিন','৭ দিন'] },
        azithromycin:  { times: ['১+১+১'],        duration: ['৩ দিন'] },
        cefixime:      { times: ['১+০+১'],    duration: ['৫ দিন','৭ দিন'] },
        cefuroxime:    { times: ['১+০+১'],    duration: ['৫ দিন','৭ দিন'] },
        // Analgesic/antipyretic
        paracetamol:   { times: ['০+০+১','১+১+০','১+০+০'], duration: ['৩ দিন','৫ দিন'] },
        ibuprofen:     { times: ['০+০+১'],    duration: ['৩ দিন'] },
        // PPI
        omeprazole:    { times: ['১+১+১'],        duration: ['১৪ দিন','28 days'] },
        esomeprazole:  { times: ['১+১+১'],        duration: ['১৪ দিন','28 days'] },
        pantoprazole:  { times: ['১+১+১'],        duration: ['১৪ দিন','28 days'] },
        rabeprazole:   { times: ['১+১+১'],        duration: ['১৪ দিন','28 days'] },
        // Antihistamines
        cetirizine:    { times: ['১+১+১'],        duration: ['৭ দিন','১৪ দিন'] },
        loratadine:    { times: ['১+১+১'],        duration: ['৭ দিন'] },
        // Chronic
        metformin:     { times: ['১+০+১'],    duration: ['৩০ দিন'] },
        glimepiride:   { times: ['১+১+১'],        duration: ['৩০ দিন'] },
        amlodipine:    { times: ['১+১+১'],        duration: ['৩০ দিন'] },
        losartan:      { times: ['১+১+১'],        duration: ['৩০ দিন'] },
      };

      const uniq = arr => [...new Set(arr.filter(Boolean))];

      function ensureWrap(){ $selWrap.toggleClass('hidden', $selList.children().length === 0); }

      function keyFromItem(item){
        const g = (item?.generic || '').toString().toLowerCase().trim();
        if (g) return g;
        return (item?.name || item?.text || '').toString().split(/\s+/)[0].toLowerCase();
      }

      function setActiveRow(id){
        activeRowId = id || null;

        // Row highlight
        $selList.find('[data-id]').removeClass('ring ring-blue-500 ring-1');
        if (activeRowId){
          $selList.find(`[data-id="${activeRowId}"]`).addClass('ring ring-blue-500 ring-1');
        }

        // Update central suggestions
        updateCentralSuggestions();
      }

      function getActiveRow(){
        if (!activeRowId) return $();
        const $row = $selList.find(`[data-id="${activeRowId}"]`);
        return $row.length ? $row : $();
      }

      function renderChips($where, items, onClick){
        $where.empty();
        items.forEach(text=>{
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
          btn.textContent = text;
          btn.addEventListener('click', onClick.bind(null, text));
          $where.append(btn);
        });
      }

      function updateCentralSuggestions(){
        const $row = getActiveRow();
        if (!$row.length) { $panel.addClass('hidden'); $label.text('none'); $timesWrap.empty(); $durWrap.empty(); return; }

        const key    = ($row.data('key') || '').toString();
        const pretty = ($row.data('display') || '').toString();

        const spec = MED_SUGG[key] || { times: [], duration: [] };
        const times = uniq([...(spec.times||[]), ...COMMON_TIMES]);
        const durs  = uniq([...(spec.duration||[]), ...COMMON_DURATION]);

        $label.text(pretty || '—');
        $panel.removeClass('hidden');

        renderChips($timesWrap, times, (text)=>{
          const $r = getActiveRow();
          if (!$r.length) return flashPanel();
          $r.find('[data-med-times]').val(text).trigger('input').focus();
        });

        renderChips($durWrap, durs, (text)=>{
          const $r = getActiveRow();
          if (!$r.length) return flashPanel();
          $r.find('[data-med-dur]').val(text).trigger('input').focus();
        });
      }

      function flashPanel(){
        $panel.addClass('ring ring-red-400 ring-1');
        setTimeout(()=> $panel.removeClass('ring ring-red-400 ring-1'), 350);
      }

      $clearActive.on('click', function(){
        setActiveRow(null);
      });

      function selectedRow(item) {
        const id       = item.id;
        const timesKey = `medicines[${id}][times_per_day]`;
        const durKey   = `medicines[${id}][duration]`;
        const key      = keyFromItem(item);
        // put near the top of the file (once)
        const TYPE_MAP = {
          tablet: 'Tab',
          tab: 'Tab',
          capsule: 'Cap',
          cap: 'Cap',
          syrup: 'Syr',
          suspension: 'Susp',
          drop: 'Drop',
          injection: 'Inj',
          inj: 'Inj',
          cream: 'Cr',
          ointment: 'Oint',
          gel: 'Gel',
        };

// inside selectedRow(item):
      const typeLabel =
        (item.type_prefix || TYPE_MAP[(item.type || '').toLowerCase()] || (item.type || '')).replace(/\.$/, '');

        // const display  = [
        //   (item.type ?? item.type ?? ''),
        //   (item.name ?? item.text ?? ''),
        //   (item.generic ? ' — ' + item.generic : ''),
        //   (item.strength ? ' (' + item.strength + ')' : '')
        // ].join('');

        const display = `${typeLabel ? typeLabel + ' - ' : ''}${item.name ?? item.text ?? ''}${
          item.strength ? ` (${item.strength})` : ''
        }`;

        const $row = $(`
          <div class="border rounded p-2" data-id="${id}" data-key="${key}" data-display="${$('<div>').text(display).html()}">
            <div class="flex items-center justify-between gap-2">
              <div class="text-sm">
                <div class="font-medium">${display}</div>
                ${'' /* manufacturer (disabled):
        <div class="text-xs text-gray-500">${item.manufacturer ?? ''}</div>
        */ }
              </div>
              <button type="button" class="text-red-600 text-sm remove-btn">Remove</button>
            </div>

            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
              <input type="hidden" name="medicines[${id}][selected]" value="1">
              <input type="text" class="border rounded px-2 py-1" name="${durKey}"   placeholder="Duration (e.g., ৫ দিন)" data-med-dur>
              <input type="text" class="border rounded px-2 py-1" name="${timesKey}" placeholder="Times/day (e.g., BD)" data-med-times>
            </div>
          </div>
        `);

        // make this the active row when clicked or focused
        $row.on('click', () => setActiveRow(id));
        $row.find('[data-med-times]').on('focus', () => setActiveRow(id));
        $row.find('[data-med-dur]').on('focus',   () => setActiveRow(id));

        // remove button
        $row.find('.remove-btn').on('click', function () {
          const remaining = $picker.select2('data').filter(d => d.id !== id).map(d => d.id);
          $picker.val(remaining).trigger('change');
          $row.remove();
          if (activeRowId === id) {
            const $first = $selList.children('[data-id]').first();
            setActiveRow($first.data('id') || null);
          }
          ensureWrap();
          if ($selList.children().length === 0) { $panel.addClass('hidden'); }
        });

        return $row;
      }

      function selectFirstRowActive(){
        const $first = $selList.children('[data-id]').first();
        setActiveRow($first.data('id') || null);
      }

      // ---- Select2 init ----
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
              $debug.text('ERROR ' + xhr.status + ':\n' + (xhr.responseText || '').slice(0,1000));
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
              <div class="font-medium">${[item.type, (item.name ?? item.text ?? '')].filter(Boolean).join(' - ')}</div>
              <div class="text-xs text-gray-600">${extra}</div>
            </div>
          `);
        },
        templateSelection: (item) => item.text || item.name || ''
      });

      $picker.on('select2:select', function (e) {
        const item = e.params.data;
        if ($selList.find(`[data-id="${item.id}"]`).length) return;
        const $row = selectedRow(item);
        $selList.append($row);
        ensureWrap();

        // make newly added row active and update the central suggestions for it
        setActiveRow(item.id);
      });

      $picker.on('select2:unselect', function (e) {
        const id = e.params.data.id;
        const wasActive = (activeRowId === id);
        $selList.find(`[data-id="${id}"]`).remove();
        ensureWrap();
        if (wasActive) selectFirstRowActive();
        if ($selList.children().length === 0) { $panel.addClass('hidden'); }
      });

      $clearBtn.on('click', function () {
        $picker.val(null).trigger('change');
        $selList.empty();
        ensureWrap();
        setActiveRow(null);
        $panel.addClass('hidden');
      });

      ensureWrap();
    });
    </script>


<script>
/** Chief Complain (C/C) – Suggestions popover **/
(function () {
  const CC_LIST = (window.CHIP_SETS && window.CHIP_SETS['#problem_description']) || [
    'Fever with sore throat','Cough, runny nose','Epigastric burning','Headache',
    'Follow-up for HTN','Follow-up for DM','Dyspepsia','Dizziness'
  ];

  const BULLET = '• ';
  const $btn  = document.getElementById('btn_cc_suggestions');
  const $menu = document.getElementById('cc_sugg_menu');
  const $list = document.getElementById('cc_sugg_list');
  const $ta   = document.getElementById('problem_description');
  if (!$btn || !$menu || !$list || !$ta) return;

  function render() {
    $list.innerHTML = '';
    CC_LIST.forEach(text => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
      b.textContent = text;
      b.addEventListener('click', () => { insertLine(text); hide(); });
      $list.appendChild(b);
    });
  }

  function toggle(){ $menu.classList.toggle('hidden'); }
  function hide(){ $menu.classList.add('hidden'); }

  function outsideClick(e){
    if ($menu.classList.contains('hidden')) return;
    if (!$menu.contains(e.target) && !$btn.contains(e.target)) hide();
  }

  function insertLine(text){
    // Ensure bullets are ON for this textarea
    $ta.dataset.bulletsOn = '1';
    const val = ($ta.value || '').trim();
    const prefix = val ? '\n' : '';
    const line = text.startsWith(BULLET) ? text : (BULLET + text);
    $ta.value = val + prefix + line;

    // caret + input event for listeners/draft saver
    try { const L = $ta.value.length; $ta.setSelectionRange(L, L); } catch {}
    $ta.dispatchEvent(new Event('input', { bubbles: true }));
    $ta.focus();
  }

  render();
  $btn.addEventListener('click', toggle);
  document.addEventListener('click', outsideClick);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
})();
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

  {{-- ===== Bulleted textareas (for OE/Problem/Advice only) ===== --}}
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
        'বেশি করে পানি খাবেন','ঝাল, তেল-চর্বিযুক্ত খাবার এড়িয়ে চলুন','ধূমপান ও মাদক সম্পূর্ণভাবে পরিহার করুন','প্রতিদিন পর্যাপ্ত ঘুমান (৬–৮ ঘণ্টা)',
        'প্রেসক্রিপশনে দেওয়া ওষুধ নিয়মিত এবং সঠিক সময়ে গ্রহণ করুন','Home glucose monitoring','BP log daily','ER if red flags'
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

window.CHIP_SETS = CHIP_SETS;

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

      document.getElementById('btn_save_draft')?.addEventListener('click', ()=>saveDraft(true));
      document.getElementById('btn_restore_draft')?.addEventListener('click', restoreDraft);
      document.getElementById('btn_clear_draft')?.addEventListener('click', clearDraft);

      ['input','change'].forEach(evt=>{
        document.addEventListener(evt, (e)=>{
          if (!(e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement || e.target instanceof HTMLSelectElement)) return;
          saveDraft(false);
        }, true);
      });
      $(document).on('select2:select select2:unselect', ()=>saveDraft(false));

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

  

  <script>
/** Doctor Advice – Suggestions popover **/
(function () {
  // Reuse the same list you already show as quick chips
  const ADVICE_LIST = (window.CHIP_SETS && window.CHIP_SETS['#doctor_advice'])
                      || [
                        'বেশি করে পানি খাবেন','ঝাল, তেল-চর্বিযুক্ত খাবার এড়িয়ে চলুন','ধূমপান ও মাদক সম্পূর্ণভাবে পরিহার করুন','প্রতিদিন পর্যাপ্ত ঘুমান (৬–৮ ঘণ্টা)',
                        'প্রেসক্রিপশনে দেওয়া ওষুধ নিয়মিত এবং সঠিক সময়ে গ্রহণ করুন','Home glucose monitoring','BP log daily','ER if red flags'
                      ];

  const BULLET = '• ';
  const $btn   = document.getElementById('btn_advice_suggestions');
  const $menu  = document.getElementById('advice_sugg_menu');
  const $list  = document.getElementById('advice_sugg_list');
  const $ta    = document.getElementById('doctor_advice');

  if (!$btn || !$menu || !$list || !$ta) return;

  // Render chips
  function render() {
    $list.innerHTML = '';
    ADVICE_LIST.forEach(text => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
      b.textContent = text;
      b.addEventListener('click', () => {
        insertAdvice(text);
        hide();
      });
      $list.appendChild(b);
    });
  }

  function toggle() { $menu.classList.toggle('hidden'); }
  function show()   { $menu.classList.remove('hidden'); }
  function hide()   { $menu.classList.add('hidden'); }

  function outsideClick(e) {
    if ($menu.classList.contains('hidden')) return;
    if (!$menu.contains(e.target) && !$btn.contains(e.target)) hide();
  }

  function insertAdvice(text) {
    // Ensure bullets are on for this textarea
    $ta.dataset.bulletsOn = '1';

    const val = ($ta.value || '').trim();
    const prefix = val ? '\n' : '';
    // Add a bullet if the line doesn't already start with one
    const line = text.startsWith(BULLET) ? text : (BULLET + text);
    $ta.value = val + prefix + line;

    // Move caret to end and trigger input for any listeners
    try { const L = $ta.value.length; $ta.setSelectionRange(L, L); } catch {}
    $ta.dispatchEvent(new Event('input', { bubbles: true }));
    $ta.focus();
  }

  // Wire up
  render();
  $btn.addEventListener('click', toggle);
  document.addEventListener('click', outsideClick);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
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
        b.classList.toggle('text-black', active);
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
      document.querySelectorAll('.hist-tab').forEach(b=>{
        b.addEventListener('click', ()=> activate(b.dataset.target));
      });

      const oeTab = document.querySelector('.hist-tab[data-target="#hist_oe"]');
      const first = oeTab || document.querySelector('.hist-tab');
      if (first) activate(first.dataset.target);

      document.querySelectorAll('.hist-pane textarea').forEach(ta=>{
        ta.addEventListener('input', refreshDots);
      });
      refreshDots();
    });
  })();
  </script>



{{-- SUGGESTION SCROLL --}}
<script>
(function () {
  const scroller = document.getElementById('med_sugg_scroller');
  const prevBtn  = document.getElementById('med_sugg_prev');
  const nextBtn  = document.getElementById('med_sugg_next');
  if (!scroller || !prevBtn || !nextBtn) return;

  const STEP = () => Math.max(200, Math.round(scroller.clientWidth * 0.8));

  function canScroll() { return scroller.scrollWidth > scroller.clientWidth + 2; }
  function atStart()   { return scroller.scrollLeft <= 1; }
  function atEnd()     { return scroller.scrollLeft + scroller.clientWidth >= scroller.scrollWidth - 1; }

  function update() {
    const show = canScroll();
    prevBtn.style.display = nextBtn.style.display = show ? '' : 'none';
    prevBtn.disabled = atStart();
    nextBtn.disabled = atEnd();
  }

  prevBtn.addEventListener('click', () => scroller.scrollBy({ left: -STEP(), behavior: 'smooth' }));
  nextBtn.addEventListener('click', () => scroller.scrollBy({ left:  STEP(), behavior: 'smooth' }));
  scroller.addEventListener('scroll', update);
  window.addEventListener('resize', update);

  // Keep arrows in sync when chips change
  const mo = new MutationObserver(update);
  mo.observe(scroller, { childList: true, subtree: true });

  // Expose manual updater in case you want to call it after rendering chips
  window.updateMedScrollerArrows = update;

  // initial
  setTimeout(update, 0);
})();
</script>

<script>
/** Suggestions button for all Segmented History panes: O/E, P/H, D/H, M/H, OH, P/A/E, DX, Previous Investigation, A/H, Special Note, Referred To */
(function () {
  const BULLET = '• ';
  const CHIP_SETS = window.CHIP_SETS || {};

  const has = v => Array.isArray(v) && v.length > 0;
  const bullet = t => String(t).startsWith(BULLET) ? t : BULLET + t;

  function insertLine(ta, text) {
    ta.dataset.bulletsOn = '1';
    const val = (ta.value || '').trim();
    ta.value = (val ? val + '\n' : '') + bullet(text);
    try { const L = ta.value.length; ta.setSelectionRange(L, L); } catch {}
    ta.dispatchEvent(new Event('input', { bubbles: true }));
    ta.focus();
  }

  function addMenu(container, ta, list) {
    if (!container || !ta || !has(list)) return;
    if (container.querySelector('[data-sugg-for="'+ ta.id +'"]')) return;

    const wrap = document.createElement('div');
    wrap.className = 'relative';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'px-2 py-1 text-xs rounded bg-gray-100 text-gray-700';
    btn.textContent = 'Suggestions ▾';
    btn.setAttribute('data-sugg-for', ta.id);

    const menu = document.createElement('div');
    menu.className = 'hidden absolute  z-30 mt-1 w-80 bg-white border rounded shadow p-2 max-h-68 overflow-auto';
    menu.innerHTML = '<div class="text-xs text-gray-500 px-1 mb-1">Click to insert</div><div class="flex flex-wrap gap-1"></div>';

    const listWrap = menu.querySelector('.flex');
    list.forEach(text => {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
      chip.textContent = text;
      chip.addEventListener('click', () => { insertLine(ta, text); hide(); });
      listWrap.appendChild(chip);
    });

    const toggle = () => menu.classList.toggle('hidden');
    const hide   = () => menu.classList.add('hidden');
    const outside = (e) => {
      if (menu.classList.contains('hidden')) return;
      if (!menu.contains(e.target) && !btn.contains(e.target)) hide();
    };

    btn.addEventListener('click', toggle);
    document.addEventListener('click', outside);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });

    wrap.appendChild(btn);
    wrap.appendChild(menu);
    container.prepend(wrap); // before Bullets/Clear
  }

  function attach(root = document) {
    root.querySelectorAll('.hist-pane').forEach(pane => {
      const ta = pane.querySelector('textarea');
      if (!ta || !ta.id) return;

      // header right-side control group (where Bullets/Clear live)
      const header    = pane.querySelector('.flex.items-center.justify-between.mb-2');
      const controls  = header?.querySelector('.flex.items-center.gap-2') || header;

      const list = CHIP_SETS['#' + ta.id]; // e.g., '#oe', '#ph', ...
      addMenu(controls, ta, list);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => attach());
  } else {
    attach();
  }
})();
</script>




@vite('resources/js/others/prescription/prescription-returnDate.js')

<script>
(function () {
  // Utilities
  const $ = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const nl2br = s => (String(s||'').trim().replace(/\n/g, '<br>'));
  const clean = s => (String(s||'').trim());
  const bulletize = s => nl2br(s); // you already store with "• " bullets; keep as-is

  // Collect medicines from the center selection list
  function collectMedicines() {
    const rows = $$('#medicine_selected [data-id]');
    return rows.map(r => {
      const display = r.getAttribute('data-display') || '';
      const times = $('[data-med-times]', r)?.value || '';
      const dur   = $('[data-med-dur]', r)?.value || '';
      return { display, times, dur };
    });
  }

  // Collect tests from the right grid
  function collectTests() {
    const rows = $$('#test_selected [data-id]');
    return rows.map(r => {
      const name = $('.font-medium', r)?.textContent || '';
      return clean(name);
    }).filter(Boolean);
  }

  function patientLabel() {
    const sel = $('#patient_select');
    if (!sel) return '';
    // Select2 renders text inside .select2-selection__rendered; fallback to option text
    const rendered = document.querySelector('#select2-patient_select-container');
    const txt = clean(rendered?.textContent || sel.options[sel.selectedIndex]?.text || '');
    if (!txt || txt.includes('+ Add new patient') || txt.includes('-- Select')) return '';
    return txt;
  }

  function doctorLabel() {
    const el = $('#doctor_id');
    return el ? clean(el.options[el.selectedIndex]?.text || '') : '';
  }

  function collectVitals() {
    const get = id => clean($('#'+id)?.value);
    return {
      bp: get('bp'),
      pulse: get('pulse'),
      temp: get('temperature_c'),
      spo2: get('spo2'),
      rr: get('respiratory_rate'),
      bmi: get('bmi')
    };
  }

  function collectHistory() {
    const val = id => clean($('#'+id)?.value);
    return {
      oe: val('oe'),
      ph: val('ph'),
      dh: val('dh'),
      mh: val('mh'),
      oh: val('oh'),
      pae: val('pae'),
      dx: val('dx'),
      previous_investigation: val('previous_investigation'),
      ah: val('ah'),
      special_note: val('special_note'),
      referred_to: val('referred_to'),
    };
  }

  function model() {
    const hist = collectHistory();
    return {
      doctor: doctorLabel(),
      patient: patientLabel(),
      problem: clean($('#problem_description')?.value),
      advice: clean($('#doctor_advice')?.value),
      return_date: clean($('[name="return_date"]')?.value),
      vitals: collectVitals(),
      medicines: collectMedicines(),
      tests: collectTests(),
      dx: hist.dx,
      hist
    };
  }

  // Simple template
  function renderHTML(m) {
  const fmt = s => String(s || '').trim();
  const lines = s => fmt(s).replace(/\n/g, '<br>');

  const vitals = m.vitals || {};
  const vitalsBits = [
    vitals.bp && `BP: <b>${vitals.bp}</b>`,
    vitals.pulse && `Pulse: <b>${vitals.pulse}</b> bpm`,
    vitals.temp && `Temp: <b>${vitals.temp}</b> °C`,
    vitals.spo2 && `SpO₂: <b>${vitals.spo2}</b>%`,
    vitals.rr && `RR: <b>${vitals.rr}</b> /min`,
    vitals.bmi && `BMI: <b>${vitals.bmi}</b>`
  ].filter(Boolean).join(' • ');

  const hist = m.hist || {};
  const histBlock = (label, val) => fmt(val) ? `
    <div class="mb-2">
      <div class="font-semibold"><b><u>${label}:</u></b></div>
      <div class="text-sm whitespace-pre-wrap">${lines(val)}</div>
    </div>` : '';

  const medsHTML = (m.medicines || []).map(md => {
    const info = [fmt(md.times), fmt(md.dur)].filter(Boolean).join(' — ');
    return `
      <li class="pl-1">
        <div class="font-medium">${md.display}</div>
        ${info ? `<div class="text-sm text-gray-600">${info}</div>` : ''}
      </li>`;
  }).join('');

  const testsHTML = (m.tests || []).map(t => `<li>${t}</li>`).join('');
// Get just the doctor's name (strip any specialization in parentheses)
const docNameRaw = typeof m.doctor === 'string' ? m.doctor : (m.doctor?.name ?? '');
const docName = docNameRaw.replace(/\s*\([^)]*\)\s*$/, '');
// ${m.doctor ? `<div class="text-sm font-bold leading-tight">${m.doctor}</div>` : ''}
  return `
  <div class="bg-white shadow md:rounded-lg p-5 md:p-6" style="border:1px solid #e5e7eb;">

    <!-- Letterhead -->
    <div class="flex items-start justify-between">
      <div>
         
        ${docName ? `<div class="text-2xl font-bold leading-tight">${docName}</div>` : ''}

        <div class="text-sm text-gray-600">Medical Officer: Chattagram Medical College Hospital</div>
      </div>
      <div class="text-right">
        <div class="text-sm font-semibold">Chamber: Epic International Hospital</div>
        <div class="text-xs text-gray-500 leading-4">
          128, Jubilee Road, Tin pool, Chattagram<br>
          Phone for Appointment: 01xxxxxxxxx <br>
          Satuerday-Friday (07.00 PM - 10.00 PM)
        </div>
      </div>
    </div>

    <!-- Patient bar -->
    <section class="mt-3" style="border:1.5px solid;">
      <div class="p-2 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white">
        <div class="space-y-1 text-sm min-w-[220px]">
          ${m.patient ? `<div>Patient Name: <span class="font-medium">${m.patient}</span></div>` : ''}
          ${vitalsBits ? `<div class="text-gray-600">${vitalsBits}</div>` : ''}
        </div>
        <div class="text-sm space-y-1">
          ${m.return_date ? `<div>Next Meeting Date: <span class="font-medium">${m.return_date}</span></div>` : ''}
        </div>
        <div class="text-sm space-y-1 md:text-right">
          <div class="text-gray-500">Date:
            <span class="font-medium text-black">${new Date().toLocaleString()}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Body: LEFT = history | RIGHT = medicines + tests -->
    <div class="mt-4 grid grid-cols-12 gap-6">
      <!-- LEFT column (History) with vertical divider -->
      <div class="col-span-12 md:col-span-5 pr-4" style="border-right:2px solid #e5e7eb;">
        ${fmt(m.problem) ? `
          <div class="mb-2">
            <div class="font-semibold"><b><u>C/C:</u></b></div>
            <div class="text-sm whitespace-pre-wrap">${lines(m.problem)}</div>
          </div>` : ''}

        ${histBlock('O/E', hist.oe)}
        ${histBlock('P/H', hist.ph)}
        ${histBlock('D/H', hist.dh)}
        ${histBlock('M/H', hist.mh)}
        ${histBlock('OH', hist.oh)}
        ${histBlock('P/A/E', hist.pae)}
        ${histBlock('DX', hist.dx)}

        ${fmt(hist.previous_investigation) ? `
          <div class="mb-2">
            <div class="font-semibold"><b><u>Previous Investigation</u></b></div>
            <div class="text-sm whitespace-pre-wrap">${lines(hist.previous_investigation)}</div>
          </div>` : ''}
      </div>

      <!-- RIGHT column (Medicines + Tests + Advice) -->
      <div class="col-span-12 md:col-span-7">
        <div class="flex items-center gap-2 mb-3">
          <div class="text-2xl font-extrabold pl-2">℞</div>
        </div>

        ${(m.medicines||[]).length ? `
          <div class="mb-4 pl-6">
            <div class="font-semibold mb-2">Medicines</div>
            <ol class="list-decimal space-y-2 pl-5">
              ${medsHTML}
            </ol>
          </div>` : ''}

        ${(m.tests||[]).length ? `
          <div class="mb-4 pl-6">
            <div class="font-semibold mb-2">Investigations</div>
            <ul class="list-disc pl-5 space-y-1">
              ${testsHTML}
            </ul>
          </div>` : ''}

        ${fmt(m.advice) ? `
          <div class="pl-6 mb-2">
            <div class="font-semibold">Doctor Advice</div>
            <div class="text-sm whitespace-pre-wrap">${lines(m.advice)}</div>
          </div>` : ''}

        ${fmt(hist.referred_to) ? `
          <div class="pl-6">
            <div class="font-semibold">Referred To</div>
            <div class="text-sm whitespace-pre-wrap">${lines(hist.referred_to)}</div>
          </div>` : ''}

        <div class="mt-9 grid grid-cols-2 gap-8 items-end">
          <div></div>
          <div class="text-right">
            <div class="h-12"></div>
            <div class="border-t border-gray-400 pt-1 text-sm">
              ${m.doctor || ''}<br>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-4 text-center text-[11px] text-gray-500">
      Thank you for visiting. For emergencies, please contact the clinic immediately
    </div>
  </div>`;
}

  function updatePreview() {
    const m = model();
    $('#rx_preview_canvas').innerHTML = renderHTML(m);
  }

  // Wire events for live updates
  function wirePreview() {
    // Initial render
    updatePreview();

    // Any input/select/textarea changes
    ['input','change'].forEach(evt => {
      document.addEventListener(evt, (e) => {
        const t = e.target;
        if (!(t instanceof HTMLInputElement || t instanceof HTMLTextAreaElement || t instanceof HTMLSelectElement)) return;
        // Only re-render for form fields within our form (cheap check)
        if (t.closest('form')) updatePreview();
      }, true);
    });

    // Select2 events for patient/medicine/test
    $(document).addEventListener?.call
    ? null
    : null; // (no-op to keep linter happy)

    // jQuery-based hooks (since Select2 is jQuery):
    $(document).addEventListener; // no-op

    if (window.jQuery) {
      const jq = window.jQuery;
      jq(document).on('select2:select select2:unselect', '#patient_select, #medicine_picker, #test_picker', updatePreview);
    }

    // Mutations in medicine/tests lists (add/remove rows)
    const mo = new MutationObserver(updatePreview);
    const medList = document.getElementById('medicine_selected');
    const testList = document.getElementById('test_selected');
    if (medList) mo.observe(medList, { childList: true, subtree: true });
    if (testList) mo.observe(testList, { childList: true, subtree: true });

    // Preview controls
    $('#rx_preview_toggle')?.addEventListener('click', () => {
      const body = $('#rx_preview_body');
      const collapsed = body.classList.toggle('hidden');
      $('#rx_preview_toggle').textContent = collapsed ? 'Expand' : 'Collapse';
    });
    $('#rx_preview_print')?.addEventListener('click', () => window.print());
  }

  // Start
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wirePreview);
  } else {
    wirePreview();
  }
})();
</script>



<script>
function renderHTML(m) {
  const fmt = s => String(s || '').trim();
  const lines = s => fmt(s).replace(/\n/g, '<br>');

  const vitals = m.vitals || {};
  const vitalsBits = [
    vitals.bp && `BP: <b>${vitals.bp}</b>`,
    vitals.pulse && `Pulse: <b>${vitals.pulse}</b> bpm`,
    vitals.temp && `Temp: <b>${vitals.temp}</b> °C`,
    vitals.spo2 && `SpO₂: <b>${vitals.spo2}</b>%`,
    vitals.rr && `RR: <b>${vitals.rr}</b> /min`,
    vitals.bmi && `BMI: <b>${vitals.bmi}</b>`
  ].filter(Boolean).join(' • ');

  const hist = m.hist || {};
  const histBlock = (label, val) => fmt(val) ? `
    <div class="mb-2">
      <div class="font-semibold"><b><u>${label}:</u></b></div>
      <div class="text-sm whitespace-pre-wrap">${lines(val)}</div>
    </div>` : '';

  const meds = (m.medicines||[]).map(md => {
    const info = [fmt(md.times), fmt(md.dur)].filter(Boolean).join(' — ');
    return `
      <li>
        <div class="font-medium">${md.display}</div>
        ${info ? `<div class="text-sm text-gray-600">${info}</div>` : ''}
      </li>`;
  }).join('');

  const tests = (m.tests||[]).map(t => `
    <li class="flex items-start"><span class="mr-2">•</span><span>${t}</span></li>
  `).join('');

  return `
  <div class="bg-white shadow md:rounded-lg p-5 md:p-6" style="border:1px solid #e5e7eb;">

    <!-- Letterhead (simple) -->
    <div class="flex items-start justify-between">
      <div>
        ${m.doctor ? `<div class="text-2xl font-bold leading-tight">${m.doctor}</div>` : ''}
        <div class="text-sm text-gray-600">Medical Officer: Chattagram Medical College Hospital</div>
      </div>
      <div class="text-right">
        <div class="text-lg font-semibold">Chamber: Epic International Hospital</div>
        <div class="text-xs text-gray-500 leading-4">
          128, Jubilee Road, Tin pool, Chattagram<br>
          Phone for Appointment: 01xxxxxxxxx <br>
          Satuerday-Friday (07.00 PM - 10.00 PM)
        </div>
      </div>
    </div>

    <!-- Patient bar -->
    <section class="mt-3" style="border:1.5px solid;">
      <div class="p-2 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white">
        <div class="space-y-1 text-sm min-w-[220px]">
          ${m.patient ? `<div>Patient Name: <span class="font-medium">${m.patient}</span></div>` : ''}
          ${vitalsBits ? `<div class="text-gray-600">${vitalsBits}</div>` : ''}
        </div>
        <div class="text-sm space-y-1">
          ${m.return_date ? `<div>Next Meeting Date: <span class="font-medium">${m.return_date}</span></div>` : ''}
        </div>
        <div class="text-sm space-y-1 md:text-right">
          <div class="text-gray-500">Date:
            <span class="font-medium text-black">${new Date().toLocaleString()}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Body: LEFT = O/E, P/H, D/H, M/H, OH, P/A/E, Prev Inv, Tests | RIGHT = Medicines, Advice, Return, Referred -->
    <div class="mt-4 grid grid-cols-12 gap-6">
      <!-- LEFT column -->
      <div class="col-span-12 md:col-span-4 pr-4" style="border-right:1px solid #e5e7eb;">
        ${fmt(m.problem) ? `
          <div class="mb-2">
            <div class="font-semibold"><b><u>C/C:</u></b></div>
            <div class="text-sm whitespace-pre-wrap">${lines(m.problem)}</div>
          </div>` : ''}

        ${histBlock('O/E', hist.oe)}
        ${histBlock('P/H', hist.ph)}
        ${histBlock('D/H', hist.dh)}
        ${histBlock('M/H', hist.mh)}
        ${histBlock('OH', hist.oh)}
        ${histBlock('P/A/E', hist.pae)}
        ${histBlock('DX', hist.dx)}

        ${fmt(hist.previous_investigation) ? `
          <div class="mb-2">
            <div class="font-semibold"><b><u>Previous Investigation</u></b></div>
            <div class="text-sm whitespace-pre-wrap">${lines(hist.previous_investigation)}</div>
          </div>` : ''}

        ${(m.tests||[]).length ? `
          <div class="mb-2">
            <div class="font-semibold"><b><u>Investigation</u></b></div>
            <ul class="mt-1">${tests}</ul>
          </div>` : ''}
      </div>

      <!-- RIGHT column -->
      <div class="col-span-12 md:col-span-8">
        <div class="flex items-center gap-2 mb-2">
          <div class="text-2xl font-extrabold pl-2">℞</div>
        </div>

        ${(m.medicines||[]).length ? `
          <div class="mb-4 pl-6">
            <div class="font-semibold mb-2">Medicines</div>
            <ol class="list-decimal space-y-2 pl-5">
              ${meds}
            </ol>
          </div>` : ''}

        ${fmt(m.advice) ? `
          <div class="pl-6 mb-2">
            <div class="font-semibold">Doctor Advice</div>
            <div class="text-sm whitespace-pre-wrap">${lines(m.advice)}</div>
          </div>` : ''}

        ${fmt(hist.referred_to) ? `
          <div class="pl-6">
            <div class="font-semibold">Referred To</div>
            <div class="text-sm whitespace-pre-wrap">${lines(hist.referred_to)}</div>
          </div>` : ''}

        <div class="mt-9 grid grid-cols-2 gap-8 items-end">
          <div></div>
          <div class="text-right">
            <div class="h-12"></div>
            <div class="border-t border-gray-400 pt-1 text-sm">
              ${m.doctor || ''}<br>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-4 text-center text-[11px] text-gray-500">
      Thank you for visiting. For emergencies, please contact the clinic immediately
    </div>
  </div>`;
}

</script>

<script>
  // ===== Doctor Advice collapse toggle (with remember state) =====
  (function(){
    const key = 'advice-open';
    function setOpen(open){
      const body = document.getElementById('advice-body');
      const btn  = document.getElementById('advice-toggle');
      const icon = btn?.querySelector('svg');
      if (!body || !btn) return;
      body.classList.toggle('hidden', !open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (icon) icon.classList.toggle('rotate-180', open);
      try { localStorage.setItem(key, open ? '1' : '0'); } catch {}
    }
    document.addEventListener('DOMContentLoaded', () => {
      // default collapsed (like CF); load remembered state
      const remembered = (localStorage.getItem(key) ?? '0') === '1';
      setOpen(remembered);
      document.getElementById('advice-toggle')?.addEventListener('click', () => {
        const body = document.getElementById('advice-body');
        setOpen(body?.classList.contains('hidden'));
      });
    });
  })();
</script>

<script>
  (function () {
    const KEY = 'hist-open';
    function setOpen(open) {
      const body = document.getElementById('hist-body');
      const btn  = document.getElementById('hist-toggle');
      const icon = btn?.querySelector('svg');
      if (!body || !btn) return;
      body.classList.toggle('hidden', !open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      icon?.classList.toggle('rotate-180', open);
      try { localStorage.setItem(KEY, open ? '1' : '0'); } catch {}
    }

    document.addEventListener('DOMContentLoaded', () => {
      const remembered = (localStorage.getItem(KEY) ?? '0') === '1'; // default collapsed
      setOpen(remembered);
      document.getElementById('hist-toggle')?.addEventListener('click', () => {
        const body = document.getElementById('hist-body');
        setOpen(body?.classList.contains('hidden'));
      });
    });
  })();
</script>

<script>
(function(){
  const key = 'cc-open';
  function setOpen(open){
    const body = document.getElementById('cc-body');
    const btn  = document.getElementById('cc-toggle');
    const icon = btn?.querySelector('svg');
    if (!body || !btn) return;
    body.classList.toggle('hidden', !open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (icon) icon.classList.toggle('rotate-180', !open); // rotate when collapsed
    try { localStorage.setItem(key, open ? '1' : '0'); } catch {}
  }
  document.addEventListener('DOMContentLoaded', () => {
    const remember = (localStorage.getItem(key) ?? '1') === '1'; // default open
    setOpen(remember);
    document.getElementById('cc-toggle')?.addEventListener('click', () => {
      const isOpen = document.getElementById('cc-body')?.classList.contains('hidden') === false;
      setOpen(!isOpen);
    });
  });
})();
</script>

<script>
(function(){
  const key = 'med-open';
  function setOpen(open){
    const body = document.getElementById('med-body');
    const btn  = document.getElementById('med-toggle');
    const icon = btn?.querySelector('svg');
    if (!body || !btn) return;
    body.classList.toggle('hidden', !open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    // rotate chevron when collapsed
    if (icon) icon.classList.toggle('rotate-180', !open);
    try { localStorage.setItem(key, open ? '1' : '0'); } catch {}
  }
  document.addEventListener('DOMContentLoaded', () => {
    // default open; restore remembered state
    const remembered = (localStorage.getItem(key) ?? '1') === '1';
    setOpen(remembered);
    document.getElementById('med-toggle')?.addEventListener('click', () => {
      const isOpen = !document.getElementById('med-body')?.classList.contains('hidden');
      setOpen(!isOpen);
    });
  });
})();
</script>

<script>
(function(){
  const key = 'test-open';
  function setOpen(open){
    const body = document.getElementById('test-body');
    const btn  = document.getElementById('test-toggle');
    const icon = btn?.querySelector('svg');
    if (!body || !btn) return;
    body.classList.toggle('hidden', !open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    // rotate chevron when collapsed
    if (icon) icon.classList.toggle('rotate-180', !open);
    try { localStorage.setItem(key, open ? '1' : '0'); } catch {}
  }
  document.addEventListener('DOMContentLoaded', () => {
    // default open; restore remembered state
    const remembered = (localStorage.getItem(key) ?? '1') === '1';
    setOpen(remembered);
    document.getElementById('test-toggle')?.addEventListener('click', () => {
      const isOpen = !document.getElementById('test-body')?.classList.contains('hidden');
      setOpen(!isOpen);
    });
  });
})();
</script>

</x-app-layout>
