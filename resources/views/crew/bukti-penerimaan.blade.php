@php
    $docNumber = 'VOLA/ACC/' . date('Y') . '/' . str_pad($application['id'], 4, '0', STR_PAD_LEFT);
    $now = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
    $issuedAt = $now->translatedFormat('d F Y');
    $year = $now->format('Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Penerimaan - {{ $application['event_title'] }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm 12mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; background: #ffffff; font-size: 9pt; line-height: 1.35; }
        .container { width: 100%; position: relative; min-height: 100%; padding-bottom: 20px; }
        .letterhead { border-bottom: 1.5px solid #4F46E5; padding-bottom: 5px; margin-bottom: 8px; }
        .letterhead-table { width: 100%; border-collapse: collapse; }
        .letterhead-table td { padding: 0; vertical-align: middle; }
        .logo-box { width: 28px; height: 28px; background: #4F46E5; color: #fff; text-align: center; line-height: 28px; font-size: 16pt; font-weight: bold; border-radius: 4px; }
        .brand-name { font-size: 13pt; font-weight: bold; color: #1f2937; letter-spacing: 0.5pt; }
        .brand-sub { font-size: 7pt; color: #6B7280; margin-top: 1px; }
        .doc-info { text-align: right; }
        .doc-label { font-size: 6pt; color: #9CA3AF; letter-spacing: 1pt; text-transform: uppercase; }
        .doc-number { font-size: 8pt; font-weight: bold; color: #4F46E5; margin-top: 2px; }
        .doc-date { font-size: 7pt; color: #6B7280; margin-top: 1px; }
        .title-block { text-align: center; margin: 6px 0; padding: 5px 10px; background: #F5F3FF; border-radius: 4px; border-left: 3px solid #4F46E5; }
        .title-label { font-size: 7pt; color: #6B7280; letter-spacing: 2pt; text-transform: uppercase; }
        .title-main { font-size: 15pt; font-weight: bold; color: #1f2937; margin-top: 2px; letter-spacing: 0.5pt; }
        .title-sub { font-size: 8pt; color: #4F46E5; font-weight: bold; margin-top: 1px; }
        .body-text { font-size: 8.5pt; color: #374151; line-height: 1.4; margin: 6px 0; text-align: justify; }
        .body-text strong { color: #1f2937; }
        .status-badge { font-weight: bold; color: #10B981; text-transform: uppercase; }
        .event-card { width: 100%; border-collapse: collapse; margin: 6px 0; border: 1px solid #D1D5DB; border-radius: 4px; overflow: hidden; page-break-inside: avoid; }
        .event-card td { padding: 3px 6px; border-bottom: 1px solid #E5E7EB; vertical-align: middle; }
        .event-card tr:last-child td { border-bottom: none; }
        .event-card .label-cell { width: 95px; font-size: 7pt; color: #6B7280; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5pt; background: #F9FAFB; }
        .event-card .value-cell { font-size: 8.5pt; color: #1f2937; font-weight: bold; }
        .instructions { margin: 6px 0; padding: 6px 8px; background: #FEF3C7; border-left: 2px solid #F59E0B; border-radius: 3px; page-break-inside: avoid; }
        .instructions-title { font-size: 8.5pt; font-weight: bold; color: #92400E; margin-bottom: 2px; }
        .instructions-list { font-size: 7.5pt; color: #78350F; line-height: 1.3; margin: 0; padding-left: 14px; }
        .instructions-list li { margin-bottom: 1px; }
        .signature-section { margin-top: 8px; width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: top; padding: 0 6px; }
        .signature-label { font-size: 7.5pt; color: #6B7280; margin-bottom: 12px; line-height: 1.3; }
        .signature-line { border-top: 1px solid #374151; padding-top: 3px; margin: 0 10px; }
        .signature-name { font-size: 8.5pt; font-weight: bold; color: #1f2937; text-transform: uppercase; letter-spacing: 0.5pt; }
        .signature-role { font-size: 7pt; color: #6B7280; font-style: italic; margin-top: 2px; }
        .footer { position: absolute; bottom: 0; left: 0; right: 0; width: 100%; padding: 6px 0 0 0; border-top: 1px solid #E5E7EB; text-align: center; background: #ffffff; }
        .footer-text { font-size: 7pt; color: #9CA3AF; line-height: 1.3; }
    </style>
</head>
<body>
        <div class="title-block">
            <div class="title-label">Official Document</div>
            <div class="title-main">BUKTI PENERIMAAN LAMARAN</div>
            <div class="title-sub">Volunteer Acceptance Letter</div>
        </div>

        <div class="body-text">
            Dengan hormat,<br><br>
            Melalui surat ini, kami dengan senang hati memberitahukan bahwa lamaran Anda untuk menjadi 
            <strong>volunteer</strong> telah <span class="status-badge">DITERIMA</span> oleh panitia penyelenggara. 
            Dokumen ini merupakan bukti resmi bahwa Anda telah lolos seleksi dan akan ditugaskan pada event berikut:
        </div>

        <table class="event-card">
            <tr>
                <td class="label-cell">Nama Volunteer</td>
                <td class="value-cell">{{ $displayName }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Event</td>
                <td class="value-cell">{{ $application['event_title'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Penyelenggara</td>
                <td class="value-cell">{{ $application['event_organizer'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Posisi / Divisi</td>
                <td class="value-cell">{{ $application['position'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Kategori Event</td>
                <td class="value-cell">{{ $application['event_category'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Lokasi Penugasan</td>
                <td class="value-cell">{{ $application['location'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal Lamaran</td>
                <td class="value-cell">{{ $application['applied_at'] }}</td>
            </tr>
            <tr>
                <td class="label-cell">Status</td>
                <td class="value-cell"><span class="status-badge">DITERIMA</span></td>
            </tr>
        </table>

        <div class="instructions">
            <div class="instructions-title">Langkah Selanjutnya :</div>
            <ol class="instructions-list">
                <li>Simpan dokumen ini sebagai bukti penerimaan lamaran Anda.</li>
                <li>Pantau menu <strong>"Event Saya"</strong> di platform VOLA untuk informasi briefing.</li>
                <li>Hadiri briefing wajib sesuai jadwal yang akan diinformasikan panitia.</li>
                <li>Persiapkan diri dan perlengkapan sesuai dengan posisi yang ditugaskan.</li>
                <li>Hubungi panitia jika ada pertanyaan melalui fitur kontak di platform VOLA.</li>
            </ol>
        </div>

        <div class="body-text">
            Demikian surat bukti penerimaan ini kami terbitkan. Selamat bergabung dan terima kasih atas 
            kontribusi Anda untuk kesuksesan event ini. Kami berharap pengalaman volunteer ini memberikan 
            manfaat yang berharga bagi Anda.
        </div>

        <table class="signature-section">
            <tr>
                <td>
                    <div class="signature-label">Hormat kami,<br>Event Organizer</div>
                    <div class="signature-line">
                        <div class="signature-name">{{ $application['event_organizer'] }}</div>
                        <div class="signature-role">Panitia Penyelenggara</div>
                    </div>
                </td>
                <td>
                    <div class="signature-label">Terverifikasi oleh,<br>VOLA Platform</div>
                    <div class="signature-line">
                        <div class="signature-name">VOLA System</div>
                        <div class="signature-role">System Verification</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-text">
                <strong>© {{ $year }} VOLA Platform Relawan Indonesia.</strong> Semua hak dilindungi.
            </div>
        </div>
    </div>
</body>
</html>