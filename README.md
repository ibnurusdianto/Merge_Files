# 🚀 Deskripsi Program
Program PHP Merge Files ini adalah sebuah aplikasi berbasis command line yang berfungsi untuk menggabungkan beberapa file teks menjadi satu file output. Program ini sangat cocok untuk yang ingin mengelola banyak file teks serta mempermudah proses penggabungan file dengan hasil yang dapat dipilih berbagai format.

![Image](https://github.com/user-attachments/assets/b3bf425f-ef87-4a03-8c49-53afcf7e513f)

# 🔧 Fitur Utama
| Fitur | Description |
| --- | --- |
| `Penggabungan Beberapa File` | Menggabungkan isi dari beberapa file teks menjadi satu file output tunggal. |
| `Progress Bar` | Menampilkan indikator proses pada CLI selama proses penggabungan agar tahu progres. |
| `Backup File Lama` | Melakukan backup file output lama sebelum menulis file hasil gabungan baru secara otomatis. |
| `Format Output Beragam` | Output hasil merge bisa disimpan dalam format TXT, PDF, DOC (HTML), atau ZIP yang berisi file teks hasil merge. |
| `Reset Konfigurasi` | Memiliki fitur reset konfigurasi ke nilai default untuk sumber file dan nama file output. |
| `Direct Mode (coming soon)` | Merge langsung tanpa proses pembacaan terlebih dahulu, menggunakan stream processing untuk performa maksimal |
| `Standard Mode (coming soon)` | Mode default dengan progress tracking dan fitur lengkap |
| `Speed Multiplier (1x - 4x) - coming soon` | Speed Multiplier (1x - 4x) |

# 📌 Catatan Tambahan
1. Pastikan ekstensi PHP dan library yang dibutuhkan (misal Imagick untuk PDF) sudah terinstall bila menggunakan output PDF.
2. Format DOC disimpan sebagai HTML sederhana agar bisa dibuka di Word dengan mudah.
3. ZIP menyimpan satu file hasil merge bernama merged.txt di dalam arsip tersebut.
  
