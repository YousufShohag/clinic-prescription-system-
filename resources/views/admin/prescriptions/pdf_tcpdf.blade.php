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
    .small { font-size: 9px; }
    /* .mb4 { margin-bottom: 40px; } */
    .mb6 { margin-bottom: 6px; }
    .mb8 { margin-bottom: 8px; }
    .mb12 { margin-bottom: 12px; }
    .mb16 { margin-bottom: 16px; }
    .mt8 { margin-top: 8px; }
    .mt16 { margin-top: 16px; }
    .bold { font-weight: 700; }
    /* .b { border: 1px solid #ddd; } */
    .p8 { padding: 8px; }
    .p10 { padding: 10px; }
    .p12 { padding: 12px; }
    .w-100 { width: 100%; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .section-title { font-weight: 600; text-transform: uppercase; font-size: 10px; color: #050505; padding: 0; margin:0; }
    /* .list-dot li { margin-bottom: 2px; } */
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
        {{-- <div style="font-size:9px; color:#555; margin:0; padding:0;">
           Address: 128, Jubilee Road, Tin pool, Chattagram
          </div> --}}
          <div style="font-size:9px; color:#555; margin:0; padding:0; line-height:1.2; ">
           Address: 128, Jubilee Road, Tin pool, Chattagram
           Phone: Phone for Appointment: 01xxxxxxxxx
          Avaiable: Satuerday-Friday 07.00 PM - 10.00 PM
          </div>
         
      </td>
    </tr>
  </table>
<br>
  <hr style="margin-top:20px;" />

  {{-- Patient band --}}
  <table class="w-100  " role="presentation" style="width:100%; font-size:8px; border-collapse:collapse; border:1px solid #050505; padding-bottom:5px;">
  <tr>
    <!-- LEFT -->
    <td style="width:40%; font-size:9px; vertical-align:top; padding:0; line-height:1;">
      
        <div style="margin:0; padding:0; line-height:0.7;">
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
    <td style="width:40%; font-size:9px; vertical-align:top; padding:0; line-height:1;">
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
    <td style="width:20%; vertical-align:top; padding:0; line-height:1; text-align:right;">
      <div style="margin:0; padding:0; line-height:1;" class="muted">
        Date: <span class="bold" style="color:#111;">{{ $prescription->created_at->format('d-m-Y') }}</span>
      </div>
      
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
      <td style="width:40%; padding-right:10px;">

  @php
    // Helper: split text into lines, accept <br>, trim, drop empties,
    // and strip a leading bullet/dash + space so <li> doesn't double it.
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

{{-- C/C (extra tight) --}}
@php $ccLines = $hasProblem ? $toLines($prescription->problem_description ?? '') : []; @endphp
  @if(!empty($ccLines))
    {{-- Pull the list up by shrinking the title's bottom margin --}}
    <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">C/C:</div></u></b>

    <table role="presentation" style="width:100%; border-collapse:collapse; margin:-6px 0 0 0; ">
      <tr >
        <td ><ul class="small list-dot" style="margin:0; line-height:1.8; font-size:9px;">
      @foreach($ccLines as $ln)
        <li style="margin:0; padding:0; line-height:1.4; {{ !$loop->first ? 'margin-top:-3px;' : '' }}">
          {{ $ln }}
        </li>
      @endforeach
    </ul></td>
      </tr>
    </table>
  @endif







  {{-- Clinical Findings --}}
  @if($hasOE || $hasVitals)
  {{-- <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">Clinical Findings</div></u></b> --}}
    {{-- <div class="section-title">Clinical Findings</div> --}}

    {{-- O/E lines as bullets --}}
    @if($hasOE)
      @php $oeLines = $toLines($prescription->oe ?? ''); @endphp
      <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">O/E:</div></u></b>
      <table  role="presentation" style="width:100%; border-collapse:collapse; margin:-6px 0 0 0; ">
        <tr>
          <td>
            @if($oeLines)
      {{-- <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">O/E:</div></u></b> --}}
              {{-- <div class="small" style="margin:0 0 2px 0;"><span class="bold">O/E:</span></div> --}}
              <ul class="small  " style="padding-left:10px; margin:0; line-height:1.25;">
                @foreach($oeLines as $ln)
                  <li>{{ $ln }}</li>
                @endforeach
              </ul>
            @endif
          </td>
        </tr>
      </table>
    @endif

    {{-- Vitals as bullets --}}
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

  {{-- Other History (each field: bullets per line) --}}
  @foreach($nonEmptyHistoryOthers as $field => $label)
   <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">{{ $label }}:</div></u></b>
    {{-- <div class="section-title">{{ $label }}</div> --}}
    @php $histLines = $toLines($prescription->$field ?? ''); @endphp
    <table role="presentation" style="width:100%; border-collapse:collapse; margin:-6px 0 0 0; ">
      <tr>
        <td>
          @if($histLines)
      <ul class="small list-dot mb12" style="padding-left:14px; margin:0; line-height:1.25;">
        @foreach($histLines as $ln)
          <li>{{ $ln }}</li>
        @endforeach
      </ul>
    @endif
        </td>
      </tr>
    </table>
  @endforeach
  {{-- Previous Investigation --}}
@if($hasPrevInv)
     @php $piLines = $toLines($prescription->previous_investigation ?? ''); @endphp
      <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">Previous Investigation</div></u></b>
      <table  role="presentation" style="width:100%; border-collapse:collapse; margin:-6px 0 0 0; ">
        <tr>
          <td>
            @if($piLines)
      {{-- <b><u><div class="section-title" style="margin: 0; padding:0; line-height:1;">O/E:</div></u></b> --}}
              {{-- <div class="small" style="margin:0 0 2px 0;"><span class="bold">O/E:</span></div> --}}
              <ul class="small  " style="padding-left:10px; margin:0; line-height:1.25;">
                @foreach($piLines as $ln)
                  <li>{{ $ln }}</li>
                @endforeach
              </ul>
            @endif
          </td>
        </tr>
      </table>
    @endif


    {{--  Investigation/Tests --}}
@if($hasTests)
  <b><u><div class="section-title" style="margin:0; padding:0; line-height:1;">Investigation</div></u></b>
  <table role="presentation" style="width:100%; border-collapse:collapse; margin:-6px 0 0 0;">
    <tr>
      <td>
        <ul class="small" style="margin:0; padding:0; list-style-position:inside; line-height:1.2;">
          @foreach($prescription->tests as $t)
            <li style="margin:0; padding:0; line-height:1.2;">
              {{ $t->name }}
            </li>
          @endforeach
        </ul>
      </td>
    </tr>
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
      <td style="width:60%; padding-left:10px;  border-left:1px solid #999;">
        <table class="w-100">
          <tr>
            <td style="width:100%;">
              <div class="bold" style="font-size:18px; display:inline;">℞</div>
             
            </td>
          </tr>
        </table>

        {{-- Medicines --}}
        @if($hasMeds)
  <table class="w-100 b">
    <tr><td><div class="bold mb6 ">Medicines:</div></td></tr>
    <tr>
      <td>
        <ol class="small" style="margin:0; padding:0; list-style-type:decimal; list-style-position:inside; line-height:1;">
          @foreach($prescription->medicines as $m)
            @php
              $metaParts = [];
              if (filled($m->pivot->times_per_day ?? null)) $metaParts[] = $m->pivot->times_per_day;
              if (filled($m->pivot->duration ?? null))     $metaParts[] = 'Duration: '.$m->pivot->duration;
              $meta = implode(' — ', $metaParts);
            @endphp

            <li style="margin:0; padding:0; line-height:1;">
              @if(filled($m->type))
                <span class="muted">{{ mb_substr($m->type, 0, 3, 'UTF-8') }}</span>.
              @endif
              <span class="bold">{{ $m->name }}</span>
              @if(filled($m->strength)) <span class="muted"> - {{ $m->strength }}</span>@endif

              @if($meta)
                <div class="muted" style="margin:-2px 0 0 0; padding:0; line-height:1.2;">
                  {{ $meta }}
                </div>
              @endif
            </li>
          @endforeach
        </ol>
      </td>
    </tr>
  </table>
@endif
        {{-- Doctor Advice --}}
       @if($hasAdvice)
        @php $advLines = $toLines($prescription->doctor_advice ?? ''); @endphp
        @if(!empty($advLines))
          <table class="w-100 b">
            <tr><td><div class="bold mb6">Advice:</div></td></tr>
            <tr>
              <td>
                <ul class="small" style="margin:0; padding:0; list-style-type:none; list-style-position:inside; line-height:1.4;">
                  @foreach($advLines as $ln)
                    <li style="margin:0; padding:0; line-height:1.3;">
                      <span aria-hidden="true" style="margin-right:3px;">&#10148;</span> {{-- → --}}
                      {{ $ln }}
                    </li>
                  @endforeach
                </ul>
              </td>
            </tr>
          </table>
        @endif
      @endif



        {{-- Next Meeting Date --}}
        @if($hasReturn)
          <table class="w-100 b " style="padding-top: 40px;">
            <tr><td><div class="bold ">Next Meeting Date: <span class="small" style="line-height:1.5;"> {{ Carbon::parse($prescription->return_date)->format('d/m/Y') }}</span></div></td></tr>
          </table>
        @endif

        {{-- Referred To --}}
        @if($hasReferred)
          <table class="w-100 b ">
            <tr><td><div class="bold ">Referred To:</div></td></tr>
            <tr><td class="small" style="line-height:1.5;">{{ $prescription->referred_to }}</td></tr>
          </table>
        @endif

        {{-- Signature --}}
        <table class="w-100 " style="padding-top: 40px;">
          <tr>
            <td style="width:20%;">&nbsp;</td>
            <td style="width:70%; text-align:right;">
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
  {{-- <div class="text-center mt16 small muted">
    {{ config('clinic.footer', 'Thank you for visiting. For emergencies, please contact the clinic immediately.') }}
  </div> --}}

</body>
</html>
