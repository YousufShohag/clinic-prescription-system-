{{-- resources/views/admin/prescriptions/pdf_tcpdf.blade.php --}}
@php
  use Illuminate\Support\Carbon;

  $doc = $prescription->doctor ?? null;
  $pat = $prescription->patient ?? null;

  $hasOE = filled($prescription->oe);
  $hasVitals = filled($prescription->bp) || filled($prescription->pulse) || filled($prescription->temperature_c)
      || filled($prescription->spo2) || filled($prescription->respiratory_rate)
      || filled($prescription->weight_kg) || filled($prescription->height_cm) || filled($prescription->bmi);

  $historyMap = [
    'ph' => 'P/H', 'dh' => 'D/H', 'mh' => 'M/H', 'oh' => 'OH',
    'pae' => 'P/A/E', 'dx' => 'DX',
    'previous_investigation' => 'Previous Investigation',
    'ah' => 'A/H', 'special_note' => 'Special Note',
    'referred_to' => 'Referred To',
  ];
  $nonEmptyHistory = collect($historyMap)->filter(
    fn($label, $field) => filled(trim((string)($prescription->$field ?? '')))
  );
  $hasPrevInv  = filled(trim((string)($prescription->previous_investigation ?? '')));
  $hasReferred = filled(trim((string)($prescription->referred_to ?? '')));
  $nonEmptyHistoryOthers = $nonEmptyHistory->except(['previous_investigation','referred_to']);

  $hasProblem = filled($prescription->problem_description);
  $hasAdvice  = filled($prescription->doctor_advice);
  $hasReturn  = filled($prescription->return_date);

  $hasMeds  = isset($prescription->medicines) && $prescription->medicines->isNotEmpty();
  $hasTests = isset($prescription->tests) && $prescription->tests->isNotEmpty();

  $ageText      = filled($pat?->age) ? ($pat->age . ' yrs') : null;
  $genderText   = filled($pat?->sex) ? ucfirst($pat->sex) : null;
  $phoneText    = filled($pat?->phone) ? $pat->phone : null;
  $bloodText    = filled($pat?->blood_group) ? $pat->blood_group : null;
  $guardianText = filled($pat?->guardian_name) ? $pat->guardian_name : null;

  $retText     = $prescription->return_date ? Carbon::parse($prescription->return_date)->format('d-m-Y') : null;
  $nextRetText = $pat?->next_return_date ? Carbon::parse($pat->next_return_date)->format('d-m-Y') : null;

  $showRightCol = $hasMeds || $hasAdvice || $hasReferred || $hasReturn;
@endphp

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Prescription #{{ $prescription->id }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    h1,h2,h3 { margin: 0; padding: 0; }
    .muted { color: #555; }
    .small { font-size: 11px; }
    /* .mb4 { margin-bottom: 40px; } */
    .mb6 { margin-bottom: 6px; }
    .mb8 { margin-bottom: 8px; }
    .mb12 { margin-bottom: 12px; }
    .mb16 { margin-bottom: 16px; }
    .mt8 { margin-top: 8px; }
    .mt16 { margin-top: 16px; }
    .bold { font-weight: 700; }
    .b { border: 1px solid #ddd; }
    .p8 { padding: 8px; }
    .p10 { padding: 10px; }
    .p12 { padding: 12px; }
    .w-100 { width: 100%; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .section-title { font-weight: 700; text-transform: uppercase; font-size: 11px; color: #555; margin-bottom: 6px; }
    .list-dot li { margin-bottom: 3px; }
    table { border-collapse: collapse; }
    td { vertical-align: top; }
  </style>
</head>
<body>

  {{-- Letterhead --}}
  <table class="w-100 mb8">
    <tr>
      <td style="width:55%; line-height:0.8;">
        <div style="font-size:12px; font-weight:bold; margin:0; padding:0;">
          {{ $doc->name ?? '' }}
        </div>

        @if(filled($doc?->degree))
          <div style="font-size:10px; color:#555; margin:0; padding:0;">
            {{ $doc->degree }} - 
              @if(filled($doc?->specialization))
              {{ $doc->specialization }}
              @endif
          </div>
        @endif

        {{-- @if(filled($doc?->specialization))
          <div style="font-size:10px; color:#555; margin:0; padding:0;">
            {{ $doc->specialization }}
          </div>
        @endif --}}

        @if(filled($doc?->bma_registration_number))
          <div style="font-size:10px; color:#555; margin:0; padding:0;">
            BMDC Registration Number: {{ $doc->bma_registration_number }}
          </div>
        @endif

        <div style="font-size:10px; color:#777; margin:0; padding:0;">
          Medical Officer: Chattagram Medical College Hospital
        </div>
    </td>

      <td style="width:45%; line-height:0.7;" class="text-right" >
        <div class="bold " style="margin:0; padding:0;">Chamber: Epic International Hospital</div>
        {{-- <div class="" style="margin:0; padding:0; font-size:10px;">
          {{ config('clinic.address', '128, Jubilee Road, Tin pool, Chattagram') }}<br>
          {{ config('clinic.phone', 'Phone for Appointment: 01xxxxxxxxx') }}<br>
          {{ config('clinic.available', 'Satuerday-Friday (07.00 PM - 10.00 PM) ') }}
        </div> --}}
        <div style="font-size:9px; color:#555; margin:0; padding:0;">
           Address: 128, Jubilee Road, Tin pool, Chattagram
          </div>
          <div style="font-size:9px; color:#555; margin:0; padding:0;">
            Phone: Phone for Appointment: 01xxxxxxxxx
          </div>
          <div style="font-size:9px; color:#555; margin:0; padding:0;">
           Avaiable: Satuerday-Friday 07.00 PM - 10.00 PM
          </div>
      </td>
    </tr>
  </table>

  <hr class="mb8" />

  {{-- Patient band --}}
  <table class="w-100 b p8">
    <tr>
      {{-- LEFT --}}
      <td style="width:40%;">
        <div class="small mb4">
          Patient Name: <span class="bold">{{ $pat->name ?? '—' }}</span>
        </div>

        @if($ageText || $genderText)
          <div class="small mb4">
            @if($ageText && $genderText)
              Age / Gender: <span class="bold">{{ $ageText }}</span> / <span class="bold">{{ $genderText }}</span>
            @elseif($ageText)
              Age: <span class="bold">{{ $ageText }}</span>
            @else
              Gender: <span class="bold">{{ $genderText }}</span>
            @endif
          </div>
        @endif

        @if($phoneText)
          <div class="small mb4">Phone: <span class="bold">{{ $phoneText }}</span></div>
        @endif
      </td>

      {{-- MIDDLE --}}
      <td style="width:30%;">
        @if(filled($pat?->id))
          <div class="small mb4">Patient ID: <span class="bold">#{{ $pat->id }}</span></div>
        @endif

        @if($bloodText)
          <div class="small mb4">Blood Group: <span class="bold">{{ $bloodText }}</span></div>
        @endif

        @if($guardianText)
          <div class="small mb4">Guardian: <span class="bold">{{ $guardianText }}</span></div>
        @endif

        @if($nextRetText && (!$retText || $nextRetText !== $retText))
          <div class="small muted">Next Visit (Patient): <span class="bold" style="color:#111;">{{ $nextRetText }}</span></div>
        @endif
      </td>

      {{-- RIGHT --}}
      <td style="width:30%;">
        <div class="small muted">Date: <span class="bold" style="color:#111;">{{ $prescription->created_at->format('d-m-Y H:i') }}</span></div>
        {{-- Barcode is drawn by TCPDF (controller); we leave space here --}}
        <div class="small muted mt8">Barcode: RX-{{ $prescription->id }}</div>
      </td>
    </tr>
  </table>

  {{-- Body --}}
  <table class="w-100 mt16">
    <tr>
      {{-- LEFT column --}}
      <td style="width:40%; padding-right:10px;">

        @if($hasProblem)
          <div class="section-title">C/C</div>
          <div class="small mb12" style="line-height:1.5;">{{ $prescription->problem_description }}</div>
        @endif

        @if($hasOE || $hasVitals)
          <div class="section-title">Clinical Findings</div>

          @if($hasOE)
            <div class="small mb6"><span class="bold">O/E:</span> {{ $prescription->oe }}</div>
          @endif

          @if($hasVitals)
            <div class="small mb12">
              @if(filled($prescription->bp))  <div>BP: <span class="bold">{{ $prescription->bp }}</span></div> @endif
              @if(filled($prescription->pulse)) <div>Pulse: <span class="bold">{{ $prescription->pulse }}</span> bpm</div> @endif
              @if(filled($prescription->temperature_c)) <div>Temp: <span class="bold">{{ number_format((float)$prescription->temperature_c, 1) }}</span> °C</div> @endif
              @if(filled($prescription->spo2)) <div>SpO₂: <span class="bold">{{ $prescription->spo2 }}</span>%</div> @endif
              @if(filled($prescription->respiratory_rate)) <div>RR: <span class="bold">{{ $prescription->respiratory_rate }}</span> /min</div> @endif
              @if(filled($prescription->weight_kg)) <div>Weight: <span class="bold">{{ number_format((float)$prescription->weight_kg, 1) }}</span> kg</div> @endif
              @if(filled($prescription->height_cm)) <div>Height: <span class="bold">{{ number_format((float)$prescription->height_cm, 1) }}</span> cm</div> @endif
              @if(filled($prescription->bmi)) <div>BMI: <span class="bold">{{ number_format((float)$prescription->bmi, 1) }}</span> kg/m²</div> @endif
            </div>
          @endif
        @endif

        {{-- Other History (except Prev.Inv. & Referred) --}}
        @foreach($nonEmptyHistoryOthers as $field => $label)
          <div class="section-title">{{ $label }}</div>
          <div class="small mb12" style="line-height:1.5;">{{ $prescription->$field }}</div>
        @endforeach

        {{-- Previous Investigation --}}
        @if($hasPrevInv)
          <div class="section-title">Previous Investigation</div>
          <div class="small mb12" style="line-height:1.5;">{{ $prescription->previous_investigation }}</div>
        @endif

        {{-- Tests --}}
        @if($hasTests)
          <div class="section-title">Investigation</div>
          <ul class="small list-dot mb12" style="padding-left: 14px;">
            @foreach($prescription->tests as $t)
              <li>{{ $t->name }}</li>
            @endforeach
          </ul>
        @endif

        {{-- Patient Notes --}}
        @if(filled($pat?->notes))
          <div class="section-title">Patient Notes</div>
          <div class="small" style="line-height:1.5;">{{ $pat->notes }}</div>
        @endif
      </td>

      {{-- RIGHT column --}}
      <td style="width:60%; padding-left:10px;">
        <table class="w-100">
          <tr>
            <td style="width:100%;">
              <div class="bold" style="font-size:18px; display:inline;">℞</div>
              <span class="muted small">Medicines & Advice</span>
            </td>
          </tr>
        </table>

        {{-- Medicines --}}
        @if($hasMeds)
          <table class="w-100 b p10 mb12">
            <tr><td><div class="bold mb6">Medicines</div></td></tr>
            <tr>
              <td>
                <table class="w-100">
                  @foreach($prescription->medicines as $m)
                    @php
                      $parts = [];
                      if(filled($m->pivot->times_per_day ?? null)) $parts[] = 'Times/day: '.$m->pivot->times_per_day;
                      if(filled($m->pivot->duration ?? null))     $parts[] = 'Duration: '.$m->pivot->duration;
                      $meta = implode(' — ', $parts);
                    @endphp
                    <tr>
                      <td style="width:4mm;">•</td>
                      <td>
                        <div class="small">
                          <span class="muted">{{ $m->type }}</span>. <span class="bold">{{ $m->name }}</span>
                          @if(filled($m->strength)) <span class="muted"> - {{ $m->strength }}</span> @endif
                          @if($meta) <div class="muted">{{ $meta }}</div> @endif
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </table>
              </td>
            </tr>
          </table>
        @endif

        {{-- Doctor Advice --}}
        @if($hasAdvice)
          <table class="w-100 b p10 mb12">
            <tr><td><div class="bold mb6">Doctor Advice</div></td></tr>
            <tr><td class="small" style="line-height:1.5;">{{ $prescription->doctor_advice }}</td></tr>
          </table>
        @endif

        {{-- Next Meeting Date --}}
        @if($hasReturn)
          <table class="w-100 b p10 mb12">
            <tr><td><div class="bold mb6">Next Meeting Date</div></td></tr>
            <tr><td class="small" style="line-height:1.5;">{{ Carbon::parse($prescription->return_date)->format('d/m/Y') }}</td></tr>
          </table>
        @endif

        {{-- Referred To --}}
        @if($hasReferred)
          <table class="w-100 b p10">
            <tr><td><div class="bold mb6">Referred To</div></td></tr>
            <tr><td class="small" style="line-height:1.5;">{{ $prescription->referred_to }}</td></tr>
          </table>
        @endif

        {{-- Signature --}}
        <table class="w-100 mt16">
          <tr>
            <td style="width:50%;">&nbsp;</td>
            <td style="width:50%; text-align:right;">
              <div style="border-top:1px solid #999; padding-top:4px;" class="small">
                {{ $doc->name ?? '' }}<br>
                @if(filled($doc?->specialization))
                  <span class="muted">{{ $doc->specialization }}</span><br>
                @endif
              </div>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>

  {{-- Footer --}}
  <div class="text-center mt16 small muted">
    {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
  </div>

</body>
</html>
