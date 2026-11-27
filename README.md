# Sistem Informasi KAPUAS — Pengarsipan Dokumen Digital Resmi

Aplikasi web sistem pengarsipan dokumen digital berbasis **Laravel 13** dan **MySQL**, dirancang khusus untuk mempermudah instansi dalam merekam, mengelompokkan, mencari, mengamankan, dan mendownload berkas dokumen resmi dengan tata kelola hak akses berbasis peran (**Role-Based Access Control / RBAC**).

---

## 📌 Fitur Utama

- **Autentikasi Aman & RBAC (Role-Based Access Control)**:
  - **Admin**: Akses penuh ke seluruh modul, termasuk Manajemen Akun Pengguna (`users`) dan pengaturan kategori.
  - **Operator**: Perekaman metadata dokumen baru, pengunggahan berkas fisik, pengubahan, penghapusan arsip, dan pengelolaan kategori.
  - **Viewer**: Mode baca saja (*read-only*), pencarian dokumen, pemfilteran kategori/tahun, pengunduhan berkas, dan peninjauan dashboard statistik.
- **Perekaman & Manajemen Berkas Arsip**:
  - Penomoran kode unik otomatis: `ARS-{TAHUN}-{0001}` secara otomatis terurut setiap tahun.
  - Pengunggahan berkas fisik dokumen ke disk storage (`PDF, DOC/DOCX, XLS/XLSX, JPG/PNG` hingga 10 MB).
  - Penggantian berkas aman (otomatis menghapus berkas lama dari server saat diganti).
  - Penghapusan aman dengan modal konfirmasi dan penghapusan berkas fisik di storage.
- **Pencarian Cepat & Filter Multi-Kriteria**:
  - Pencarian kata kunci pada judul dokumen dan kode arsip.
  - Filter berdasarkan klasifikasi kategori arsip.
  - Filter berdasarkan tahun pengunggahan.
  - Paginasi rapi dengan mempertahankan parameter query URL (*URL query string preservation*).
- **Dashboard Analitik & Visualisasi Data**:
  - Kartu ringkasan metrik: Total Arsip, Dokumen Masuk Bulan Ini, Total Kategori, dan Total Pengguna.
  - Filter tahun fleksibel pada dashboard.
  - Grafik batang bulanan 12 bulan aktivitas pengarsipan (Chart.js).
  - Grafik donat proporsi kategori dokumen (Chart.js).
  - Tabel 5 dokumen arsip terbaru dengan akses langsung ke unduhan berkas.
- **Keamanan & Proteksi Relasi Basis Data**:
  - Pencegahan *self-deletion* pada akun Admin yang sedang login.
  - Proteksi *foreign key restriction* (`onDelete('restrict')`): akun pengguna dan kategori yang masih memiliki data arsip terkait dilarang dihapus untuk menjaga keutuhan dokumen arsip instansi.

---

## 🛠️ Prasyarat Sistem

- **PHP**: Versi 8.2 atau lebih baru (disarankan PHP 8.3+) dengan ekstensi `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`.
- **Composer**: Versi 2.x
- **Basis Data**: MySQL / MariaDB (minimal MySQL 8.0)
- **Node.js & npm**: Node.js v18+ dan npm v9+

---

## 🚀 Panduan Instalasi & Menjalankan

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi:

### 1. Masuk ke Direktori Proyek
```bash
cd d:/KAPUASS
```

### 2. Instal Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file template `.env.example` menjadi `.env` jika belum ada:
```bash
copy .env.example .env
```

Pastikan konfigurasi database di dalam `.env` mengarah ke server MySQL lokal Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kapuas
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Application Encryption Key
```bash
php artisan key:generate
```

### 5. Jalankan Migration & Seeder
Buat seluruh tabel dan isi data awal akun pengguna serta sampel arsip:
```bash
php artisan migrate:fresh --seed
```

### 6. Hubungkan Storage Link untuk Berkas Publik
```bash
php artisan storage:link
```

### 7. Instal Dependensi Frontend & Kompilasi Aset
```bash
npm install
npm run build
```

### 8. Jalankan Server Pengembangan
```bash
php artisan serve
```
Aplikasi dapat diakses melalui peramban web di: **`http://localhost:8000`**

---

## 🔑 Akun Bawaan (Default Seeded Users)

Semua akun menggunakan kata sandi default: **`password`**

| Peran (Role) | Alamat Email | Kata Sandi | Deskripsi Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@kapuas.go.id` | `password` | Akses penuh (Dashboard, Arsip, Kategori, Kelola Pengguna) |
| **Operator** | `operator@kapuas.go.id` | `password` | Kelola arsip (Tambah, Edit, Hapus, Download) & Kategori |
| **Viewer** | `viewer@kapuas.go.id` | `password` | Hanya lihat, cari, filter, unduh arsip & tinjau dashboard |

> *Tersedia tombol demo cepat di halaman Login untuk mengisi email dan kata sandi masing-masing peran dalam satu klik.*

---

## 🧪 Menjalankan Pengujian Otomatis (Automated Tests)

Aplikasi dilengkapi dengan pengujian fitur (*Feature Tests*) lengkap yang menguji alur autentikasi, matriks hak akses RBAC, CRUD arsip, kode unik otomatis, dan proteksi akun pengguna:

```bash
php artisan test
```

Semua 16 skenario pengujian dengan 56 assertions telah terverifikasi lolos 100%.

---

## 📐 Asumsi & Keputusan Teknis

1. **Struktur Tabel**:
   - Tabel pengguna menggunakan nama `users` (standar Laravel Auth) dengan kolom kustom `id_user` (Primary Key), `nama_user`, `email`, `password`, dan `role`.
   - Tabel klasifikasi menggunakan nama `kategori` dengan kolom `id_kategori` (PK) dan `nama_kategori`.
   - Tabel dokumen menggunakan nama `arsip` dengan kolom `id_arsip` (PK), relasi `id_user`, `id_kategori`, `kode_arsip` (unique), `judul_arsip`, `file_path`, dan `tgl_upload`.
2. **Integritas Referensial**:
   - Foreign key pada tabel `arsip` menggunakan aturan `onDelete('restrict')`.
   - Kebijakan ini dipilih agar dokumen arsip resmi pemerintah tidak hilang secara tidak sengaja ketika suatu akun pegawai dimutasi atau ketika nama kategori hendak dihapus. Sistem memberikan notifikasi peringatan jika ada upaya menghapus entitas yang masih memiliki arsip.
3. **Format Kode Arsip**:
   - Menggunakan format `ARS-{TAHUN}-{0001}` (contoh: `ARS-2026-0001`), dengan nomor urut yang bertambah otomatis per tahun dokumen.
4. **Penyimpanan Berkas**:
   - Berkas fisik disimpan di `storage/app/public/arsip/{tahun}/{filename-unik}` dan dapat diakses dengan aman melalui controller download maupun symlink public storage.
