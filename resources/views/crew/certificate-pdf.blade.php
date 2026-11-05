@php
    $certHash = strtoupper(substr(md5($event['id'] . $displayName), 0, 12));
    $certId   = 'VOLA-CERT-' . $certHash;

    $now       = \Carbon\Carbon::now('Asia/Jakarta')->locale('id');
    $issuedAt  = $now->translatedFormat('d F Y, H:i');
    $year      = $now->format('Y');

    $nameLen  = mb_strlen($displayName);
    $nameSize = $nameLen > 32 ? 20 : ($nameLen > 22 ? 26 : 32);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Volunteer - {{ $displayName }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            background: #ffffff;
        }

        .outer-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            width: 277mm;   /* 277 + 2*2 = 281 */
            height: 190mm;  /* 190 + 2*2 = 194 */
            border: 2mm solid #4F46E5;
        }

        .inner-border {
            position: absolute;
            top: 12.5mm;
            left: 12.5mm;
            width: 270mm;   /* 270 + 2*0.5 = 271 */
            height: 184mm;  /* 184 + 2*0.5 = 185 */
            border: 0.5mm solid #4F46E5;
        }

        .corner {
            position: absolute;
            width: 16mm;
            height: 16mm;
        }
        .corner-tl { top: 8mm;    left: 8mm;   background: #4F46E5; }
        .corner-tr { top: 8mm;    right: 8mm;  background: #F97316; }
        .corner-bl { bottom: 8mm; left: 8mm;   background: #A855F7; }
        .corner-br { bottom: 8mm; right: 8mm;  background: #EC4899; }

        .content {
            position: absolute;
            top: 16mm;
            left: 28mm;
            width: 241mm;   /* 297 - 28 - 28 */
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 0;
            vertical-align: middle;
        }
        .logo-box {
            width: 13mm;
            height: 13mm;
            background: #4F46E5;
            color: #ffffff;
            text-align: center;
            line-height: 13mm;
            font-size: 20pt;
            font-weight: bold;
        }
        .logo-brand {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 1pt;
        }
        .logo-sub {
            font-size: 7pt;
            color: #6B7280;
            margin-top: 1mm;
        }
        .header-right {
            text-align: right;
        }
        .doc-label {
            font-size: 7pt;
            color: #9CA3AF;
            letter-spacing: 2pt;
            text-transform: uppercase;
        }
        .doc-value {
            font-size: 11pt;
            font-weight: bold;
            color: #4F46E5;
            margin-top: 1mm;
        }

        .title-section {
            text-align: center;
            margin-top: 5mm;
        }
        .presented-text {
            font-size: 8pt;
            color: #6B7280;
            letter-spacing: 4pt;
            text-transform: uppercase;
        }
        .main-title {
            font-size: 38pt;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 8pt;
            margin-top: 3mm;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #4F46E5;
            letter-spacing: 6pt;
            text-transform: uppercase;
            margin-top: 1mm;
        }
        .divider {
            width: 90mm;
            height: 0.8mm;
            background: #4F46E5;
            margin: 4mm auto 0 auto;
        }

        .recipient-section {
            text-align: center;
            margin-top: 6mm;
        }
        .awarded-label {
            font-size: 7.5pt;
            color: #9CA3AF;
            letter-spacing: 4pt;
            text-transform: uppercase;
        }
        .recipient-name {
            font-family: 'Times', 'Georgia', serif;
            font-weight: bold;
            font-style: italic;
            color: #4F46E5;
            margin-top: 2mm;
        }
        .name-line {
            width: 120mm;
            height: 0.3mm;
            background: #4F46E5;
            margin: 1mm auto 0 auto;
        }

        .description {
            text-align: center;
            margin: 6mm 14mm 0 14mm;
            font-size: 9.5pt;
            color: #4B5563;
            line-height: 1.6;
        }
        .description strong {
            color: #1f2937;
        }
        
        .position {
            font-weight: bold;
            color: #4F46E5;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 3mm 0;
            table-layout: fixed;
            margin-top: 6mm;
        }
        .info-table td {
            width: 25%;
            padding: 0;
            vertical-align: top;
        }
        .info-card {
            border: 0.3mm solid #D1D5DB;
            border-radius: 2mm;
            padding: 3mm 2mm;
            text-align: center;
            background: #F9FAFB;
        }
        .info-label {
            font-size: 6.5pt;
            color: #6B7280;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 1.5mm;
        }
        .info-value {
            font-size: 9pt;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.3;
        }
        .info-value-small {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1f2937;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 6mm;
        }
        .signature-table td {
            padding: 0 6mm;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 0.4mm solid #374151;
            margin: 0 8mm;
            padding-top: 2mm;
        }
        .signature-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 1pt;
            text-transform: uppercase;
        }
        .signature-role {
            font-size: 7.5pt;
            color: #6B7280;
            font-style: italic;
            margin-top: 1mm;
        }

        .seal-ring {
            width: 22mm;
            height: 22mm;
            padding: 1mm;
            margin: 0 auto;
            border: 0.6mm solid #4F46E5;
            border-radius: 50%;
        }
        .seal {
            width: 22mm;
            height: 22mm;
            background: #4F46E5;
            border-radius: 50%;
            text-align: center;
            color: #ffffff;
        }
        .seal-inner {
            padding-top: 3.2mm;
        }
        .seal-stars {
            font-size: 6pt;
            letter-spacing: 1pt;
            line-height: 1;
        }
        .seal-text {
            font-size: 5.5pt;
            font-weight: bold;
            letter-spacing: 1.5pt;
            line-height: 1.4;
        }
        .seal-year {
            font-size: 15pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .footer {
            text-align: center;
            margin-top: 5mm;
            padding-top: 2mm;
            border-top: 0.2mm solid #E5E7EB;
        }
        .footer-text {
            font-size: 6.5pt;
            color: #9CA3AF;
            letter-spacing: 0.5pt;
        }
        .footer-id {
            font-size: 6pt;
            color: #9CA3AF;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
    <div class="outer-border"></div>
    <div class="inner-border"></div>

    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <div class="content">

        <table class="header-table">
            <tr>
                <td style="width: 15mm;">
                    <div class="logo-box">V</div>
                </td>
                <td>
                    <div class="logo-brand">VOLA</div>
                    <div class="logo-sub">Platform Relawan Indonesia</div>
                </td>
                <td class="header-right">
                    <div class="doc-label">Document Type</div>
                    <div class="doc-value">Certificate of Service</div>
                </td>
            </tr>
        </table>

        <div class="title-section">
            <div class="presented-text">This Certificate is Proudly Presented to</div>
            <div class="main-title">VOLUNTEER</div>
            <div class="subtitle">Certificate of Service</div>
            <div class="divider"></div>
        </div>

        <div class="recipient-section">
            <div class="awarded-label">Awarded to</div>
            <div class="recipient-name" style="font-size: {{ $nameSize }}pt;">{{ $displayName }}</div>
            <div class="name-line"></div>
        </div>

        <div class="description">
            In recognition of your outstanding dedication, commitment, and valuable service
            as <span class="position">{{ $event['position'] }}</span>
            for the event <strong>{{ $event['event_title'] }}</strong>
            organized by <strong>{{ $event['organizer'] }}</strong>.
            Your contributions have made a meaningful impact in advancing our mission
            and supporting the community.
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <div class="info-card">
                        <div class="info-label">Event Date</div>
                        <div class="info-value">{{ $event['date'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="info-card">
                        <div class="info-label">Location</div>
                        <div class="info-value">{{ $event['location'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="info-card">
                        <div class="info-label">Category</div>
                        <div class="info-value">{{ $event['category'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="info-card">
                        <div class="info-label">Certificate ID</div>
                        <div class="info-value-small">{{ $certId }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="signature-table">
            <tr>
                <td style="width: 38%;">
                    <div class="signature-line">
                        <div class="signature-name">{{ $event['organizer'] }}</div>
                        <div class="signature-role">Event Organizer</div>
                    </div>
                </td>
                <td style="width: 24%;">
                    <div class="seal-ring">
                        <div class="seal">
                            <div class="seal-inner">
                                <div class="seal-stars">* * *</div>
                                <div class="seal-text">OFFICIAL</div>
                                <div class="seal-year">{{ $year }}</div>
                                <div class="seal-text">VERIFIED</div>
                                <div class="seal-stars">* * *</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td style="width: 38%;">
                    <div class="signature-line">
                        <div class="signature-name">VOLA Platform</div>
                        <div class="signature-role">System Verification</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-text">
                &copy; {{ $year }} VOLA Platform Relawan Indonesia. All rights reserved.
            </div>
            <div class="footer-id">
                ID: {{ $certId }} |
                Issued: {{ $issuedAt }} WIB |
                This is an electronically generated certificate.
            </div>
        </div>
    </div>
</body>
</html>