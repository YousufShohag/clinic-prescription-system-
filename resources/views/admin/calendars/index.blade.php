<x-app-layout>
  <div class="container mx-auto py-8">

    <div class="flex justify-between items-center mb-6">
      {{-- <h2 class="text-2xl font-semibold">Calendar</h2>
      <a href="{{ route('appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Appointment</a> --}}
    </div>

    {{-- Optional: filter by doctor for the calendar --}}
    {{-- <form method="GET" class="mb-4 flex items-end gap-3">
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
    </form> --}}

    {{-- Calendar --}}
    <div class="bg-white rounded shadow p-4 mb-6">
 
      {{-- <div id="calendar"></div> --}}


    <div id="calendar"
        data-calendar-url="{{ route('appointments.calendarData') }}"
        data-day-url="{{ route('appointments.day') }}">
    </div>

      
    </div>

    {{-- Your list/table (kept) --}}
    {{-- <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @forelse($appointments as $appointment)
          <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-2">{{ $appointment->patient->name ?? '-' }}</td>
            <td class="px-4 py-2">{{ $appointment->doctor->name ?? '-' }}</td>
            <td class="px-4 py-2">
              {{ $appointment->scheduled_at?->format('d/m/Y H:i') }}
            </td>
            <td class="px-4 py-2 max-w-[240px] truncate" title="{{ $appointment->notes }}">{{ $appointment->notes ?? '-' }}</td>
            <td class="px-4 py-2 flex flex-wrap gap-2">
              <a href="{{ route('appointments.edit', $appointment) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-sm">Edit</a>
              <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No appointments found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
      @if(method_exists($appointments, 'links'))
        <div class="p-3 border-t bg-gray-50">
          {{ $appointments->withQueryString()->links() }}
        </div>
      @endif
    </div> --}}
  </div>

  {{-- Modal (Day History) --}}
  <div id="dayModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow w-[95vw] max-w-3xl max-h-[85vh] overflow-hidden">
      <div class="flex items-center justify-between p-4 border-b">
        <div>
          <div class="text-lg font-semibold">Appointments on <span id="modalDate"></span></div>
          <div class="text-sm text-gray-600"><span id="modalCount">0</span> item(s)</div>
        </div>
        <button class="px-2 py-1 border rounded" onclick="closeDayModal()">Close</button>
      </div>
      <div id="modalBody" class="p-4 overflow-y-auto" style="max-height:70vh">
        {{-- filled by JS --}}
      </div>
    </div>
  </div>

  {{-- FullCalendar + JS --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  @vite('resources/js/others/calendars-index.js')
  {{-- <script>
    const doctorId = document.getElementById('filter_doctor')?.value || '';
    const modal    = document.getElementById('dayModal');
    const modalDate= document.getElementById('modalDate');
    const modalCount = document.getElementById('modalCount');
    const modalBody  = document.getElementById('modalBody');

    function closeDayModal(){ modal.classList.add('hidden'); }

    function openDayModal(dateStr){
      modal.classList.remove('hidden');
      modalDate.textContent = dateStr;
      modalCount.textContent = '...';
      modalBody.innerHTML = '<div class="text-gray-500">Loading...</div>';

      const params = new URLSearchParams({ date: dateStr });
      if (doctorId) params.append('doctor_id', doctorId);

      fetch(`{{ route('appointments.day') }}?` + params.toString())
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
                <div class="text-sm text-gray-600">${it.status}</div>
              </div>
              <div class="text-sm text-gray-600">Doctor: ${it.doctor}</div>
              ${it.notes ? `<div class="text-sm mt-1">${it.notes}</div>` : ''}
              <div class="mt-2 flex gap-2">
                <a href="${it.show_url}" class="text-blue-600 text-sm hover:underline">Open</a>
                <a href="${it.edit_url}" class="text-blue-600 text-sm hover:underline">Edit</a>
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

    document.addEventListener('DOMContentLoaded', function() {
      const el = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        height: 'auto',
        dayMaxEventRows: 1,
        datesSet: function(info) {
          // Load counts whenever the visible range changes
          const params = new URLSearchParams({
            start: info.startStr,
            end: info.endStr
          });
          if (doctorId) params.append('doctor_id', doctorId);

          fetch(`{{ route('appointments.calendarData') }}?` + params.toString())
            .then(r => r.json())
            .then(events => {
              calendar.removeAllEvents();
              calendar.addEventSource(events);
            })
            .catch(err => console.error(err));
        },
        dateClick: function(info){
          openDayModal(info.dateStr);
        },
        eventClick: function(info){
          const d = info.event.extendedProps?.date || info.event.startStr;
          openDayModal(d);
        }
      });
      calendar.render();
    });
  </script> --}}
</x-app-layout>


