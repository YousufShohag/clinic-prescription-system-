<x-app-layout>
  {{-- CSRF for AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Select2 for patient search --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

  <div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold">Appointments</h2>
      <button id="openCreateBtn" type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Add Appointment
      </button>
    </div>

    {{-- Filters --}}
    <form method="GET" class="mb-4 flex items-end gap-3">
      @php $doctorId = request('doctor_id'); @endphp
      <div>
        <label class="block text-sm text-gray-700">Doctor</label>
        <select name="doctor_id" id="filter_doctor" class="border rounded px-3 py-2">
          <option value="">All</option>
          @foreach(($doctors ?? \App\Models\Doctor::orderBy('name')->get(['id','name'])) as $d)
            <option value="{{ $d->id }}" @selected((string)$doctorId===(string)$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <button class="px-3 py-2 border rounded">Apply</button>
      <a href="{{ route('appointments.index') }}" class="px-3 py-2 border rounded">Reset</a>
    </form>

    {{-- Two-column layout: LEFT table, RIGHT calendar (swap col-spans if you prefer) --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      {{-- LEFT: Day table --}}
      <div class="md:col-span-7">
        <div class="bg-white rounded shadow">
          <div class="flex items-center justify-between p-4 border-b">
            <div>
              <div class="text-lg font-semibold">
                Appointments on <span id="rightDate">{{ now()->toDateString() }}</span>
              </div>
              <div class="text-sm text-gray-600"><span id="rightCount">0</span> item(s)</div>
            </div>
            <div id="rightStatus" class="text-sm text-gray-500"></div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-100">
                <tr>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody id="dayTbody" class="bg-white divide-y divide-gray-200">
                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Select a date on the calendar.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- RIGHT: Calendar --}}
      <div class="md:col-span-5">
        <div class="bg-white rounded shadow p-4">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- CREATE MODAL --}}
  <div id="createModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow w-[95vw] max-w-2xl overflow-hidden">
      <div class="flex items-center justify-between p-4 border-b">
        <div class="text-lg font-semibold">New Appointment</div>
        <button type="button" class="px-2 py-1 border rounded" onclick="closeCreateModal()">Close</button>
      </div>
      <form id="createForm" class="p-4 space-y-4">
        {{-- errors --}}
        <div id="createErrors" class="hidden border border-red-300 bg-red-50 text-red-700 rounded p-3 text-sm"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Doctor <span class="text-red-500">*</span></label>
            <select name="doctor_id" id="form_doctor" class="w-full border rounded px-3 py-2" required>
              @foreach(($doctors ?? \App\Models\Doctor::orderBy('name')->get(['id','name'])) as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
            <select name="patient_id" id="form_patient" class="w-full border rounded px-3 py-2" required></select>
            <div class="text-xs text-gray-500 mt-1">Start typing to search patients…</div>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
            <input type="date" name="date" id="form_date" class="w-full border rounded px-3 py-2" required>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Time <span class="text-red-500">*</span></label>
            <input type="time" name="start_time" id="form_time" class="w-full border rounded px-3 py-2" required>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Duration (minutes)</label>
            <input type="number" name="duration_min" id="form_duration" min="0" step="5" class="w-full border rounded px-3 py-2" placeholder="e.g., 30">
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">Status</label>
            <select name="status" id="form_status" class="w-full border rounded px-3 py-2">
              <option value="scheduled">Scheduled</option>
              <option value="checked_in">Checked in</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm text-gray-700 mb-1">Notes</label>
            <textarea name="notes" id="form_notes" rows="3" class="w-full border rounded px-3 py-2"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="px-3 py-2 border rounded" onclick="closeCreateModal()">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
        </div>
      </form>
    </div>
  </div>

  {{-- FullCalendar --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

  <script>
    const doctorFilterSelect = document.getElementById('filter_doctor');
    const rightDate   = document.getElementById('rightDate');
    const rightCount  = document.getElementById('rightCount');
    const rightStat   = document.getElementById('rightStatus');
    const dayTbody    = document.getElementById('dayTbody');

    // ---------- helpers ----------
    function csrf() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }
    function fetchJSON(url, paramsObj) {
      const u = new URL(url, window.location.origin);
      if (paramsObj) Object.entries(paramsObj).forEach(([k, v]) => {
        if (v !== undefined && v !== null && v !== '') u.searchParams.set(k, v);
      });
      return fetch(u.toString(), { credentials: 'same-origin' })
        .then(async r => {
          if (!r.ok) throw new Error(`HTTP ${r.status}: ${(await r.text()).slice(0,300)}`);
          return r.json();
        });
    }
    function todayStr() { return new Date().toISOString().slice(0,10); }

    // ---------- right table ----------
    function renderDayRows(items) {
      if (!items?.length) {
        dayTbody.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">No appointments.</td></tr>';
        return;
      }
      dayTbody.innerHTML = items.map(it => `
        <tr class="hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">${it.time || '—'}</td>
          <td class="px-3 py-2">${it.patient}${it.phone ? `<div class="text-xs text-gray-500">${it.phone}</div>` : ''}</td>
          <td class="px-3 py-2">${it.doctor || '—'}</td>
          <td class="px-3 py-2">${it.status || '—'}</td>
          <td class="px-3 py-2 max-w-[260px] truncate" title="${it.notes || ''}">${it.notes || '—'}</td>
          <td class="px-3 py-2">
            <div class="flex gap-2">
              ${it.show_url ? `<a href="${it.show_url}" class="text-blue-600 text-sm hover:underline">Open</a>` : ''}
              ${it.edit_url ? `<a href="${it.edit_url}" class="text-blue-600 text-sm hover:underline">Edit</a>` : ''}
            </div>
          </td>
        </tr>
      `).join('');
    }

    function loadDay(dateStr){
      const doctorId = doctorFilterSelect?.value || '';
      rightDate.textContent = dateStr;
      rightCount.textContent = '...';
      rightStat.textContent = 'Loading…';
      dayTbody.innerHTML = '<tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Loading…</td></tr>';

      fetchJSON(`{{ route('appointments.day') }}`, { date: dateStr, doctor_id: doctorId })
        .then(data => {
          rightCount.textContent = data.count ?? 0;
          rightStat.textContent = '';
          renderDayRows(data.items || []);
        })
        .catch(err => {
          console.error('Day load error:', err);
          rightStat.textContent = 'Failed to load';
          dayTbody.innerHTML = `<tr><td colspan="6" class="px-3 py-6 text-center text-red-600">Failed to load: ${err.message}</td></tr>`;
        });
    }

    // ---------- calendar ----------
    let _calendar;
    document.addEventListener('DOMContentLoaded', function() {
      const el = document.getElementById('calendar');
      _calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 'auto',
        dayMaxEventRows: 1,
        datesSet: function(info) {
          const doctorId = doctorFilterSelect?.value || '';
          const params = { start: info.startStr, end: info.endStr };
          if (doctorId) params.doctor_id = doctorId;

          fetchJSON(`{{ route('appointments.calendarData') }}`, params)
            .then(events => {
              _calendar.removeAllEvents();
              _calendar.addEventSource(events);
            })
            .catch(err => console.error('Calendar load error:', err));
        },
        dateClick: function(info){
          openCreateModal(info.dateStr); // open modal prefilled with date
        },
        eventClick: function(info){
          const d = info.event.extendedProps?.date || info.event.startStr;
          openCreateModal(d);
        }
      });
      _calendar.render();

      // load today's table initially
      loadDay(todayStr());
    });

    // if doctor filter changes, reload current day and calendar
    doctorFilterSelect?.addEventListener('change', () => {
      const curDay = rightDate.textContent || todayStr();
      // reload events in current visible range
      const v = _calendar.view;
      const params = { start: v.currentStart.toISOString().slice(0,10), end: v.currentEnd.toISOString().slice(0,10) };
      const doctorId = doctorFilterSelect.value || '';
      if (doctorId) params.doctor_id = doctorId;
      fetch(`{{ route('appointments.calendarData') }}?` + new URLSearchParams(params).toString())
        .then(r=>r.json()).then(events => { _calendar.removeAllEvents(); _calendar.addEventSource(events); });
      // reload table
      loadDay(curDay);
    });

    // ---------- create modal ----------
    const modal = document.getElementById('createModal');
    const form  = document.getElementById('createForm');
    const errBox= document.getElementById('createErrors');

    function openCreateModal(dateStr){
      // preset doctor from filter
      const filterDoc = doctorFilterSelect?.value || '';
      if (filterDoc) document.getElementById('form_doctor').value = filterDoc;

      // preset date (default: today)
      document.getElementById('form_date').value = dateStr || todayStr();
      // preset time to next half-hour
      const now = new Date();
      const mins = now.getMinutes();
      const rounded = mins < 30 ? 30 : 60;
      now.setMinutes(rounded, 0, 0);
      document.getElementById('form_time').value = now.toTimeString().slice(0,5);

      // clear other fields
      document.getElementById('form_duration').value = '';
      document.getElementById('form_status').value   = 'scheduled';
      document.getElementById('form_notes').value    = '';

      errBox.classList.add('hidden'); errBox.innerHTML = '';
      modal.classList.remove('hidden');
    }
    function closeCreateModal(){ modal.classList.add('hidden'); }
    window.openCreateModal = openCreateModal;
    window.closeCreateModal = closeCreateModal;

    document.getElementById('openCreateBtn')?.addEventListener('click', () => openCreateModal(todayStr()));

    // Patient select2
    $(function(){
      $('#form_patient').select2({
        placeholder: 'Search patients…',
        width: '100%',
        minimumInputLength: 1,
        ajax: {
          url: "{{ route('patients.search') }}",
          dataType: 'json',
          delay: 200,
          data: params => ({ term: params.term, page: params.page || 1 }),
          processResults: (data, params) => {
            params.page = params.page || 1;
            const results = (data?.results || []).map(p => ({
              id: p.id,
              text: p.name,
              phone: p.phone,
              email: p.email
            }));
            return { results, pagination: { more: !!data?.pagination?.more } };
          }
        },
        templateResult: item => {
          if (!item.id) return item.text;
          const extra = [item.phone, item.email].filter(Boolean).join(' • ');
          return $(`<div><div class="font-medium">${item.text}</div>${extra ? `<div class="text-xs text-gray-600">${extra}</div>` : ''}</div>`);
        }
      });
    });

    // Submit create form via AJAX
    form.addEventListener('submit', function(e){
      e.preventDefault();

      const d = document.getElementById('form_date').value;
      const t = document.getElementById('form_time').value;
      const payload = {
        doctor_id:   document.getElementById('form_doctor').value,
        patient_id:  $('#form_patient').val(),
        date:        d,                // for date/start_time schema
        start_time:  t,
        duration_min:document.getElementById('form_duration').value || null,
        status:      document.getElementById('form_status').value || null,
        notes:       document.getElementById('form_notes').value || null,
        scheduled_at: d && t ? (d + ' ' + t + ':00') : null // for scheduled_at schema
      };

      errBox.classList.add('hidden'); errBox.innerHTML = '';

      fetch(`{{ route('appointments.store') }}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf(),
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      })
      .then(async r => {
        if (r.ok) return r.json().catch(()=>({})); // 200/201 with/without json
        if (r.status === 422) {
          const data = await r.json().catch(()=> ({}));
          const errors = data.errors || {};
          const list = Object.values(errors).flat().map(m => `<li>${m}</li>`).join('');
          errBox.innerHTML = `<ul class="list-disc list-inside">${list || 'Validation failed.'}</ul>`;
          errBox.classList.remove('hidden');
          throw new Error('Validation failed');
        }
        const txt = await r.text();
        throw new Error(`HTTP ${r.status}: ${txt.slice(0,300)}`);
      })
      .then(() => {
        closeCreateModal();
        // Refresh calendar events in current range
        const v = _calendar.view;
        const params = { start: v.currentStart.toISOString().slice(0,10), end: v.currentEnd.toISOString().slice(0,10) };
        const doctorId = doctorFilterSelect?.value || '';
        if (doctorId) params.doctor_id = doctorId;

        fetch(`{{ route('appointments.calendarData') }}?` + new URLSearchParams(params).toString())
          .then(r=>r.json()).then(events => { _calendar.removeAllEvents(); _calendar.addEventSource(events); });

        // Reload currently shown day table
        loadDay(d || todayStr());
      })
      .catch(err => {
        console.error('Create failed:', err);
        if (errBox.classList.contains('hidden')) {
          errBox.innerHTML = `Failed to save: ${err.message}`;
          errBox.classList.remove('hidden');
        }
      });
    });
  </script>

  <style>
    /* modal center helper */
    #createModal { display:flex; }
    #createModal.hidden { display:none; }
  </style>
</x-app-layout>
