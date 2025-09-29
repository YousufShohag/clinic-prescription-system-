<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Dashboard</h2>
  </x-slot>

  <!-- <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8"> -->
  <div class="py-8 mx-auto sm:px-6 lg:px-8 space-y-8" style="max-width: 1600px;">
    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-5 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Patients</div>
        <div class="mt-1 text-3xl font-semibold text-indigo-600">{{ number_format($totalPatient) }}</div>
      </div>

      <div class="bg-white p-5 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Prescriptions</div>
        <div class="mt-1 text-3xl font-semibold text-blue-600">{{ number_format($totalPrescription) }}</div>
      </div>

      <div class="bg-white p-5 rounded-lg shadow">
        <div class="text-sm text-gray-500">Appointments Today</div>
        <div class="mt-1 text-3xl font-semibold text-emerald-600">{{ number_format($appointmentsTodayCount) }}</div>
      </div>

      <div class="bg-white p-5 rounded-lg shadow">
        <div class="text-sm text-gray-500">Appointments Tomorrow</div>
        <div class="mt-1 text-3xl font-semibold text-orange-600">{{ number_format($appointmentsTomorrowCount) }}</div>
      </div>
    </div>

    {{-- Today & Tomorrow side-by-side --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Today --}}
      <div class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <div>
            <div class="font-semibold">Today’s Appointments</div>
            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::today()->toFormattedDateString() }}</div>
          </div>
          <span class="text-sm text-gray-600">Total: <b>{{ $appointmentsTodayCount }}</b></span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left">Time</th>
                <th class="px-3 py-2 text-left">Patient</th>
                <th class="px-3 py-2 text-left">Doctor</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Notes</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($todayAppointments as $a)
                <tr class="hover:bg-gray-50">
                  <td class="px-3 py-2 whitespace-nowrap">
                    @if(isset($a->scheduled_at))
                      {{ \Carbon\Carbon::parse($a->scheduled_at)->format('H:i') }}
                    @elseif(isset($a->start_time))
                      {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}
                    @else — @endif
                  </td>
                  <td class="px-3 py-2">
                    {{ $a->patient->name ?? '—' }}
                    @if(!empty($a->patient?->phone))
                      <div class="text-xs text-gray-500">{{ $a->patient->phone }}</div>
                    @endif
                  </td>
                  <td class="px-3 py-2">{{ $a->doctor->name ?? '—' }}</td>
                  <td class="px-3 py-2">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                      @class([
                        'bg-emerald-50 text-emerald-700' => $a->status === 'completed',
                        'bg-yellow-50 text-yellow-700'   => $a->status === 'scheduled' || $a->status === 'checked_in',
                        'bg-red-50 text-red-700'         => $a->status === 'cancelled',
                        'bg-gray-100 text-gray-700'      => !in_array($a->status, ['completed','scheduled','checked_in','cancelled']),
                      ])
                    ">
                      {{ ucfirst($a->status ?? '—') }}
                    </span>
                  </td>
                  <td class="px-3 py-2 max-w-[240px] truncate" title="{{ $a->notes }}">{{ $a->notes ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No appointments today.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Tomorrow --}}
      <div class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
          <div>
            <div class="font-semibold">Tomorrow’s Appointments</div>
            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::tomorrow()->toFormattedDateString() }}</div>
          </div>
          <span class="text-sm text-gray-600">Total: <b>{{ $appointmentsTomorrowCount }}</b></span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 py-2 text-left">Time</th>
                <th class="px-3 py-2 text-left">Patient</th>
                <th class="px-3 py-2 text-left">Doctor</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-left">Notes</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              @forelse($tomorrowAppointments as $a)
                <tr class="hover:bg-gray-50">
                  <td class="px-3 py-2 whitespace-nowrap">
                    @if(isset($a->scheduled_at))
                      {{ \Carbon\Carbon::parse($a->scheduled_at)->format('H:i') }}
                    @elseif(isset($a->start_time))
                      {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}
                    @else — @endif
                  </td>
                  <td class="px-3 py-2">
                    {{ $a->patient->name ?? '—' }}
                    @if(!empty($a->patient?->phone))
                      <div class="text-xs text-gray-500">{{ $a->patient->phone }}</div>
                    @endif
                  </td>
                  <td class="px-3 py-2">{{ $a->doctor->name ?? '—' }}</td>
                  <td class="px-3 py-2">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs
                      @class([
                        'bg-emerald-50 text-emerald-700' => $a->status === 'completed',
                        'bg-yellow-50 text-yellow-700'   => $a->status === 'scheduled' || $a->status === 'checked_in',
                        'bg-red-50 text-red-700'         => $a->status === 'cancelled',
                        'bg-gray-100 text-gray-700'      => !in_array($a->status, ['completed','scheduled','checked_in','cancelled']),
                      ])
                    ">
                      {{ ucfirst($a->status ?? '—') }}
                    </span>
                  </td>
                  <td class="px-3 py-2 max-w-[240px] truncate" title="{{ $a->notes }}">{{ $a->notes ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No appointments tomorrow.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</x-app-layout>
