<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Riwayat - {{ $displayName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            line-height: 1.6;
            background: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            padding: 40px 50px;
            border-radius: 0 0 20px 20px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
        }

        .logo span {
            color: #FDE68A;
        }

        .badge-crew {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 8px;
            letter-spacing: 1px;
        }

        .doc-title {
            font-size: 12px;
            opacity: 0.9;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header-content h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .header-content p {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .header-content .email {
            font-size: 12px;
            opacity: 0.75;
        }

        .content {
            padding: 40px 50px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            flex: 1;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 20px;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #4F46E5;
        }

        .stat-sub {
            font-size: 10px;
            color: #9CA3AF;
            margin-top: 4px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4F46E5;
            display: inline-block;
        }

        .event-list {
            margin-bottom: 30px;
        }

        .event-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .event-title {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            flex: 1;
        }

        .event-status {
            background: #D1FAE5;
            color: #047857;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
            margin-left: 10px;
        }

        .event-organizer {
            font-size: 11px;
            color: #4F46E5;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .event-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .event-detail-item {
            flex: 1;
            min-width: 150px;
        }

        .event-detail-label {
            font-size: 9px;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .event-detail-value {
            font-size: 11px;
            color: #374151;
            font-weight: 500;
        }

        .footer {
            padding: 30px 50px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
        }

        .footer p {
            font-size: 10px;
            color: #9CA3AF;
            margin-bottom: 4px;
        }

        .footer .brand {
            font-weight: 700;
            color: #4F46E5;
        }

        .page-break {
            page-break-before: always;
        }

        .certificate-note {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px 20px;
            border-radius: 6px;
            margin-top: 30px;
        }

        .certificate-note h3 {
            font-size: 12px;
            font-weight: 700;
            color: #92400E;
            margin-bottom: 6px;
        }

        .certificate-note p {
            font-size: 11px;
            color: #78350F;
            line-height: 1.5;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-box p {
            font-size: 10px;
            color: #6B7280;
            margin-bottom: 60px;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        .signature-role {
            font-size: 10px;
            color: #6B7280;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-top">
            <div class="logo">VOLA<span>.</span><span class="badge-crew">CREW</span></div>
            <div class="doc-title">Ringkasan Riwayat Volunteer</div>
        </div>
        <div class="header-content">
            <h1>{{ $displayName }}</h1>
            <p>📍 {{ $displayCity }}, Indonesia</p>
            <p class="email">✉️ {{ $displayEmail }}</p>
        </div>
    </div>

    <div class="content">

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Event</div>
                <div class="stat-value">{{ $totalEvents }}</div>
                <div class="stat-sub">Event telah diselesaikan</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Estimasi Jam</div>
                <div class="stat-value">{{ $totalHours }}+</div>
                <div class="stat-sub">Jam kontribusi</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Kategori</div>
                <div class="stat-value">{{ $totalCategories }}</div>
                <div class="stat-sub">Jenis event berbeda</div>
            </div>
        </div>

        <h2 class="section-title">📋 Riwayat Event yang Diikuti</h2>
        
        <div class="event-list">
            @foreach($allHistories as $index => $event)
            <div class="event-card">
                <div class="event-header">
                    <div class="event-title">
                        {{ $index + 1 }}. {{ $event['event_title'] }}
                    </div>
                    <div class="event-status">{{ $event['status_label'] }}</div>
                </div>
                <div class="event-organizer">{{ $event['organizer'] }}</div>
                <div class="event-details">
                    <div class="event-detail-item">
                        <div class="event-detail-label">Posisi</div>
                        <div class="event-detail-value">{{ $event['position'] }}</div>
                    </div>
                    <div class="event-detail-item">
                        <div class="event-detail-label">Tanggal</div>
                        <div class="event-detail-value">{{ $event['date'] }}</div>
                    </div>
                    <div class="event-detail-item">
                        <div class="event-detail-label">Lokasi</div>
                        <div class="event-detail-value">{{ $event['location'] }}</div>
                    </div>
                    <div class="event-detail-item">
                        <div class="event-detail-label">Kategori</div>
                        <div class="event-detail-value">{{ $event['category'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="certificate-note">
            <h3>📜 Tentang Sertifikat</h3>
            <p>
                Seluruh event di atas telah diverifikasi dan sertifikat resmi telah diterbitkan oleh masing-masing penyelenggara. 
                Sertifikat dapat diunduh kapan saja melalui dashboard VOLA pada menu <strong>Riwayat Pengalaman</strong>.
                Setiap sertifikat memiliki kode verifikasi unik yang dapat divalidasi oleh pihak ketiga.
            </p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Jakarta, {{ date('d F Y') }}</p>
                <div class="signature-line">VOLA Platform</div>
                <div class="signature-role">Sistem Terverifikasi Otomatis</div>
            </div>
        </div>

    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh <span class="brand">VOLA Platform</span></p>
        <p>© 2026 VOLA Platform Relawan Indonesia. All rights reserved.</p>
        <p style="margin-top: 8px; font-size: 9px; color: #D1D5DB;">
            ID Dokumen: VOLA-{{ strtoupper(substr(md5($displayName . date('Y-m-d')), 0, 12)) }} | 
            Generated: {{ date('d/m/Y H:i') }} WIB
        </p>
    </div>
</body>
</html>