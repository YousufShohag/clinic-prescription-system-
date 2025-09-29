{{-- resources/views/admin/prescriptions/index.blade.php --}}
<x-app-layout>
  <!-- <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8"> -->
  <div class="mx-auto py-8 px-4 sm:px-6 lg:px-8 max-w-8xl">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
      <h2 class="text-2xl font-semibold">All Prescriptions</h2>
      <a href="{{ route('prescriptions.create') }}"
         class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + New Prescription
      </a>
    </div>

    {{-- Filter bar --}}
   <form method="GET" class="bg-white shadow rounded p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

    {{-- Search --}}
    <div class="md:col-span-3">
      <label class="block text-xs text-gray-600 mb-1">Search</label>
      <input
        type="text"
        name="q"
        value="{{ old('q', isset($q) ? $q : '') }}"
        placeholder="Patient/Doctor/Problem or #ID"
        class="w-full border rounded px-3 py-2"
      />
    </div>

    {{-- Doctor --}}
    <div class="md:col-span-2">
      <label class="block text-xs text-gray-600 mb-1">Doctor</label>
      <select name="doctor_id" class="w-full border rounded px-3 py-2">
        <option value="">All</option>
        @foreach(isset($doctors) ? $doctors : [] as $d)
          <option value="{{ $d->id }}" {{ (isset($doctorId) && $doctorId == $d->id) ? 'selected' : '' }}>
            {{ $d->name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Return status --}}
    <div class="md:col-span-2">
      <label class="block text-xs text-gray-600 mb-1">Return status</label>
      @php $__status = isset($status) ? $status : ''; @endphp
      <select name="status" class="w-full border rounded px-3 py-2">
        <option value="">Any</option>
        <option value="overdue"  {{ $__status === 'overdue'  ? 'selected' : '' }}>Overdue</option>
        <option value="today"    {{ $__status === 'today'    ? 'selected' : '' }}>Today</option>
        <option value="upcoming" {{ $__status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
        <option value="none"     {{ $__status === 'none'     ? 'selected' : '' }}>No return</option>
      </select>
    </div>

    {{-- Date range --}}
    <div class="md:col-span-3">
      <label class="block text-xs text-gray-600 mb-1">Date range</label>
      <div class="grid grid-cols-2 gap-2">
        <input
          type="date"
          name="from"
          value="{{ (isset($from) && $from) ? (is_string($from) ? $from : $from->format('Y-m-d')) : '' }}"
          class="w-full border rounded px-3 py-2"
        />
        <input
          type="date"
          name="to"
          value="{{ (isset($to) && $to) ? (is_string($to) ? $to : $to->format('Y-m-d')) : '' }}"
          class="w-full border rounded px-3 py-2"
        />
      </div>
    </div>

    {{-- Actions (same row) --}}
    <div class="md:col-span-2 flex items-end justify-end gap-2">
      <button class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-black">Apply</button>
      <a href="{{ route('prescriptions.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Reset</a>
    </div>

    </div>
    </form>


    <div class="bg-white shadow rounded overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient & Problem</th>
            {{-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th> --}}
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return</th>
            {{-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th> --}}
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-100">
          @php
            $today = now()->startOfDay();
          @endphp

          @forelse ($prescriptions as $p)
            @php
              $ret = $p->return_date
                ? \Illuminate\Support\Carbon::parse($p->return_date)->startOfDay()
                : null;

              $state   = '—';
              $badge   = 'bg-gray-100 text-gray-700';
              $rowTint = '';

              if ($ret) {
                if ($ret->lt($today)) {
                  $state = 'Overdue';  $badge = 'bg-red-100 text-red-800';    $rowTint = 'bg-red-50/40';
                } elseif ($ret->isSameDay($today)) {
                  $state = 'Today';    $badge = 'bg-amber-100 text-amber-800'; $rowTint = 'bg-amber-50/40';
                } else {
                  $state = 'Upcoming'; $badge = 'bg-green-100 text-green-800'; $rowTint = 'bg-green-50/40';
                }
              }

              $medCount  = isset($p->medicines_count) ? $p->medicines_count : ((isset($p->medicines) && $p->medicines) ? $p->medicines->count() : 0);
              $testCount = isset($p->tests_count)     ? $p->tests_count     : ((isset($p->tests) && $p->tests) ? $p->tests->count() : 0);
            @endphp

            {{-- Main row --}}
            <tr class="hover:bg-gray-50 {{ $rowTint }}">
              <td class="px-4 py-3 text-sm text-gray-700">#{{ $p->id }}</td>

              <td class="px-4 py-3">
                <div class="text-sm font-medium">
  @php
    $pat    = $p->patient ?? null;
    $pid    = $pat->id   ?? null;
    $pname  = $pat->name ?? '—';
    $ageTxt = isset($pat) && $pat->age !== null ? ($pat->age.'y') : '';
    $sexTxt = isset($pat) && !empty($pat->sex) ? ucfirst($pat->sex) : '';
  @endphp

  @if($pid)<span class="text-gray-500">#{{ $pid }}</span> - @endif{{ $pname }}

  @if($ageTxt || $sexTxt)
    <span class="text-gray-500 font-normal">
      • {{ $ageTxt }}@if($sexTxt)/{{ $sexTxt }}@endif
    </span>
  @endif
</div>

                @if(!empty($p->problem_description))
                  <div class="text-xs text-gray-600">
                    {{ \Illuminate\Support\Str::limit($p->problem_description, 80) }}
                  </div>
                @endif
                <button type="button"
                        class="mt-1 text-xs text-blue-600 hover:underline"
                        data-toggle="row-{{ $p->id }}">Preview</button>
              </td>

              {{-- <td class="px-4 py-3 text-sm">
                {{ isset($p->doctor) && $p->doctor ? $p->doctor->name : '—' }}
              </td> --}}

              <td class="px-4 py-3 text-sm">
                <div>{{ $p->created_at->format('d M Y, h:i A') }}</div>
                <div class="text-xs text-gray-500">{{ $p->created_at->diffForHumans() }}</div>
              </td>

              <td class="px-4 py-3">
                <div class="text-sm">{{ $ret ? $ret->format('d/m/Y') : 'No return' }}</div>
                <div class="mt-1">
                  <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $badge }}">{{ $state }}</span>
                </div>
              </td>

              {{-- <td class="px-4 py-3 text-sm">
                <span class="inline-block bg-blue-50 text-blue-800 text-xs px-2 py-0.5 rounded">
                  Meds: {{ $medCount }}
                </span>
                <span class="inline-block bg-purple-50 text-purple-800 text-xs px-2 py-0.5 rounded ml-1">
                  Tests: {{ $testCount }}
                </span>
              </td> --}}

              <td class="px-4 py-3">
                <div class="flex flex-wrap items-center gap-3 text-sm">
                  <a href="{{ route('prescriptions.show', $p->id) }}" class="text-blue-600 hover:underline">View</a>
                  <a href="{{ route('prescriptions.edit', $p->id) }}" class="text-green-600 hover:underline">Edit</a>
                  <a href="{{ route('prescriptions.pdf.mpdf', $p->id) }}" target="_blank" class="text-purple-700 hover:underline">Print</a>
                  <form action="{{ route('prescriptions.destroy', $p->id) }}"
                        method="POST" class="inline"
                        onsubmit="return confirm('Delete this prescription?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                  </form>
                </div>
              </td>
            </tr>

            {{-- Inline preview row (Medicines, Tests, Advice only) --}}
<tr id="row-{{ $p->id }}" class="hidden">
  <td colspan="7" class="px-6 py-4">
    <div class="rounded border {{ $rowTint }} p-4 text-sm">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        {{-- Medicines --}}
        <div>
          <div class="font-semibold mb-1">Medicines</div>
          @php
            $meds = (isset($p->medicines) && $p->medicines) ? $p->medicines : collect();
          @endphp
          @if($meds->count())
            <ol class="list-decimal pl-5 space-y-1">
              @foreach($meds as $m)
                @php
                  $parts = [];
                  if (!empty($m->pivot) && !empty($m->pivot->times_per_day)) $parts[] = $m->pivot->times_per_day;
                  if (!empty($m->pivot) && !empty($m->pivot->duration))     $parts[] = $m->pivot->duration;
                @endphp
                <li>
                  <div class="font-medium">
                    {{ isset($m->type) ? substr($m->type, 0, 3).'.' : '' }}
                    {{ $m->name ?? '' }}
                    @if(!empty($m->strength))
                      <span class="text-xs text-gray-600">({{ $m->strength }})</span>
                    @endif
                  </div>
                  @if(!empty($parts))
                    <div class="text-xs text-gray-600">{{ implode(' — ', $parts) }}</div>
                  @endif
                </li>
              @endforeach
            </ol>
          @else
            <div class="text-gray-500">—</div>
          @endif
        </div>

        {{-- Tests --}}
        <div>
          <div class="font-semibold mb-1">Tests</div>
          @php
            $tests = (isset($p->tests) && $p->tests) ? $p->tests : collect();
          @endphp
          @if($tests->count())
            <ul class="list-disc pl-5 space-y-1">
              @foreach($tests as $t)
                <li>{{ $t->name ?? 'Test' }}</li>
              @endforeach
            </ul>
          @else
            <div class="text-gray-500">—</div>
          @endif
        </div>

        {{-- Advice --}}
        <div>
          <div>
                      <div class="font-semibold mb-1">Advice</div>
                      <div class="whitespace-pre-wrap text-gray-700">{{ !empty($p->doctor_advice) ? $p->doctor_advice : '—' }}</div>
                    </div>
        </div>
      </div>
    </div>
  </td>
</tr>

          @empty
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                No prescriptions found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="p-4">
        {{ $prescriptions->links() }}
      </div>
    </div>
  </div>

    {{-- Tiny JS for preview toggles --}}
    @vite('resources/js/others/prescriptions-index.js')

</x-app-layout>
