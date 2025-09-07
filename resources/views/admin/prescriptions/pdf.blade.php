{{-- resources/views/admin/prescriptions/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Prescription #{{ $prescription->id }}</title>

  {{-- Keep PDF-only CSS extremely simple; avoid grid/flex --}}
  <style>
    @page { margin: 14mm 12mm 20mm 12mm; }
    html, body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    .border { border: 1px solid #ddd; }
    .rounded-lg { border-radius: 6px; }
    .p-2 { padding: 8px; }
    .p-4 { padding: 12px; }
    .p-6 { padding: 16px; }
    .p-8 { padding: 20px; }
    .text-sm { font-size: 12px; }
    .text-xs { font-size: 11px; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .font-medium { font-weight: 600; }
    .font-semibold { font-weight: 600; }
    .font-extrabold { font-weight: 800; }
    .mt-2 { margin-top: 8px; }
    .mt-4 { margin-top: 16px; }
    .mt-6 { margin-top: 24px; }
    .mt-10 { margin-top: 40px; }
    .mb-0_5 { margin-bottom: 4px; }
    .mb-1 { margin-bottom: 6px; }
    .mb-2 { margin-bottom: 8px; }
    .mb-4 { margin-bottom: 16px; }
    .mr-1 { margin-right: 4px; }
    .mr-2 { margin-right: 8px; }
    .leading-6 { line-height: 1.5; }
    .uppercase { text-transform: uppercase; }
    .tracking-wider { letter-spacing: 0.05em; }
    .tracking-wide { letter-spacing: 0.03em; }

    /* keep barcodes/images sane */
    .barcode-wrap img { height: 40px; }
  </style>
</head>
<body>
  {{-- Include the same template, but in "PDF mode" --}}
  @include('admin.prescriptions.show', ['prescription' => $prescription, 'pdf' => true])
</body>
</html>
