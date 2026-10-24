# PRD: Vola (MVP)

> Dokumen ini adalah sumber kebenaran untuk AI agent. Baca seluruhnya sebelum menulis kode.
> Kerjakan **per fase** (bagian 12). Jangan mengerjakan fase berikutnya sebelum fase aktif lulus kriteria selesai.

---

## 0. Aturan untuk agent (WAJIB DIPATUHI)

1. Bangun **hanya** fitur berlabel **P0** dan **P1**. Fitur **P2** dan bagian "Out of Scope" dilarang dibuat, termasuk stub/placeholder halaman.
2. Jika ada hal ambigu atau bertentangan dengan dokumen ini, **berhenti dan tanyakan**. Jangan berasumsi diam-diam. Asumsi yang sudah diambil ada di bagian 14.
3. Gunakan nama tabel, kolom, enum, dan route **persis** seperti di dokumen ini.
4. Setiap fitur harus punya **feature test** (Pest) yang mencakup skenario sukses dan minimal satu skenario gagal (validasi/otorisasi).
5. Sebelum menyatakan satu fase selesai, jalankan: `php artisan migrate:fresh --seed`, `php artisan test`, dan `./vendor/bin/pint`. Semuanya harus hijau.
6. Semua otorisasi lewat **Policy/middleware**, tidak boleh hanya disembunyikan di UI.
7. Tidak ada rahasia di repo. Semua kredensial di `.env`; perbarui `.env.example` setiap menambah variabel.
8. Commit kecil dan bermakna (`feat:`, `fix:`, `test:`, `chore:`). Satu fase = satu branch `feature/fase-N-nama`.
9. Teks antarmuka **Bahasa Indonesia**. Kode, nama variabel, nama tabel: **Bahasa Inggris**.
10. Jangan mengubah file di luar cakupan fase aktif kecuali diperlukan, dan sebutkan alasannya di ringkasan.

---

## 1. Ringkasan produk

**Vola** adalah platform web yang menyatukan informasi event dan rekrutmen volunteer/event crew dalam satu sistem.

**Masalah** (hasil survei): info volunteer tersebar di Instagram/TikTok/WhatsApp; pendaftaran lewat Google Form berpindah-pindah platform; informasi event tidak lengkap; status dan hasil seleksi tidak jelas.

**Solusi:** EO mengajukan event beserta kebutuhan posisi, Admin me-review, EO membayar biaya layanan (Midtrans Sandbox), event terpublikasi otomatis, volunteer melamar, EO menyeleksi, hasil diumumkan lewat platform.

**Tujuan MVP:** satu alur end-to-end berjalan di staging:

```
EO daftar → Admin verifikasi EO → EO buat event + posisi → submit
→ Admin review (setujui) → invoice dibuat → EO bayar (Midtrans Sandbox)
→ webhook → event otomatis aktif/publik → volunteer cari & melamar
→ EO terima/tolak → volunteer melihat hasil + info koordinasi → riwayat
```

**Non-goals MVP:** aplikasi mobile native, chat, sertifikat PDF, laporan/mediasi, suspend user, artikel, export CSV, grafik.

---

## 2. Pengguna dan peran

| Role | Kemampuan inti |
|---|---|
| `volunteer` | Cari/filter event, lihat detail, melamar posisi, pantau status lamaran, lihat hasil + info koordinasi, riwayat event, kelola profil + CV |
| `eo` | Kelola profil organisasi, buat event + posisi, submit review, bayar invoice, lihat pendaftar, terima/tolak, lihat kru diterima |
| `admin` | Verifikasi akun EO, review/setujui/tolak event, lihat event aktif dan transaksi |

Satu user memiliki tepat satu role. Akun `admin` dibuat lewat seeder, **tidak ada registrasi admin**.

---

## 3. Tech stack

| Lapisan | Pilihan |
|---|---|
| Bahasa/Framework | PHP 8.3+, Laravel (versi stabil terbaru) |
| Auth | Laravel Breeze (Blade) dengan penyesuaian role. **Bukan** Supabase Auth |
| Login sosial | `laravel/socialite` untuk login via Google (OAuth 2.0) |
| Frontend | Blade + Tailwind CSS + Alpine.js (tanpa SPA) |
| Database | PostgreSQL (Supabase hosted, diakses dari Laravel sebagai Postgres biasa) |
| Storage file | Laravel Filesystem, disk private. Untuk produksi dapat memakai Supabase Storage via driver S3 |
| Pembayaran | `midtrans/midtrans-php`, Snap, **Sandbox** |
| Test | Pest |
| Style | Laravel Pint |
| Queue/Scheduler | Database queue, `schedule:run` untuk perintah terjadwal |

Koneksi Supabase: gunakan koneksi langsung atau session pooler (port 5432) untuk migrasi. Jangan gunakan transaction pooler (6543) untuk migrasi. RLS Supabase tidak dipakai karena akses database hanya dari server Laravel.

Konfigurasi: `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, mata uang IDR (integer rupiah, tanpa desimal).

---

## 4. Struktur kode

```
app/
  Enums/            # Role, UserStatus, VerificationStatus, EventStatus,
                    # ApplicationStatus, PaymentStatus (PHP backed enum)
  Http/
    Controllers/    # Public/, Volunteer/, Eo/, Admin/, Webhook/
    Middleware/     # EnsureRole, EnsureEoVerified
    Requests/       # Form Request per aksi tulis
  Models/
  Policies/         # EventPolicy, ApplicationPolicy, PaymentPolicy
  Services/         # EventWorkflowService, ApplicationService, PaymentService
  Notifications/
resources/views/
  layouts/  components/  public/  volunteer/  eo/  admin/
tests/Feature/      # dikelompokkan per modul
database/
  migrations/  seeders/  factories/
docs/ui/            # tangkapan layar mockup Figma (lihat bagian 13)
```

Logika bisnis penting berada di **Service**, bukan di controller.

---

## 5. Model data

Gunakan foreign key dengan constraint dan index seperti tertera. Semua tabel memakai `timestamps` kecuali disebut lain.

### users
```
id, name, email (unique), email_verified_at, password (nullable),
google_id (nullable, unique),
phone, avatar_path,
role enum(volunteer, eo, admin) [index],
status enum(active, suspended) default active,
remember_token, timestamps
```
`password` nullable karena akun yang mendaftar lewat Google tidak memiliki password lokal kecuali diset belakangan lewat halaman pengaturan. `google_id` menyimpan ID unik akun Google untuk mencocokkan login berikutnya.

### volunteer_profiles (1:1 users)
```
id, user_id (unique, FK cascade),
bio, institution, city,
skills jsonb, experience text,
cv_path, portfolio_path, timestamps
```

### organizer_profiles (1:1 users)
```
id, user_id (unique, FK cascade),
organization_name, organization_type, description, city,
pic_name, pic_phone, social_link,
document_path,
verification_status enum(pending, verified, rejected) default pending [index],
verified_by (FK users, nullable), verified_at, rejection_reason,
timestamps
```

### event_categories
```
id, name, slug (unique)
```
Seeder: Festival, Seminar, Sosial, Kampus, Olahraga, Musik, Komunitas.

### events
```
id, organizer_id (FK organizer_profiles), category_id (FK event_categories),
title, slug (unique), description, poster_path,
location, city [index],
start_date, end_date, start_time, end_time,
registration_deadline [index],
contact_info, coordination_link, pic_name, pic_phone,
benefits jsonb,
status enum(draft, pending_review, awaiting_payment, rejected,
            active, closed, finished) default draft [index],
rejection_reason, reviewed_by (FK users, nullable), reviewed_at, published_at,
deleted_at (soft delete), timestamps
```

### event_positions
```
id, event_id (FK cascade),
name, description, requirements, quota (unsigned int, min 1),
timestamps
```

### applications
```
id, volunteer_profile_id (FK), position_id (FK event_positions),
status enum(pending, accepted, rejected, withdrawn) default pending [index],
cover_letter, applied_at, decided_at, decision_note,
timestamps
UNIQUE (volunteer_profile_id, position_id)
```

### payments
```
id, event_id (FK, TIDAK unique),
invoice_number (unique), midtrans_order_id (unique), snap_token,
transaction_id (nullable), payment_type (nullable),
amount (unsigned int),
status enum(pending, paid, failed, expired, cancelled) default pending [index],
paid_at, expired_at, payload jsonb (raw webhook),
timestamps
```
Satu event boleh punya beberapa baris `payments` (invoice kedaluwarsa dibuat ulang dengan `order_id` baru).

### Bawaan Laravel
`notifications` (tabel bawaan, jangan buat sendiri), `sessions`, `password_reset_tokens`, `jobs`, `failed_jobs`, `cache`.

### Hitungan turunan (JANGAN disimpan sebagai kolom)
- Kuota terisi = jumlah `applications` berstatus `accepted` pada posisi itu.
- Total pelamar, pelamar menunggu: query/scope.
- Riwayat volunteer = `applications` berstatus `accepted` pada event berstatus `finished`.

### Urutan migrasi
users → volunteer_profiles, organizer_profiles, event_categories → events → event_positions → applications → payments → notifications.

### Seeder
1 admin (`admin@vola.test`), kategori, dan data dummy: 10 volunteer, 10 EO (campuran pending/verified/rejected), 15 event (campuran status), minimal 20 lamaran (campuran status), beberapa payments. Semua password dummy: `password`.

---

## 6. State machine

### EO (`organizer_profiles.verification_status`)
`pending → verified` (aksi Admin) | `pending → rejected` (aksi Admin, wajib alasan).
EO yang ditolak dapat memperbarui dokumen dan kembali ke `pending`.

### Event (`events.status`)
| Dari | Ke | Aktor | Syarat |
|---|---|---|---|
| draft | pending_review | EO (pemilik) | EO `verified`, minimal 1 posisi, semua field wajib terisi |
| pending_review | awaiting_payment | Admin | Sistem membuat `payments` berstatus `pending` |
| pending_review | rejected | Admin | Wajib `rejection_reason` |
| rejected | draft | EO | Edit lalu submit ulang |
| awaiting_payment | active | Sistem | Webhook menyatakan `paid`; set `published_at` |
| active | closed | EO / sistem | EO menutup manual, atau deadline lewat |
| active / closed | finished | EO / sistem | Tanggal selesai lewat, atau EO menandai selesai |

Event `draft` dan `rejected` dapat diedit. Event `pending_review` ke atas **terkunci** dari edit oleh EO.

### Payment (`payments.status`)
`pending → paid | failed | expired | cancelled`. Hanya `paid` yang mengaktifkan event. Status `paid` bersifat final (webhook berikutnya diabaikan).

### Application (`applications.status`)
`pending → accepted | rejected` (EO) atau `pending → withdrawn` (volunteer). Status akhir tidak dapat diubah, kecuali `rejected → accepted` tidak diizinkan di MVP.

---

## 7. Aturan bisnis

**Melamar (volunteer):**
- Event harus `active`, `registration_deadline >= hari ini`, dan posisi punya sisa kuota (`accepted < quota`).
- Tidak boleh melamar posisi yang sama dua kali (unique constraint + validasi ramah).
- Profil volunteer minimal terisi: `bio` dan `skills`. CV opsional tetapi disarankan.
- User dengan `status = suspended` tidak dapat login atau melamar.

**Menerima pendaftar (EO):**
- Hanya EO pemilik event. Lakukan di `DB::transaction` dengan `lockForUpdate()` pada baris `event_positions`, cek `accepted < quota` sebelum set `accepted`. Jika penuh, tolak dengan pesan jelas.
- Menerima/menolak mengisi `decided_at` dan mengirim notifikasi ke volunteer.

**Info koordinasi:** `coordination_link`, `pic_name`, `pic_phone` hanya tampil kepada volunteer yang lamarannya `accepted` (dan kepada EO pemilik + Admin). Tidak boleh bocor di halaman publik.

**Pembayaran:**
- Biaya layanan: nilai konfigurasi `config('vola.listing_fee')`, default `450000` (IDR), dapat diubah lewat `.env` `VOLA_LISTING_FEE`.
- Invoice: `INV-{tahun}-{urutan 3 digit}`. `midtrans_order_id` unik per percobaan.
- Endpoint webhook `POST /midtrans/notification`: tanpa auth dan tanpa CSRF; **wajib verifikasi `signature_key`** (SHA512 dari `order_id + status_code + gross_amount + server_key`); idempoten; memperbarui `payments`, menyimpan payload mentah, dan mengaktifkan event dalam satu transaksi.
- Jangan mempercayai redirect browser sebagai bukti pembayaran. Status hanya dari webhook (atau pengecekan status ke Midtrans lewat tombol "Cek Status").
- Invoice `pending` yang melewati `expired_at` ditandai `expired`; EO dapat membuat invoice baru.

**Upload file:**
- CV/portfolio: PDF saja, maks 5 MB. Dokumen legalitas EO: PDF/JPG/PNG, maks 5 MB. Poster: JPG/PNG/WEBP, maks 2 MB.
- Disimpan di disk private; akses lewat controller dengan otorisasi (atau signed URL). Poster event publik boleh disk public.
- Nama file di-hash, jangan pakai nama asli.

**Akses:**
- EO hanya boleh melihat/mengubah event miliknya.
- EO belum `verified` boleh login dan mengelola profil, tetapi **tidak boleh submit event**.
- Volunteer hanya melihat lamarannya sendiri.

**Keamanan dasar:** CSRF aktif, rate limit login, validasi lewat Form Request, escape output Blade, mass-assignment dijaga dengan `$fillable`, password di-hash, form password menggunakan input bertipe password.

---

## 8. Fitur dan kriteria penerimaan

Prioritas: **P0** wajib, **P1** dikerjakan setelah semua P0 hijau.

### F1. Autentikasi dan role (P0)
- Registrasi memilih role `volunteer` atau `eo`; login; logout; lupa/reset password bawaan Breeze.
- **Login dengan Google**: tombol "Masuk dengan Google" di halaman login dan register, memakai `laravel/socialite`.
  - Jika email Google **belum terdaftar**: buat user baru otomatis, simpan `google_id`, `email_verified_at` diisi langsung (email sudah terverifikasi oleh Google), `password` dikosongkan (`null`). Karena role belum diketahui saat login Google, arahkan ke halaman pilih role (`volunteer` atau `eo`) sebelum masuk dashboard — role tidak boleh default diam-diam.
  - Jika email Google **sudah terdaftar** (baik dari daftar manual maupun Google sebelumnya): login langsung ke akun yang sama; jika akun itu sebelumnya daftar manual dan belum punya `google_id`, tautkan `google_id` ke akun tersebut setelah mencocokkan email.
  - Akun yang `password`-nya `null` (login Google saja) **tidak bisa** login manual lewat email+password sampai mengatur password lewat halaman pengaturan.
- Middleware `EnsureRole:{role}` pada grup route.
- **Diterima jika:** user tanpa role yang tepat mendapat 403 di area role lain; setelah login diarahkan ke dashboard sesuai role; akun `suspended` ditolak login; login Google untuk email baru diarahkan memilih role dulu; login Google untuk email lama langsung masuk tanpa duplikat akun.

### F2. Profil (P0)
- Volunteer: edit biodata, institusi, kota, skills, pengalaman, upload CV dan portfolio.
- EO: edit profil organisasi, upload dokumen legalitas.
- **Diterima jika:** validasi tipe/ukuran file bekerja; file tersimpan di disk private; user tidak bisa mengakses file user lain.

### F3. Verifikasi EO oleh Admin (P0)
- Admin melihat daftar EO (filter status), membuka dokumen, menyetujui atau menolak dengan alasan.
- **Diterima jika:** EO `pending` tidak dapat submit event; setelah `verified` dapat submit; alasan penolakan tampil ke EO.

### F4. CRUD event dan posisi oleh EO (P0)
- Buat/edit/hapus (soft delete, hanya saat `draft`/`rejected`) event dengan banyak posisi (nama, deskripsi, syarat, kuota), upload poster, `benefits`, info koordinasi.
- Simpan sebagai draft; tombol "Kirim untuk Review" memicu transisi `draft → pending_review`.
- **Diterima jika:** validasi lengkap; event bukan milik EO tidak bisa diakses; event terkunci setelah submit.

### F5. Review event oleh Admin (P0)
- Antrean event `pending_review`, detail lengkap, tombol Setujui dan Tolak (alasan wajib).
- Setujui: status `awaiting_payment` dan pembuatan `payments` pending. Tolak: status `rejected`.
- **Diterima jika:** hanya Admin yang dapat mengakses; transisi sesuai tabel state machine; EO menerima notifikasi (P1) atau melihat status di dashboard.

### F6. Pembayaran Midtrans Sandbox (P0)
- Halaman tagihan EO menampilkan invoice, tombol Bayar (memunculkan Snap), tombol Cek Status.
- Webhook memperbarui status dan mengaktifkan event.
- **Diterima jika:** skenario sukses, gagal, pending, dan kedaluwarsa masing-masing tertangani; webhook dengan signature salah ditolak (403); webhook duplikat tidak mengubah hasil; event menjadi `active` hanya setelah `paid`.

### F7. Katalog publik (P0)
- Landing page (statis + daftar event aktif terbaru), halaman daftar event dengan pencarian kata kunci (judul, posisi, lokasi), filter kategori, kota, dan status rekrutmen, paginasi, halaman detail event (posisi, sisa kuota, syarat, benefit, info penyelenggara).
- Hanya event `active` (dan yang `closed` ditandai jelas) yang tampil publik.
- **Diterima jika:** event non-aktif tidak pernah muncul di publik; filter dapat dikombinasikan; info koordinasi tidak tampil.

### F8. Melamar (P0)
- Volunteer memilih posisi, mengisi cover letter, mengirim.
- **Diterima jika:** semua aturan di bagian 7 ditegakkan dengan pesan error Bahasa Indonesia; pengguna yang belum login diarahkan login lalu kembali ke event.

### F9. Lamaran Saya (P0)
- Daftar lamaran dengan tab status (Semua, Menunggu, Diterima, Ditolak), tombol Batalkan untuk yang `pending`.
- **Diterima jika:** hanya lamaran milik sendiri; pembatalan mengubah status `withdrawn` dan tidak dapat dibatalkan ulang.

### F10. Seleksi pendaftar oleh EO (P0)
- Tabel pendaftar per event (filter posisi/status, pencarian nama/institusi), lihat profil + unduh CV, tombol Terima/Tolak.
- Kuota terisi tampil per posisi.
- **Diterima jika:** kondisi balapan kuota tertangani (ada test yang menerima melebihi kuota dan harus ditolak); hanya EO pemilik yang berhak.

### F11. Hasil dan koordinasi (P0)
- Volunteer melihat status akhir di Lamaran Saya. Jika `accepted`, muncul kartu info koordinasi (link grup, PIC).
- EO memiliki halaman Koordinasi Kru: daftar volunteer diterima per posisi beserta kontak.
- **Diterima jika:** info koordinasi hanya terlihat oleh yang berhak.

### F12. Riwayat volunteer (P0)
- Daftar event yang diikuti (lamaran `accepted` + event `finished`), dengan filter kategori.
- **Diterima jika:** data diturunkan dari `applications`, bukan tabel baru.

### F13. Notifikasi in-app (P1)
- Memakai `notifications` bawaan Laravel (kanal `database`). Dropdown lonceng, tandai dibaca.
- Pemicu: EO diverifikasi/ditolak, event disetujui/ditolak, pembayaran sukses, lamaran diterima/ditolak, pelamar baru untuk EO.

### F14. Dashboard ringkas (P1)
- EO: jumlah event aktif, pelamar baru, event menunggu review, tagihan menunggu.
- Admin: jumlah EO pending, event menunggu review, event aktif, transaksi. Hanya angka dan daftar, tanpa grafik.
- Volunteer: jumlah lamaran aktif, menunggu, diterima + rekomendasi event terbaru.

### F15. Riwayat pembayaran dan transaksi (P1)
- EO: daftar invoice + status. Admin: daftar semua transaksi (read-only) dengan filter status/bulan.

### F16. Event Saya (P1)
- EO: daftar event dengan tab status (Semua, Aktif, Menunggu Review, Selesai). Volunteer: event yang diterima (akan datang / selesai).

### F17. Penutupan dan penyelesaian event (P1)
- EO menutup rekrutmen dan menandai selesai. Perintah terjadwal `events:sync-status` (harian) menutup event yang deadline-nya lewat dan menyelesaikan event yang tanggalnya lewat, serta menandai `payments` kedaluwarsa.

### F18. Pengaturan akun (P1)
- Ubah email dan password.

### Out of Scope (P2, JANGAN dibuat)
Laporan/pengaduan dan mediasi, suspend/aktivasi user dari UI, sertifikat PDF, export CSV, artikel, grafik pertumbuhan, preferensi rekomendasi, profil publik/share, verifikasi WhatsApp, chat, pencarian lanjutan (geolokasi), multi-role per user.

---

## 9. Route (nama, path, middleware)

**Publik**
```
GET  /                          home
GET  /events                    events.index      (q, category, city, status)
GET  /events/{event:slug}       events.show
POST /midtrans/notification     midtrans.notification   (tanpa auth, tanpa CSRF)
```
**Auth:** rute Breeze (register, login, logout, reset password), ditambah:
```
GET  /auth/google/redirect    auth.google.redirect
GET  /auth/google/callback    auth.google.callback
POST /auth/google/choose-role auth.google.chooseRole   (khusus user baru dari Google)
```

**Volunteer** (`auth`, `role:volunteer`)
```
GET    /dashboard
GET    /profile, PUT /profile
GET    /applications                          applications.index
POST   /events/{event}/positions/{position}/apply   applications.store
DELETE /applications/{application}            applications.withdraw
GET    /my-events
GET    /history
GET    /settings
```
**EO** (`auth`, `role:eo`, prefix `/eo`, name `eo.`)
```
GET  /dashboard
GET/PUT /organization
GET  /events, /events/create, POST /events, /events/{event}/edit, PUT/DELETE /events/{event}
POST /events/{event}/submit           (butuh EnsureEoVerified)
POST /events/{event}/close, /events/{event}/finish
GET  /events/{event}/applicants
POST /applications/{application}/accept, /applications/{application}/reject
GET  /events/{event}/crew
GET  /payments, POST /payments/{payment}/pay, POST /payments/{payment}/check
```
**Admin** (`auth`, `role:admin`, prefix `/admin`, name `admin.`)
```
GET  /dashboard
GET  /eo-verifications, POST /eo-verifications/{organizer}/approve, /reject
GET  /event-reviews, /event-reviews/{event}, POST .../approve, .../reject
GET  /active-events
GET  /transactions
```

---

## 10. Antarmuka

- Referensi visual: mockup Figma (tautan di proposal) dan tangkapan layar di `docs/ui/` (lihat bagian 13). **Ikuti tata letak dan alur mockup**, tetapi konten mockup adalah data contoh.
- Layout: dashboard beranda role memakai **sidebar kiri** + topbar (pencarian, notifikasi, menu profil). Halaman publik memakai navbar atas.
- Gaya: dominan warna indigo/ungu sebagai warna aksi, kartu putih dengan sudut membulat, badge status berwarna (hijau diterima/aktif, kuning menunggu, merah ditolak), font sans geometris. Definisikan warna sebagai token Tailwind di `tailwind.config`.
- Wajib **responsif** (desktop dan mobile). Sidebar menjadi menu lipat di mobile.
- Buat komponen Blade yang dapat dipakai ulang: `x-badge-status`, `x-card`, `x-event-card`, `x-empty-state`, `x-pagination`, `x-alert`, `x-modal`, `x-file-input`.
- Setiap daftar wajib punya empty state dan pesan error/validasi Bahasa Indonesia.
- Sidebar Volunteer: Beranda, Cari Event, Lamaran Saya, Event Saya, Riwayat, Profil Saya. Sidebar EO: Beranda, Event Saya, Pendaftar, Koordinasi Kru, Pembayaran & Tagihan, Profil Organisasi. Sidebar Admin: Beranda, Verifikasi EO, Review Event, Event Aktif, Transaksi.

---

## 11. Non-fungsional

- Performa: hindari N+1 (gunakan eager loading, uji dengan `preventLazyLoading` di non-produksi); paginasi 12 item pada daftar.
- Keandalan: semua transisi status dan pembayaran dalam transaksi database.
- Keamanan: lihat bagian 7. Tambahkan test otorisasi untuk setiap grup route.
- Kualitas: Pint lulus, tidak ada `dd()`/`dump()` tersisa, tidak ada TODO tanpa penjelasan.
- Observability: log webhook Midtrans (tanpa membocorkan server key).
- Target uji: seluruh feature test hijau; skenario pembayaran sukses, gagal, pending tercakup.

---

## 12. Rencana eksekusi per fase

Setiap fase diakhiri: migrate:fresh --seed, test, pint hijau, ringkasan perubahan.

### Fase 0. Fondasi
- Inisialisasi Laravel, Breeze (Blade), Tailwind, Alpine, Pest, Pint. Koneksi PostgreSQL. `.env.example` lengkap.
- Enum, semua migrasi, model + relasi, factory, seeder (bagian 5).
- Layout dasar (publik + dashboard sidebar), komponen Blade dasar, token warna.
- **Selesai jika:** `migrate:fresh --seed` sukses; halaman home dan login tampil; test smoke lulus.

### Fase 1. Auth, role, profil (F1, F2)
- Registrasi dengan role, middleware `EnsureRole`, redirect per role, profil volunteer/EO dengan upload file.
- Install `laravel/socialite`, buat kredensial OAuth di Google Cloud Console (Client ID, Client Secret, Authorized redirect URI mengarah ke `auth.google.callback`), simpan di `.env` (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`) dan `config/services.php`.
- Alur login Google: redirect → callback → cek email di `users` → user baru ke `auth.google.chooseRole`, user lama langsung login.
- **Selesai jika:** kriteria F1 dan F2 terpenuhi + test otorisasi role + test login Google (user baru diarahkan pilih role, user lama langsung masuk, tidak ada duplikat akun untuk email yang sama).

### Fase 2. Verifikasi EO dan CRUD event (F3, F4)
- Halaman Admin Verifikasi EO; `EnsureEoVerified`; CRUD event + posisi + poster; draft dan submit.
- **Selesai jika:** F3 dan F4 terpenuhi; test transisi `draft → pending_review`.

### Fase 3. Review Admin (F5)
- Antrean review, setujui/tolak, pembuatan `payments` pending saat setuju.
- **Selesai jika:** F5 terpenuhi. *(Padanan Milestone 1)*

### Fase 4. Katalog publik (F7)
- Landing, list + search + filter, detail event, scope `open()`.
- **Selesai jika:** F7 terpenuhi; test bahwa event non-aktif tak tampil.

### Fase 5. Pembayaran (F6)
- `PaymentService`, Snap, webhook dengan verifikasi signature, idempotensi, aktivasi event, halaman tagihan EO, cek status, kedaluwarsa.
- **Selesai jika:** F6 terpenuhi dengan test untuk paid/failed/pending/expired/signature salah/duplikat.

### Fase 6. Melamar dan seleksi (F8, F9, F10, F11)
- `ApplicationService` (apply, accept dengan lock, reject, withdraw), Lamaran Saya, Pendaftar EO, Koordinasi Kru, tampilan info koordinasi bersyarat.
- **Selesai jika:** F8 hingga F11 terpenuhi; ada test kuota balapan. *(Padanan Milestone 2)*

### Fase 7. Riwayat dan P1
- F12 riwayat, lalu F13 notifikasi, F14 dashboard, F15, F16, F17 (scheduler), F18.
- **Selesai jika:** semua kriteria terpenuhi.

### Fase 8. Stabilisasi
- Audit N+1, audit otorisasi seluruh route, responsivitas, perbaikan bug, seeder demo bersih, README (instalasi, env, cara uji Midtrans sandbox + tunnel webhook), dokumentasi route.
- **Selesai jika:** seluruh test hijau dan alur end-to-end (bagian 1) dapat didemokan dari database seed baru.

---

## 13. Referensi dan berkas pendukung

- Proposal: `docs/Proposal_PBL_VOLA_Revisi.pdf` (konteks, survei, jadwal).
- Mockup: simpan tangkapan layar per halaman di `docs/ui/volunteer/`, `docs/ui/eo/`, `docs/ui/admin/` (nama berkas sama dengan judul halaman: beranda, cari-event, detail-event, lamaran-saya, dst). Agent harus merujuk ke berkas ini untuk tata letak.
- Figma dan desain database: tautan ada di lampiran proposal (9.2, 9.3).

---

## 14. Asumsi dan keputusan terbuka

Agent menggunakan asumsi ini kecuali dikoreksi:

| # | Asumsi | Dampak jika berubah |
|---|---|---|
| A1 | Laravel monolith Blade, **bukan** SPA + API terpisah | Arsitektur frontend berubah |
| A2 | Auth memakai Laravel Breeze, bukan Supabase Auth | Skema `users` berubah |
| A3 | Biaya layanan flat `450000` per event, via konfigurasi | Logika pembuatan invoice |
| A4 | Volunteer boleh melamar beberapa posisi berbeda di event yang sama (unique per volunteer + posisi) | Perlu `event_id` di `applications` bila hanya satu posisi per event |
| A5 | CV tidak di-snapshot saat melamar (EO melihat CV terbaru) | Tambah kolom snapshot di `applications` |
| A6 | Klaim "100% Gratis" di mockup landing page diganti agar konsisten dengan adanya biaya EO | Hanya salinan teks |
| A7 | Sertifikat, laporan, artikel ditunda ke pasca-MVP | Tabel tambahan nanti |
| A8 | User baru dari login Google wajib memilih role (volunteer/EO) sebelum masuk dashboard — tidak ada default otomatis | Alur onboarding tambahan setelah callback Google |

Jika menemukan konflik atau kebutuhan baru di luar dokumen ini, **catat di bagian "Pertanyaan untuk pemilik proyek" pada ringkasan fase dan berhenti**, jangan memutuskan sendiri.

---

## 15. Format ringkasan di akhir setiap fase

Agent harus menutup fase dengan ringkasan berformat:

```
Fase N selesai
- Yang dibuat: ...
- File utama yang berubah: ...
- Test: X lulus / Y total
- Keputusan/asumsi baru: ...
- Pertanyaan untuk pemilik proyek: ...
- Siap lanjut ke Fase N+1: ya/tidak (alasan)
```
