# DOKUMENTASI PROYEK TRAVELFLOW

## 📋 RINGKASAN EKSEKUTIF

**TravelFlow** adalah platform pemesanan wisata online yang dirancang khusus untuk mempermudah pengalaman perjalanan wisatawan di Yogyakarta. Platform ini menghubungkan wisatawan dengan berbagai paket wisata, kendaraan transportasi, dan layanan terkait dalam satu ekosistem yang terintegrasi.

### Informasi Proyek
- **Nama Aplikasi**: TravelFlow
- **Tagline**: "Pesan Wisata Jogja"
- **Tujuan**: Menyediakan platform e-commerce wisata yang mudah, aman, dan terpercaya
- **Target Pengguna**: Wisatawan lokal dan internasional yang ingin berkunjung ke Yogyakarta

---

## 🎯 FITUR UTAMA

### 1. **Sistem Autentikasi & Akun Pengguna**
- **Login & Register**: Pengguna dapat membuat akun baru atau masuk dengan email dan password
- **Profil Pengguna**: Menampilkan informasi pribadi (nama, email, nomor telepon, alamat)
- **Session Management**: Sistem session untuk memastikan keamanan pengguna yang login

### 2. **Eksplorasi Wisata**
- **Daftar Wisata**: Menampilkan semua destinasi wisata yang tersedia
- **Filter Kategori**: Pengguna dapat memfilter wisata berdasarkan kategori (contoh: Adventure, Kuliner, Budaya, dll)
- **Detail Wisata Lengkap**: Setiap wisata menampilkan:
  - Nama dan lokasi
  - Deskripsi detail
  - Gambar/foto wisata
  - Harga mulai dari
  - Rating & ulasan dari pengunjung lain
  - Paket-paket yang tersedia

### 3. **Sistem Pemesanan (Booking)**
- **Pilih Paket**: Setiap wisata memiliki berbagai paket (contoh: Sunrise Tour, Sunset Tour, Jeep Adventure, dll)
- **Pilih Transportasi**: Berbagai pilihan kendaraan dengan kapasitas berbeda:
  - Motor (2 orang)
  - Mobil Pribadi (4 orang)
  - Minibus (10 orang)
  - Bus Medium (20 orang)
  - Bus Besar (40 orang)
- **Tentukan Jumlah Peserta**: Pengguna dapat memilih berapa banyak orang yang akan ikut
- **Pilih Tanggal**: Memilih tanggal keberangkatan untuk wisata
- **Perhitungan Harga Otomatis**: Total = (Harga Paket × Jumlah Orang) + Harga Transportasi

### 4. **Keranjang Belanja (Shopping Cart)**
- **Tambah Pemesanan**: Pengguna dapat menambahkan beberapa paket wisata berbeda ke keranjang
- **Kelola Keranjang**: Melihat daftar semua pemesanan yang sudah ditambahkan
- **Hapus Item**: Menghapus paket tertentu dari keranjang
- **Kosongkan Semua**: Mengosongkan seluruh isi keranjang
- **Pilih Hotel**: Opsi untuk menambahkan akomodasi hotel ke pemesanan

### 5. **Proses Pembayaran**
- **Form Pembayaran**: Mengisi data pemesan (nama, nomor telepon)
- **Review Total**: Menampilkan rincian lengkap pembayaran sebelum checkout
- **Generate Kode Booking**: Setiap transaksi mendapat kode unik untuk referensi
- **Status Transaksi**: Transaksi dapat berstatus pending, confirmed, atau cancelled

### 6. **Riwayat Transaksi & Struk**
- **Daftar Pemesanan**: Melihat semua riwayat pemesanan yang sudah dilakukan
- **Detail Transaksi**: Informasi lengkap setiap pemesanan (tanggal, paket, harga, status)
- **Struk Pembayaran**: Cetak atau lihat struk pembayaran untuk setiap transaksi
- **Total Pengeluaran**: Statistik total yang sudah dihabiskan pengguna

### 7. **Sistem Rating & Ulasan**
- **Beri Rating**: Pengguna dapat memberikan rating bintang (1-5) untuk wisata yang sudah dikunjungi
- **Tulis Ulasan**: Menambahkan komentar tertulis tentang pengalaman wisata
- **Lihat Ulasan Orang Lain**: Membaca pengalaman dan ulasan dari pengguna lain
- **Tampilan Rating**: Menampilkan rata-rata rating dan jumlah ulasan untuk setiap wisata

### 8. **Dashboard Pengguna**
- **Greeting Dinamis**: Sapaan yang berubah sesuai waktu (Pagi/Siang/Sore/Malam)
- **Statistik Ringkas**:
  - Total jumlah pemesanan
  - Total pengeluaran
  - Pemesanan terbaru (3 transaksi terakhir)
- **Notifikasi**: Sistem notifikasi untuk informasi penting

### 9. **Sistem Notifikasi**
- **Notifikasi Real-time**: Informasi tentang status pemesanan
- **Mark as Read**: Menandai notifikasi sebagai sudah dibaca
- **Notification Bell**: Menampilkan jumlah notifikasi yang belum dibaca

### 10. **Profil Pengguna**
- **Edit Profil**: Mengubah informasi pribadi (nama, nomor telepon, alamat)
- **Foto Profil**: Upload foto profil pengguna
- **Ubah Password**: Mengubah password akun untuk keamanan

---

## 🗄️ STRUKTUR DATABASE

### Tabel Users (Pengguna)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_user | INT | Primary Key |
| nama | VARCHAR(100) | Nama lengkap pengguna |
| email | VARCHAR(100) | Email (unik) |
| password | VARCHAR(255) | Password terenkripsi |
| no_telp | VARCHAR(30) | Nomor telepon |
| alamat | TEXT | Alamat pengguna |
| foto | VARCHAR(255) | Path foto profil |
| created_at | TIMESTAMP | Waktu daftar |

### Tabel Wisata (Destinasi Wisata)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_wisata | INT | Primary Key |
| nama_wisata | VARCHAR(150) | Nama destinasi |
| lokasi | VARCHAR(150) | Lokasi destinasi |
| kategori | VARCHAR(50) | Kategori wisata |
| deskripsi | TEXT | Deskripsi lengkap |
| gambar | VARCHAR(255) | Foto wisata |
| harga | DECIMAL(10,2) | Harga dasar |

### Tabel Paket (Paket Wisata)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_paket | INT | Primary Key |
| id_wisata | INT | Foreign Key ke Wisata |
| nama_paket | VARCHAR(100) | Nama paket (Sunrise, Sunset, dll) |
| durasi | VARCHAR(50) | Durasi paket (cth: 4 jam, Full Day) |
| harga | DECIMAL(10,2) | Harga per orang |
| deskripsi | TEXT | Detail paket |

### Tabel Kendaraan (Transportasi)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_kendaraan | INT | Primary Key |
| nama_kendaraan | VARCHAR(100) | Nama kendaraan |
| kapasitas | INT | Jumlah kapasitas orang |
| harga | DECIMAL(10,2) | Harga sewa kendaraan |

### Tabel Transaksi (Pemesanan)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_transaksi | INT | Primary Key |
| id_user | INT | Foreign Key ke Users |
| id_wisata | INT | Foreign Key ke Wisata |
| id_paket | INT | Foreign Key ke Paket |
| id_kendaraan | INT | Foreign Key ke Kendaraan |
| jumlah_orang | INT | Jumlah peserta |
| tanggal | DATE | Tanggal keberangkatan |
| nama_pemesan | VARCHAR(150) | Nama pemesan |
| no_telp | VARCHAR(30) | Nomor telepon pemesan |
| kode_booking | VARCHAR(50) | Kode referensi booking |
| total | DECIMAL(12,2) | Total biaya |
| status | ENUM | Status: pending/confirmed/cancelled |
| created_at | TIMESTAMP | Waktu pemesanan |

### Tabel Notifikasi
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_notif | INT | Primary Key |
| id_user | INT | Foreign Key ke Users |
| judul | VARCHAR(150) | Judul notifikasi |
| pesan | TEXT | Isi pesan |
| icon | VARCHAR(50) | Icon notifikasi |
| is_read | TINYINT(1) | Status dibaca |
| created_at | TIMESTAMP | Waktu notifikasi |

### Tabel Ulasan (Rating & Review)
| Field | Tipe | Keterangan |
|-------|------|-----------|
| id_ulasan | INT | Primary Key |
| id_user | INT | Foreign Key ke Users |
| id_wisata | INT | Foreign Key ke Wisata |
| id_transaksi | INT | Foreign Key ke Transaksi |
| rating | TINYINT(1) | Rating (1-5 bintang) |
| komentar | TEXT | Komentar ulasan |
| created_at | TIMESTAMP | Waktu ulasan |

---

## 🔄 USER FLOW (Alur Penggunaan)

### 1. **Pengunjung Baru → Registrasi & Login**
```
Pengunjung Website 
→ Klik "Daftar"
→ Isi Email & Password
→ Akun Terbuat
→ Klik "Login"
→ Masuk ke Dashboard
```

### 2. **Eksplorasi & Pilihan Destinasi**
```
Dashboard
→ Klik "Daftar Wisata"
→ Lihat daftar destinasi wisata
→ Pilih destinasi yang ingin dikunjungi
→ Lihat detail wisata, paket, dan fasilitas
```

### 3. **Pembuatan Perjalanan / Keranjang Perjalanan**
```
Setelah memilih destinasi
→ Tentukan tanggal mulai perjalanan
→ Tentukan durasi perjalanan (jumlah hari)
→ Sistem menyusun itinerary otomatis
→ Setiap hari maksimal 2 destinasi wisata
→ Jika destinasi melebihi kapasitas, sistem menampilkan notifikasi
```

### 4. **Penentuan Hotel (Opsional)**
```
Setelah itinerary terbentuk
→ Pengguna dapat memilih hotel sebagai penginapan
→ Jika tidak memilih hotel, sistem tetap melanjutkan pemesanan
→ Lama menginap mengikuti durasi perjalanan
→ Biaya hotel dihitung terpisah dari tiket wisata
```

### 5. **Penentuan Titik Penjemputan**
```
Sebelum pembayaran
→ Pilih titik penjemputan
→ Opsi titik penjemputan mencakup hotel, bandara, stasiun, terminal, atau alamat pengguna
→ Titik penjemputan menjadi lokasi awal keberangkatan menuju destinasi
```

### 6. **Checkout & Pembayaran**
```
Ringkasan perjalanan
→ Tampilkan itinerary per hari
→ Tampilkan tanggal keberangkatan, durasi, hotel, titik penjemputan
→ Tampilkan total tiket wisata, total hotel, biaya transportasi, dan grand total
→ Klik "Lanjutkan Pembayaran"
→ Isi data pemesan
→ Konfirmasi pembayaran
→ Transaksi berhasil dan generate struk
```

### 7. **Riwayat & Manajemen Pemesanan**
```
Dashboard
→ Klik "Riwayat Transaksi"
→ Lihat semua pemesanan
→ Klik detail untuk melihat itinerary dan status pembayaran
→ Lihat struk
→ (Optional) Tulis ulasan
```

---

## 💻 TEKNOLOGI YANG DIGUNAKAN

### Backend
- **Language**: PHP 7.x / 8.x
- **Database**: MySQL/MariaDB
- **Server**: Apache (XAMPP)
- **Session Management**: PHP Native Sessions

### Frontend
- **HTML5**: Struktur halaman
- **CSS3**: Styling responsif
- **JavaScript**: Interaktivitas frontend
- **Bootstrap 5**: Framework CSS untuk responsive design
- **Google Fonts (Plus Jakarta Sans)**: Typography

### Database
- **MySQL/MariaDB**: Relational Database Management System
- **Foreign Keys**: Untuk relasi antar tabel
- **Timestamps**: Untuk tracking waktu

### Infrastructure
- **Development Server**: XAMPP (Apache, MySQL, PHP)
- **Version Control**: Git (optional)

---

## 🏗️ ARSITEKTUR SISTEM

### Model View Controller (MVC) Pattern
```
├── Index.php (Landing Page)
├── Auth/ (Controller Authentication)
│   ├── login.php (View)
│   ├── register.php (View)
│   ├── proses_login.php (Controller)
│   └── logout.php (Controller)
├── User/ (View Pengguna)
│   ├── dashboard.php
│   ├── wisata.php
│   ├── detail.php
│   ├── hotel.php
│   ├── keranjang.php
│   ├── pembayaran.php
│   ├── struk.php
│   ├── profil.php
│   ├── riwayat.php
│   └── ulasan.php
├── Config/ (Konfigurasi & Fungsi)
│   ├── koneksi.php (Database Connection)
│   └── notif.php (Notification Functions)
└── Assets/ (Resources)
    ├── CSS/
    ├── JS/
    └── IMG/
```

### Alur Data
```
User Input (Frontend)
    ↓
HTTP Request
    ↓
PHP Controller (Process)
    ↓
Database Query
    ↓
MySQL Database
    ↓
Result (Response)
    ↓
HTML/JSON Output
    ↓
Browser Render
    ↓
User Sees Result
```

---

## 📊 FITUR BISNIS

### 1. **Revenue Model**
- **Booking Commission**: Setiap pemesanan menghasilkan revenue
- **Transaction Fee**: Biaya transaksi dari setiap booking
- **Partnership Opportunities**: Kerjasama dengan guide, hotel, restaurant

### 2. **Competitive Advantage**
- Platform terpadu: Wisata + Transportasi dalam satu tempat
- User-friendly interface: Mudah digunakan untuk semua usia
- Sistem rating & review: Transparansi dan kepercayaan
- Manajemen profil lengkap: Tracking riwayat perjalanan

### 3. **Potensi Pengembangan**
- Payment Gateway Integration: Pembayaran online via bank/e-wallet
- Rekomendasi Wisata: AI-powered recommendations
- Social Features: Share pengalaman dengan teman
- Mobile App: Native Android/iOS application
- Multi-language: Internasionalisasi untuk wisatawan asing
- Admin Dashboard: Panel untuk manajemen wisata & transaksi
- Chat Support: Live chat dengan customer service

---

## 🔐 FITUR KEAMANAN

### Implementasi Saat Ini
- **Session-based Authentication**: Pengguna harus login untuk akses fitur
- **Session Validation**: Setiap halaman protected memvalidasi session
- **Password Storage**: Password disimpan dengan hashing
- **Input Sanitization**: Menggunakan `mysqli_real_escape_string()`

### Rekomendasi Keamanan Tambahan
- **Password Hashing**: Implementasi bcrypt atau argon2
- **HTTPS**: Enkripsi komunikasi client-server
- **CSRF Protection**: Token untuk mencegah cross-site request forgery
- **SQL Injection Prevention**: Prepared statements
- **Rate Limiting**: Batasi login attempts
- **2FA (Two-Factor Authentication)**: Verifikasi tambahan

---

## 📱 RESPONSIVE DESIGN

Aplikasi dirancang dengan:
- **Mobile-First Approach**: Responsif untuk semua ukuran layar
- **Bootstrap 5 Grid System**: Untuk layout yang fleksibel
- **Viewport Meta Tag**: Optimasi untuk mobile devices
- **Flexible Images**: Gambar yang scale sesuai ukuran layar

### Target Devices
- 📱 Smartphone (320px - 768px)
- 📱 Tablet (768px - 1024px)
- 💻 Desktop (1024px - ke atas)

---

## 🚀 CARA MENJALANKAN APLIKASI

### Persyaratan
- XAMPP terinstall
- MySQL running
- PHP 7.4 atau lebih tinggi

### Setup Steps
1. **Buka phpMyAdmin**
   ```
   http://localhost/phpmyadmin
   ```

2. **Import Database**
   - Klik "New"
   - Upload file `data/travelflow.sql`
   - Database `travelflow` akan terbuat otomatis

3. **Akses Aplikasi**
   ```
   http://localhost/travelflow
   ```

4. **Testing**
   - Daftar akun baru
   - Explore wisata
   - Buat pemesanan
   - Checkout
   - Lihat riwayat transaksi

---

## 👥 USER PERSONAS

### 1. **Wisatawan Lokal**
- Usia: 25-45 tahun
- Tujuan: Liburan akhir pekan atau cuti
- Kebutuhan: Kemudahan dalam booking dan harga kompetitif
- Perilaku: Cek review sebelum pesan

### 2. **Wisatawan Internasional**
- Usia: 20-60 tahun
- Tujuan: Backpacking atau tour grup
- Kebutuhan: Informasi jelas dan guide berkualitas
- Perilaku: Preferensi paket adventure dan kuliner

### 3. **Travel Agent**
- Peran: Reseller atau partner
- Kebutuhan: Fitur bulk booking dan harga khusus
- Perilaku: Butuh dashboard untuk manage klien

---

## 📈 METRICS & KPI

### Business Metrics
- Total Bookings per Bulan
- Average Order Value (AOV)
- Customer Lifetime Value (CLV)
- Conversion Rate
- Customer Satisfaction Score

### Technical Metrics
- Page Load Time
- Server Uptime
- Error Rate
- Database Query Performance
- User Session Duration

---

## 🔧 MAINTENANCE & SUPPORT

### Regular Maintenance
- Database optimization setiap bulan
- Security updates untuk dependencies
- Backup data setiap hari
- Monitor server performance

### Support Channels
- Email support untuk customer
- Chat support untuk quick response
- FAQ section untuk self-service
- Social media untuk community

---

## 📝 KESIMPULAN

**TravelFlow** adalah solusi e-commerce wisata yang komprehensif untuk market Yogyakarta. Dengan fitur-fitur lengkap, database terstruktur, dan user interface yang intuitif, aplikasi ini siap memberikan pengalaman booking wisata yang seamless kepada pengguna.

### Kekuatan Platform
✅ Integrasi wisata + transportasi dalam satu platform  
✅ Sistem booking yang mudah dan cepat  
✅ Rating & ulasan untuk transparansi  
✅ Dashboard analytics untuk pengguna  
✅ Mobile-responsive design  
✅ Database terstruktur dengan relationships yang baik  

### Peluang Pengembangan
🚀 Payment gateway integration  
🚀 Mobile application  
🚀 Admin dashboard untuk management  
🚀 AI recommendations  
🚀 Multi-language support  
🚀 Advanced analytics & reporting  

---

**Dokumen ini dapat dijadikan slide presentasi dengan menambahkan visual, screenshot aplikasi, dan diagram yang lebih detail.**
