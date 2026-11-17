# NASKAH PRESENTASI UJIKOM — BAGI KOPI

**Aplikasi web digitalisasi kedai kopi · Tim Biru**

| | |
|---|---|
| **Durasi** | 6 menit 45 detik (batas 7 menit; sisa 15 detik) |
| **Deck** | 10 slide  ·  Bagi_Kopi_Presentation.pptx |
| **Tim** | Tim Biru — Muh. Rapa Aditya & Putra Ari Widian |
| **Institusi** | SMK Budi Bakti Ciwidey  ·  Kabupaten Bandung, Jawa Barat |
| **Versi** | 1.0  ·  16 November 2026 |

> **Target latihan:** 6:20–6:45. Jangan mengejar 7:00; sisa waktu adalah ruang bernapas.

## Cara memakai naskah

1. Naskah ini ditulis untuk **diucapkan**, bukan dibaca kata per kata. Hafalkan alur dan angka kunci; sisanya bicara natural.
2. Teks **tebal** adalah kata yang diberi tekanan. Baris **CUE** adalah instruksi panggung—jangan dibaca.
3. Jeda satu detik setiap pindah slide. Lebih baik berhenti sejenak daripada mengisi dengan “eee”.
4. Jika lewat 6:50, potong detail—bukan slide. Daftar kalimat pemangkas ada di bagian akhir.

## Peta waktu

| Slide | Waktu | Durasi | Fokus |
|---|---|---|---|
| 01 · Pembuka | 0:00–0:25 | 25 detik | Pembuka & peta presentasi |
| 02 · Latar belakang | 0:25–1:05 | 40 detik | Masalah manual & dampaknya |
| 03 · Persona | 1:05–1:45 | 40 detik | Dua sisi meja yang sama |
| 04 · Solusi | 1:45–2:30 | 45 detik | Empat peran, satu alur |
| 05 · Batasan lingkup | 2:30–3:05 | 35 detik | In-scope vs out-of-scope |
| 06 · Arsitektur & ERD | 3:05–4:00 | 55 detik | Laravel, MySQL, relasi data |
| 07 · Fitur unggulan | 4:00–4:55 | 55 detik | Anti-double booking & QRIS |
| 08 · QA & security | 4:55–5:35 | 40 detik | 51 skenario, 3 temuan |
| 09 · Keberlanjutan | 5:35–6:15 | 40 detik | Roadmap & dampak sosial |
| 10 · Penutup & demo | 6:15–6:45 | 30 detik | Tiga langkah demo |

**Total 6:45.** Demo 60–90 detik dijalankan setelah presentasi, kecuali penguji meminta di dalam durasi—gunakan versi 45 detik.

---

## Naskah per slide

### SLIDE 01 · PEMBUKA (0:00–0:25)

**Visual:** Cover: logo Bagi Kopi, judul “Dari meja ke layar”, nama tim.  
**Tujuan:** Membuka dengan tenang, menyebut identitas, dan memberi peta presentasi.

Assalamualaikum warahmatullahi wabarakatuh. Bapak/Ibu penguji, kami **Tim Biru** dari SMK Budi Bakti Ciwidey—Muh. Rapa Aditya dan Putra Ari Widian.

Kami membawa **Bagi Kopi**, aplikasi web yang mendigitalkan alur kedai kopi: dari QR di meja, POS kasir, sampai laporan pemilik.

Dalam **tujuh menit**, kami paparkan masalah, solusi, arsitektur, kualitas, dan keberlanjutannya.

> **CUE ·** Berdiri di sisi kanan layar, tidak menutupi proyektor. Senyum, tatap penguji, jeda satu detik sebelum pindah slide.

---

### SLIDE 02 · LATAR BELAKANG: KEDAI RAMAI, OPERASIONAL MANUAL (0:25–1:05)

**Visual:** Alur As-Is empat langkah dan empat kerugian pengelola.  
**Tujuan:** Menunjukkan bahwa masalahnya sistemik, bukan sekadar lambat.

Semuanya dimulai dari hal sederhana: **kertas**. Di Bagi Kopi, pesanan masih dicatat manual. Pelanggan memanggil pramusaji, total dihitung dengan kalkulator, pembayaran dicatat, lalu omzet direkap malam hari.

Satu pesanan bisa melewati **empat pencatatan**. Saat jam sibuk, dampaknya terasa: antrean panjang, salah hitung, meja dan stok tidak real-time, dan laporan tertunda.

Artinya, pemilik mengambil keputusan hari ini dengan data kemarin. Karena itu kebutuhan intinya satu: **alur digital dari meja sampai laporan**.

> **CUE ·** Tunjuk diagram empat langkah saat menyebut “empat pencatatan”. Tekan kata “kertas” dan “data kemarin”.

---

### SLIDE 03 · PERSONA: DUA SISI MEJA YANG SAMA (1:05–1:45)

**Visual:** Kartu persona Farhan (pelanggan) dan Pak Dadan (pemilik), plus tiga peran staf.  
**Tujuan:** Membuat masalah terasa manusiawi dan menjelaskan siapa yang dilayani.

Ada dua sisi dari meja yang sama. Di sisi pelanggan, **Farhan, 24 tahun**. Ia ingin melihat menu dengan foto dan harga, memesan langsung dari meja, dan tahu total sejak awal—tanpa menunggu kasir.

Di sisi pemilik, **Pak Dadan, 48 tahun**. Ia tidak ingin menunggu rekap malam; ia ingin tahu penjualan hari ini, menu terlaris, dan performa staf.

Admin, kasir, dan manager adalah pengguna pendukung yang bekerja di atas **data yang sama**.

> **CUE ·** Sebut nama persona pelan-pelan. Jeda setelah kebutuhan Farhan dan Pak Dadan; jangan buru-buru ke slide solusi.

---

### SLIDE 04 · SOLUSI: SATU APLIKASI, EMPAT PERAN (1:45–2:30)

**Visual:** Empat kartu peran dan alur QR meja → pesanan → pembayaran → laporan.  
**Tujuan:** Menjelaskan solusi sebagai satu alur, bukan daftar fitur.

Solusinya **Bagi Kopi**: satu aplikasi, empat peran. Pelanggan memindai QR meja, memilih menu, memakai promo, lalu checkout dan membayar QRIS.

Kasir memproses pesanan di POS, menerima pembayaran, dan mencetak struk. Admin mengelola produk, meja, supplier, promo, dan karyawan. Manager membuka dashboard dan mengunduh laporan PDF.

Yang kami tonjolkan bukan jumlah fiturnya, tetapi **alurnya**: QR meja, pesanan, pembayaran, laporan—tanpa rekap ganda.

> **CUE ·** Gerakkan tangan mengikuti alur kiri ke kanan. Jangan membaca kartu peran satu per satu.

---

### SLIDE 05 · BATASAN LINGKUP (2:30–3:05)

**Visual:** Dua kolom: in-scope (tujuh item) dan out-of-scope (enam item).  
**Tujuan:** Menunjukkan proyek terukur dan mencegah ekspektasi berlebih.

Kami sengaja menetapkan batas. Yang dibangun dan diuji: aplikasi web **Laravel 12 dan MySQL** yang responsif, katalog dan QR meja, POS dengan dine-in dan take-away, pembayaran tunai dan QRIS sandbox, master data, dashboard, laporan PDF, serta tiga role dengan proteksi akses.

Di luar lingkup: aplikasi native, mode offline, multi-outlet, akuntansi penuh, printer thermal, loyalty, dan settlement produksi.

Batas ini membuat rilis **terukur**, setiap fitur bisa diuji, dan ekspektasi penguji jelas.

> **CUE ·** Saat menyebut out-of-scope, angkat tangan kanan sebagai pemisah. Jangan terdengar minta maaf—ini keputusan desain.

---

### SLIDE 06 · ARSITEKTUR TEKNIS DAN ERD (3:05–4:00)

**Visual:** Diagram client–Laravel–MySQL–Midtrans/Dompdf dan ERD delapan entitas inti.  
**Tujuan:** Menunjukkan struktur yang rapi dan alasan teknis di baliknya.

Secara teknis, browser memakai **Blade dan Tailwind**. **Laravel 12** menangani route, middleware role, controller, dan policy. Alurnya MVC: request masuk, dicek hak akses, diproses, lalu dikembalikan ke view.

**MySQL** menyimpan 11 tabel domain; delapan entitas inti terlihat di ERD. Karyawan dan meja menghasilkan transaksi; satu transaksi punya banyak detail—setiap detail menyimpan menu, jumlah, dan subtotal; setiap transaksi punya satu pembayaran; produk dikelompokkan per kategori; promo memengaruhi transaksi.

Relasi dijaga **foreign key**, jadi integritas data tidak hanya bergantung pada aplikasi. Midtrans menangani QRIS, Dompdf menghasilkan laporan PDF.

> **CUE ·** Tunjuk kotak Application saat menyebut middleware dan policy. Tunjuk garis 1—N di ERD saat menyebut detail transaksi.

---

### SLIDE 07 · FITUR UNGGULAN: ANTI-DOUBLE BOOKING DAN INVOICE QR (4:00–4:55)

**Visual:** Dua panel: meja terisi ditolak; alur QRIS dari charge sampai struk.  
**Tujuan:** Membuktikan dua kontrol inti yang membedakan aplikasi.

Dua fitur yang paling kami tonjolkan. Pertama, **anti-double booking**. Setiap meja punya QR dan satu sesi aktif. Di dalam transaksi database, server mengunci pengecekan pesanan aktif—pending, ready, atau diproses—lalu menolak meja yang masih terpakai. Jadi dua pelanggan yang checkout bersamaan tidak bisa mendapatkan meja yang sama; ini diuji pada NT06.

Kedua, **invoice QR**. Midtrans membuat QR sesuai total; webhook menandai lunas, memicu struk, dan menyimpan riwayat. Callback ganda aman karena idempotent, dan QR kedaluwarsa membatalkan pesanan serta mengembalikan stok. Ini diuji pada FT07b, NT12, dan NT13.

QRIS saat ini masih sandbox; untuk produksi kami memerlukan merchant ID, client key, dan server key.

> **CUE ·** Tekan “satu meja, satu sesi”. Saat menyebut idempotent, lanjutkan dengan “callback ganda aman”—jangan berhenti menjelaskan istilah.

---

### SLIDE 08 · TESTING QA DAN SECURITY (4:55–5:35)

**Visual:** Empat metrik besar: 51 / 241 / 48 / 3, cakupan tes, dan tiga temuan non-blocking.  
**Tujuan:** Meyakinkan penguji dengan data, sekaligus jujur pada batas.

Kualitas kami **uji, bukan asumsikan**. Ada 51 skenario otomatis, 241 assertion, dan 48 lulus—94 persen. Cakupannya auth dan RBAC, IDOR, POS dan pembayaran, promo dan stok, upload, XSS/SQLi, serta halaman error.

Tiga temuan kami nyatakan terbuka: dua skenario QRIS butuh kredensial sandbox, satu edge-case FIFO butuh tie-breaker id. Mitigasinya mock gateway di CI dan orderBy id.

Kami tidak mengklaim sempurna; kami menunjukkan **apa yang sudah diuji dan apa yang belum**.

> **CUE ·** Tunjuk angka 51 dan 48 saat menyebutnya. Jeda sebelum kalimat terakhir—itu kalimat kepercayaan.

---

### SLIDE 09 · KEBERLANJUTAN DAN DAMPAK SOSIAL (5:35–6:15)

**Visual:** Roadmap tiga fase 0–3, 3–6, 6–12 bulan dan empat dampak.  
**Tujuan:** Menunjukkan proyek tidak berhenti di UKK.

Keberlanjutannya kami susun **dua belas bulan**. Nol sampai tiga bulan: stabilisasi—backup harian, monitoring, pelatihan staf, dan aktivasi QRIS produksi.

Tiga sampai enam bulan: loyalty, stok otomatis, dan pilot multi-meja atau multi-outlet. Enam sampai dua belas bulan: analitik demand, PWA, dan integrasi supplier.

Dampaknya bukan hanya teknis: omzet lebih terukur, staf naik keterampilan, pelanggan lebih cepat, dan konsumsi kertas berkurang. Visi kami, Bagi Kopi menjadi **template digitalisasi UMKM kopi** yang mudah direplikasi.

> **CUE ·** Hitung fase dengan jari: nol, tiga, enam, dua belas. Saat fase terakhir, turunkan tempo dan tatap penguji.

---

### SLIDE 10 · PENUTUP DAN DEMO (6:15–6:45)

**Visual:** Tiga langkah demo dan ajakan menuju demo.  
**Tujuan:** Menutup dalam 30 detik dan membuka jalan ke demo.

Kami siap demo tiga langkah dalam kurang dari dua menit: **pindai QR meja, pesan dan bayar, lalu lihat laporan**.

Jika Bapak/Ibu mengizinkan, kami mulai sekarang. Terima kasih atas perhatiannya—kami terbuka untuk pertanyaan.

> **CUE ·** Setelah “terima kasih”, diam satu detik, baru bersiap ke laptop demo. Jangan langsung memutar badan.

---

## Demo 90 detik

Jalankan **setelah** naskah 6:45, atau saat penguji meminta. Jika demo wajib masuk 7 menit, pakai versi 45 detik di bawah tabel.

| Waktu | Aksi | Kalimat |
|---|---|---|
| 0:00–0:15 | Buka tab pelanggan; tunjukkan QR meja 03. | “Ini QR meja 03. Pelanggan langsung masuk ke katalog tanpa memasang aplikasi.” |
| 0:15–0:45 | Tambah dua menu + opsi, masukkan promo, checkout. | “Saya pilih dua menu, tambah opsi, dan pakai promo. Total dihitung otomatis.” |
| 0:45–1:05 | Pilih tunai dan selesaikan pembayaran. | “Saya bayar tunai dulu agar demo tidak bergantung jaringan. Untuk QRIS, alurnya sama—QR dibuat Midtrans dan status lunas masuk otomatis.” |
| 1:05–1:25 | Buka kasir; tunjukkan pesanan masuk; ubah status. | “Pesanan langsung muncul di POS kasir; statusnya bisa diubah.” |
| 1:25–1:40 | Buka dashboard manager; tunjukkan omzet dan menu terlaris; unduh PDF. | “Manager melihat angka yang sama secara real-time, lalu mengunduh laporan PDF.” |

**Versi 45 detik:** QR meja → katalog → checkout tunai → dashboard. Tanpa ubah status dan tanpa unduh PDF.

**Jika demo gagal:** jangan debug di depan penguji. Katakan “Kami siapkan backup alur”, tampilkan screenshot/video, lalu lanjut ke laporan. Jangan restart server saat presentasi.

**Persiapan:** empat tab—pelanggan, kasir, manager, PDF. Data demo: satu meja kosong, dua menu stok > 0, satu promo aktif, satu transaksi selesai. Gunakan database demo; jangan reset `db_perkoci`.

---

## Bank tanya-jawab penguji

Jawab **2–4 kalimat**. Mulai dari kesimpulan, baru alasan. Jika tidak tahu, akui dan catat—jangan mengarang.

**01 · Apa bedanya Bagi Kopi dengan aplikasi kasir yang sudah ada?**

Kami tidak menyaingi aplikasi enterprise. Bagi Kopi fokus pada satu kedai: QR self-order, POS, QRIS, dan laporan dalam satu alur. Fitur bisa disesuaikan dan biaya operasionalnya terkendali untuk UMKM. Multi-outlet dan akuntansi penuh justru kami nyatakan di luar lingkup.

**02 · Mengapa memilih web, bukan aplikasi mobile native?**

Pelanggan tidak perlu memasang aplikasi—cukup pindai QR dari browser. Update cukup di server, biaya perawatan lebih rendah, dan satu basis kode untuk ponsel maupun desktop. Untuk kebutuhan sekarang web responsif sudah cukup; PWA masuk roadmap 6–12 bulan.

**03 · Mengapa Laravel dan MySQL?**

Laravel 12 memberi MVC, Eloquent, validasi, middleware, policy, dan ekosistem keamanan yang matang—cocok untuk tim kecil dengan waktu terbatas. MySQL 8 stabil untuk data transaksional dan relasi. Kombinasi ini mempercepat pengembangan tanpa mengorbankan struktur.

**04 · Bagaimana anti-double booking bekerja jika dua orang checkout bersamaan?**

Pengecekan pesanan aktif—pending, ready, atau diproses—dilakukan di dalam transaksi database dengan lock. Request pertama membuat pesanan; request kedua yang datang bersamaan melihat sesi meja sudah terpakai lalu ditolak. Skenario ini diuji pada NT06.

**05 · Bagaimana stok dan promo tetap konsisten saat pesanan batal?**

Stok dan kuota promo dikunci dan diperiksa dalam transaksi yang sama; jika stok kurang, seluruh pesanan dibatalkan. Saat QRIS kedaluwarsa atau pesanan dibatalkan, stok dan kuota dikembalikan lewat satu jalur idempoten dengan penanda sudah_dibalik—jadi tidak terbalik dua kali.

**06 · Bagaimana keamanan akses antar-role?**

Akses staf memakai guard karyawan, middleware role, Gate untuk panel admin/POS/laporan, dan Policy Transaksi untuk anti-IDOR. Kasir hanya boleh melihat transaksi miliknya atau pesanan web; admin dan manager sesuai kewenangan. Login dibatasi throttle, password di-hash, dan form memakai CSRF.

**07 · Apakah QRIS sudah bisa dipakai produksi?**

Belum. QRIS berjalan di Midtrans sandbox untuk menguji pembuatan QR, webhook, idempotensi, dan kedaluwarsa. Untuk go-live kami memerlukan merchant ID, client key, dan server key production, serta pengujian settlement dengan merchant.

**08 · Mengapa 3 tes gagal? Apakah aplikasi layak?**

Tiga temuan bukan kegagalan alur inti: dua skenario QRIS membutuhkan kredensial sandbox, satu edge-case FIFO butuh tie-breaker id. Keduanya non-blocking dan sudah masuk backlog dengan mitigasi mock gateway dan orderBy id. Di slide kami tampilkan 48 dari 51 lulus; suite regresi default saat ini 53 tes, 252 assertion, lulus. Kami memilih jujur menunjukkan batas.

**09 · Apakah aplikasi sudah di-deploy?**

Ya. Aplikasi sudah kami deploy di Render lewat Docker dan dapat diakses di bagikopiordersystem.onrender.com. Untuk demo UKK kami tetap menyiapkan lingkungan lokal dan backup tangkapan layar agar tidak bergantung pada internet; instance gratis bisa cold start.

**10 · Jelaskan rancangan database-nya.**

Ada 11 tabel domain: karyawan, meja, kategori, produk, opsi produk, supplier, transaksi, detail transaksi, pembayaran, promo, dan pemakaian promo—ditambah tabel infrastruktur seperti sessions dan cache. Relasi inti 1–N dan 1–1 dijaga foreign key; pembayaran unik per transaksi; detail menyimpan qty dan subtotal.

**11 · Apa tantangan terbesar dalam proyek ini?**

Menjaga konsistensi stok dan promo saat pembatalan serta callback ganda. Solusinya transaksi database, lock, dan flag idempoten. Tantangan kedua, menguji QRIS tanpa kredensial produksi, kami atasi dengan mock gateway dan skenario sandbox.

**12 · Apa yang belum diuji?**

Kami belum menguji beban tinggi, multi-outlet, printer thermal, dan settlement QRIS produksi. Semuanya di luar lingkup atau roadmap. Yang sudah diuji: alur inti, keamanan dasar, dan skenario negatif yang tampil di slide 8.

**13 · Apakah bisa multi-outlet?**

Belum. Skema saat ini satu kedai; multi-outlet membutuhkan tabel outlet, relasi ke meja/produk/karyawan, dan isolasi data. Itu masuk roadmap 3–6 bulan sebagai pilot, bukan klaim saat ini.

**14 · Apa rencana setelah UKK?**

Stabilisasi tiga bulan: backup, monitoring, pelatihan, QRIS produksi. Setelah itu loyalty, stok otomatis, pilot multi-outlet, lalu analitik demand, PWA, dan integrasi supplier. Kami juga ingin Bagi Kopi menjadi template digitalisasi UMKM kopi.

**15 · Bagaimana pembagian kerja tim?**

Kami berdua terlibat dari analisis sampai pengujian. Pembagian teknisnya: [nama] fokus pada [modul], [nama] pada [modul]; semua perubahan di-review dan diuji bersama.

*Isi bagian pembagian kerja dengan pembagian riil sebelum tampil. Jangan mengarang di depan penguji.*

### Tiga jebakan umum

- **“Apakah 100% aman?”** → “Tidak ada sistem yang 100% aman. Yang kami lakukan: validasi, RBAC, CSRF, dan pengujian negatif. Yang belum: pengujian beban dan penetrasi independen.”
- **“Apakah semua fitur selesai?”** → “Sesuai lingkup, ya. Di luar lingkup, belum—dan itu keputusan desain, bukan kelalaian.”
- **“Kenapa sandbox, bukan produksi?”** → “Kredensial merchant belum tersedia. Sandbox menguji alur teknis tanpa dana nyata; produksi butuh merchant ID, client key, dan server key.”

---

## Checklist sebelum tampil

| Latihan & waktu | Data & perangkat |
|---|---|
| □ Baca naskah 3× dengan stopwatch. Target 6:20–6:45. | □ 1 meja kosong; 2 menu stok > 0; 1 promo aktif; 1 transaksi selesai. |
| □ Rekam sekali; tandai filler “eee”, “gitu”, “kan”. | □ Login admin, kasir, manager sudah diuji. |
| □ Latihan tanpa teks, hanya melihat cue dan angka. | □ QR meja, halaman pelanggan, POS, dashboard, PDF. |
| □ Latihan demo, termasuk skenario gagal. | □ Backup screenshot/video setiap alur. |
| □ Hafalkan urutan: masalah → persona → solusi → batas → arsitektur → fitur → QA → roadmap → demo. | □ Laptop, charger, remote, flash drive (PPT + PDF), hotspot. |
|  | □ Projector 16:9; browser zoom 100%; notifikasi mati. |
|  | □ Jangan tampilkan .env, password, atau data pelanggan asli. |
|  | □ Gunakan database demo; jangan reset db_perkoci. |

> **Jika mepet:** potong detail ERD (slide 6), nomor tes (slide 7), visi (slide 9), dan izin demo (slide 10). Jangan potong slide 1, 8, atau 10.

**Salam penutup** (“Wassalamualaikum warahmatullahi wabarakatuh”) diucapkan setelah Q&A dan demo, bukan di akhir slide 10.

*Disusun dari isi dan speaker notes `Bagi_Kopi_Presentation.pptx` (10 slide).*