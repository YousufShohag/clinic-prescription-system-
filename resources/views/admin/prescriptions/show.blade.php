{{-- resources/views/prescriptions/show.blade.php --}}
<x-app-layout>
  <div class="container mx-auto  ">
    {{-- Print / Back controls (hidden on print) --}}
    <div class="no-print flex items-center justify-between mb-4 py-4">
      <a href="{{ route('prescriptions.index') }}" class="text-blue-600 hover:underline">← Back to prescriptions</a>
      <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Print</button>
       <a href="{{ route('prescriptions.pdf.tcpdf', $prescription->id) }}" target="_blank"
      class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
      Print as PDF (TCPDF)
    </a>
    <a href="{{ route('prescriptions.pdf.mpdf', $prescription->id) }}" target="_blank"
   class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
  Print as PDF (mPDF)
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

    <div id="rx-paper" class="mx-auto bg-white shadow md:rounded-lg p-6 md:p-8 max-w-4xl">

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

      {{-- <hr class="my-2 border-gray-300 rx-keep"> --}}

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

      <section class="rx-keep bg-white" style="border:1.5px solid;">
        <div id="patient-bar" class="p-2 grid grid-cols-1 md:grid-cols-3 gap-4   bg-white rx-keep">
          {{-- LEFT: Name + Age/Gender + Contact --}}
          <div class="min-w-[220px] space-y-1 text-sm">
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
          <div class="text-sm space-y-1">
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
          <div class="text-sm text-left md:min-w-[200px] space-y-1">
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
      </section>
<div style=" font-size:10px;" class=" text-right">Software Developed By: YoDuPa Limited Chattagram. Email: yodupa@gmail.com</div>
      {{-- Body: LEFT = Problem, Clinical Findings, Other History, Previous Investigation, Tests. RIGHT = Medicines, Advice, Next Meeting Date, Referred To --}}
      <div id="rx-body" class="mt-4 grid grid-cols-12 gap-6">
        {{-- LEFT column --}}
        <div class="col-span-12 md:col-span-4">
          {{-- Problem --}}
          @if($hasProblem)
            <div class="text-sm">
             <div class="font-medium "><b><u>C/C:</u></b></div>
              <div class="text-sm  whitespace-pre-wrap">{{ $prescription->problem_description }}</div>
            </div>
          @endif

          {{-- Clinical Findings --}}
          @if($hasOE || $hasVitals)
            <div class="mb-2">
              {{-- <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Clinical Findings</div> --}}

              @if($hasOE)
                <div class="text-sm ">
                   <div class="font-medium "><b><u>O/E:</u></b></div>
                  <span class="text-sm  whitespace-pre-wrap">{{ $prescription->oe }}</span>
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
            <div class="mb-1">
              <div class="font-medium"><b><u>{{ $label .':' }}</u></b></div>
              <div class="text-sm  whitespace-pre-wrap">{{ $prescription->$field }}</div>
            </div>
          @endforeach

          {{-- Previous Investigation (LEFT) --}}
          @if($hasPrevInv)
            <div class="mb-4">
              <div class="font-medium"><b><u>Previous Investigation</u></b></div>
              {{-- <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Previous Investigation</div> --}}
              <div class="text-sm  whitespace-pre-wrap">{{ $prescription->previous_investigation }}</div>
            </div>
          @endif

          {{-- Tests (LEFT) --}}
          @if($hasTests)
            <div class="mb-4">
              <div class="font-medium"><b><u>Investigation</u></b></div>
              {{-- <div class="uppercase tracking-wider text-xs text-gray-500 mb-1">Investigation</div> --}}
              <ul class="">
                @foreach($prescription->tests as $t)
                  <li class="flex items-start">
                    <div class="mr-1">•</div>
                    <div class="flex-1">
                      <div class="text-sm  whitespace-pre-wrap">{{ $t->name }}</div>
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
          <div class="col-span-12 md:col-span-8" style="border-left:1px solid;">
            <div class="flex items-center gap-2 ">
              <div class="text-2xl font-extrabold" style="padding-left: 10px;">℞</div>
              {{-- <div class="text-sm text-gray-500">Medicines & Advice</div> --}}
            </div>

            {{-- Medicines --}}
            @if($hasMeds)
              <div class="rounded-lg mb-4" style="padding-left: 25px;">
                <div class="font-semibold mb-2">Medicines</div>
                <ol class="list-decimal space-y-2 pl-5">
                  @foreach($prescription->medicines as $m)
                    <li>
                      <div class="flex-1">
                        <div class="font-medium">
                          <span class="text-sm">{{ Str::substr($m->type, 0, 3) }}</span>.
                          {{ $m->name }}
                          @if(filled($m->strength))
                            - <span class="text-sm text-gray-600">{{ $m->strength }}</span>
                          @endif
                        </div>
                        @php
                          $parts = [];
                          if(filled($m->pivot->times_per_day ?? null)) $parts[] = ''.$m->pivot->times_per_day;
                          if(filled($m->pivot->duration ?? null))     $parts[] = ' '.$m->pivot->duration;
                        @endphp
                        @if(!empty($parts))
                          <div class="text-sm text-gray-600">{{ implode(' — ', $parts) }}</div>
                        @endif
                      </div>
                    </li>
                  @endforeach
                </ol>
              </div>

            @endif

            {{-- Doctor Advice --}}
            @if($hasAdvice)
              <div class=" rounded-lg pl-6 mb-2">
                <div class="font-semibold ">Doctor Advice</div>
                <div class="text-sm whitespace-pre-wrap">{{ $prescription->doctor_advice }}</div>
              </div>
            @endif

            {{-- Next Meeting Date --}}
            @if($hasReturn)
              <div class=" rounded-lg pl-4 mb-1">
                <div class="font-semibold ">Next Meeting Date: {{ \Carbon\Carbon::parse($prescription->return_date)->format('d/m/Y') }}</div>
                {{-- <div class="text-sm leading-6">
                  {{ \Carbon\Carbon::parse($prescription->return_date)->format('d/m/Y') }}
                </div> --}}
              </div>
            @endif

            {{-- Referred To --}}
            @if($hasReferred)
              <div class=" rounded-lg pl-4">
                <div class="font-semibold ">Referred To</div>
                <div class="text-sm leading-6 whitespace-pre-wrap">{{ $prescription->referred_to }}</div>
              </div>
            @endif

            {{-- Signature --}}
            <div class="rx-keep mt-9 grid grid-cols-2 gap-8 items-end">
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
      <div id="rx-footer" class="mt-6 text-center text-[11px] text-gray-500">
        <span>Thank you for visiting. For emergencies, please contact the clinic immediately</span>     
      </div>
    </div>
  </div>

  {{-- PRINT CSS --}}
  <style>
  /* HARDENED A4 PRINT RULES */
  @media print {
  @page { size: A4 portrait; margin: 0mm 0mm 0mm 0mm; }

  html, body {
    background: #fff !important;
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
/* Chrome only CSS */
@supports (-webkit-touch-callout: none) {
  body {
    margin: 0;
  }
}
  /* --- Patient bar: lock to one horizontal row with 3 columns on print --- */
#patient-bar{
  display: flex !important;
  flex-wrap: nowrap !important;       /* keep all three blocks on the same row */
  align-items: flex-start !important;
  justify-content: space-between !important;
  gap: 8mm !important;
  break-inside: avoid !important;
  page-break-inside: avoid !important;
  padding: 6px 8px !important;
}

/* Column widths: 40% | 30% | 30% (matches your screenshot) */
#patient-bar > div:nth-child(1){ flex: 0 0 30% !important; min-width: 0 !important; }
#patient-bar > div:nth-child(2){ flex: 0 0 30% !important; min-width: 0 !important; }
#patient-bar > div:nth-child(3){ flex: 0 0 20% !important; min-width: 0 !important; }

/* Keep field labels + values tidy */
#patient-bar > div > div{
  display: block !important;
  white-space: nowrap !important;     /* avoid mid-line wraps like 'Age / Gender' */
  line-height: 1.25 !important;
  margin-bottom: 2px !important;
}

/* Date + barcode compact, aligned right like the image */
#patient-bar > div:nth-child(3){
  text-align: right !important;
}
#patient-bar svg{
  height: 26px !important;            /* adjust if you want bigger/smaller */
  width: auto !important;
  display: inline-block !important;
  margin-top: 2px !important;
}

/* Slightly smaller type so the whole row fits neatly */
#patient-bar, #patient-bar *{
  font-size: 9.5pt !important;
}


  .no-print { display: none !important; }

  /* Paper container: no min-height, no positioning quirks */
  #rx-paper{
    /* max-width: calc(210mm - 20mm) !important; 10+10mm page side margins */
    width: 110% !important;

    background: #fff !important;
    padding: 5mm 0mm 0mm 0mm !important; /* inner gutter so it breathes */

    border-radius: 6px !important;          /* keeps your nice rounded look */
    box-shadow: 0 0 0 rgba(0,0,0,0) !important;  drop shadow not needed on print
    border: 1px solid #e5e7eb !important;   /* subtle outline */

    position: static !important;
    overflow: visible !important;
  }

  /* Header & patient bar should not split */
  .rx-keep, thead, img, svg {
    break-inside: avoid !important;
    page-break-inside: avoid !important;
  }

  /* ===== Two-column PRINT layout (robust for pagination) ===== */
  /* Use flex for print (Grid sometimes refuses to break rows) */
  #rx-body{
    display: flex !important;
    gap: 10mm !important;
  }
  /* left = ~38%, right = ~62% like your screenshot */
  #rx-body > .md\:col-span-4 { width: 38% !important; }
  #rx-body > .md\:col-span-8 { width: 62% !important; border-left: 2px solid !important; }

  /* If the right column is absent, left takes full width */
  #rx-body > :only-child { width: 100% !important; }

  /* Allow cards/lists to break normally (prevents big white gaps) */
  /* (Do NOT blanket-avoid breaks on .border/.p- classes) */

  /* Tighter cards for Medicines/Advice look */
  /* .border { border-color: #030303 !important; }
  .rounded-lg { border-radius: 8px !important; } */
  .p-4 { padding: 10px !important; }
  .mb-4 { margin-bottom: 8px !important; }

  /* Typography sizing tuned to match your sample */
  body { font-size: 11pt !important; line-height: 1.35 !important; }
  .text-sm { font-size: 10pt !important; }
  .text-xs { font-size: 9pt  !important; }
  .text-2xl { font-size: 16pt !important; font-weight: 700 !important; }
  .font-semibold, .font-medium { font-weight: 600 !important; }
  .uppercase { letter-spacing: .06em !important; }

  /* Barcode: keep compact */
  svg { height: 28px !important; width: auto !important; }

  /* Footer: flow after content (no fixed position) */
  /* #rx-footer{
    position: static !important;
    margin-top: 6mm !important;
    text-align: center !important;
    font-size: 9pt !important;
    color: #121313 !important;
  } */

  #rx-footer {
  position: fixed !important;
  bottom: 0 !important;
  left: 0 !important;
  width: 100% !important;
  text-align: center !important;
  font-size: 9pt !important;
  color: #121313 !important;
  background: #fff !important; /* optional, to avoid overlap text visibility */
  padding: 5px 0 !important;
}

  /* Avoid widows/orphans on titles */
  h1,h2,h3,.text-xl,.text-2xl { break-after: avoid !important; }
}

  </style>
</x-app-layout>
