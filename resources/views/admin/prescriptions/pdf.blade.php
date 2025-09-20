{{-- resources/views/admin/prescriptions/pdf.blade.php --}}


{{-- Top toolbar: only show when NOT rendering PDF --}}



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

  // Helper used throughout (same as your TCPDF view)
  $toLines = function ($text) {
      $text = (string)($text ?? '');
      $text = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", $text); // <br> -> \n
      $lines = preg_split('/\r\n|\r|\n/', $text);
      $lines = array_map('trim', $lines);
      $lines = array_filter($lines, fn($s) => $s !== '');
      $strip = function ($s) {
          return preg_replace('/^\s*(?:[-–—*•‣·\x{2022}\x{2023}\x{25E6}\x{2043}])\s+/u', '', $s);
      };
      return array_values(array_map($strip, $lines));
  };
@endphp

<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  {{-- <title>Prescription #{{ $prescription->id }}</title> --}}
  <title></title>
  <style>
    /* IDENTICAL metrics to your TCPDF CSS; only change is the font-family:
       mPDF controller already sets default_font to notosansbengali. */
    body { font-family: notosansbengali, DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    h1,h2,h3 { margin: 0; padding: 0; }
    .muted { color: #555; }
    .small { font-size: 9px; }
    .mb6 { margin-bottom: 6px; }
    .mb8 { margin-bottom: 8px; }
    .mb12 { margin-bottom: 12px; }
    .mb16 { margin-bottom: 16px; }
    .mt8 { margin-top: 8px; }
    .mt16 { margin-top: 16px; }
    .bold { font-weight: 700; }
    .p8 { padding: 8px; }
    .p10 { padding: 10px; }
    .p12 { padding: 12px; }
    .w-100 { width: 100%; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .section-title { font-weight: 600; text-transform: uppercase; font-size: 10px; color: #050505; padding: 0; margin:0; }
    table { border-collapse: collapse; }
    td { vertical-align: top; }
    .title-tight { margin:0 !important; line-height:1; }
    .list-tight  { margin:-6px 0 0 0; padding-left:6px; line-height:1; }
    .list-tight li { margin:0; padding:0; line-height:1; }
    .ul-nopad { margin:0; padding:0; list-style-position:inside; }
    .ul-nopad li { margin:0; padding:0; line-height:1; }
  </style>
</head>
<body>
  {{-- Letterhead --}}
  <table class="w-100 mb8">
    <tr>
      <td style="width:55%; line-height:1.5;">
        <div style="font-size:20px; font-weight:bold; margin:0; padding:0;">
          {{ $doc->name ?? '' }}
        </div>

        @if(filled($doc?->degree))
          <div style="font-size:12px; color:#2a2a2a; margin:0; padding:0;">
            {{ $doc->degree }} -
            @if(filled($doc?->specialization))
              {{ $doc->specialization }}
            @endif
          </div>
        @endif

      
        @if(filled($doc?->bma_registration_number))
          <div style="font-size:12px; color:#2a2a2a; margin:0; padding:0;">
            BMDC Registration Number: {{ $doc->bma_registration_number }}
          </div>
        @endif

        <div style="font-size:12px; color:#2a2a2a; margin:0; padding:0;">
          Medical Officer: Chattagram Medical College Hospital
        </div>
      </td>

      <td style="width:35%; line-height:1.5;" class="text-left">
        <div class="bold" style="font-size:15px; font-weight:bold; margin:0; padding:0;">Chamber: Epic International Hospital</div>
        <div style="font-size:12px; color:#2a2a2a; margin:0; padding:0;">
          Address: 128, Jubilee Road, Tin pool, Chattagram
          Phone for Appointment: 01xxxxxxxxx, 01xxxxxx
          Avaiable: Satuerday-Friday 07.00 PM - 10.00 PM
        </div>
      </td>
    </tr>
  </table>
{{-- 
  <hr style="margin-top:5px;" /> --}}

  {{-- Patient band --}}
  <table class="w-100" role="presentation" style="width:100%; font-size:18px; border-collapse:collapse; border:1px solid #050505; ">
    <tr style="padding:10px;">
      <!-- LEFT -->
      <td style="width:40%; font-size:15px; vertical-align:top; padding:0; line-height:1.5; padding:10px; ">
        <div style="margin:0; padding:0;">
          Patient Name: <span class="bold">{{ $pat->name ?? '—' }}</span>
        </div>

        @if($ageText || $genderText)
          <div style="margin: -1px 0 0 0; padding:0; line-height:0.7;">
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
          <div style="margin: -1px 0 0 0; padding:0; line-height:0.7;">
            Phone: <span class="bold">{{ $phoneText }}</span>
          </div>
        @endif
      </td>

      <!-- MIDDLE -->
      <td style="width:40%; font-size:15px; vertical-align:top; padding:0; line-height:1.5; padding:10px;">
        @if(filled($pat?->id))
          <div style="margin:0; padding:0; line-height:0.7;">Patient ID: <span class="bold">#{{ $pat->id }}</span></div>
        @endif

        @if($bloodText)
          <div style="margin:-1px 0 0 0; padding:0; line-height:0.7;">Blood Group: <span class="bold">{{ $bloodText }}</span></div>
        @endif

        @if($guardianText)
          <div style="margin:-1px 0 0 0; padding:0; line-height:0.7;">Guardian: <span class="bold">{{ $guardianText }}</span></div>
        @endif
      </td>

      <!-- RIGHT -->
      {{-- <td style="width:20%; font-size:15px; vertical-align:top; padding:0; line-height:1.5; padding:10px;">
        <div style="" class="muted">
          Date: <span class="bold" style="color:#111;">{{ $prescription->created_at->format('d-m-Y') }}</span>
        </div>
      </td> --}}
       {{-- Right cell of patient bar --}}
<td style="width:30%; font-size:15px; vertical-align:top; padding:0; line-height:1.5; padding:10px; text-align:right;">
  <div>Prescription #: <strong>{{ $prescription->id }}</strong></div>
          <div style="" class="muted">
              Date: <span class="bold" style="color:#111;">{{ $prescription->created_at->format('d-m-Y') }}</span>
            </div>  

  {{-- @isset($barcodeDataUri)
    <div style="margin-top:1px;">
      <img src="{{ $barcodeDataUri }}" alt="RX Barcode" style="height:28px; width:auto;" />
    </div>
  @endisset --}}
 {{-- 1) Clickable barcode inside PDF --}}
  @isset($barcodeDataUri, $downloadUrl)
    <div style="margin-top:4px;">
      <a href="{{ $downloadUrl }}">
        <img src="{{ $barcodeDataUri }}" alt="RX Barcode" style="height:28px; width:auto;" />
      </a>
    </div>
  @endisset

    {{-- 3) Human-readable short text fallback --}}
  {{-- @isset($downloadUrl)
    <div class="small muted" style="margin-top:2px; word-wrap:break-word;">
      Download: {{ $downloadUrl }}
    </div>
  @endisset --}}

    {{-- 2) QR code for phone scanning (opens the download link) --}}
  {{-- @isset($qrDataUri)
    <div style="margin-top:6px;">
      <img src="{{ $qrDataUri }}" alt="Download QR" style="height:70px; width:70px;" />
    </div>
  @endisset --}}
  {{-- 1) Clickable barcode inside PDF --}}
  {{-- @isset($barcodeDataUri, $downloadUrl)
    <div style="margin-top:4px;">
      <a href="{{ $downloadUrl }}">
        <img src="{{ $barcodeDataUri }}" alt="RX Barcode" style="height:28px; width:auto;" />
      </a>
    </div>
  @endisset --}}


</td>

    </tr>
  </table>

  <div class="mt16 small" style="text-align:right;">
    <span class="muted" style="margin:0; padding:0; font-size:7px;">
      Software Developed By: YoDuPa Limited Chattagram. Email: yodupa@gmail.com
    </span>
  </div>

  {{-- Body --}}
  <table class="w-100 mt16">
    <tr>
      {{-- LEFT column --}}
      <td style="width:40%; padding-right:10px; font-size:15px; vertical-align:top; padding:0; line-height:1.5;">

        {{-- C/C --}}
        @php $ccLines = $hasProblem ? $toLines($prescription->problem_description ?? '') : []; @endphp
        @if(!empty($ccLines))
        {{-- Heading --}}
        <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:2px 0 4px 0;">
          C/C:
        </div>

        {{-- Bullets directly under C/C --}}
        <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
          @foreach($ccLines as $ln)
            <li style="margin:0 0 4px 0; padding:0;">{{ $ln }}</li>
          @endforeach
        </ul>
      @endif


        {{-- Clinical Findings --}}
        @if($hasOE || $hasVitals)
          @if($hasOE)
            @php $oeLines = $toLines($prescription->oe ?? ''); @endphp
            @if(!empty($oeLines))
              {{-- Heading --}}
              <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:8px 0 4px 0;">
                O/E:
              </div>

              {{-- Bullets directly under O/E --}}
              <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
                @foreach($oeLines as $ln)
                  <li style="margin:0 0 4px 0; padding:0;">{{ $ln }}</li>
                @endforeach
              </ul>
            @endif
          @endif


          @if($hasVitals)
            <ul class="small list-dot mb12" style="padding-left:14px; margin:0; line-height:1.25;">
              @if(filled($prescription->bp))                <li>BP: <span class="bold">{{ $prescription->bp }}</span></li> @endif
              @if(filled($prescription->pulse))             <li>Pulse: <span class="bold">{{ $prescription->pulse }}</span> bpm</li> @endif
              @if(filled($prescription->temperature_c))     <li>Temp: <span class="bold">{{ number_format((float)$prescription->temperature_c, 1) }}</span> °C</li> @endif
              @if(filled($prescription->spo2))              <li>SpO₂: <span class="bold">{{ $prescription->spo2 }}</span>%</li> @endif
              @if(filled($prescription->respiratory_rate))  <li>RR: <span class="bold">{{ $prescription->respiratory_rate }}</span> /min</li> @endif
              @if(filled($prescription->weight_kg))         <li>Weight: <span class="bold">{{ number_format((float)$prescription->weight_kg, 1) }}</span> kg</li> @endif
              @if(filled($prescription->height_cm))         <li>Height: <span class="bold">{{ number_format((float)$prescription->height_cm, 1) }}</span> cm</li> @endif
              @if(filled($prescription->bmi))               <li>BMI: <span class="bold">{{ number_format((float)$prescription->bmi, 1) }}</span> kg/m²</li> @endif
            </ul>
          @endif
        @endif

        {{-- Other History (bulleted per line) --}}
        @foreach($nonEmptyHistoryOthers as $field => $label)
          @php
            $histLines = $toLines($prescription->$field ?? '');
          @endphp
          @continue(empty($histLines))

          {{-- Heading --}}
          <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:8px 0 4px 0;">
            {{ $label }}:
          </div>

          {{-- Bullets directly under the heading --}}
          <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
            @foreach($histLines as $ln)
              <li style="margin:0 0 4px 0; padding:0;">{{ $ln }}</li>
            @endforeach
          </ul>
        @endforeach


        {{-- Previous Investigation --}}
        @if($hasPrevInv)
          @php
            $piLines = $toLines($prescription->previous_investigation ?? '');
          @endphp
          @if(!empty($piLines))
            {{-- Heading --}}
            <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:8px 0 4px 0;">
              Previous Investigation:
            </div>

            {{-- Bullets directly under the heading --}}
            <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
              @foreach($piLines as $ln)
                <li style="margin:0 0 4px 0; padding:0;">{{ $ln }}</li>
              @endforeach
            </ul>
          @endif
        @endif


        {{-- Investigation/Tests --}}
        @if($hasTests)
            {{-- Heading --}}
          <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:8px 0 4px 0;">
            Investigation:
          </div>

          {{-- Bullets directly under the heading --}}
          <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
            @foreach($prescription->tests as $t)
              <li style="margin:0 0 4px 0; padding:0;">{{ $t->name }}</li>
            @endforeach
          </ul>
        @endif

         {{-- Next Meeting Date --}}
        @if($hasReturn)
          <table class="w-100" style="font-size:15px; font-weight:600;  line-height:1.3; margin:8px 0 4px 0;">
            <tr><td><div class="bold"><u>Next Meeting Date:</u> <span class="" style="line-height:1.5;">{{ Carbon::parse($prescription->return_date)->format('d/m/Y') }}</span></div></td></tr>
          </table>
        @endif

        {{-- Patient Notes --}}
        @if(filled($pat?->notes))
          <div class="section-title">Patient Notes</div>
          @php $pnLines = $toLines($pat->notes ?? ''); @endphp
          @if($pnLines)
            <ul class="small list-dot" style="padding-left:14px; margin:0; line-height:1.25;">
              @foreach($pnLines as $ln)
                <li>{{ $ln }}</li>
              @endforeach
            </ul>
          @endif
        @endif

      </td>

      {{-- RIGHT column --}}
      <td style="width:60%; padding-left:10px; border-left:1px solid #999;">
        <table class="w-100">
          <tr>
            <td style="width:100%;">
              <div class="bold" style="font-size:28px; font-family:DejaVu Sans, notosansbengali, sans-serif;">℞</div>
              

            </td>
          </tr>
        </table>

        {{-- Medicines --}}
        {{-- Medicines --}}
          @if($hasMeds)
            {{-- Bangla digit map + helper (declare once) --}}
            @php
              $bnDigits = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];
              $toBn = function ($s) use ($bnDigits) { return strtr((string)$s, $bnDigits); };
            @endphp

            <div class="bold mb8" style="font-size:20px; line-height:2; padding:0;">Medicines:</div>

            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;" >
              @foreach($prescription->medicines as $m)
                {{-- @php
                  // Build meta (times/duration) and convert any digits to Bangla
                  $metaParts = [];
                  if (filled($m->pivot->times_per_day ?? null)) $metaParts[] = $m->pivot->times_per_day;
                  if (filled($m->pivot->duration ?? null))     $metaParts[] = ' ' . $m->pivot->duration;
                  $meta = $toBn(implode(' — ', $metaParts));
                @endphp --}}

 @php
    // Map stored keys (if any) to Bangla; pass through if already Bangla
    $mealMap = [
      'before_meal' => 'খাবারের আগে',
      'after_meal'  => 'খাবারের পরে',
      'with_meal'   => 'খাবারের সাথে',
      'midday'      => 'দুপুরে',
      'bedtime'     => 'রাতে শোবার আগে',
    ];
    $mealRaw = $m->pivot->meal_time ?? null;
    $mealBn  = $mealRaw ? ($mealMap[$mealRaw] ?? $mealRaw) : null;

    // Build meta: times/day — meal_time — duration
    $metaParts = [];
    if (filled($m->pivot->times_per_day ?? null)) $metaParts[] = $m->pivot->times_per_day;
    if (filled($mealBn))                           $metaParts[] = $mealBn;          // << here
    if (filled($m->pivot->duration ?? null))       $metaParts[] = $m->pivot->duration;

    // Convert any digits to Bangla
    $meta = $toBn(implode(' — ', $metaParts));
  @endphp

                <tr>
                  <!-- Number gutter (Bangla numerals) -->
                  <td style="width:10mm; text-align:right; vertical-align:top; padding-right:4mm; font-size:17px; line-height:1.6;">
                    {{ $toBn($loop->iteration) }}.
                    {{-- If you prefer Bangla danda instead of dot, use:  {{ $toBn($loop->iteration) }}। --}}
                  </td>

                  <!-- Content -->
                  <td style="vertical-align:top; font-size:17px; line-height:1.6;">
                    <div>
                      @if(filled($m->type))
                        <span class="muted">{{ mb_substr($m->type, 0, 3, 'UTF-8') }}</span>.
                      @endif
                      <span class="bold">{{ $m->name }}</span>
                      @if(filled($m->strength))
                        <span class="muted"> - {{ $m->strength }}</span>
                      @endif
                    </div>

                    @if($meta)
                      <div class="muted" style="margin-top:2px; line-height:1.2;">
                        {{ $meta }}
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </table>
          @endif




        {{-- Doctor Advice --}}
       @if($hasAdvice)
          @php
            $advLines = $toLines($prescription->doctor_advice ?? '');
          @endphp

          @if(!empty($advLines))
            <div class="bold mb6" style="margin-bottom: 6px; font-size:20px; line-height:1.5; padding:0;">উপদেশঃ</div>

            {{-- Bangla digits map (declare once) --}}
            @php
              $bnDigits = ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯'];
            @endphp

            <table class="mb8" width="100%" cellspacing="0" cellpadding="0"
                  style="border-collapse:collapse; font-size:18px; line-height:1.2;">
              @foreach($advLines as $ln)
                <tr>
                  {{-- Single number gutter with Bangla numerals --}}
                  <td style="width:8mm; text-align:right; vertical-align:top; padding-right:4mm;">
                    {{ strtr((string)$loop->iteration, $bnDigits) }}.
                  </td>

                  {{-- Advice text --}}
                  <td style="vertical-align:top;">
                    {{ $ln }}
                  </td>
                </tr>
              @endforeach
            </table>
          @endif
        @endif



       

        {{-- Referred To --}}
       @if($hasReferred)
          @php
            $refLines = $toLines($prescription->referred_to ?? '');
          @endphp
          @if(!empty($refLines))
            {{-- Heading --}}
            <div style="font-size:15px; font-weight:600; text-decoration:underline; line-height:1.3; margin:8px 0 4px 0;">
              Referred To:
            </div>

            {{-- Bullets directly under the heading --}}
            <ul style="margin:0; padding-left:14px; font-size:12px; line-height:1.5; list-style-type:disc;">
              @foreach($refLines as $ln)
                <li style="margin:0 0 4px 0; padding:0;">{{ $ln }}</li>
              @endforeach
            </ul>
          @endif
        @endif

<br>

        {{-- Signature --}}
       <table class="w-100" style="margin-top: 10px;" >
          <tr>
            <td style="width:20%;">&nbsp;   {{-- 2) QR code for phone scanning (opens the download link) --}}
              @isset($qrDataUri)
                <div style="margin-top:6px;">
                  <img src="{{ $qrDataUri }}" alt="Download QR" style="height:70px; width:70px;" />
                </div>
              @endisset
            </td>
            <td style="width:70%; text-align:right;  ">
              <div style="text-align:right; ">
                <span class="" style="border-top:1px solid #999;  display:inline-block; ">
                  {{ $doc->name ?? '' }}
                </span>
              </div>
              @if(filled($doc?->specialization))
                <div class="small muted" style="margin-top:2px;">{{ $doc->specialization }}</div>
              @endif
            </td>
          </tr>
        </table>


      </td>
    </tr>
  </table>

  {{-- Footer (optional; uncomment if you want it fixed like in your other view)
  <div class="text-center mt16 small muted">
    {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
  </div> --}}
</body>
</html>
