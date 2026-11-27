# Prompt untuk Google Antigravity — Redesign UI Sistem Informasi KAPUAS

> Tempel prompt ini di Antigravity dengan **Planning Mode** (bukan Fast Mode), karena task ini multi-file dan butuh verifikasi visual lewat browser.

---

## PROMPT

Kamu bekerja di dalam codebase Laravel 13 yang sudah ada bernama **Sistem Informasi KAPUAS** — aplikasi pengarsipan dokumen digital instansi dengan RBAC (Admin, Operator, Viewer), dashboard analitik (Chart.js), dan manajemen arsip dokumen. Stack: Laravel 13 + Blade + Tailwind CSS + Vite.

Tugasmu: **redesign seluruh tampilan UI (view layer) aplikasi** agar elegan, modern, minimalis, dan mudah dipahami pengguna awam — tanpa mengubah logika bisnis, route, controller, atau struktur database yang sudah ada. Ini murni pekerjaan di layer Blade view + Tailwind config + assets.

### Langkah kerja yang wajib kamu ikuti

1. **Eksplorasi dulu sebelum menulis kode.** Baca struktur folder `resources/views`, `routes/web.php`, controller terkait, dan `tailwind.config.js` (atau konfigurasi Tailwind v4 di `resources/css`) yang sudah ada. Pahami komponen Blade yang sudah dipakai berulang (layout, partial, komponen form/table) sebelum membuat yang baru, agar tidak duplikat.
2. **Buat design token terlebih dahulu** di konfigurasi Tailwind (warna, radius, shadow) berdasarkan palet resmi di bawah, supaya konsisten dipakai di semua view.
3. **Kerjakan per halaman secara berurutan**, commit/checkpoint di setiap halaman selesai: Login → Layout utama (sidebar+topbar) → Dashboard → Daftar Arsip Dokumen → Form Tambah/Edit Dokumen → Manajemen Kategori → Manajemen Pengguna (khusus Admin).
4. **Setelah setiap halaman selesai, jalankan `php artisan serve` (atau gunakan server dev yang sudah jalan) dan buka browser untuk verifikasi visual** — pastikan tidak ada elemen rusak, warna sesuai token, dan responsive di lebar tablet (768px) serta mobile (375px).
5. **Jangan mengubah** nama route, nama field database, validasi form, maupun logic controller. Jika sebuah perubahan visual butuh penyesuaian kecil di controller (misal menambah data untuk badge status), tanyakan dulu / catat sebagai temuan, jangan langsung mengubah logic inti tanpa konfirmasi.

### Design Token (Tailwind) — logo brand KAPUAS (tiga gunung biru + sungai)

```js
colors: {
  navy: { DEFAULT: '#003CAA', dark: '#0046B4' },
  primary: { DEFAULT: '#0050C8', light: '#1478F0' },
  accent: { DEFAULT: '#3296FA', light: '#46AAFA' },
  ice: '#D2E6FA',
  ink: { DEFAULT: '#1E293B', muted: '#64748B' },
  line: '#E2E8F0',
  success: '#16A34A',
  warning: '#F59E0B',
  danger: '#DC2626',
}
```
- Gradient signature aplikasi: `from-navy to-accent` (dipakai di sidebar, header, tombol utama, panel branding login).
- Border radius standar: `rounded-xl` (12px) untuk card, `rounded-lg` (8px) untuk tombol/input.
- Shadow: gunakan shadow tipis/soft (`shadow-sm` / custom `0 1px 3px rgba(0,0,0,0.06)`), hindari shadow tegas/hitam pekat.
- Font: Inter atau Plus Jakarta Sans (load via Google Fonts atau bundle lokal, sesuaikan dengan yang tersedia offline jika instansi butuh tanpa CDN eksternal).

### Spesifikasi per halaman

**Layout utama (dipakai semua halaman setelah login)**
- Sidebar kiri fixed, gradient `navy → primary`, logo di atas, menu: Dashboard, Arsip Dokumen, Kategori, Pengguna (hanya tampil untuk role Admin), Laporan. Menu aktif: background putih transparan 10-15% + garis aksen `accent` di sisi kiri.
- Topbar putih: judul halaman dinamis, search bar global, ikon notifikasi, avatar + nama pengguna + badge role (Admin = navy solid, Operator = primary solid, Viewer = outline abu).
- Sidebar collapse jadi ikon saja di tablet, hamburger menu di mobile.

**Login**
- Split-screen: panel kiri gradient `navy → accent` dengan logo besar dan tagline; form kanan putih bersih, input rounded dengan label di atas, tombol submit gradient penuh.

**Dashboard**
- 4 kartu statistik (Total Arsip, Dokumen Masuk Bulan Ini, Total Kategori, Total Pengguna), masing-masing warna berbeda dari token di atas, ikon + angka besar.
- Filter dropdown tahun.
- Bar chart 12 bulan aktivitas (gradasi biru) dan donut chart proporsi kategori — styling Chart.js disesuaikan pakai warna token, bukan default palette Chart.js.
- Tabel "5 Dokumen Arsip Terbaru" dengan hover baris `ice` tipis.

**Daftar Arsip Dokumen**
- Search bar besar + filter kategori & tahun (dropdown/pill).
- Tabel: kolom Kode Arsip (badge monospace `ARS-{TAHUN}-{0001}`), Judul, Kategori (badge warna sesuai kategori), Tanggal Upload, Aksi (ikon lihat/edit/unduh/hapus + tooltip).
- Modal konfirmasi hapus: overlay gelap, ikon warning merah lembut, penjelasan konsekuensi singkat, tombol Batal (outline) & Hapus (solid danger).

**Form Tambah/Edit Dokumen**
- Dua kolom di desktop (field pendek kiri, deskripsi kanan).
- Upload area dashed border `accent`, ikon upload, teks instruksi + keterangan format & ukuran file yang didukung (PDF, DOC/DOCX, XLS/XLSX, JPG/PNG, maks 10MB).
- Preview file terpilih (nama, ukuran, ikon tipe) dengan opsi ganti/hapus sebelum submit.
- Error validasi tampil di bawah masing-masing field, teks kecil merah — bukan alert global.

**Manajemen Kategori & Manajemen Pengguna**
- Pola tabel sama seperti Daftar Arsip (konsisten), form tambah/edit dalam modal (bukan halaman terpisah) karena field-nya sedikit.
- Pada Manajemen Pengguna: badge role berwarna, tombol nonaktifkan/hapus akun disable otomatis untuk akun Admin yang sedang login (sudah ada logic-nya di backend — cukup refleksikan state disabled di UI).

### Kriteria selesai (acceptance criteria)

- [ ] Semua halaman memakai token warna yang sama, tidak ada warna hardcoded di luar palet.
- [ ] Sidebar & topbar konsisten di semua halaman setelah login.
- [ ] Semua halaman responsive dan sudah diverifikasi di browser pada 3 lebar: desktop (1440px), tablet (768px), mobile (375px).
- [ ] Tidak ada perubahan pada route, controller logic, atau skema database.
- [ ] Kontras teks di atas elemen navy/primary memenuhi kontras yang nyaman dibaca (teks putih atau ice blue di atas background gelap).
- [ ] Berikan ringkasan file apa saja yang diubah/ditambahkan di akhir task.
