# PROMPT UNTUK AI CODING AGENT — Rebuild "Sistem Informasi KAPUAS" dengan Laravel + MySQL

> Salin seluruh isi file ini dan tempelkan ke AI coding agent kamu (Claude Code, Cursor, Copilot Workspace, dll). Prompt ini sudah disusun berdasarkan ERD, LRS, Class Diagram, Sequence Diagram, Use Case Diagram, dan Activity Diagram yang sudah dibuat.

---

## 1. KONTEKS PROYEK

Buatkan aplikasi web **Sistem Informasi KAPUAS** — sebuah sistem pengarsipan dokumen digital — menggunakan:

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL
- **Frontend**: Blade + Tailwind CSS (boleh ditambah Alpine.js untuk interaktivitas ringan). Jika lebih nyaman, boleh gunakan Laravel Breeze sebagai starter kit autentikasi.
- **Autentikasi & Otorisasi**: Laravel Breeze/Fortify untuk login, dan **role-based access control** custom (kolom `role` di tabel users atau tabel `roles` terpisah — pilih salah satu, jelaskan alasannya).
- **Penyimpanan file**: Laravel Filesystem (`storage/app/public`) dengan symbolic link (`php artisan storage:link`).
- **Tujuan aplikasi**: mengelola arsip/dokumen (upload, kategorisasi, pencarian, filter, download) dengan 3 peran pengguna: **Admin, Operator, Viewer**.

Bangun aplikasi ini **step by step**, mulai dari migration & model, lalu autentikasi, lalu setiap modul use case, dan terakhir dashboard. Setelah setiap modul selesai, jalankan `php artisan migrate` / test dasar untuk memastikan tidak ada error sebelum lanjut ke modul berikutnya.

---

## 2. HAK AKSES / ROLE (berdasarkan Use Case Diagram)

Ada 3 aktor: **Admin**, **Operator**, **Viewer**. Implementasikan middleware role (`role:admin`, `role:operator`, `role:viewer`) dengan matriks hak akses berikut:

| Fitur / Use Case              | Admin | Operator | Viewer |
|--------------------------------|:-----:|:--------:|:------:|
| Login                          | ✅    | ✅       | ✅     |
| Kelola User (CRUD akun)        | ✅    | ❌       | ❌     |
| Input Data Arsip               | ✅    | ✅       | ❌     |
| Upload Berkas                  | ✅    | ✅       | ❌     |
| Edit & Hapus Arsip             | ✅    | ✅       | ❌     |
| Cari & Filter Arsip            | ✅    | ✅       | ✅     |
| Download Berkas                | ✅    | ✅       | ✅     |
| Lihat Dashboard                | ✅    | ✅       | ✅     |

Catatan: **Kelola User hanya untuk Admin.** Operator memiliki hak yang sama dengan Admin untuk pengelolaan arsip, tetapi tidak bisa mengatur akun pengguna lain. Viewer hanya bisa melihat, mencari/filter, mendownload, dan melihat dashboard (read-only, tidak ada tombol tambah/edit/hapus).

Buat halaman **Login** sebagai halaman pertama (guest), dan setelah login redirect ke **Dashboard** sesuai role.

---

## 3. DATABASE — MIGRATION (berdasarkan ERD & LRS)

Buat migration Laravel persis sesuai struktur berikut (nama tabel gunakan bentuk jamak standar Laravel: `users`, `kategoris`/`categories`, `arsips`/`archives` — silakan pilih penamaan Inggris standar Laravel tapi kolomnya tetap sesuai daftar di bawah, atau pakai nama tabel Indonesia `arsip` & `kategori` bila ingin konsisten dengan diagram; **konsisten-kan penamaan di seluruh kode**).

### Tabel `users`
| Kolom       | Tipe               | Keterangan                          |
|-------------|--------------------|--------------------------------------|
| id_user     | bigint, PK, AI      | Primary key                         |
| nama_user   | varchar(150)        | Nama lengkap pengguna               |
| email       | varchar(150), unique| Email untuk login                   |
| password    | varchar(255)        | Password (di-hash dengan bcrypt)    |
| role        | enum('admin','operator','viewer'), default 'viewer' | Peran pengguna |
| email_verified_at | timestamp, nullable | standar Laravel |
| remember_token | varchar(100), nullable | standar Laravel |
| created_at / updated_at | timestamp | standar Laravel |

### Tabel `kategori`
| Kolom          | Tipe                | Keterangan          |
|----------------|---------------------|----------------------|
| id_kategori    | bigint, PK, AI       | Primary key          |
| nama_kategori  | varchar(100), unique | Nama kategori arsip  |
| created_at / updated_at | timestamp   | standar Laravel      |

### Tabel `arsip`
| Kolom         | Tipe                          | Keterangan                                        |
|---------------|-------------------------------|-----------------------------------------------------|
| id_arsip      | bigint, PK, AI                 | Primary key                                        |
| id_user       | bigint, FK → users.id_user     | Siapa yang mengunggah arsip (relasi "Mengunggah")  |
| id_kategori   | bigint, FK → kategori.id_kategori | Kategori arsip (relasi "Memiliki")             |
| kode_arsip    | varchar(50), unique             | Kode unik arsip, auto-generate (lihat poin 6.3)   |
| judul_arsip   | varchar(255)                    | Judul/nama dokumen                                 |
| file_path     | varchar(255)                    | Path file di storage                               |
| tgl_upload    | date                             | Tanggal upload arsip                               |
| created_at / updated_at | timestamp             | standar Laravel                                    |

Gunakan `foreignId(...)->constrained(...)->onDelete('cascade')` atau `restrict` (jelaskan pilihanmu — disarankan `restrict` agar arsip tidak hilang tak sengaja saat user/kategori dihapus, dan tambahkan validasi "tidak bisa hapus kategori/user jika masih dipakai arsip").

Buat juga **Seeder**:
- `UserSeeder`: 1 akun admin, 1 akun operator, 1 akun viewer (password default `password`, wajib di-hash).
- `KategoriSeeder`: minimal 4 kategori contoh (misal: Surat Masuk, Surat Keluar, SPJ, Kontrak, Dokumen Lainnya).
- `ArsipSeeder` (opsional): beberapa data dummy arsip untuk testing dashboard & filter.

---

## 4. MODEL & RELASI (berdasarkan Class Diagram)

### Model `User` (extends Authenticatable)
- Primary key custom: `id_user` (set `protected $primaryKey = 'id_user';`)
- Fillable: `nama_user, email, password, role`
- Relasi: `hasMany(Arsip::class, 'id_user', 'id_user')`
- Method helper: `isAdmin()`, `isOperator()`, `isViewer()` untuk cek role dengan mudah di Blade/Controller.

### Model `Kategori`
- Primary key custom: `id_kategori`
- Fillable: `nama_kategori`
- Relasi: `hasMany(Arsip::class, 'id_kategori', 'id_kategori')`

### Model `Arsip`
- Primary key custom: `id_arsip`
- Fillable: `id_user, id_kategori, kode_arsip, judul_arsip, file_path, tgl_upload`
- Cast: `tgl_upload` → `date`
- Relasi: `belongsTo(User::class, 'id_user', 'id_user')`, `belongsTo(Kategori::class, 'id_kategori', 'id_kategori')`
- Tambahkan **accessor** `getFileUrlAttribute()` yang mengembalikan URL publik file (via `Storage::url($this->file_path)`).
- Tambahkan **scope** untuk pencarian & filter: `scopeSearch($query, $keyword)` (cari di `judul_arsip` dan `kode_arsip`), `scopeFilterKategori($query, $idKategori)`, `scopeFilterTahun($query, $tahun)` (berdasarkan `tgl_upload`).

---

## 5. AUTENTIKASI (Use Case: Login) — mengikuti Activity Diagram Login

Alur (ikuti persis activity diagram "Login" yang sudah dibuat):
1. User membuka aplikasi → sistem menampilkan form login (email & password).
2. User menginput email & password, klik tombol login.
3. Sistem mengirim & memvalidasi kredensial ke database (`Auth::attempt()`).
4. Jika **gagal** (email/password salah) → tampilkan notifikasi error di form login, jangan redirect.
5. Jika **berhasil** → cocokkan role user, arahkan ke halaman Dashboard sesuai role.

Implementasi teknis:
- Gunakan Laravel Breeze untuk scaffolding awal, lalu sesuaikan `LoginController`/`AuthenticatedSessionController` agar memakai kolom `email` & `password` custom user model di atas.
- Setelah login sukses, redirect semua role ke `/dashboard` (isi dashboard bisa disesuaikan per role di dalam view, bukan route terpisah).
- Buat middleware `CheckRole` (`app/Http/Middleware/CheckRole.php`) yang menerima parameter role (`role:admin,operator`) untuk membatasi akses route tertentu.
- Sediakan tombol/menu **Logout** yang menghapus session.

---

## 6. MODUL: KELOLA ARSIP

### 6.1 Input Data Arsip (Use Case: Input Data Arsip + Upload Berkas)
Ikuti Activity Diagram "Input Data Arsip":
1. User (Admin/Operator) klik menu **"Data Arsip"** → sistem menampilkan seluruh data arsip (tabel dengan pagination).
2. User klik tombol **"Tambah Arsip"** → sistem menampilkan form input arsip (field: kategori (dropdown), judul arsip, tanggal upload, file upload).
3. User mengisi data, memilih/mengunggah berkas, klik **Simpan**.
4. Sistem mengirim data form + file (request POST) ke controller.
5. Sistem **memvalidasi**:
   - Data wajib: `id_kategori` (required, exists di tabel kategori), `judul_arsip` (required, string, max:255), `tgl_upload` (required, date).
   - File wajib: `file` (required saat tambah, `mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png`, `max:10240` KB — sesuaikan batas ukuran sesuai kebutuhan).
6. **Jika validasi gagal** (format/ukuran salah atau field kosong) → tampilkan pesan error di form (format sesuai *Sequence Diagram*: "tampil pesan format atau ukuran salah"), form tidak ter-submit, data tidak tersimpan.
7. **Jika validasi berhasil**:
   - Generate `kode_arsip` otomatis, misal format: `ARS-{TAHUN}-{4 digit auto increment}`, contoh `ARS-2026-0001`.
   - Simpan file fisik ke `storage/app/public/arsip/{tahun}/{namafile-unik}` (gunakan `Str::uuid()` atau `time()` agar nama file tidak bentrok).
   - Simpan data ke tabel `arsip` (`id_user` = user yang login).
   - Tampilkan notifikasi sukses "Arsip berhasil ditambahkan".
   - Perbarui tampilan tabel data arsip (redirect back ke index atau refresh via Ajax — pilih salah satu, sertakan alasan).
8. Sediakan juga tombol **Batal** yang menutup form tanpa menyimpan apa pun (sesuai Sequence Diagram: `batal()`).

### 6.2 Edit & Hapus Arsip (Use Case: Edit & Hapus Arsip)
Ikuti Activity Diagram "Edit dan Hapus Arsip":
1. Dari halaman **Data Arsip**, user memilih satu baris arsip dan memilih tindakan **Edit** atau **Hapus**.
2. **Edit**:
   - Sistem menampilkan form edit terisi data lama.
   - User boleh mengubah data teks dan/atau mengunggah file baru (file lama tetap dipakai jika tidak upload file baru).
   - Sistem validasi ulang (sama seperti input, tapi `file` bersifat `nullable` saat edit).
   - Jika file baru diunggah: hapus file lama dari storage, simpan file baru, update `file_path`.
   - Simpan perubahan ke database → notifikasi "Arsip berhasil diperbarui" → refresh tabel.
3. **Hapus**:
   - Tampilkan **konfirmasi** (modal/dialog) sebelum menghapus.
   - Jika dikonfirmasi: hapus record dari database **dan** hapus file fisik dari storage.
   - Tampilkan notifikasi "Arsip berhasil dihapus" → refresh tabel.
4. Gunakan **soft delete** (opsional, sebutkan sebagai rekomendasi) agar data bisa dipulihkan, atau hard delete sesuai kebutuhan — jelaskan pilihan yang diambil.

### 6.3 Cari & Filter Arsip (Use Case: Cari & Filter Arsip — semua role bisa akses)
Ikuti Activity Diagram "Cari dan Filter Arsip":
1. Di halaman Data Arsip, sediakan input **kata kunci pencarian** (judul/kode arsip) dan **dropdown filter** (kategori, tahun, rentang tanggal).
2. Saat user klik tombol **Cari/Filter**, kirim query ke server (bisa via GET query string agar bisa di-bookmark, atau live search dengan Alpine.js/Ajax — pilih salah satu).
3. Sistem memproses pencarian menggunakan scope Eloquent (lihat poin 4) — kombinasikan kondisi keyword + kategori + tahun dengan `where`/`when()`.
4. Jika **tidak ditemukan** → tampilkan pesan "Data arsip tidak ditemukan" beserta ilustrasi/empty state, dan tombol reset filter.
5. Jika **ditemukan** → tampilkan daftar arsip hasil pencarian dengan pagination, pertahankan query string filter di link pagination (`->appends(request()->query())`).

### 6.4 Download Berkas (Use Case: Download Berkas — semua role bisa akses)
Ikuti Activity Diagram "Download Berkas":
1. User memilih arsip dan klik tombol **Download**.
2. Sistem mengecek ketersediaan file di storage (`Storage::disk('public')->exists($filePath)`).
3. Jika file **tidak ditemukan** → tampilkan pesan error "Berkas tidak ditemukan / sudah dihapus dari server", jangan lanjutkan proses download, catat log peringatan (`Log::warning(...)`).
4. Jika file **ditemukan** → kirim file ke browser menggunakan `Storage::download()` dengan nama file yang human-readable (misal `judul_arsip.pdf`).
5. **Viewer** hanya bisa download, tidak bisa lihat tombol edit/hapus/tambah pada tampilan tabel (atur dengan Blade `@can`/`@role` directive).

---

## 7. MODUL: KELOLA USER (Use Case: Kelola User — khusus Admin)

Buat CRUD lengkap untuk manajemen akun pengguna, hanya bisa diakses Admin (`middleware(['auth','role:admin'])`):
1. Halaman daftar user (tabel: nama, email, role, aksi edit/hapus).
2. Form **Tambah User**: nama_user, email (unique), password (min 8 karakter, dikonfirmasi), role (dropdown: admin/operator/viewer). Validasi lengkap sebelum simpan, hash password dengan `Hash::make()`.
3. Form **Edit User**: bisa ubah nama, email, role, dan opsional ganti password (kosongkan jika tidak ingin ganti password).
4. **Hapus User**: tampilkan konfirmasi, cegah admin menghapus akun dirinya sendiri (`if ($user->id_user === auth()->id())` tolak dengan pesan error), dan cegah hapus user yang masih memiliki data arsip terkait (atau alihkan `id_user` arsip terkait ke NULL/admin — tentukan kebijakan, jelaskan di kode).
5. Tampilkan notifikasi sukses/gagal untuk tiap aksi (tambah/edit/hapus).

---

## 8. MODUL: DASHBOARD (Use Case: Lihat Dashboard — semua role bisa akses)

Ikuti Activity Diagram "Lihat Dashboard":
1. Saat user klik menu **Dashboard**, sistem mengambil data statistik arsip dari database dan mengolahnya menjadi ringkasan.
2. Jika data arsip **belum ada** → tampilkan pesan "Data belum tersedia" dengan tampilan kosong yang rapi (empty state), bukan chart kosong yang error.
3. Jika data **tersedia**, tampilkan:
   - **Kartu ringkasan (summary cards)**: total arsip, jumlah per kategori (misal Surat, SPJ, Kontrak, Dokumen Lainnya).
   - **Grafik batang/area**: jumlah arsip per bulan (12 bulan, tahun berjalan atau tahun yang dipilih via dropdown filter tahun).
   - **Grafik pie/donut**: proporsi arsip per kategori.
   - **Tabel arsip terbaru**: 5 arsip terakhir yang diunggah (judul, tanggal, kategori, uploader, tombol lihat/download).
4. Gunakan library chart untuk Blade, misalnya **Chart.js** (via CDN) atau ApexCharts — buat controller `DashboardController@index` yang menyiapkan semua data (`total`, `perKategori`, `perBulan`, `recentArsip`) dan kirim ke view sebagai variabel, jangan hitung di Blade.
5. Sediakan filter tahun di dashboard (dropdown tahun-tahun yang punya data + tahun berjalan).

---

## 9. STRUKTUR ROUTE (contoh, sesuaikan bila perlu)

```php
// routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bisa diakses Admin & Operator
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/arsip/create', [ArsipController::class, 'create'])->name('arsip.create');
        Route::post('/arsip', [ArsipController::class, 'store'])->name('arsip.store');
        Route::get('/arsip/{arsip}/edit', [ArsipController::class, 'edit'])->name('arsip.edit');
        Route::put('/arsip/{arsip}', [ArsipController::class, 'update'])->name('arsip.update');
        Route::delete('/arsip/{arsip}', [ArsipController::class, 'destroy'])->name('arsip.destroy');
    });

    // Bisa diakses semua role (Admin, Operator, Viewer)
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/{arsip}/download', [ArsipController::class, 'download'])->name('arsip.download');

    // Khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });
});
```

---

## 10. VALIDASI & PESAN (gunakan Bahasa Indonesia untuk semua notifikasi/flash message)

Gunakan **Form Request** terpisah untuk tiap aksi (`StoreArsipRequest`, `UpdateArsipRequest`, `StoreUserRequest`, dsb.) agar controller bersih. Contoh pesan custom:
- `judul_arsip.required` → "Judul arsip wajib diisi."
- `file.mimes` → "Format berkas tidak didukung. Gunakan PDF, Word, Excel, atau gambar."
- `file.max` → "Ukuran berkas maksimal 10 MB."
- `id_kategori.required` → "Kategori arsip wajib dipilih."

Gunakan `session()->flash('success', '...')` / `session()->flash('error', '...')` dan tampilkan sebagai alert/toast di layout utama (`layouts/app.blade.php`).

---

## 11. STRUKTUR FOLDER YANG DIHARAPKAN

```
app/
  Http/
    Controllers/
      AuthenticatedSessionController.php
      DashboardController.php
      ArsipController.php
      UserManagementController.php
      KategoriController.php
    Middleware/
      CheckRole.php
    Requests/
      StoreArsipRequest.php
      UpdateArsipRequest.php
      StoreUserRequest.php
      UpdateUserRequest.php
  Models/
    User.php
    Kategori.php
    Arsip.php
database/
  migrations/
    xxxx_create_users_table.php
    xxxx_create_kategori_table.php
    xxxx_create_arsip_table.php
  seeders/
    UserSeeder.php
    KategoriSeeder.php
    ArsipSeeder.php
    DatabaseSeeder.php
resources/
  views/
    layouts/app.blade.php
    auth/login.blade.php
    dashboard/index.blade.php
    arsip/index.blade.php
    arsip/create.blade.php
    arsip/edit.blade.php
    users/index.blade.php
    users/create.blade.php
    users/edit.blade.php
routes/
  web.php
```

---

## 12. NON-FUNCTIONAL & KUALITAS KODE

- Gunakan **Eloquent**, hindari raw query kecuali benar-benar perlu.
- Semua form pakai **CSRF token** (`@csrf`) dan method spoofing (`@method('PUT')`/`@method('DELETE')`) sesuai standar Blade.
- Gunakan **pagination** (`->paginate(10)`) di semua tabel data (arsip, user).
- Tampilan **responsive** (mobile-friendly) dengan Tailwind CSS.
- Tambahkan **`.env.example`** yang sudah berisi konfigurasi `DB_CONNECTION=mysql`, `DB_DATABASE=sima_kapuas`, dll.
- Sertakan instruksi setup di `README.md`: clone → `composer install` → copy `.env` → `php artisan key:generate` → `php artisan migrate --seed` → `php artisan storage:link` → `npm install && npm run build` → `php artisan serve`.
- Tambahkan penanganan error umum (404 file tidak ditemukan, 403 akses ditolak untuk role yang salah — buat halaman error kustom sederhana).
- Tulis kode dengan komentar secukupnya pada bagian logika penting (validasi, generate kode arsip, dsb.).

---

## 13. URUTAN PENGERJAAN YANG DIMINTA

Kerjakan **secara bertahap** dan tampilkan/jalankan hasil tiap tahap sebelum lanjut:

1. Inisialisasi project Laravel + konfigurasi `.env` untuk MySQL.
2. Migration + Model + Seeder (tabel `users`, `kategori`, `arsip`).
3. Autentikasi (Login/Logout) + middleware role.
4. Layout utama (sidebar menu berbeda sesuai role, navbar, alert flash message).
5. Modul Dashboard (statistik & grafik).
6. Modul Data Arsip: index (tabel + cari & filter), create, edit, delete, download.
7. Modul Kelola User (khusus admin).
8. Uji akhir: login sebagai admin/operator/viewer, pastikan hak akses sesuai matriks di poin 2, dan seluruh alur activity diagram (login, input arsip, edit/hapus arsip, upload berkas, cari & filter, download, kelola user, lihat dashboard) berjalan sesuai deskripsi di atas.

Jika ada bagian yang ambigu dari struktur diagram asli saya, buat asumsi yang paling logis untuk sistem pengarsipan dokumen instansi pemerintah, dan sebutkan asumsi tersebut secara singkat di README.
