# PROMPT LANJUTAN UNTUK AI CODING AGENT — Fitur Drawer Program/Kegiatan/Sub Kegiatan + Master Data

> Ini adalah **prompt lanjutan** dari prompt sebelumnya ("Rebuild Sistem Informasi KAPUAS dengan Laravel + MySQL"). Tempelkan ke AI coding agent yang sama, di project Laravel yang sudah dibuat sebelumnya, sebagai instruksi tambahan/perubahan.

---

## 1. KONTEKS PERUBAHAN

Ada 2 perubahan/penambahan besar:

1. Pada halaman **Tambah Arsip / Edit Arsip**, field **Program**, **Kegiatan**, dan **Sub Kegiatan** **BUKAN input teks bebas**. Ketiganya harus dipilih melalui **drawer (panel geser dari samping / slide-over)** yang menampilkan pilihan secara **hierarkis dan berjenjang**: Tahun Penetapan → Program → Kegiatan → Sub Kegiatan. User tidak boleh mengetik nama program/kegiatan/sub kegiatan sendiri di form arsip — hanya boleh **memilih** dari data master yang sudah ada.
2. Tambahkan menu pengaturan baru bernama **"Master Data"** (sejajar dengan menu "Kelola User", khusus untuk role **Admin**) yang berisi **3 poin pengelolaan**:
   - **Poin 1: Hierarki Program** — kelola data Program, Kegiatan, dan Sub Kegiatan secara berjenjang.
   - **Poin 2: Sub Jenis SPJ** — kelola daftar sub jenis SPJ.
   - **Poin 3: Tahun Arsip** — kelola daftar tahun arsip.

Semua data pada form Tambah/Edit Arsip (Program, Kegiatan, Sub Kegiatan, Sub Jenis SPJ, Tahun Arsip) sekarang **bersumber dari Master Data ini**, bukan diketik manual.

---

## 2. DATABASE TAMBAHAN (migration baru)

### Tabel `programs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK, AI | |
| nama_program | varchar(255) | Nama program |
| tahun_penetapan | year / integer(4) | Tahun program ditetapkan |
| created_at / updated_at | timestamp | |

Tambahkan unique constraint gabungan `(nama_program, tahun_penetapan)` agar tidak ada program duplikat di tahun yang sama.

### Tabel `kegiatans`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK, AI | |
| program_id | bigint, FK → programs.id | Kegiatan turunan dari program tertentu |
| nama_kegiatan | varchar(255) | Nama kegiatan |
| created_at / updated_at | timestamp | |

### Tabel `sub_kegiatans`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK, AI | |
| kegiatan_id | bigint, FK → kegiatans.id | Sub kegiatan turunan dari kegiatan tertentu |
| nama_sub_kegiatan | varchar(255) | Nama sub kegiatan |
| created_at / updated_at | timestamp | |

### Tabel `sub_jenis_spjs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK, AI | |
| nama_sub_jenis_spj | varchar(150), unique | Nama sub jenis SPJ |
| created_at / updated_at | timestamp | |

### Tabel `tahun_arsips`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK, AI | |
| tahun | year / integer(4), unique | Tahun arsip, mis. 2026 |
| created_at / updated_at | timestamp | |

### Update tabel `arsip` (migration tambahan `ALTER TABLE` / migration baru)
Tambahkan kolom (semua nullable, sebagai FK):
- `sub_kegiatan_id` → FK ke `sub_kegiatans.id` (relasi turunan sudah membawa info kegiatan & program, jadi **tidak perlu simpan `program_id`/`kegiatan_id` langsung di tabel `arsip`** — cukup ambil lewat relasi berjenjang: `arsip->subKegiatan->kegiatan->program`).
- `sub_jenis_spj_id` → FK ke `sub_jenis_spjs.id`
- `tahun_arsip_id` → FK ke `tahun_arsips.id`

Gunakan `->nullOnDelete()` atau `->restrictOnDelete()` untuk relasi ini (jelaskan pilihanmu — disarankan `restrict` agar data master tidak bisa dihapus jika masih dipakai arsip, tampilkan pesan error yang jelas jika admin mencoba menghapus data master yang masih terpakai).

---

## 3. MODEL & RELASI BARU

```
Program        hasMany   Kegiatan (program_id)
Kegiatan       belongsTo Program (program_id)
               hasMany   SubKegiatan (kegiatan_id)
SubKegiatan    belongsTo Kegiatan (kegiatan_id)
               hasMany   Arsip (sub_kegiatan_id)
SubJenisSpj    hasMany   Arsip (sub_jenis_spj_id)
TahunArsip     hasMany   Arsip (tahun_arsip_id)
Arsip          belongsTo SubKegiatan, SubJenisSpj, TahunArsip
```

Tambahkan accessor di model `SubKegiatan`:
```php
public function getFullPathAttribute(): string
{
    return "{$this->kegiatan->program->nama_program} > {$this->kegiatan->nama_kegiatan} > {$this->nama_sub_kegiatan}";
}
```
Accessor ini dipakai untuk menampilkan teks hasil pilihan di form arsip (contoh: "Peningkatan Infrastruktur > Pembangunan Jalan > Pengaspalan Jalan Desa A").

Buat juga **Form Request validation** baru: `StoreProgramRequest`, `StoreKegiatanRequest`, `StoreSubKegiatanRequest`, `StoreSubJenisSpjRequest`, `StoreTahunArsipRequest` (masing-masing dengan aturan unique & required sesuai tabel di atas).

---

## 4. KOMPONEN DRAWER (Cascading Picker) DI HALAMAN TAMBAH/EDIT ARSIP

Ganti field "Program", "Kegiatan", "Sub Kegiatan" yang sebelumnya berupa input teks bebas menjadi **SATU field readonly** bertuliskan placeholder **"Pilih Program / Kegiatan / Sub Kegiatan"** (menampilkan `full_path` jika sudah dipilih), dengan tombol **"Pilih"** di sampingnya. Saat tombol diklik, buka **drawer/panel geser dari kanan** (gunakan Alpine.js + Tailwind transition, atau library seperti Alpine `x-transition`, tanpa perlu framework JS berat) yang berisi:

1. **Step 1 — Pilih Tahun Penetapan**: dropdown/list tahun-tahun yang ada di tabel `programs` (distinct `tahun_penetapan`).
2. **Step 2 — Pilih Program**: setelah tahun dipilih, tampilkan daftar Program dengan `tahun_penetapan` tersebut (radio list / clickable card).
3. **Step 3 — Pilih Kegiatan**: setelah program dipilih, tampilkan daftar Kegiatan milik program tersebut.
4. **Step 4 — Pilih Sub Kegiatan**: setelah kegiatan dipilih, tampilkan daftar Sub Kegiatan milik kegiatan tersebut. Klik salah satu Sub Kegiatan = pilihan final.
5. Setiap step tampilkan **breadcrumb** di atas drawer (mis. `2026 > Program A > Kegiatan B`) dan tombol **"Kembali"** untuk mundur ke step sebelumnya.
6. Setelah Sub Kegiatan dipilih, drawer tertutup otomatis, dan field di form utama terisi dengan `full_path` + `sub_kegiatan_id` tersimpan di hidden input.
7. Muat data step 2–4 via **Ajax/fetch ke endpoint API internal** (bukan reload halaman penuh), contoh endpoint:
   - `GET /api/master/programs?tahun=2026`
   - `GET /api/master/kegiatans?program_id=1`
   - `GET /api/master/sub-kegiatans?kegiatan_id=3`

Tambahkan juga **field select biasa (dropdown searchable)** untuk **Sub Jenis SPJ** dan **Tahun Arsip** di form arsip (tidak perlu drawer khusus, cukup `<select>` yang datanya diambil dari tabel `sub_jenis_spjs` dan `tahun_arsips`).

Validasi backend: `sub_kegiatan_id` wajib diisi dan harus `exists:sub_kegiatans,id` (begitu juga `sub_jenis_spj_id` dan `tahun_arsip_id` sesuai kebutuhan — tentukan mana yang wajib/opsional, sebutkan asumsimu).

---

## 5. HALAMAN MASTER DATA (khusus Admin)

Buat menu baru **"Master Data"** di sidebar (hanya tampil untuk role Admin, di bawah/dekat menu "Kelola User"), dengan route prefix `/master-data`, dilindungi middleware `role:admin`. Tampilkan sebagai **3 tab / 3 card** dalam satu halaman (`master-data/index.blade.php`), atau 3 sub-halaman terpisah dengan tab navigasi — pilih salah satu, sebutkan alasannya.

### Poin 1 — Hierarki Program (`/master-data/hierarki-program`)
Tampilkan sebagai **struktur pohon (tree view) yang bisa expand/collapse**: Program → Kegiatan → Sub Kegiatan.
- Di bagian atas, sediakan **dropdown filter "Pilih Tahun Penetapan"** untuk menyaring daftar program yang ditampilkan berdasarkan tahun.
- Tombol **"+ Tambah Program Baru"** → buka modal berisi form: `nama_program`, `tahun_penetapan` (dropdown tahun atau input angka 4 digit). Simpan → tampil di tree.
- Di setiap baris Program, ada tombol **"+ Tambah Kegiatan"** → buka modal form `nama_kegiatan` (program sudah otomatis terpilih sesuai baris yang diklik). Simpan → kegiatan baru muncul sebagai child di tree.
- Di setiap baris Kegiatan, ada tombol **"+ Tambah Sub Kegiatan"** → buka modal form `nama_sub_kegiatan` (kegiatan sudah otomatis terpilih). Simpan → sub kegiatan baru muncul sebagai child paling bawah di tree.
- Sediakan juga aksi **Edit** dan **Hapus** pada masing-masing level (Program/Kegiatan/Sub Kegiatan), dengan validasi: **tidak bisa hapus Program/Kegiatan/Sub Kegiatan jika masih dipakai oleh data arsip** (tampilkan pesan error yang jelas, arahkan admin untuk memindahkan/menghapus arsip terkait dulu).

### Poin 2 — Sub Jenis SPJ (`/master-data/sub-jenis-spj`)
Halaman CRUD sederhana (list + tombol tambah):
- Tabel daftar Sub Jenis SPJ (kolom: Nama Sub Jenis SPJ, Jumlah Arsip Terkait, Aksi).
- Tombol **"+ Tambah Sub Jenis SPJ Baru"** → modal form `nama_sub_jenis_spj` (validasi unique).
- Aksi Edit & Hapus per baris (cegah hapus jika masih dipakai arsip, sama seperti poin 1).

### Poin 3 — Tahun Arsip (`/master-data/tahun-arsip`)
Halaman CRUD sederhana (list + tombol tambah):
- Tabel daftar Tahun Arsip (kolom: Tahun, Jumlah Arsip Terkait, Aksi).
- Tombol **"+ Tambah Tahun Arsip Baru"** → modal form `tahun` (input angka 4 digit, validasi unique & masuk akal, mis. antara 2000–2100).
- Aksi Edit & Hapus per baris (cegah hapus jika masih dipakai arsip).

Semua aksi tambah/edit/hapus di halaman Master Data menampilkan **notifikasi sukses/gagal** (flash message), dan **tidak reload halaman penuh** jika memungkinkan (gunakan Ajax/Alpine agar tree & tabel langsung ter-update).

---

## 6. STRUKTUR ROUTE TAMBAHAN

```php
Route::middleware(['auth', 'role:admin'])->prefix('master-data')->name('master-data.')->group(function () {
    Route::get('/hierarki-program', [ProgramController::class, 'index'])->name('program.index');
    Route::post('/program', [ProgramController::class, 'store'])->name('program.store');
    Route::put('/program/{program}', [ProgramController::class, 'update'])->name('program.update');
    Route::delete('/program/{program}', [ProgramController::class, 'destroy'])->name('program.destroy');

    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

    Route::post('/sub-kegiatan', [SubKegiatanController::class, 'store'])->name('subkegiatan.store');
    Route::put('/sub-kegiatan/{subKegiatan}', [SubKegiatanController::class, 'update'])->name('subkegiatan.update');
    Route::delete('/sub-kegiatan/{subKegiatan}', [SubKegiatanController::class, 'destroy'])->name('subkegiatan.destroy');

    Route::resource('sub-jenis-spj', SubJenisSpjController::class)->except(['show','create','edit']);
    Route::resource('tahun-arsip', TahunArsipController::class)->except(['show','create','edit']);
});

// Endpoint internal untuk drawer picker di form arsip (bisa dipakai semua role yang boleh input arsip: admin & operator)
Route::middleware(['auth', 'role:admin,operator'])->prefix('api/master')->group(function () {
    Route::get('/tahun-program', [ProgramController::class, 'daftarTahun']);
    Route::get('/programs', [ProgramController::class, 'byTahun']);       // ?tahun=2026
    Route::get('/kegiatans', [KegiatanController::class, 'byProgram']);   // ?program_id=1
    Route::get('/sub-kegiatans', [SubKegiatanController::class, 'byKegiatan']); // ?kegiatan_id=3
});
```

---

## 7. SEEDER TAMBAHAN

Buat `MasterDataSeeder` berisi data contoh realistis:
- 2 tahun penetapan program (mis. 2025, 2026), masing-masing 2 program.
- Tiap program punya 2 kegiatan.
- Tiap kegiatan punya 2–3 sub kegiatan.
- 4–5 sub jenis SPJ contoh (mis. "SPJ Belanja Langsung", "SPJ Belanja Tidak Langsung", "SPJ Perjalanan Dinas", "SPJ Pengadaan Barang/Jasa").
- Tahun arsip: 2023–2026.

Daftarkan seeder ini di `DatabaseSeeder.php` sebelum `ArsipSeeder` dijalankan (karena arsip butuh referensi ke data master ini).

---

## 8. URUTAN PENGERJAAN

1. Buat migration + model + relasi untuk `programs`, `kegiatans`, `sub_kegiatans`, `sub_jenis_spjs`, `tahun_arsips`, lalu tambahkan kolom FK baru ke tabel `arsip`.
2. Buat `MasterDataSeeder` dan jalankan `php artisan migrate:fresh --seed` untuk memastikan tidak ada error relasi.
3. Buat Controller + route untuk Master Data (Poin 1, 2, 3) sesuai struktur di atas, lengkap dengan validasi cegah-hapus-jika-terpakai.
4. Buat halaman Master Data (tree view Hierarki Program, tabel Sub Jenis SPJ, tabel Tahun Arsip) dengan modal tambah/edit dan konfirmasi hapus.
5. Ubah form Tambah/Edit Arsip: hapus input teks bebas Program/Kegiatan/Sub Kegiatan lama, ganti dengan field readonly + tombol "Pilih" yang membuka drawer cascading picker, serta tambahkan dropdown Sub Jenis SPJ dan Tahun Arsip.
6. Buat endpoint API internal untuk drawer (`/api/master/...`) dan hubungkan dengan Alpine.js/fetch di sisi frontend.
7. Uji end-to-end: tambah data master baru dari halaman Master Data → langsung muncul sebagai opsi di drawer picker saat menambah arsip baru → arsip tersimpan dengan `sub_kegiatan_id`, `sub_jenis_spj_id`, `tahun_arsip_id` yang benar → tampil dengan benar di tabel Data Arsip dan Dashboard.

Jika ada bagian yang kurang jelas, buat asumsi paling logis untuk sistem pengarsipan dokumen anggaran/kegiatan instansi pemerintah, dan sebutkan asumsi tersebut secara singkat di README.
