{{-- resources/views/prescriptions/show.blade.php --}}
<x-app-layout>
    {{-- Print / Back controls (hidden on print) --}}
    <div class="no-print flex items-center justify-between px-2 py-2">
      <a href="{{ route('prescriptions.index') }}" class="text-blue-600 hover:underline">← Back to prescriptions</a>
      <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-2">Print</button>
       <a href="{{ route('prescriptions.pdf.tcpdf', $prescription->id) }}" target="_blank"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Print as PDF (TCPDF)
      </a>
    </div>

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

    <div id="rx-paper" class="mx-auto bg-white md:rounded-lg p-6 md:p-8 max-w-4xl">

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
            {{ config('clinic.phone', 'Phone for Appointment: 01xxxxxxxxx') }} <br>
            {{ config('clinic.available', 'Satuerday-Friday (07.00 PM - 10.00 PM) ') }}
          </div>
          {{-- Optional header meta
          @if($showRxId)
            <div class="text-xs text-gray-500 mt-1">Prescription #{{ $prescription->id }}</div>
          @endif
          <div class="text-xs text-gray-500">Date: {{ $prescription->created_at->format('d M Y, h:i A') }}</div>
          --}}
        </div>
      </div>

      <hr class="my-2 border-gray-300 rx-keep">

      {{-- ================= Patient Header (no image) ================= --}}
      @php
        $pat = $prescription->patient ?? null;

        $showPatientName = filled($pat?->name);
        $showRxId        = filled($prescription->id);

        $ageText      = filled($pat?->age) ? ($pat->age . ' yrs') : null;
        $genderText   = filled($pat?->sex) ? ucfirst($pat->sex) : null;
        $phoneText    = filled($pat?->phone) ? $pat->phone : null;
        $emailText    = filled($pat?->email) ? $pat->email : null;
        $bloodText    = filled($pat?->blood_group) ? $pat->blood_group : null;
        $guardianText = filled($pat?->guardian_name) ? $pat->guardian_name : null;

        $showContact = $phoneText || $emailText;
      @endphp

      
      <div class="rx-keep mt-2 rounded-lg border bg-white grid grid-cols-12 gap-6">
          {{-- LEFT: Name + Age/Gender + Contact --}}
          <div class="col-span-12 md:col-span-4 space-y-1 text-sm">
            @if($showPatientName)
              <div>Patient Name: <span class="font-medium">{{ $pat->name }}</span></div>
            @endif

            @if($ageText && $genderText)
              <div>Age / Gender:
                <span class="font-medium">{{ $ageText }}</span> /
                <span class="font-medium">{{ $genderText }}</span>
              </div>
            @elseif($ageText)
              <div>Age: <span class="font-medium">{{ $ageText }}</span></div>
            @elseif($genderText)
              <div>Gender: <span class="font-medium">{{ $genderText }}</span></div>
            @endif

            @if($phoneText)
              <div>Phone: <span class="font-medium">{{ $phoneText }}</span></div>
            @endif
            {{-- If you want to show email too, uncomment:
            @if($emailText)
              <div>Email: <span class="font-medium">{{ $emailText }}</span></div>
            @endif
            --}}
          </div>

          {{-- MIDDLE: IDs + misc --}}
          <div class="col-span-12 md:col-span-4 space-y-1 text-sm">
            @php
              $retText     = $prescription->return_date
                              ? \Illuminate\Support\Carbon::parse($prescription->return_date)->format('d-m-Y')
                              : null;
              $nextRetText = $pat?->next_return_date
                              ? \Illuminate\Support\Carbon::parse($pat->next_return_date)->format('d-m-Y')
                              : null;
            @endphp

            @if(filled($pat?->id))
              <div>Patient ID: <span class="font-medium">#{{ $pat->id }}</span></div>
            @endif

            @if($bloodText)
              <div>Blood Group: <span class="font-medium">{{ $bloodText }}</span></div>
            @endif
            @if($guardianText)
              <div>Guardian: <span class="font-medium">{{ $guardianText }}</span></div>
            @endif

            {{-- @if($nextRetText && (!$retText || $nextRetText !== $retText))
              <div class="text-gray-600">Next Visit (Patient): <span class="font-medium text-black">{{ $nextRetText }}</span></div>
            @endif --}}
          </div>

          {{-- RIGHT: Date + Barcode --}}
          <div class="col-span-12 md:col-span-4 space-y-1 text-sm">
            <div class="text-gray-500">Date:
              <span class="font-medium text-black">{{ $prescription->created_at->format('d-m-Y H:i') }}</span>
            </div>

            @if($showRxId)
              @php
                $barcodeValue = "RX-{$prescription->id}";
                $dns = new Milon\Barcode\DNS1D();
                // $dns->setStorPath(storage_path('framework/barcodes')); // optional
                $barcodeSvg = $dns->getBarcodeSVG($barcodeValue, 'C128', 1.8, 40, 'black', true);
              @endphp
              <div class="leading-none">{!! $barcodeSvg !!}</div>
            @endif
          </div>
      </div>

      {{-- Body: LEFT = Problem, Clinical Findings, Other History, Previous Investigation, Tests. RIGHT = Medicines, Advice, Next Meeting Date, Referred To --}}
      <div class="mt-6 grid grid-cols-12 gap-6">
        {{-- LEFT column --}}
        <div class="col-span-12 md:col-span-4">
          {{-- Problem --}}
          @if($hasProblem)
            <div class="mb-4">
              <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">C/C</div>
              <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->problem_description }}</div>
            </div>
          @endif

          {{-- Clinical Findings --}}
          @if($hasOE || $hasVitals)
            <div class="mb-4">
              <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Clinical Findings</div>

              @if($hasOE)
                <div class="text-sm leading-6 mb-2">
                  <span class="font-medium">O/E:</span><br>
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
            <div class="mb-2">
              <div class="uppercase tracking-wide text-[11px] text-gray-500 mb-0.5">{{ $label }}</div>
              <div class="text-[13px] leading-5 whitespace-pre-wrap">{{ $prescription->$field }}</div>
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
              <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Investigation</div>
              <ul class="space-y-2">
                @foreach($prescription->tests as $t)
                  <li class="flex items-start">
                    <div class="mr-1">•</div>
                    <div class="flex-1">
                      <div class="text-sm leading-6 whitespace-pre-wrap">{{ $t->name }}</div>
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

        {{-- RIGHT column: Medicines, Advice, Next Meeting Date, Referred To --}}
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
                          if(filled($m->pivot->times_per_day ?? null)) $parts[] = 'Times/day: '.$m->pivot->times_per_day;
                          if(filled($m->pivot->duration ?? null))     $parts[] = 'Duration: '.$m->pivot->duration;
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

            {{-- Next Meeting Date --}}
            @if($hasReturn)
              <div class="border rounded-lg p-4 mb-4">
                <div class="font-semibold mb-2">Next Meeting Date</div>
                <div class="text-sm leading-6">
                  {{ \Carbon\Carbon::parse($prescription->return_date)->format('d/m/Y') }}
                </div>
              </div>
            @endif

            {{-- Referred To --}}
            @if($hasReferred)
              <div class="border rounded-lg p-4">
                <div class="font-semibold mb-2">Referred To</div>
                <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->referred_to }}</div>
              </div>
            @endif

            {{-- Signature --}}
            <div class="rx-keep mt-10 grid grid-cols-2 gap-8 items-end">
              <div class="text-left"></div>
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
          </div>
        @endif
      </div>

      {{-- Footer (fixed at bottom for print) --}}
      <div id="rx-footer" class="text-center text-[11px] text-gray-500">
        {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
      </div>
    </div>

  {{-- PRINT CSS --}}
  <style>
  /* HARDENED A4 PRINT RULES */
  @media print {
    /* Control page size + margins (browser headers/footers OFF) */
    @page {
      size: A4 portrait;
      margin: 14mm 12mm 10mm 12mm; /* top right bottom left */
    }

    html, body {
      background: #fff !important;
      margin: 0 !important;
      padding: 0 !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .no-print { display: none !important; }

    /* Lock the sheet to the remaining area inside @page margins */
    #rx-paper{
      /* Total A4: 210 × 297mm. Subtract our @page margins */
      max-width: calc(210mm - (12mm + 12mm)) !important;
      min-height: calc(297mm - (14mm + 20mm)) !important;

      width: 100% !important;
      margin: 0 auto !important;
      background: #fff !important;

      /* inner padding so content doesn’t slam into edges */
      padding: 20px 18px !important; /* ~5–6mm visual padding */

      /* rounded + shadow will print because of color-adjust above */
      border-radius: 8px !important;
      box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -2px rgba(0,0,0,.05) !important;

      border: none !important;
      position: relative !important;
      overflow: visible !important;
    }

    /* Ensure responsive grid works in print */
    .grid{ display: grid !important; }
    .grid-cols-12{ grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
    .gap-6{ gap: 1.5rem !important; }
    .md\:col-span-4{ grid-column: span 4 / span 4 !important; }
    .md\:col-span-8{ grid-column: span 8 / span 8 !important; }

    /* Keep critical blocks intact across page breaks */
    .rx-keep,
    .border,
    .rounded-lg,
    .p-2, .p-4, .p-6, .p-8,
    .text-right,
    ul, li, table, thead, tr, img, svg {
      break-inside: avoid !important;
      page-break-inside: avoid !important;
    }
    thead { display: table-header-group !important; }

    /* Sticky footer inside the page box */
    #rx-footer{
      position: fixed !important;
      left: 0; right: 0;
      bottom: 10mm; /* sits above bottom margin */
      text-align: center;
      font-size: 11px;
      color: #6b7280;
    }

    /* Paper-friendly typography */
    body { font-size: 12pt !important; line-height: 1.35 !important; }
    h1,h2,h3,.text-2xl,.text-xl { break-after: avoid !important; }
    svg { width: auto; height: auto; }
  }
  </style>
</x-app-layout>
