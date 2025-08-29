<x-app-layout>
  <div class="w-full min-h-screen bg-white p-6 md:p-8">
    <div class="no-print flex items-center justify-between mb-6">
      <h2 class="text-3xl font-semibold">Doctor → Patients Report</h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('reports.doctorPatients.export', request()->query()) }}"
           class="px-3 py-2 border rounded hover:bg-gray-50">Export CSV</a>
        <button onclick="window.print()" class="px-3 py-2 border rounded hover:bg-gray-50">Print</button>
      </div>
    </div>

    {{-- Filters --}}
    @php
      $preselectDoctorId = (string) ($filters['doctor_id'] ?? '');
    @endphp
    <form method="GET" action="{{ route('reports.doctorPatients') }}" class="no-print grid grid-cols-1 md:grid-cols-6 gap-3 mb-6">
      <div class="md:col-span-2">
        <label class="block text-sm text-gray-700">Doctor</label>
        <select name="doctor_id" class="w-full border rounded px-3 py-2">
          @foreach($doctors as $d)
            <option value="{{ $d->id }}" @selected((string)$d->id === $preselectDoctorId)>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-700">From</label>
        <input type="date" name="from" value="{{ $from }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm text-gray-700">To</label>
        <input type="date" name="to" value="{{ $to }}" class="w-full border rounded px-3 py-2">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm text-gray-700">Search patient (name/phone/email)</label>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="e.g., Rahim / 01xxx">
      </div>
      <div class="md:col-span-6">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
        <a href="{{ route('reports.doctorPatients') }}" class="px-4 py-2 border rounded hover:bg-gray-50 ml-2">Reset</a>
      </div>
    </form>

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="border rounded p-4 bg-gray-50">
        <div class="text-sm text-gray-600">Doctor</div>
        <div class="text-xl font-semibold">{{ $doctor?->name ?? '—' }}</div>
        <div class="text-xs text-gray-500">{{ $from }} → {{ $to }}</div>
      </div>
      <div class="border rounded p-4 bg-gray-50">
        <div class="text-sm text-gray-600">Unique Patients (in range)</div>
        <div class="text-2xl font-semibold">{{ number_format($uniquePatients) }}</div>
      </div>
      <div class="border rounded p-4 bg-gray-50">
        <div class="text-sm text-gray-600">Total Prescriptions (in range)</div>
        <div class="text-2xl font-semibold">{{ number_format($totalPrescriptions) }}</div>
      </div>
    </div>

    {{-- Table --}}
    <div class="border rounded">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left px-3 py-2 border">Patient</th>
              <th class="text-left px-3 py-2 border">Phone</th>
              <th class="text-right px-3 py-2 border">Rx Count</th>
              <th class="text-left px-3 py-2 border">First Visit</th>
              <th class="text-left px-3 py-2 border">Last Visit</th>
              <th class="text-left px-3 py-2 border no-print">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $r)
              <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 border">
                  {{ $r->patient_name ?? '—' }}
                  <div class="text-xs text-gray-500">ID: {{ $r->patient_id }}</div>
                </td>
                <td class="px-3 py-2 border">{{ $r->patient_phone ?? '—' }}</td>
                <td class="px-3 py-2 border text-right font-semibold">{{ $r->rx_count }}</td>
                <td class="px-3 py-2 border">{{ $r->first_at ? \Illuminate\Support\Carbon::parse($r->first_at)->format('Y-m-d') : '—' }}</td>
                <td class="px-3 py-2 border">{{ $r->last_at ? \Illuminate\Support\Carbon::parse($r->last_at)->format('Y-m-d') : '—' }}</td>
                <td class="px-3 py-2 border no-print">
                  <a class="text-blue-600 hover:underline"
                    href="{{ route('reports.prescriptions', ['doctor_id' => $doctor?->id, 'patient_id' => $r->patient_id, 'from' => $from, 'to' => $to]) }}">
                    View prescriptions
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">No patients found in this range.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">
        {{ $rows->withQueryString()->links() }}
      </div>
    </div>
  </div>

  <style>
    @media print {
      .no-print { display: none !important; }
      .card { box-shadow: none !important; border: 1px solid #ccc; }
      a[href]:after { content: ""; } /* no raw URLs */
    }
  </style>
</x-app-layout>
