<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

class PrescriptionPdfController extends Controller
{
    /**
     * Show PDF inline in the browser.
     */
    public function mpdf(Prescription $prescription)
    {
        [$pdfBinary, $filename] = $this->renderPdfBinary($prescription);

        return response($pdfBinary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Force the PDF to download as an attachment.
     * Route should be protected by 'signed' middleware.
     */
    public function download(Prescription $prescription)
    {
        [$pdfBinary, $filename] = $this->renderPdfBinary($prescription);

        return response($pdfBinary, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Renders the PDF and returns [binary, filename].
     */
    private function renderPdfBinary(Prescription $prescription): array
    {
        /* ---------- Ensure barcode cache dir ---------- */
        $barcodeDir = storage_path('framework/barcodes');
        if (!File::exists($barcodeDir)) {
            @File::makeDirectory($barcodeDir, 0775, true);
        }

        /* ---------- 1D BARCODE (Code 128) ---------- */
        $barcodeValue = 'RX-' . $prescription->id;
        $dns1 = new DNS1D();
        $dns1->setStorPath($barcodeDir);
        // getBarcodePNG returns base64 (last arg true)
        $barcodePngBase64 = $dns1->getBarcodePNG($barcodeValue, 'C128', 2, 40, [0, 0, 0], true);
        $barcodeDataUri   = 'data:image/png;base64,' . $barcodePngBase64;

        /* ---------- SIGNED DOWNLOAD URL ---------- */
        // For expiring links, use temporarySignedRoute(..., now()->addDays(30), ...)
        $downloadUrl = URL::signedRoute('prescriptions.pdf.download', [
            'prescription' => $prescription->id,
        ]);

        /* ---------- 2D QR CODE (encodes the download URL) ---------- */
        $dns2 = new DNS2D();
        $dns2->setStorPath($barcodeDir);
        // 'QRCODE' type; width/height scale 4; black; base64=true
        $qrPngBase64 = $dns2->getBarcodePNG($downloadUrl, 'QRCODE', 4, 4, [0, 0, 0], true);
        $qrDataUri   = 'data:image/png;base64,' . $qrPngBase64;

        /* ---------- RENDER BLADE ---------- */
        $html = view('admin.prescriptions.pdf', [
            'prescription'   => $prescription,
            'barcodeDataUri' => $barcodeDataUri,  // 1D barcode (ID)
            'barcodeValue'   => $barcodeValue,
            'qrDataUri'      => $qrDataUri,       // QR (opens download URL)
            'downloadUrl'    => $downloadUrl,     // used for <a> and fallback text
            'isPdf'          => true,
        ])->render();

        /* ---------- MPDF SETUP ---------- */
        $tmp = storage_path('app/mpdf-temp');
        if (!is_dir($tmp)) { @mkdir($tmp, 0775, true); }

        $defaultConfig     = (new ConfigVariables())->getDefaults();
        $fontDirs          = $defaultConfig['fontDir'];
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData          = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'margin_left'      => 8,
            'margin_right'     => 8,
            'margin_top'       => 6,
            'margin_bottom'    => 16,
            'tempDir'          => $tmp,
            'fontDir'          => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata'         => $fontData + [
                'notosansbengali' => [
                    'R' => 'NotoSansBengali-Regular.ttf',
                    'B' => 'NotoSansBengali-Bold.ttf',
                ],
            ],
            'default_font'     => 'notosansbengali',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'useOTL'           => 0xFF,
            'useKashida'       => 0,
        ]);

        /* ---------- CSS in HEADER_CSS ---------- */
        $baseCss = 'body{font-family:notosansbengali,"DejaVu Sans",sans-serif;font-size:12px;color:#111}.rx{font-family:"DejaVu Sans",notosansbengali,sans-serif;}';
        $mpdf->WriteHTML($baseCss, HTMLParserMode::HEADER_CSS);

        if (file_exists(public_path('css/pdf.css'))) {
            $mpdf->WriteHTML(file_get_contents(public_path('css/pdf.css')), HTMLParserMode::HEADER_CSS);
        }

        if (preg_match_all('/<style\b[^>]*>(.*?)<\/style>/is', $html, $m)) {
            foreach ($m[1] as $css) {
                $mpdf->WriteHTML($css, HTMLParserMode::HEADER_CSS);
            }
            $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);
        }

        /* ---------- Write body ---------- */
        $mpdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        /* ---------- Output ---------- */
        $filename = 'prescription-' . $prescription->id . '.pdf';
        $binary   = $mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN);

        return [$binary, $filename];
    }
}
