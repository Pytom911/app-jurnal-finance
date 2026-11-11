# PROMPT PEMBUATAN BACKEND PERSONAL FINANCE APP

Bertindaklah sebagai Senior Laravel Backend Developer. Bantu saya membangun backend aplikasi Personal Finance Management menggunakan Laravel dengan struktur kode yang rapi, aman, konsisten, dan mudah dikembangkan.

Jangan membuat fitur di luar spesifikasi. Jangan mengubah aturan bisnis yang sudah ditentukan.

## 1. TECH STACK

- Framework: Laravel 13
- PHP: 8.4
- Database: MySQL
- ORM: Eloquent
- Authentication: Laravel Authentication
- Architecture: MVC
- Database: jurnal_financial

Gunakan standar Laravel terbaru yang kompatibel dengan Laravel 13.

## 2. DATABASE TABLES

Buat 5 tabel utama:

1. users
2. akun_keuangan
3. kategori_transaksi
4. transaksi
5. tantangan

### A. Migration users

Gunakan tabel users bawaan Laravel.

Kolom:
- id
- name
- email
- password
- email_verified_at (jika diperlukan)
- timestamps

### B. Migration akun_keuangan

Kolom:
- id
- user_id (foreign key ke users)
- nama (VARCHAR 100)
- jenis (ENUM: cair, tabungan)
- timestamps

Aturan:
- Setiap user memiliki satu akun Saldo Cair.
- Setiap user memiliki satu akun Saldo Tabungan.
- Kombinasi user_id dan jenis harus unik.
- Saldo tidak disimpan sebagai angka tetap.
- Saldo dihitung dari riwayat transaksi.

### C. Migration kategori_transaksi

Kolom:
- id
- user_id (foreign key ke users)
- nama (VARCHAR 100)
- jenis (ENUM: pemasukan, pengeluaran)
- timestamps

Aturan:
- User dapat membuat kategori sendiri.
- Kategori pemasukan dan pengeluaran harus dipisahkan.
- Kategori hanya dapat digunakan oleh pemiliknya.

### D. Migration transaksi

Kolom:
- id
- user_id (foreign key ke users)
- kategori_id (nullable, foreign key)
- tantangan_id (nullable, foreign key)
- jenis (ENUM: pemasukan, pengeluaran, transfer, saldo_awal)
- sumber_pemasukan (nullable ENUM: uang_saku, penghasilan_sendiri)
- akun_asal_id (nullable, foreign key ke akun_keuangan)
- akun_tujuan_id (nullable, foreign key ke akun_keuangan)
- nominal (DECIMAL 15,2)
- tanggal (DATE)
- keterangan (nullable TEXT)
- timestamps
- deleted_at (soft deletes)

Aturan:
- Semua pemasukan masuk ke Saldo Cair.
- Pengeluaran hanya boleh menggunakan Saldo Cair.
- Saldo Tabungan tidak boleh digunakan untuk pengeluaran.
- Transfer dapat dilakukan antara Saldo Cair dan Saldo Tabungan.
- Transfer tidak dihitung sebagai pemasukan.
- Saldo awal dicatat menggunakan jenis saldo_awal.
- Penghasilan sendiri dapat dikaitkan dengan tantangan aktif.
- Uang saku tidak menambah progress tantangan.
- Pengeluaran tidak mengurangi progress tantangan.
- Transaksi yang dihapus tidak dihitung dalam saldo maupun progress.

### E. Migration tantangan

Kolom:
- id
- user_id (foreign key ke users)
- nama (VARCHAR 150)
- target (DECIMAL 15,2)
- tanggal_mulai (DATE)
- tanggal_tercapai (nullable DATE)
- tanggal_selesai (nullable DATE)
- status (ENUM: aktif, selesai, perlu_penyesuaian)
- timestamps

Aturan:
- Setiap user hanya boleh memiliki satu tantangan berstatus aktif.
- Progress tidak disimpan secara permanen.
- Progress dihitung berdasarkan total pemasukan sendiri yang terhubung ke tantangan.
- Tantangan otomatis selesai ketika progress mencapai target.
- Tantangan selesai masuk ke riwayat.
- Tantangan baru dapat dibuat setelah tidak ada tantangan aktif.
- Jika transaksi lama diedit atau dihapus sehingga progress tantangan selesai turun di bawah target, status menjadi perlu_penyesuaian.
- Tantangan yang berstatus perlu_penyesuaian tetap masuk riwayat dan tidak otomatis aktif kembali.
- Tantangan baru tetap boleh dibuat.

## 3. MODEL ELOQUENT

Buat model:

1. User
2. AkunKeuangan
3. KategoriTransaksi
4. Transaksi
5. Tantangan

Implementasikan seluruh relasi Eloquent dengan benar.

Relasi yang dibutuhkan:

User:
- hasMany AkunKeuangan
- hasMany KategoriTransaksi
- hasMany Transaksi
- hasMany Tantangan

AkunKeuangan:
- belongsTo User
- hasMany Transaksi sebagai akun asal
- hasMany Transaksi sebagai akun tujuan

KategoriTransaksi:
- belongsTo User
- hasMany Transaksi

Transaksi:
- belongsTo User
- belongsTo KategoriTransaksi
- belongsTo AkunKeuangan sebagai akun asal
- belongsTo AkunKeuangan sebagai akun tujuan
- belongsTo Tantangan

Tantangan:
- belongsTo User
- hasMany Transaksi

Gunakan nama relasi yang jelas dan konsisten.

Tambahkan casting untuk nominal, tanggal, dan atribut yang sesuai.

## 4. CONTROLLER

Buat controller berikut:

### A. AkunKeuanganController

Fungsi:
- index()
- show()
- saldo()

Ketentuan:
- Menampilkan akun keuangan milik user.
- Menampilkan Saldo Cair.
- Menampilkan Saldo Tabungan.
- Menampilkan Total Saldo.
- Saldo dihitung berdasarkan transaksi aktif yang belum dihapus.
- User tidak boleh mengakses akun milik user lain.

### B. KategoriTransaksiController

Fungsi:
- index()
- store()
- update()
- destroy()

Ketentuan:
- CRUD kategori.
- Validasi jenis kategori.
- Kategori tidak boleh digunakan oleh user lain.
- Cegah penghapusan kategori yang masih digunakan transaksi.

### C. TransaksiController

Fungsi:
- index()
- store()
- show()
- update()
- destroy()

Ketentuan:
- Mendukung pemasukan uang saku.
- Mendukung pemasukan penghasilan sendiri.
- Mendukung pengeluaran.
- Mendukung transfer antar saldo.
- Mendukung pencatatan saldo awal.
- Mendukung pencarian transaksi.
- Mendukung filter berdasarkan tanggal, jenis, dan kategori.
- Mendukung pagination.

Setiap operasi transaksi harus menggunakan DB::transaction().

Ketika transaksi dibuat, diedit, atau dihapus:
- Validasi kepemilikan user.
- Validasi jenis transaksi.
- Validasi akun asal dan tujuan.
- Validasi saldo mencukupi.
- Validasi aturan penggunaan saldo.
- Perbarui progress dan status tantangan jika transaksi berkaitan dengan penghasilan sendiri.

Jangan izinkan saldo cair menjadi negatif.

Jangan izinkan pengeluaran mengambil uang dari Saldo Tabungan.

### D. TantanganController

Fungsi:
- index()
- store()
- show()
- update()
- destroy()
- riwayat()

Ketentuan:
- User dapat membuat tantangan.
- Hanya satu tantangan aktif.
- User dapat menentukan nama dan target.
- Progress dihitung dari transaksi penghasilan sendiri.
- Tampilkan nominal progress.
- Tampilkan persentase progress.
- Tampilkan sisa target.
- Tampilkan tanggal mulai.
- Tampilkan tanggal tercapai.
- Tampilkan durasi penyelesaian.
- Tampilkan riwayat tantangan.
- Tantangan yang sudah selesai tidak boleh diedit sembarangan.
- Tantangan berstatus perlu_penyesuaian harus tetap mempertahankan informasi riwayat.

## 5. BUSINESS LOGIC

### A. Perhitungan Saldo Cair

Saldo Cair =
Saldo Awal Cair
+ Total Pemasukan
- Total Pengeluaran
- Total Transfer Keluar
+ Total Transfer Masuk

### B. Perhitungan Saldo Tabungan

Saldo Tabungan =
Saldo Awal Tabungan
+ Total Transfer Masuk
- Total Transfer Keluar

### C. Total Saldo

Total Saldo = Saldo Cair + Saldo Tabungan.

Transfer antar akun tidak boleh mengubah Total Saldo.

### D. Perhitungan Challenge

Progress =
Total nominal transaksi dengan kondisi:
- jenis = pemasukan
- sumber_pemasukan = penghasilan_sendiri
- tantangan_id sesuai
- deleted_at IS NULL

Persentase =
Progress / Target × 100.

Sisa Target =
Target - Progress.

Jangan menghitung pengeluaran dan uang saku sebagai progress.

### E. Otomatisasi Challenge

Ketika progress mencapai atau melebihi target:
- Ubah status menjadi selesai.
- Isi tanggal_tercapai.
- Isi tanggal_selesai.

Jika transaksi terkait diedit atau dihapus:
- Hitung ulang progress.
- Jika tantangan aktif masih mencapai target, pertahankan atau selesaikan sesuai kondisi.
- Jika tantangan selesai mengalami penurunan progress di bawah target, ubah status menjadi perlu_penyesuaian.
- Jangan menghapus riwayat penyelesaian sebelumnya.
- Jangan mengaktifkan ulang tantangan secara otomatis.

## 6. KEAMANAN DAN VALIDASI

Terapkan:
- Validasi menggunakan Form Request jika diperlukan.
- Authorization untuk setiap data milik user.
- Mass assignment protection melalui fillable atau guarded.
- Database transaction untuk operasi yang melibatkan banyak perubahan.
- Foreign key constraint.
- Validasi nominal harus lebih besar dari nol.
- Validasi tanggal.
- Validasi saldo tidak boleh negatif.
- Hindari IDOR.
- Hindari manipulasi user_id dari request.
- Jangan mempercayai nominal atau progress dari frontend.

Gunakan authenticated user sebagai sumber user_id.

## 7. STRUKTUR KODE

Gunakan struktur MVC Laravel yang rapi.

Pisahkan business logic yang kompleks ke Service Class.

Buat:
- TransactionService
- BalanceService
- ChallengeService

Controller hanya menangani request, validasi, pemanggilan service, dan response.

Hindari menumpuk semua logika di controller.

## 8. OUTPUT YANG SAYA INGINKAN

Kerjakan secara bertahap:

Tahap 1:
- Migration seluruh tabel.
- Foreign key dan index.
- Unique constraint.

Tahap 2:
- Seluruh Model.
- Relasi Eloquent.
- Casting dan fillable.

Tahap 3:
- Service Class.
- BalanceService.
- TransactionService.
- ChallengeService.

Tahap 4:
- Controller.
- Form Request.
- Authorization.

Tahap 5:
- Routes web.php atau api.php sesuai kebutuhan aplikasi.

Tahap 6:
- Seeder akun Saldo Cair dan Saldo Tabungan untuk setiap user baru.

Tahap 7:
- Feature tests untuk seluruh aturan bisnis.

Mulai dari Tahap 1, yaitu membuat seluruh Migration database.
