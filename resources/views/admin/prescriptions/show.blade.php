<x-app-layout>
<div class="container mx-auto py-6">
    {{-- Print / Back controls (hidden on print) --}}
    <div class="no-print flex items-center justify-between mb-4">
        <a href="{{ route('prescriptions.index') }}" class="text-blue-600 hover:underline">← Back to prescriptions</a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print</button>
    </div>

    <div id="rx-paper" class="mx-auto bg-white shadow md:rounded-lg p-6 md:p-8 max-w-4xl">
        {{-- Letterhead --}}
        <div class="rx-keep flex items-start justify-between">
            <div>
                <div class="text-2xl font-bold leading-tight">
                    {{ $prescription->doctor->name ?? 'Doctor Name' }}
                </div>
                @if($prescription->doctor->degree)
                    <div class="text-sm text-gray-600">{{ $prescription->doctor->degree }}</div>
                @endif
                @if($prescription->doctor->specialization)
                    <div class="text-sm text-gray-600">{{ $prescription->doctor->specialization }}</div>
                @endif
                @if($prescription->doctor->bma_registration_number)
                    <div class="text-sm text-gray-600">{{'BMDC Registration Number: '. $prescription->doctor->bma_registration_number }}</div>
                @endif
                <div class="text-sm text-gray-600">{{'Medical Officer: Chattagram Medical College Hospital' }}</div>
                {{-- @if($prescription->doctor->phone || $prescription->doctor->email)
                    <div class="text-xs text-gray-500 mt-1">
                        @if($prescription->doctor->phone) {{ $prescription->doctor->phone }} @endif
                        @if($prescription->doctor->phone && $prescription->doctor->email) • @endif
                        @if($prescription->doctor->email) {{ $prescription->doctor->email }} @endif
                    </div>
                @endif --}}
            </div>

            <div class="text-right">
                <div class="text-lg font-semibold">
                    {{ config('Savron', 'Chamber: Epic International Hospital') }}
                </div>
                <div class="text-xs text-gray-500 leading-4">
                    {{ config('clinic.address', '128, Jubilee Road, Tin pool, Chattagram') }}<br>
                    {{ config('clinic.phone', 'Phone for Appointment: 01xxxxxxxxx') }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Prescription #{{ $prescription->id }}</div>
                <div class="text-xs text-gray-500">Date: {{ $prescription->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <hr class="my-4 border-gray-300 rx-keep">

        {{-- Patient bar --}}
        <div class="rx-keep grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div>
                <div class="text-gray-500">Patient</div>
                <div class="font-medium">{{ $prescription->patient->name }}</div>
            </div>
            <div>
                <div class="text-gray-500">Contact</div>
                <div>
                    @if($prescription->patient->phone) {{ $prescription->patient->phone }} @endif
                    @if($prescription->patient->phone && $prescription->patient->email)  @endif
                    @if($prescription->patient->email)@endif
                    {{-- @if($prescription->patient->email) {{ $prescription->patient->email }} @endif --}}
                </div>
            </div>
            <div>
                <div class="text-gray-500">Recorded</div>
                <div>{{ $prescription->created_at->format('d-m-Y H:i') }}</div>
            </div>
        </div>

        {{-- Body: Left = problem/advice/clinical findings, Right = Medicines & Tests --}}
        <div class="mt-6 grid grid-cols-12 gap-6">
            {{-- Left column --}}
            <div class="col-span-12 md:col-span-4">
                {{-- Problem --}}
                <div class="mb-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Problem</div>
                    <div class="text-sm leading-6">{{ $prescription->problem_description ?: '—' }}</div>
                </div>

                {{-- Clinical Findings --}}
                @php
                    $hasOE = filled($prescription->oe);
                    $hasVitals = filled($prescription->bp) || filled($prescription->pulse) || filled($prescription->temperature_c)
                                 || filled($prescription->spo2) || filled($prescription->respiratory_rate)
                                 || filled($prescription->weight_kg) || filled($prescription->height_cm) || filled($prescription->bmi);
                @endphp

                @if($hasOE || $hasVitals)
                    <div class="mb-4">
                        <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Clinical Findings</div>

                        @if($hasOE)
                            <div class="text-sm leading-6 mb-2">
                                <span class="font-medium">O/E:</span>
                                <span class="text-gray-700">{{ $prescription->oe }}</span>
                            </div>
                        @endif

                        @if($hasVitals)
                            <div class="space-y-1 text-sm">
                                @if(filled($prescription->bp))
                                    <div><span class="text-gray-500">BP:</span> <span class="font-medium">{{ $prescription->bp }}</span></div>
                                @endif
                                @if(filled($prescription->pulse))
                                    <div><span class="text-gray-500">Pulse:</span> <span class="font-medium">{{ $prescription->pulse }}</span> bpm</div>
                                @endif
                                @if(filled($prescription->temperature_c))
                                    <div><span class="text-gray-500">Temp:</span> <span class="font-medium">{{ number_format((float)$prescription->temperature_c, 1) }}</span> °C</div>
                                @endif
                                @if(filled($prescription->spo2))
                                    <div><span class="text-gray-500">SpO₂:</span> <span class="font-medium">{{ $prescription->spo2 }}</span>%</div>
                                @endif
                                @if(filled($prescription->respiratory_rate))
                                    <div><span class="text-gray-500">RR:</span> <span class="font-medium">{{ $prescription->respiratory_rate }}</span> /min</div>
                                @endif
                                @if(filled($prescription->weight_kg))
                                    <div><span class="text-gray-500">Weight:</span> <span class="font-medium">{{ number_format((float)$prescription->weight_kg, 1) }}</span> kg</div>
                                @endif
                                @if(filled($prescription->height_cm))
                                    <div><span class="text-gray-500">Height:</span> <span class="font-medium">{{ number_format((float)$prescription->height_cm, 1) }}</span> cm</div>
                                @endif
                                @if(filled($prescription->bmi))
                                    <div><span class="text-gray-500">BMI:</span> <span class="font-medium">{{ number_format((float)$prescription->bmi, 1) }}</span> kg/m²</div>
                                @endif
                            </div>
                        @endif

                    </div>
                @endif

                {{-- Doctor Advice --}}
                <div class="mb-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Doctor Advice</div>
                    <div class="text-sm leading-6">{{ $prescription->doctor_advice ?: '—' }}</div>
                </div>

              <div class="mb-4">
    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Next Meeting Date</div>
    <div class="text-sm leading-6">
        {{ $prescription->return_date ? \Carbon\Carbon::parse($prescription->return_date)->format('d/m/Y') : '—' }}
    </div>
</div>

                {{-- Optional notes --}}
                @if(!empty($prescription->patient->notes))
                    <div class="mb-4">
                        <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Patient Notes</div>
                        <div class="text-sm leading-6">{{ $prescription->patient->notes }}</div>
                    </div>
                @endif
            </div>

            {{-- Right column (wide): Medicines & Tests --}}
            <div class="col-span-12 md:col-span-8">
                {{-- Rx header --}}
                <div class="flex items-center gap-2 mb-2">
                    <div class="text-2xl font-extrabold">℞</div>
                    <div class="text-sm text-gray-500">Medicines & Tests</div>
                </div>

                {{-- Medicines --}}
                <div class="border rounded-lg p-4 mb-4">
                    <div class="font-semibold mb-2">Medicines</div>
                    @if($prescription->medicines->isEmpty())
                        <div class="text-sm text-gray-500">No medicines</div>
                    @else
                        <ul class="space-y-2">
                            @foreach($prescription->medicines as $m)
                                <li class="flex items-start">
                                    <div class="mt-1 mr-2">•</div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $m->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            @php
                                                $parts = [];
                                                if(!empty($m->pivot->times_per_day)) $parts[] = 'Times/day: '.$m->pivot->times_per_day;
                                                if(!empty($m->pivot->duration)) $parts[] = 'Duration: '.$m->pivot->duration;
                                            @endphp
                                            {{ $parts ? implode(' — ', $parts) : '—' }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Tests --}}
                <div class="border rounded-lg p-4">
                    <div class="font-semibold mb-2">Tests</div>
                    @if($prescription->tests->isEmpty())
                        <div class="text-sm text-gray-500">No tests</div>
                    @else
                        <ul class="space-y-2">
                            @foreach($prescription->tests as $t)
                                <li class="flex items-start">
                                    <div class="mt-1 mr-2">•</div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $t->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            @if(!is_null($t->price)) ৳{{ rtrim(rtrim(number_format((float)$t->price, 2, '.', ''), '0'), '.') }} @else — @endif
                                            @if($t->note) <span class="text-xs text-gray-500">— {{ $t->note }}</span> @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Signature --}}
                {{-- ===== QR + Signatures (two-column) ===== --}}
@php
    // Human-friendly text
    $qrText = "RX#{$prescription->id}\nDoctor: " . ($prescription->doctor->name ?? '-') .
              "\nPatient: " . ($prescription->patient->name ?? '-');

    // Link to the show page (adjust if your route is different)
    $qrLink = route('prescriptions.show', $prescription->id);

    // Final QR payload: readable text + URL
    $qrPayload = $qrText . "\n" . $qrLink;
@endphp

<div class="rx-keep mt-10 grid grid-cols-2 gap-8 items-end">
    {{-- LEFT: QR + patient/guardian signature line --}}
    <div class="text-left">
        <div class="flex items-end gap-4">
            <div class="leading-none">
                {!! QrCode::size(110)->margin(1)->generate($qrPayload) !!}
            </div>
            <div class="text-[11px] text-gray-500 leading-tight">
                Scan to view/verify<br>
                RX #{{ $prescription->id }}
            </div>
        </div>

    </div>

    {{-- RIGHT: Doctor signature line --}}
    <div class="text-right">
        <div class="h-12"></div>
        <div class="border-t border-gray-400 pt-1 text-sm">
            {{ $prescription->doctor->name ?? 'Doctor Name' }}<br>
            <span class="text-gray-500">{{ $prescription->doctor->specialization ?? '' }}</span><br>
            {{-- <span class="text-gray-500">
                {{ 'BMDC Registration Number: ' . ($prescription->doctor->bma_registration_number ?? '') }}
            </span> --}}
        </div>
    </div>
</div>

            </div>
        </div>

        {{-- Footer (fixed at bottom for print) --}}
        <div id="rx-footer" class="mt-6 text-center text-[11px] text-gray-500">
            {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
        </div>
    </div>
</div>



<style>
@media print {
  /* A4 page */
  @page {
    size: A4 portrait;
    margin: 14mm 12mm 20mm 12mm; /* extra bottom for footer */
  }

  html, body {
    background: #fff !important;
    margin: 0;
    padding: 0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .no-print { display: none !important; }

  /* Keep your card look, fit within A4 printable width */
  #rx-paper {
    max-width: calc(210mm - 24mm) !important; /* 210mm - left/right margins */
    width: 100% !important;
    margin: 0 auto !important;
    background: #fff !important;
    padding: 2rem !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,.1),
                0 4px 6px -2px rgba(0,0,0,.05) !important;
    border: none !important;
    position: relative; /* for fixed footer space calc */
    min-height: calc(297mm - 34mm); /* page height - top/bottom margins */
  }

  /* Force grid like md: on print */
  .grid { display: grid !important; }
  .grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
  .gap-6 { gap: 1.5rem !important; }
  .md\:col-span-4 { grid-column: span 4 / span 4 !important; }
  .md\:col-span-8 { grid-column: span 8 / span 8 !important; }
  .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }

  /* Avoid awkward breaks */
  .rx-keep, .text-right, .border, .rounded-lg, .p-4, .p-6, .p-8 { page-break-inside: avoid; }
  ul, li, table, tr { page-break-inside: avoid; }
  thead { display: table-header-group; }

  /* Fixed footer at bottom of page */
  #rx-footer {
    position: fixed;
    bottom: 10mm;   /* adjust if needed */
    left: 0;
    right: 0;
    text-align: center;
    font-size: 11px;
    color: #6b7280;
  }
}
</style>
</x-app-layout>
