<x-app-layout>
<div class="container mx-auto py-6">
    {{-- Print / Back controls (hidden on print) --}}
    <div class="no-print flex items-center justify-between mb-4">
        <a href="{{ route('prescriptions.index') }}" class="text-blue-600 hover:underline">← Back to prescriptions</a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print</button>
    </div>

    <div id="rx-paper" class="mx-auto bg-white shadow md:rounded-lg p-6 md:p-8 max-w-4xl">
        @php
            $doc = $prescription->doctor ?? null;
            $pat = $prescription->patient ?? null;

            // O/E + vitals flags
            $hasOE = filled($prescription->oe);
            $hasVitals = filled($prescription->bp) || filled($prescription->pulse) || filled($prescription->temperature_c)
                || filled($prescription->spo2) || filled($prescription->respiratory_rate)
                || filled($prescription->weight_kg) || filled($prescription->height_cm) || filled($prescription->bmi);

            // History fields
            $historyMap = [
                'ph' => 'P/H', 'dh' => 'D/H', 'mh' => 'M/H', 'oh' => 'OH',
                'pae' => 'P/A/E', 'dx' => 'DX',
                'previous_investigation' => 'Previous Investigation',
                'ah' => 'A/H', 'special_note' => 'Special Note',
                'referred_to' => 'Referred To',
            ];
            $nonEmptyHistory = collect($historyMap)->filter(fn($label, $field) => filled(trim((string)($prescription->$field ?? ''))));

            // We will render these separately
            $hasPrevInv  = filled(trim((string)($prescription->previous_investigation ?? '')));
            $hasReferred = filled(trim((string)($prescription->referred_to ?? '')));

            // Other history excludes prev_inv & referred_to
            $nonEmptyHistoryOthers = $nonEmptyHistory->except(['previous_investigation','referred_to']);

            // Problem / Advice / Return
            $hasProblem = filled($prescription->problem_description);
            $hasAdvice  = filled($prescription->doctor_advice);
            $hasReturn  = filled($prescription->return_date);

            // Medicines / Tests
            $hasMeds  = isset($prescription->medicines) && $prescription->medicines->isNotEmpty();
            $hasTests = isset($prescription->tests) && $prescription->tests->isNotEmpty();

            // Right side shows if any of these exist (tests are on the left now)
            $showRightCol = $hasMeds || $hasAdvice || $hasReferred || $hasReturn;

            // Doctor header bits
            $showDocName = filled($doc?->name);
            $showDegree  = filled($doc?->degree);
            $showSpec    = filled($doc?->specialization);
            $showBMDC    = filled($doc?->bma_registration_number);

            // Patient bar bits
            $showPatientName = filled($pat?->name);
            $showAgeGender   = filled($pat?->age) || filled($pat?->sex);
            $showContact     = filled($pat?->phone) || filled($pat?->email);

            // QR/signature and header ID only when there is an ID
            $showRxId = filled($prescription->id);
        @endphp

        {{-- Letterhead --}}
        <div class="rx-keep flex items-start justify-between">
            <div>
                @if($showDocName)
                    <div class="text-2xl font-bold leading-tight">{{ $doc->name }}</div>
                @endif
                @if($showDegree)
                    <div class="text-sm text-gray-600">{{ $doc->degree }}</div>
                @endif
                @if($showSpec)
                    <div class="text-sm text-gray-600">{{ $doc->specialization }}</div>
                @endif
                @if($showBMDC)
                    <div class="text-sm text-gray-600">BMDC Registration Number: {{ $doc->bma_registration_number }}</div>
                @endif
                <div class="text-sm text-gray-600">Medical Officer: Chattagram Medical College Hospital</div>
            </div>

            <div class="text-right">
                <div class="text-lg font-semibold">
                    {{ config('Savron', 'Chamber: Epic International Hospital') }}
                </div>
                <div class="text-xs text-gray-500 leading-4">
                    {{ config('clinic.address', '128, Jubilee Road, Tin pool, Chattagram') }}<br>
                    {{ config('clinic.phone', 'Phone for Appointment: 01xxxxxxxxx') }}
                </div>
                @if($showRxId)
                    <div class="text-xs text-gray-500 mt-1">Prescription #{{ $prescription->id }}</div>
                @endif
                <div class="text-xs text-gray-500">Date: {{ $prescription->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <hr class="my-4 border-gray-300 rx-keep">

        {{-- Patient bar --}}
        {{-- Patient header (patient-friendly, no image) --}}
@php
    $pat = $prescription->patient ?? null;

    $showPatientName = filled($pat?->name);
    $showRxId        = filled($prescription->id);

    $ageText    = filled($pat?->age) ? ($pat->age . ' yrs') : null;
    $genderText = filled($pat?->sex) ? ucfirst($pat->sex) : null;
    $phoneText  = filled($pat?->phone) ? $pat->phone : null;
    $emailText  = filled($pat?->email) ? $pat->email : null;

    $showContact = $phoneText || $emailText;
@endphp

<section class="rx-keep mt-2 rounded-lg border bg-white">
  {{-- header bar --}}
 

  {{-- content --}}
  {{-- ================= Patient Header (no image) ================= --}}
@php
    $pat = $prescription->patient ?? null;

    $showPatientName = filled($pat?->name);
    $showRxId        = filled($prescription->id);

    $ageText    = filled($pat?->age) ? ($pat->age . ' yrs') : null;
    $genderText = filled($pat?->sex) ? ucfirst($pat->sex) : null;
    $phoneText  = filled($pat?->phone) ? $pat->phone : null;
    $emailText  = filled($pat?->email) ? $pat->email : null;

    $showContact = $phoneText || $emailText;
@endphp

<div class="p-4 flex flex-col md:flex-row md:items-start md:justify-between gap-4 border rounded-lg bg-white rx-keep">
  {{-- LEFT: Name + ID + Age/Gender + Contact --}}
  <div class="min-w-[220px] space-y-1 text-sm">
    @if($showPatientName)
      <div>Patient Name: <span class="font-medium">{{ $pat->name }}</span></div>
    @endif

    @if(filled($pat?->id))
      <div>Patient ID: <span class="font-medium">#{{ $pat->id }}</span></div>
    @endif

    @if($ageText && $genderText)
      <div>Age / Gender: <span class="font-medium">{{ $ageText }}</span> / <span class="font-medium">{{ $genderText }}</span></div>
    @elseif($ageText)
      <div>Age: <span class="font-medium">{{ $ageText }}</span></div>
    @elseif($genderText)
      <div>Gender: <span class="font-medium">{{ $genderText }}</span></div>
    @endif

    @if($showContact)
      <div class="flex flex-wrap gap-x-6 gap-y-1 text-gray-700">
        @if($phoneText)
          <span>Phone: <span class="font-medium">{{ $phoneText }}</span></span>
        @endif
        @if($emailText)
          <span>Email: <span class="font-medium">{{ $emailText }}</span></span>
        @endif
      </div>
    @endif
  </div>

  {{-- RIGHT: Rx ID + Date (+ optional phone again if you want) --}}
  <div class="text-sm text-left md:min-w-[200px] space-y-1">
    @if($showRxId)
      <div class="text-gray-500">Prescription # <span class="font-medium text-black">{{ $prescription->id }}</span></div>
    @endif

    {{-- If you also want phone repeated on the right, keep this block; else remove it --}}
    @if($phoneText)
      <div>Phone: <span class="font-medium">{{ $phoneText }}</span></div>
    @endif

    <div class="text-gray-500">Date: <span class="font-medium text-black">{{ $prescription->created_at->format('d-m-Y H:i') }}</span></div>
  </div>
</div>

</section>


        {{-- Body: LEFT = Problem, CF, Other History, Previous Investigation, Tests. RIGHT = Medicines, Advice, Next Meeting Date, Referred To --}}
        <div class="mt-6 grid grid-cols-12 gap-6">
            {{-- LEFT column --}}
            <div class="col-span-12 md:col-span-4">
                {{-- Problem --}}
                @if($hasProblem)
                <div class="mb-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Problem</div>
                    <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->problem_description }}</div>
                </div>
                @endif

                {{-- Clinical Findings --}}
                @if($hasOE || $hasVitals)
                    <div class="mb-4">
                        <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Clinical Findings</div>

                        @if($hasOE)
                            <div class="text-sm leading-6 mb-2">
                                <span class="font-medium">O/E:</span>
                                <span class="text-gray-700 whitespace-pre-wrap">{{ $prescription->oe }}</span>
                            </div>
                        @endif

                        @if($hasVitals)
                            <div class="space-y-1 text-sm">
                                @if(filled($prescription->bp))  <div><span class="text-gray-500">BP:</span> <span class="font-medium">{{ $prescription->bp }}</span></div> @endif
                                @if(filled($prescription->pulse)) <div><span class="text-gray-500">Pulse:</span> <span class="font-medium">{{ $prescription->pulse }}</span> bpm</div> @endif
                                @if(filled($prescription->temperature_c)) <div><span class="text-gray-500">Temp:</span> <span class="font-medium">{{ number_format((float)$prescription->temperature_c, 1) }}</span> °C</div> @endif
                                @if(filled($prescription->spo2)) <div><span class="text-gray-500">SpO₂:</span> <span class="font-medium">{{ $prescription->spo2 }}</span>%</div> @endif
                                @if(filled($prescription->respiratory_rate)) <div><span class="text-gray-500">RR:</span> <span class="font-medium">{{ $prescription->respiratory_rate }}</span> /min</div> @endif
                                @if(filled($prescription->weight_kg)) <div><span class="text-gray-500">Weight:</span> <span class="font-medium">{{ number_format((float)$prescription->weight_kg, 1) }}</span> kg</div> @endif
                                @if(filled($prescription->height_cm)) <div><span class="text-gray-500">Height:</span> <span class="font-medium">{{ number_format((float)$prescription->height_cm, 1) }}</span> cm</div> @endif
                                @if(filled($prescription->bmi)) <div><span class="text-gray-500">BMI:</span> <span class="font-medium">{{ number_format((float)$prescription->bmi, 1) }}</span> kg/m²</div> @endif
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Other history (except Previous Investigation & Referred To) --}}
                @foreach($nonEmptyHistoryOthers as $field => $label)
                    <div class="mb-4">
                        <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">{{ $label }}</div>
                        <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->$field }}</div>
                    </div>
                @endforeach

                {{-- Previous Investigation (LEFT) --}}
                @if($hasPrevInv)
                <div class="mb-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Previous Investigation</div>
                    <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->previous_investigation }}</div>
                </div>
                @endif

                {{-- Tests (LEFT) --}}
                @if($hasTests)
                <div class="mb-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Tests</div>
                    <ul class="space-y-2">
                        @foreach($prescription->tests as $t)
                            <li class="flex items-start">
                                <div class="mt-1 mr-2">•</div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $t->name }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Optional patient notes --}}
                @if(filled($pat?->notes))
                <div class="mt-4">
                    <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Patient Notes</div>
                    <div class="text-sm leading-6 whitespace-pre-wrap">{{ $pat->notes }}</div>
                </div>
                @endif
            </div>

            {{-- RIGHT column: Medicines, Advice, Next Meeting Date (after Advice), Referred To --}}
            @if($showRightCol)
            <div class="col-span-12 md:col-span-8">
                <div class="flex items-center gap-2 mb-2">
                    <div class="text-2xl font-extrabold">℞</div>
                    <div class="text-sm text-gray-500">Medicines & Advice</div>
                </div>

                {{-- Medicines --}}
                @if($hasMeds)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="font-semibold mb-2">Medicines</div>
                    <ul class="space-y-2">
                        @foreach($prescription->medicines as $m)
                            <li class="flex items-start">
                                <div class="mt-1 mr-2">•</div>
                                <div class="flex-1">
                                    <div class="font-medium">
                                        <span class="text-sm text-gray-600">{{ $m->type }}</span>.
                                        {{ $m->name }}
                                        @if(filled($m->strength))
                                            - <span class="text-sm text-gray-600">{{ $m->strength }}</span>
                                        @endif
                                    </div>
                                    @php
                                        $parts = [];
                                        if(filled($m->pivot->times_per_day)) $parts[] = 'Times/day: '.$m->pivot->times_per_day;
                                        if(filled($m->pivot->duration))     $parts[] = 'Duration: '.$m->pivot->duration;
                                    @endphp
                                    @if(!empty($parts))
                                        <div class="text-sm text-gray-600">{{ implode(' — ', $parts) }}</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Doctor Advice --}}
                @if($hasAdvice)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="font-semibold mb-2">Doctor Advice</div>
                    <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->doctor_advice }}</div>
                </div>
                @endif

                {{-- Next Meeting Date: needs to be AFTER Advice (so placed here) --}}
                @if($hasReturn)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="font-semibold mb-2">Next Meeting Date</div>
                    <div class="text-sm leading-6">
                        {{ \Carbon\Carbon::parse($prescription->return_date)->format('d/m/Y') }}
                    </div>
                </div>
                @endif

                {{-- Referred To (RIGHT as requested) --}}
                @if($hasReferred)
                <div class="border rounded-lg p-4">
                    <div class="font-semibold mb-2">Referred To</div>
                    <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->referred_to }}</div>
                </div>
                @endif

                {{-- QR + Signature: show only if we have an ID --}}
                @if($showRxId)
                    @php
                        $qrText = "RX#{$prescription->id}\nDoctor: " . ($doc->name ?? '-') .
                                  "\nPatient: " . ($pat->name ?? '-');
                        $qrLink = route('prescriptions.show', $prescription->id);
                        $qrPayload = $qrText . "\n" . $qrLink;
                    @endphp
                    <div class="rx-keep mt-10 grid grid-cols-2 gap-8 items-end">
                        <div class="text-left">
                            <div class="flex items-end gap-4">
                                <div class="leading-none">{!! QrCode::size(110)->margin(1)->generate($qrPayload) !!}</div>
                                <div class="text-[11px] text-gray-500 leading-tight">
                                    Scan to view/verify<br>RX #{{ $prescription->id }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="h-12"></div>
                            <div class="border-t border-gray-400 pt-1 text-sm">
                                {{ $doc->name ?? '' }}<br>
                                @if(filled($doc?->specialization))
                                    <span class="text-gray-500">{{ $doc->specialization }}</span><br>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Footer (fixed at bottom for print) --}}
        <div id="rx-footer" class="mt-6 text-center text-[11px] text-gray-500">
            {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
        </div>
    </div>
</div>

<style>
@media print {
  @page { size: A4 portrait; margin: 14mm 12mm 20mm 12mm; }
  html, body { background:#fff!important; margin:0; padding:0; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  .no-print { display:none!important; }

  #rx-paper{
    max-width: calc(210mm - 24mm)!important;
    width:100%!important; margin:0 auto!important; background:#fff!important;
    padding:2rem!important; border-radius:0.5rem!important;
    box-shadow:0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05)!important;
    border:none!important; position:relative; min-height:calc(297mm - 34mm);
  }

  .grid{ display:grid!important; }
  .grid-cols-12{ grid-template-columns:repeat(12,minmax(0,1fr))!important; }
  .gap-6{ gap:1.5rem!important; }
  .md\:col-span-4{ grid-column:span 4 / span 4!important; }
  .md\:col-span-8{ grid-column:span 8 / span 8!important; }
  .rx-keep, .text-right, .border, .rounded-lg, .p-4, .p-6, .p-8 { page-break-inside:avoid; }
  ul, li, table, tr { page-break-inside:avoid; }
  thead { display:table-header-group; }

  #rx-footer{
    position:fixed; bottom:10mm; left:0; right:0; text-align:center;
    font-size:11px; color:#6b7280;
  }
}
</style>
</x-app-layout>
