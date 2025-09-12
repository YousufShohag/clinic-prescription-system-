// resources/js/others/calendars-index.js

(function () {
  // Grab important DOM nodes
  const calEl     = document.getElementById('calendar');
  const modal     = document.getElementById('dayModal');
  const modalDate = document.getElementById('modalDate');
  const modalCount= document.getElementById('modalCount');
  const modalBody = document.getElementById('modalBody');

  if (!calEl) return;

  // Read URLs from data-* (set in Blade)
  const CAL_URL = calEl.dataset.calendarUrl; // route('appointments.calendarData')
  const DAY_URL = calEl.dataset.dayUrl;      // route('appointments.day')

  // Helper to read doctor filter live (if present)
  const getDoctorId = () => document.getElementById('filter_doctor')?.value || '';

  // Expose closeDayModal globally (because Blade has an inline onclick on the Close btn)
  window.closeDayModal = function closeDayModal() {
    modal?.classList.add('hidden');
  };

  function openDayModal(dateStr) {
    if (!modal) return;

    modal.classList.remove('hidden');
    modalDate.textContent = dateStr;
    modalCount.textContent = '...';
    modalBody.innerHTML = '<div class="text-gray-500">Loading...</div>';

    const params = new URLSearchParams({ date: dateStr });
    const doctorId = getDoctorId();
    if (doctorId) params.append('doctor_id', doctorId);

    fetch(`${DAY_URL}?${params.toString()}`)
      .then(r => r.json())
      .then(data => {
        modalCount.textContent = data.count ?? 0;
        const items = data.items || [];
        if (items.length === 0) {
          modalBody.innerHTML = '<div class="text-gray-500">No appointments.</div>';
          return;
        }
        const html = items.map(it => `
          <div class="border rounded p-3 mb-3">
            <div class="flex items-center justify-between">
              <div class="font-semibold">${it.time} — ${it.patient} ${it.phone ? '<span class="text-xs text-gray-500">(' + it.phone + ')</span>' : ''}</div>
              <div class="text-sm text-gray-600">${it.status ?? ''}</div>
            </div>
            <div class="text-sm text-gray-600">Doctor: ${it.doctor ?? ''}</div>
            ${it.notes ? `<div class="text-sm mt-1">${it.notes}</div>` : ''}
            <div class="mt-2 flex gap-2">
              ${it.show_url ? `<a href="${it.show_url}" class="text-blue-600 text-sm hover:underline">Open</a>` : ''}
              ${it.edit_url ? `<a href="${it.edit_url}" class="text-blue-600 text-sm hover:underline">Edit</a>` : ''}
            </div>
          </div>
        `).join('');
        modalBody.innerHTML = html;
      })
      .catch(err => {
        console.error(err);
        modalBody.innerHTML = '<div class="text-red-600">Failed to load.</div>';
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    // FullCalendar via CDN exposes global "FullCalendar"
    if (!window.FullCalendar) {
      console.error('FullCalendar not found. Make sure the CDN script is loaded before this file.');
      return;
    }

    const calendar = new FullCalendar.Calendar(calEl, {
      initialView: 'dayGridMonth',
      height: 'auto',
      dayMaxEventRows: 1,

      datesSet: function (info) {
        if (!CAL_URL) return;
        const params = new URLSearchParams({
          start: info.startStr,
          end: info.endStr
        });
        const doctorId = getDoctorId();
        if (doctorId) params.append('doctor_id', doctorId);

        fetch(`${CAL_URL}?${params.toString()}`)
          .then(r => r.json())
          .then(events => {
            calendar.removeAllEvents();
            calendar.addEventSource(events || []);
          })
          .catch(err => console.error(err));
      },

      dateClick: function (info) {
        openDayModal(info.dateStr);
      },

      eventClick: function (info) {
        const d = (info.event.extendedProps && info.event.extendedProps.date) || info.event.startStr;
        openDayModal(d);
      }
    });

    calendar.render();

    // If the doctor filter exists and changes, re-run the datesSet fetch
    const filter = document.getElementById('filter_doctor');
    if (filter) {
      filter.addEventListener('change', () => {
        // Force refresh by triggering datesSet logic
        const view = calendar.view;
        calendar.dispatch({ type: 'datesSet', start: view.currentStart, end: view.currentEnd, startStr: view.currentStartStr, endStr: view.currentEndStr, timeZone: calendar.getOption('timeZone') || 'local' });
      });
    }
  });
})();
