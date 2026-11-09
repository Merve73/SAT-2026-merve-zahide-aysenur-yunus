-- TamirBul veritabani.
-- Nasil yuklenir: XAMPP'ta MySQL Start, tarayicida http://localhost/phpmyadmin
-- Iceri Aktar (Import) dugmesine basin ve bu dosyayi secin.
-- Dosyayi tekrar ice aktarirsaniz eski deneme verisi silinir, ornek veriler geri gelir.
-- Deneme sifresi tum kullanicilarda: 123456

-- Turkce karakterler icin.
SET NAMES utf8mb4;

-- Veritabani yoksa olusturur. Varsa dokunmaz, sonra onu seceriz.
CREATE DATABASE IF NOT EXISTS tamirbul CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;

-- Bundan sonraki komutlar tamirbul icinde calissin.
USE tamirbul;

-- Eski tablolar varsa kaldiririz. Once bagli tablolar, sonra ana tablolar.
DROP TABLE IF EXISTS randevular;
DROP TABLE IF EXISTS talepler;
DROP TABLE IF EXISTS mesajlar;
DROP TABLE IF EXISTS hizmetler;
DROP TABLE IF EXISTS sayfa_icerikleri;
DROP TABLE IF EXISTS site_ayarlari;
DROP TABLE IF EXISTS kullanicilar;

-- Uyeler: musteri, tamirci ve yonetici ayni tabloda. rol sutunu ayirir.
CREATE TABLE kullanicilar (
    -- Her kaydin kendi numarasi. MySQL kendisi 1, 2, 3 diye artirir.
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(50) NOT NULL,
    soyad VARCHAR(50) NOT NULL,
    -- Ayni e-posta iki kez kaydolamasin diye UNIQUE.
    eposta VARCHAR(100) NOT NULL UNIQUE,
    -- Sifrenin kendisi degil, password_hash ile uretilmis hali durur.
    sifre VARCHAR(255) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    -- rol sadece su uc degerden biri olmali: musteri, tamirci, admin
    rol VARCHAR(20) NOT NULL,
    sehir VARCHAR(50) NOT NULL,
    -- Tamirci ise bilgisayar, telefon veya tablet. Musteride bos kalabilir.
    uzmanlik VARCHAR(30) NOT NULL,
    hakkinda TEXT NOT NULL,
    -- 1 ise tamirci onaylidir ve talep alabilir. 0 ise bekler.
    onayli TINYINT NOT NULL DEFAULT 0,
    kayit_tarihi DATETIME NOT NULL
);

-- Sitede satilan tamir hizmetleri. Yonetici panelden ekler.
CREATE TABLE hizmetler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(120) NOT NULL,
    -- bilgisayar, telefon veya tablet
    cihaz_turu VARCHAR(30) NOT NULL,
    aciklama TEXT NOT NULL,
    baslangic_fiyat DECIMAL(10,2) NOT NULL,
    -- 1 ise sitede gorunur, 0 ise gizlenir ama silinmez.
    aktif TINYINT NOT NULL DEFAULT 1
);

-- Musterinin actigi tamir istegi.
CREATE TABLE talepler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- kullanicilar.id ile eslesir. Musteri kim.
    musteri_id INT NOT NULL,
    -- hizmetler.id ile eslesir. Hizmet secilmezse NULL kalir.
    hizmet_id INT NULL,
    -- Talebi ustlenen tamirci. Daha kimse almadiysa NULL.
    tamirci_id INT NULL,
    cihaz_turu VARCHAR(30) NOT NULL,
    marka VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    ariza TEXT NOT NULL,
    -- beklemede, tamirci_atandi, randevu_kuruldu, tamirde, tamamlandi, iptal
    durum VARCHAR(30) NOT NULL,
    -- Musterinin sorguladigi kod. Ornek: TB1001
    takip_kodu VARCHAR(20) NOT NULL,
    olusturma_tarihi DATETIME NOT NULL
);

-- Tamircinin bir talebe verdigi gun ve saat.
CREATE TABLE randevular (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- talepler.id
    talep_id INT NOT NULL,
    -- kullanicilar.id icinden tamirci
    tamirci_id INT NOT NULL,
    randevu_tarihi DATE NOT NULL,
    randevu_saati VARCHAR(10) NOT NULL,
    -- bekliyor, tamamlandi, iptal
    durum VARCHAR(30) NOT NULL,
    not_metni TEXT NOT NULL
);

-- Iletisim formundan gelen yazilar. Sadece yonetici gorur.
CREATE TABLE mesajlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(80) NOT NULL,
    eposta VARCHAR(100) NOT NULL,
    konu VARCHAR(150) NOT NULL,
    mesaj TEXT NOT NULL,
    -- 0 okunmadi, 1 okundu.
    okundu TINYINT NOT NULL DEFAULT 0,
    tarih DATETIME NOT NULL
);

-- Site genelindeki kisa metinler. Anahtar benzersizdir, deger metindir.
-- Yeni bir yazi eklemek icin bu tabloya satir eklenir. admin/ayarlar.php onu kutuda gosterir.
CREATE TABLE site_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anahtar VARCHAR(50) NOT NULL UNIQUE,
    deger TEXT NOT NULL
);

-- Hakkinda gibi uzun sayfa yazilari.
CREATE TABLE sayfa_icerikleri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sayfa VARCHAR(50) NOT NULL UNIQUE,
    baslik VARCHAR(150) NOT NULL,
    icerik TEXT NOT NULL
);

-- Ornek uyeler. Sifre hepsinde 123456. Veritabaninda hash durur, duz sifre durmaz.
INSERT INTO kullanicilar (ad, soyad, eposta, sifre, telefon, rol, sehir, uzmanlik, hakkinda, onayli, kayit_tarihi) VALUES
('Yönetici', 'Hesap', 'admin@tamirbul.test', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0500 000 00 01', 'admin', 'İstanbul', '', 'Site yöneticisi', 1, '2026-10-01 09:00:00'),
('Ayşe', 'Yılmaz', 'ayse@ornek.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0532 111 11 11', 'musteri', 'İstanbul', '', '', 1, '2026-10-02 11:00:00'),
('Mehmet', 'Demir', 'mehmet@ornek.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0533 222 22 22', 'musteri', 'Ankara', '', '', 1, '2026-10-03 12:00:00'),
('Can', 'Kaya', 'can@tamir.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0541 333 33 33', 'tamirci', 'İstanbul', 'telefon', 'Telefon ekranı ve batarya değişimi yapıyorum.', 1, '2026-10-04 13:00:00'),
('Elif', 'Şahin', 'elif@tamir.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0542 444 44 44', 'tamirci', 'Ankara', 'bilgisayar', 'Format, fan temizliği ve yavaşlayan bilgisayar bakımı.', 0, '2026-10-05 14:00:00');

INSERT INTO hizmetler (ad, cihaz_turu, aciklama, baslangic_fiyat, aktif) VALUES
('Telefon ekran değişimi', 'telefon', 'Kırık veya dokunmayan ekranın değişimi. Fiyat modele göre artar.', 1500, 1),
('Telefon batarya değişimi', 'telefon', 'Çabuk biten bataryanın değişimi.', 800, 1),
('Bilgisayar formatı', 'bilgisayar', 'Yedek alındıktan sonra sistem kurulumu ve temel programlar.', 500, 1),
('Bilgisayar fan temizliği', 'bilgisayar', 'Isınan bilgisayarda fan ve termal macun bakımı.', 400, 1),
('Tablet ekran değişimi', 'tablet', 'Kırık tablet ekranının değişimi.', 1800, 1),
('Tablet şarj soketi', 'tablet', 'Şarj olmayan tablette soket tamiri.', 700, 1);

-- Ayşe'nin talebi henuz bos. Mehmet'in talebini Can ustlenmis.
INSERT INTO talepler (musteri_id, hizmet_id, tamirci_id, cihaz_turu, marka, model, ariza, durum, takip_kodu, olusturma_tarihi) VALUES
(2, 1, NULL, 'telefon', 'Apple', 'iPhone 11', 'Ekran kırık, dokunmatik çalışmıyor.', 'beklemede', 'TB1001', '2026-10-10 15:00:00'),
(3, 3, 4, 'bilgisayar', 'Lenovo', 'ThinkPad', 'Çok ısınıyor ve kendi kendine kapanıyor.', 'randevu_kuruldu', 'TB1002', '2026-10-11 16:30:00');

INSERT INTO randevular (talep_id, tamirci_id, randevu_tarihi, randevu_saati, durum, not_metni) VALUES
(2, 4, '2026-10-20', '14:00', 'bekliyor', 'Şarj aletini de yanında getirsin.');

INSERT INTO mesajlar (ad, eposta, konu, mesaj, okundu, tarih) VALUES
('Ali Vural', 'ali@ornek.com', 'Fiyat sorusu', 'iPhone 11 ekran değişimi ne kadar sürer?', 0, '2026-10-12 10:15:00');

INSERT INTO site_ayarlari (anahtar, deger) VALUES
('site_adi', 'TamirBul'),
('telefon', '0500 000 00 00'),
('eposta', 'info@tamirbul.test'),
('adres', 'Örnek Mahallesi, Okul Sokak No: 1'),
('calisma_saatleri', 'Hafta içi 09:00 - 18:00'),
('anasayfa_baslik', 'Telefon, tablet ve bilgisayar tamiri için tamirci bul'),
('anasayfa_aciklama', 'Arızayı yazın. Onaylı tamirci randevu gününü görsün. Başlangıç fiyatı hizmete göre değişir.'),
('adim1_baslik', 'Arızayı anlatın'),
('adim1_aciklama', 'Cihazın markasını, modelini ve ne olduğunu kısaca yazın.'),
('adim2_baslik', 'Tamirci üstlensin'),
('adim2_aciklama', 'Onaylı tamirci talebi görünce size gün ve saat önerir.'),
('adim3_baslik', 'Randevuya gidin'),
('adim3_aciklama', 'Takip kodu ile işin hangi adımda olduğunu sorgulayın.');

INSERT INTO sayfa_icerikleri (sayfa, baslik, icerik) VALUES
('hakkimizda', 'Hakkımızda', 'TamirBul, bilgisayar programcılığı dönem projesidir. Amaç müşteri ile tamirciyi aynı sayfada buluşturmaktır.\n\nSitedeki hizmetler, tamirciler, sayfa yazıları ve iletişim bilgileri veritabanından gelir. Yönetici bu bilgileri panelden değiştirir. Böylece her değişiklik için kodu yeniden yazmaya gerek kalmaz.');
