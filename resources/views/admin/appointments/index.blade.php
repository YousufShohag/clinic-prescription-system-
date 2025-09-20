{{-- resources/views/admin/appointments/index.blade.php --}}
<x-app-layout>
  {{-- CSRF for AJAX --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Select2 for patient search --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

  <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
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

    {{-- Two-column layout: LEFT day list, RIGHT calendar --}}
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

            {{-- Day toolbar: search + print --}}
            <div class="flex items-center gap-2">
              <div class="relative">
                <input id="daySearch" type="text" placeholder="Search time / patient / phone / notes…"
                  class="w-64 border rounded px-3 py-1.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <button id="clearSearchBtn" type="button"
                  class="absolute right-1 top-1 text-gray-400 hover:text-gray-600 text-sm hidden"
                  aria-label="Clear search">✕</button>
              </div>
              <button id="printBtn" type="button"
                class="px-3 py-1.5 border rounded text-sm hover:bg-gray-50"
                title="Print current list">Print</button>
            </div>

            <div id="rightStatus" class="text-sm text-gray-500"></div>
          </div>

          {{-- SCROLL CONTAINER + STICKY HEADER TABLE --}}
          <div id="dayScroll" class="overflow-x-auto max-h-[72vh] overflow-y-auto">
            <table class="table-sticky min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">No.</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Time</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Patient</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Doctor</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Notes</th>
                  <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody id="dayTbody" class="bg-white divide-y divide-gray-200">
                <tr><td colspan="7" class="px-3 py-6 text-center text-gray-500">Select a date on the calendar.</td></tr>
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
    const dayScroll   = document.getElementById('dayScroll');
    const daySearch   = document.getElementById('daySearch');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const printBtn    = document.getElementById('printBtn');

    let _calendar;
    let _dayItems = [];
    let _dayFiltered = [];

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
    function escapeHTML(s){ return (s||'').toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

    // ---------- filtering & rendering ----------
    function applyFilter(){
      const q = (daySearch.value || '').trim().toLowerCase();
      clearSearchBtn.classList.toggle('hidden', q === '');
      if (!q) {
        _dayFiltered = _dayItems.slice();
      } else {
        _dayFiltered = _dayItems.filter(it => {
          const hay = [
            it.number ?? '',
            it.time ?? '',
            it.patient ?? '',
            it.phone ?? '',
            it.doctor ?? '',
            it.status ?? '',
            it.notes ?? ''
          ].join(' ').toLowerCase();
          return hay.includes(q);
        });
      }
      renderDayRows(_dayFiltered);
      const total = _dayItems.length;
      const shown = _dayFiltered.length;
      rightCount.textContent = (q && total !== shown) ? `${shown} / ${total}` : `${total}`;
    }

    function renderDayRows(items) {
      if (!items?.length) {
        dayTbody.innerHTML = '<tr><td colspan="7" class="px-3 py-6 text-center text-gray-500">No appointments.</td></tr>';
        dayScroll?.scrollTo({ top: 0, behavior: 'auto' });
        return;
      }
      dayTbody.innerHTML = items.map((it, idx) => {
        const no = (it.number != null && it.number !== '') ? it.number : (idx + 1);

        const editLink = it.edit_url ? `
          <a href="${it.edit_url}" class="inline-flex items-center gap-1.5 rounded px-2 py-1 text-blue-600 hover:text-blue-700 hover:bg-blue-50 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 013.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z"/>
            </svg>
            
          </a>` : '';

        const deleteBtn = it.delete_url ? `
          <button type="button" class="delete-btn inline-flex items-center gap-1.5 rounded px-2 py-1 text-red-600 hover:text-red-700 hover:bg-red-50 text-sm"
                  data-url="${it.delete_url}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 7h12M10 11v6m4-6v6M9 7l1-2h4l1 2M6 7l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12"/>
            </svg>
            
          </button>` : '';

        return `
          <tr class="hover:bg-gray-50">
            <td class="px-3 py-2 whitespace-nowrap">${no}</td>
            <td class="px-3 py-2 whitespace-nowrap">${it.time || '—'}</td>
            <td class="px-3 py-2">${it.patient}${it.phone ? `<div class="text-xs text-gray-500">${it.phone}</div>` : ''}</td>
            <td class="px-3 py-2">${it.doctor || '—'}</td>
            <td class="px-3 py-2">${it.status || '—'}</td>
            <td class="px-3 py-2 max-w-[260px] truncate" title="${escapeHTML(it.notes) || ''}">${escapeHTML(it.notes) || '—'}</td>
            <td class="px-3 py-2">
              <div class="flex gap-2 items-center">${editLink}${deleteBtn}</div>
            </td>
          </tr>
        `;
      }).join('');
      dayScroll?.scrollTo({ top: 0, behavior: 'auto' });
    }

    function loadDay(dateStr){
      const doctorId = doctorFilterSelect?.value || '';
      rightDate.textContent = dateStr;
      rightCount.textContent = '...';
      rightStat.textContent = 'Loading…';
      dayTbody.innerHTML = '<tr><td colspan="7" class="px-3 py-6 text-center text-gray-500">Loading…</td></tr>';
      dayScroll?.scrollTo({ top: 0, behavior: 'auto' });

      fetchJSON(`{{ route('appointments.day') }}`, { date: dateStr, doctor_id: doctorId })
        .then(data => {
          _dayItems = data.items || [];
          rightStat.textContent = '';
          daySearch.value = '';
          clearSearchBtn.classList.add('hidden');
          _dayFiltered = _dayItems.slice();
          renderDayRows(_dayFiltered);
          rightCount.textContent = _dayItems.length;
        })
        .catch(err => {
          console.error('Day load error:', err);
          rightStat.textContent = 'Failed to load';
          dayTbody.innerHTML = `<tr><td colspan="7" class="px-3 py-6 text-center text-red-600">Failed to load: ${err.message}</td></tr>`;
        });
    }

    // ---------- calendar ----------
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
          loadDay(info.dateStr);
          openCreateModal(info.dateStr);
          document.querySelector('#dayScroll')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        eventClick: function(info){
          const d = info.event.extendedProps?.date || info.event.startStr;
          loadDay(d);
          openCreateModal(d);
          document.querySelector('#dayScroll')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
      _calendar.render();

      // initial table load (today)
      loadDay(todayStr());
    });

    // doctor filter -> reload current day and calendar
    doctorFilterSelect?.addEventListener('change', () => {
      const curDay = rightDate.textContent || todayStr();
      const v = _calendar.view;
      const params = { start: v.currentStart.toISOString().slice(0,10), end: v.currentEnd.toISOString().slice(0,10) };
      const doctorId = doctorFilterSelect.value || '';
      if (doctorId) params.doctor_id = doctorId;
      fetch(`{{ route('appointments.calendarData') }}?` + new URLSearchParams(params).toString())
        .then(r=>r.json()).then(events => { _calendar.removeAllEvents(); _calendar.addEventSource(events); });
      loadDay(curDay);
    });

    // search events
    daySearch?.addEventListener('input', applyFilter);
    clearSearchBtn?.addEventListener('click', () => { daySearch.value = ''; applyFilter(); daySearch.focus(); });

    // ---------- print current list ----------
    printBtn?.addEventListener('click', () => {
      const items = _dayFiltered.length ? _dayFiltered : [];
      if (!items.length) { alert('No appointments to print.'); return; }
      const dateTitle = rightDate.textContent || todayStr();
      const doctorName = (doctorFilterSelect?.selectedOptions?.[0]?.text || 'All');

      const rows = items.map((it, idx) => {
        const no = (it.number != null && it.number !== '') ? it.number : (idx + 1);
        return `
          <tr>
            <td>${no}</td>
            <td>${escapeHTML(it.time || '')}</td>
            <td>${escapeHTML(it.patient || '')}${it.phone ? `<div style="font-size:11px;color:#6b7280">${escapeHTML(it.phone)}</div>` : ''}</td>
            <td>${escapeHTML(it.doctor || '')}</td>
            <td>${escapeHTML(it.status || '')}</td>
            <td>${escapeHTML(it.notes || '')}</td>
          </tr>
        `;
      }).join('');

      const html = `
        <!DOCTYPE html><html><head><meta charset="utf-8">
        <title>Appointments ${escapeHTML(dateTitle)}</title>
        <style>
          *{box-sizing:border-box} body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,Apple Color Emoji,Segoe UI Emoji; padding:24px; color:#111827;}
          h1{font-size:20px; margin:0 0 4px 0}
          .sub{color:#6b7280; font-size:12px; margin-bottom:16px}
          table{width:100%; border-collapse:collapse; font-size:13px}
          th,td{border:1px solid #e5e7eb; padding:8px; vertical-align:top; text-align:left}
          th{background:#f3f4f6; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.02em; color:#374151}
          @page { size: A4 portrait; margin: 14mm; }
          @media print { button{display:none} }
        </style></head><body>
          <h1>Appointments – ${escapeHTML(dateTitle)}</h1>
          <div class="sub">Doctor: ${escapeHTML(doctorName)} • Printed: ${new Date().toLocaleString()}</div>
          <table>
            <thead><tr>
              <th>No.</th><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th><th>Notes</th>
            </tr></thead>
            <tbody>${rows}</tbody>
          </table>
          <div style="margin-top:18px"><button onclick="window.print()">Print</button></div>
        </body></html>
      `;
      const w = window.open('', '_blank');
      w.document.open();
      w.document.write(html);
      w.document.close();
      w.onload = () => w.print();
    });

    // ---------- delegated delete handler (attach ONCE) ----------
    dayTbody.addEventListener('click', async (e) => {
      const btn = e.target.closest('.delete-btn');
      if (!btn) return;

      const url = btn.dataset.url;
      if (!url) { alert('Delete URL missing.'); return; }

      const row = btn.closest('tr');
      const who = row?.querySelector('td:nth-child(3)')?.innerText?.trim() || 'this appointment';
      if (!confirm(`Delete ${who}? This cannot be undone.`)) return;

      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.textContent = 'Deleting…';

      try {
        const res = await fetch(url, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrf(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin'
        });

        if (res.status === 204 || res.ok) {
          // refresh calendar events in current range
          const v = _calendar.view;
          const params = {
            start: v.currentStart.toISOString().slice(0,10),
            end:   v.currentEnd.toISOString().slice(0,10)
          };
          const doctorId = doctorFilterSelect?.value || '';
          if (doctorId) params.doctor_id = doctorId;

          fetch(`{{ route('appointments.calendarData') }}?` + new URLSearchParams(params).toString())
            .then(r=>r.json()).then(events => { _calendar.removeAllEvents(); _calendar.addEventSource(events); });

          // reload currently shown day table
          loadDay(rightDate.textContent || todayStr());
          return;
        }

        if (res.status === 419) throw new Error('Session expired (419). Refresh the page and try again.');
        if (res.status === 401) throw new Error('Unauthorized (401).');
        throw new Error(`HTTP ${res.status}`);
      } catch (err) {
        alert('Delete failed: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
      }
    });

    // ---------- create modal ----------
    const modal = document.getElementById('createModal');
    const form  = document.getElementById('createForm');
    const errBox= document.getElementById('createErrors');

    function openCreateModal(dateStr){
      const filterDoc = doctorFilterSelect?.value || '';
      if (filterDoc) document.getElementById('form_doctor').value = filterDoc;

      document.getElementById('form_date').value = dateStr || todayStr();

      const now = new Date();
      const mins = now.getMinutes();
      const rounded = mins < 30 ? 30 : 60;
      now.setMinutes(rounded, 0, 0);
      document.getElementById('form_time').value = now.toTimeString().slice(0,5);

      document.getElementById('form_duration').value = '';
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
        date:        d,
        start_time:  t,
        duration_min:document.getElementById('form_duration').value || null,
        notes:       document.getElementById('form_notes').value || null,
        scheduled_at: d && t ? (d + ' ' + t + ':00') : null
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
        if (r.ok) return r.json().catch(()=>({}));
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
        const v = _calendar.view;
        const params = { start: v.currentStart.toISOString().slice(0,10), end: v.currentEnd.toISOString().slice(0,10) };
        const doctorId = doctorFilterSelect?.value || '';
        if (doctorId) params.doctor_id = doctorId;

        fetch(`{{ route('appointments.calendarData') }}?` + new URLSearchParams(params).toString())
          .then(r=>r.json()).then(events => { _calendar.removeAllEvents(); _calendar.addEventSource(events); });

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
    /* Modal center helper */
    #createModal { display:flex; }
    #createModal.hidden { display:none; }

    /* Sticky header for the scrolling table */
    .table-sticky thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      background: #f3f4f6;
    }

    /* Optional: nicer scroll on webkit */
    #dayScroll { scrollbar-gutter: stable; }
    #dayScroll::-webkit-scrollbar { width: 10px; height: 10px; }
    #dayScroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 8px; }
    #dayScroll::-webkit-scrollbar-track { background: #f3f4f6; }
  </style>
</x-app-layout>
