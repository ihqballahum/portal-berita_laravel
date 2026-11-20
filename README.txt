PORTAL BERITA - PHP native + MySQL
==================================
1. Taruh folder ini di  D:\laragon\www\portal_berita
2. Nyalakan MySQL di Laragon (Start All).
3. Import database/portal_berita.sql lewat phpMyAdmin (tab Import, tanpa memilih database dulu).
4. Jalankan dari terminal Laragon:
       cd /d D:\laragon\www\portal_berita
       php -S localhost:8000
5. Buka:
       http://localhost:8000/cek.php          (cek instalasi)
       http://localhost:8000/                 (website)
       http://localhost:8000/admin/login.php  (dashboard)

Login: admin / admin123   atau   editor / editor123
Pengaturan database ada di config.php
