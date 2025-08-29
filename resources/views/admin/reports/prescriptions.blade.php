<x-app-layout>
  <div class="w-full min-h-screen bg-white p-6 md:p-8">
    {{-- Header --}}
    <div class="no-print flex items-center justify-between mb-6">
      <div>
        <h2 class="text-3xl font-semibold">Prescriptions Story</h2>
        <div class="text-sm text-gray-600 mt-1">
          @if($doctor) <span class="mr-2">Doctor: <span class="font-medium">{{ $doctor->name }}</span></span>@endif
          @if($patient) <span class="mr-2">Patient: <span class="font-medium">{{ $patient->name }}</span></span>@endif
          <span>Date: {{ $from }} → {{ $to }}</span>
          <span class="ml-2">Total: <span class="font-medium">{{ $total }}</span></span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('reports.prescriptions.export', request()->query()) }}"
           class="px-3 py-2 border rounded hover:bg-gray-50">Export CSV</a>
        <button onclick="window.print()" class="px-3 py-2 border rounded hover:bg-gray-50">Print</button>
        <a href="{{ url()->previous() }}"
           class="px-3 py-2 border rounded hover:bg-gray-50">Back</a>
      </div>
    </div>

    {{-- Filters sticky bar (optional refine) --}}
    <form method="GET" action="{{ route('reports.prescriptions') }}"
          class="no-print grid grid-cols-1 md:grid-cols-6 gap-3 mb-6">
      <input type="hidden" name="doctor_id" value="{{ $filters['doctor_id'] ?? '' }}">
      <input type="hidden" name="patient_id" value="{{ $filters['patient_id'] ?? '' }}">
      <div>
        <label class="block text-sm text-gray-700">From</label>
        <input type="date" name="from" value="{{ $from }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm text-gray-700">To</label>
        <input type="date" name="to" value="{{ $to }}" class="w-full border rounded px-3 py-2">
      </div>
      <div class="md:col-span-3">
        <label class="block text-sm text-gray-700">Search (problem / advice / O/E)</label>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="w-full border rounded px-3 py-2" placeholder="e.g., fever">
      </div>
      <div class="md:col-span-6">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
        <a href="{{ route('reports.prescriptions', ['doctor_id' => $doctor?->id, 'patient_id' => $patient?->id, 'from' => $from, 'to' => $to]) }}"
           class="px-4 py-2 border rounded hover:bg-gray-50 ml-2">Reset</a>
      </div>
    </form>

    {{-- Timeline / list --}}
    <div class="space-y-4">
      @forelse($prescriptions as $rx)
        <div class="border rounded-lg p-4">
          <div class="flex items-start justify-between">
            <div>
              <div class="text-sm text-gray-500">
                #{{ $rx->id }} • {{ optional($rx->created_at)->format('d M Y, h:i A') }}
              </div>
              <div class="text-lg font-semibold">
                {{ $rx->patient->name ?? '—' }}
                <span class="text-sm text-gray-500">
                  @if($rx->patient?->phone) • {{ $rx->patient->phone }} @endif
                </span>
              </div>
              <div class="text-sm text-gray-600">
                <span class="font-medium">Doctor:</span> {{ $rx->doctor->name ?? '—' }}
                @if($rx->doctor?->specialization) • {{ $rx->doctor->specialization }} @endif
              </div>
            </div>
            <div class="no-print">
              <a href="{{ route('prescriptions.show', $rx->id) }}"
                 class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">
                View
              </a>
            </div>
          </div>

          <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <div class="text-xs text-gray-500 uppercase mb-1">Problem</div>
              <div class="text-sm">{{ $rx->problem_description ?: '—' }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 uppercase mb-1">Medicines</div>
              <div class="text-sm">{{ $rx->medicines_count }} item(s)</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 uppercase mb-1">Tests</div>
              <div class="text-sm">{{ $rx->tests_count }} item(s)</div>
            </div>
          </div>

          @if($rx->return_date)
            <div class="mt-3 text-sm text-gray-600">
              <span class="font-medium">Next visit:</span>
              {{ \Illuminate\Support\Carbon::parse($rx->return_date)->format('Y-m-d') }}
            </div>
          @endif
        </div>
      @empty
        <div class="border rounded p-6 text-center text-gray-500">
          No prescriptions found for this selection.
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $prescriptions->withQueryString()->links() }}
    </div>
  </div>

  <style>
    @media print {
      .no-print { display: none !important; }
      a[href]:after { content: ""; }
    }
  </style>
</x-app-layout>
