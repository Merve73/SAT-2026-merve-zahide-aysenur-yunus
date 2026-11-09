-- DOSYA: veritabani/tamirbul.sql
-- KİM: Kişi 4
-- NE YAPAR: Veritabanını, tabloları ve örnek verileri oluşturur.
-- Nasıl yüklenir: baslat.bat ilk açılışta kendisi yükler. Elle: phpMyAdmin > İçeri Aktar > bu dosya.
-- Tekrar yüklenirse eski veriler silinir, örnek veriler geri gelir. Deneme şifresi: 123456

-- Türkçe karakterler için bağlantı ayarı.
SET NAMES utf8mb4;
-- Veritabanı yoksa oluşturur. Türkçe sıralama (utf8mb4_turkish_ci).
CREATE DATABASE IF NOT EXISTS tamirbul CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
-- Bundan sonraki komutlar tamirbul içinde çalışır.
USE tamirbul;

-- Eski tablolar varsa silinir.
DROP TABLE IF EXISTS yazi_stilleri;
-- Silinir.
DROP TABLE IF EXISTS hizmet_cihazlari;
-- Silinir.
DROP TABLE IF EXISTS islem_kayitlari;
-- Silinir.
DROP TABLE IF EXISTS slaytlar;
-- Silinir.
DROP TABLE IF EXISTS adimlar;
-- Silinir.
DROP TABLE IF EXISTS sohbet_mesajlari;
-- Silinir.
DROP TABLE IF EXISTS teklifler;
-- Silinir.
DROP TABLE IF EXISTS randevular;
-- Silinir.
DROP TABLE IF EXISTS talepler;
-- Silinir.
DROP TABLE IF EXISTS tamirci_yetkinlikleri;
-- Silinir.
DROP TABLE IF EXISTS mesajlar;
-- Silinir.
DROP TABLE IF EXISTS hizmetler;
-- Silinir.
DROP TABLE IF EXISTS cihaz_turleri;
-- Silinir.
DROP TABLE IF EXISTS kategoriler;
-- Silinir.
DROP TABLE IF EXISTS ilceler;
-- Silinir.
DROP TABLE IF EXISTS iller;
-- Silinir.
DROP TABLE IF EXISTS sayfa_icerikleri;
-- Silinir.
DROP TABLE IF EXISTS site_ayarlari;
-- Silinir.
DROP TABLE IF EXISTS kullanicilar;

-- HARİTA: Tablo oluşturma (CREATE TABLE)
-- Üyeler: müşteri, tamirci ve yönetici aynı tabloda. rol sütunu ayırır.
CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası, MySQL kendisi artırır
    ad VARCHAR(50) NOT NULL, -- ad
    soyad VARCHAR(50) NOT NULL, -- soyad
    eposta VARCHAR(100) NOT NULL UNIQUE, -- giriş adı, UNIQUE: aynı e-posta iki kez olmaz
    sifre VARCHAR(255) NOT NULL, -- şifrenin password_hash ile üretilmiş hali
    telefon VARCHAR(20) NOT NULL, -- telefon
    rol VARCHAR(20) NOT NULL, -- müşteri, tamirci veya admin
    isletme_adi VARCHAR(100) NOT NULL DEFAULT '', -- tamircinin dükkan adı
    sehir VARCHAR(50) NOT NULL, -- il adı. Örnek: Yozgat
    ilce VARCHAR(50) NOT NULL DEFAULT '', -- ilçe adı. Örnek: Sorgun
    adres TEXT NOT NULL, -- tamircinin dükkan adresi
    hakkinda TEXT NOT NULL, -- tamircinin tanıtım yazısı
    onayli TINYINT NOT NULL DEFAULT 0, -- 0 onay bekliyor, 1 onaylı, 2 reddedildi
    kayit_tarihi DATETIME NOT NULL -- kayıt zamanı
);

-- İller. Numara olarak plaka kullanılır.
CREATE TABLE iller (
    id INT PRIMARY KEY, -- plaka numarası. Örnek: 66
    ad VARCHAR(50) NOT NULL -- il adı
);

-- İlçeler.
CREATE TABLE ilceler (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    il_id INT NOT NULL, -- hangi il (iller.id)
    ad VARCHAR(50) NOT NULL -- ilçe adı
);

-- Ana cihaz kategorileri. Örnek: Mobil & Bilişim
CREATE TABLE kategoriler (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    ad VARCHAR(80) NOT NULL, -- kategori adı
    aciklama TEXT NOT NULL, -- kartta görünen kısa yazı
    resim VARCHAR(255) NOT NULL DEFAULT '' -- kart resmi: internet adresi veya yüklenen dosya adı
);

-- Her kategorinin cihaz türleri. Örnek: Buzdolabı
CREATE TABLE cihaz_turleri (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    kategori_id INT NOT NULL, -- hangi kategori (kategoriler.id)
    ad VARCHAR(80) NOT NULL -- tür adı
);

-- Tamircinin tamir edebildiği kategoriler. Bir tamircinin birden çok satırı olur.
CREATE TABLE tamirci_yetkinlikleri (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    tamirci_id INT NOT NULL, -- hangi tamirci (kullanicilar.id)
    kategori_id INT NOT NULL -- hangi kategori (kategoriler.id)
);

-- Tamir hizmetleri. Yönetici panelden ekler.
CREATE TABLE hizmetler (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    ad VARCHAR(120) NOT NULL, -- hizmet adı
    kategori_id INT NOT NULL, -- hangi kategori (kategoriler.id)
    aciklama TEXT NOT NULL, -- açıklama
    baslangic_fiyat DECIMAL(10,2) NOT NULL, -- fiyat. DECIMAL: kuruşlu sayı
    resim VARCHAR(255) NOT NULL DEFAULT '', -- kart resmi. Boşsa kategori resmi kullanılır
    aktif TINYINT NOT NULL DEFAULT 1 -- 1 sitede görünür, 0 gizli
);

-- Hizmetin hangi cihaz türleri için olduğu. Bir hizmetin birden çok satırı olur.
-- Örnek: "Bilgisayar formatı" hem Laptop hem Masaüstü için. Hizmetin hiç satırı yoksa kategorideki bütün cihazlar için geçerlidir.
CREATE TABLE hizmet_cihazlari (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    hizmet_id INT NOT NULL, -- hangi hizmet (hizmetler.id)
    cihaz_turu_id INT NOT NULL -- hangi cihaz türü (cihaz_turleri.id)
);

-- HARİTA: İşlem kayıtları tablosu (log)
-- Sitede yapılan önemli işler. ortak/islem-kaydi.php içindeki log_yaz yazar, admin/kayitlar.php okur.
CREATE TABLE islem_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    kullanici_id INT NULL, -- işi yapan kişi (kullanicilar.id). Giriş yapmamışsa NULL
    kisi VARCHAR(120) NOT NULL DEFAULT '', -- kişinin adı soyadı. Kişi sonra silinse de ad kayıtta kalır
    rol VARCHAR(20) NOT NULL DEFAULT '', -- musteri, tamirci, admin. Ziyaretçide boş
    islem VARCHAR(100) NOT NULL, -- ne yapıldı. Örnek: Giriş yapıldı
    ayrinti VARCHAR(255) NOT NULL DEFAULT '', -- ek bilgi. Örnek: Talep numarası 4. Şifre ASLA yazılmaz
    ip VARCHAR(45) NOT NULL DEFAULT '', -- işi yapan bilgisayarın adresi
    tarih DATETIME NOT NULL -- ne zaman
);

-- Ana sayfadaki "Nasıl Çalışır?" kartları.
CREATE TABLE adimlar (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    sira INT NOT NULL DEFAULT 0, -- küçük sayı önce gösterilir
    baslik VARCHAR(100) NOT NULL, -- kart başlığı
    aciklama TEXT NOT NULL, -- kart yazısı
    resim VARCHAR(255) NOT NULL DEFAULT '' -- kart resmi
);

-- Ana sayfanın üstünde sırayla değişen resimler.
CREATE TABLE slaytlar (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    sira INT NOT NULL DEFAULT 0, -- küçük sayı önce gösterilir
    resim VARCHAR(255) NOT NULL, -- resim adresi veya dosya adı
    aciklama VARCHAR(150) NOT NULL DEFAULT '' -- resim açılmazsa görünen yazı
);

-- Müşterinin açtığı tamir talebi.
CREATE TABLE talepler (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    musteri_id INT NOT NULL, -- talebi açan müşteri (kullanicilar.id)
    hizmet_id INT NULL, -- seçilen hizmet (hizmetler.id), seçilmezse NULL
    tamirci_id INT NULL, -- teklifi kabul edilen tamirci, daha seçilmediyse NULL
    kategori_id INT NOT NULL, -- kategori (kategoriler.id)
    cihaz_turu VARCHAR(80) NOT NULL, -- cihaz türünün adı. Örnek: Akıllı Telefon
    marka VARCHAR(50) NOT NULL, -- marka
    model VARCHAR(50) NOT NULL, -- model, boş olabilir
    ariza TEXT NOT NULL, -- arıza açıklaması
    resim VARCHAR(255) NOT NULL DEFAULT '', -- yüklenen fotoğrafın dosya adı
    sehir VARCHAR(50) NOT NULL, -- tamirin yapılacağı il
    ilce VARCHAR(50) NOT NULL, -- tamirin yapılacağı ilçe
    durum VARCHAR(30) NOT NULL, -- beklemede, tamirci_atandi, randevu_kuruldu, tamirde, tamamlandi, iptal
    takip_kodu VARCHAR(20) NOT NULL, -- sorgulama kodu. Örnek: TB1001
    puan TINYINT NOT NULL DEFAULT 0, -- müşterinin verdiği puan 1-5. 0 ise puanlanmadı
    olusturma_tarihi DATETIME NOT NULL -- açılış zamanı
);

-- Tamircinin talebe verdiği fiyat teklifi.
CREATE TABLE teklifler (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    talep_id INT NOT NULL, -- hangi talep (talepler.id)
    tamirci_id INT NOT NULL, -- teklifi veren tamirci (kullanicilar.id)
    fiyat DECIMAL(10,2) NOT NULL, -- teklif fiyatı
    sure VARCHAR(50) NOT NULL, -- tahmini süre. Örnek: Aynı gün
    aciklama TEXT NOT NULL, -- tamircinin notu
    durum VARCHAR(20) NOT NULL, -- bekliyor, kabul, red
    tarih DATETIME NOT NULL -- teklif zamanı
);

-- Müşteri ile tamirci arasındaki mesajlar. Bir sohbet = talep_id + tamirci_id.
CREATE TABLE sohbet_mesajlari (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    talep_id INT NOT NULL, -- hangi talep
    tamirci_id INT NOT NULL, -- sohbetin tamirci tarafı
    gonderen_id INT NOT NULL, -- mesajı yazan kişi (müşteri veya tamirci)
    mesaj TEXT NOT NULL, -- mesaj yazısı
    tarih DATETIME NOT NULL -- gönderilme zamanı
);

-- Tamircinin kurduğu randevu.
CREATE TABLE randevular (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    talep_id INT NOT NULL, -- hangi talep
    tamirci_id INT NOT NULL, -- randevuyu kuran tamirci
    randevu_tarihi DATE NOT NULL, -- gün. DATE: sadece tarih
    randevu_saati VARCHAR(10) NOT NULL, -- saat. Örnek: 14:00
    durum VARCHAR(30) NOT NULL, -- bekliyor, tamamlandi, iptal
    not_metni TEXT NOT NULL -- tamircinin notu
);

-- İletişim formundan gelen mesajlar. Sadece yönetici görür.
CREATE TABLE mesajlar (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    ad VARCHAR(80) NOT NULL, -- yazanın adı
    eposta VARCHAR(100) NOT NULL, -- yazanın e-postası
    konu VARCHAR(150) NOT NULL, -- konu
    mesaj TEXT NOT NULL, -- mesaj
    okundu TINYINT NOT NULL DEFAULT 0, -- 0 okunmadı, 1 okundu
    tarih DATETIME NOT NULL -- gönderilme zamanı
);

-- Sitedeki kısa yazılar. Yönetici "Site yazıları" sayfasından değiştirir.
CREATE TABLE site_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    anahtar VARCHAR(50) NOT NULL UNIQUE, -- kodda kullanılan ad. Örnek: telefon
    deger TEXT NOT NULL, -- ekranda görünen yazı
    etiket VARCHAR(100) NOT NULL DEFAULT '' -- yönetim ekranında kutunun üstündeki ad
);

-- Yönetimden değişen yazıların görünüşü. Boş sütun: sitenin kendi görünüşü.
-- Kodların CSS karşılığı ortak/yazi-stili.php içindeki listede yazar.
CREATE TABLE yazi_stilleri (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    anahtar VARCHAR(80) NOT NULL UNIQUE, -- hangi yazı. site_ayarlari.anahtar veya sayfa_hakkimizda_baslik gibi
    renk VARCHAR(20) NOT NULL DEFAULT '', -- örnek: kirmizi
    boyut VARCHAR(20) NOT NULL DEFAULT '', -- örnek: buyuk
    kalinlik VARCHAR(20) NOT NULL DEFAULT '', -- örnek: kalin
    egik VARCHAR(20) NOT NULL DEFAULT '', -- duz veya egik
    hizalama VARCHAR(20) NOT NULL DEFAULT '', -- sol, orta, sag
    yazi_tipi VARCHAR(20) NOT NULL DEFAULT '' -- örnek: georgia
);

-- Uzun sayfa yazıları. Örnek: Hakkımızda
CREATE TABLE sayfa_icerikleri (
    id INT AUTO_INCREMENT PRIMARY KEY, -- kayıt numarası
    sayfa VARCHAR(50) NOT NULL UNIQUE, -- sayfanın kodu. Örnek: hakkımızda
    baslik VARCHAR(150) NOT NULL, -- başlık
    icerik TEXT NOT NULL -- uzun yazı
);

-- HARİTA: Örnek veriler (INSERT İNTO)
-- Örnek üyeler. Şifre hepsinde 123456. Veritabanında hash durur, düz şifre durmaz.
INSERT INTO kullanicilar (ad, soyad, eposta, sifre, telefon, rol, isletme_adi, sehir, ilce, adres, hakkinda, onayli, kayit_tarihi) VALUES
('Yönetici', 'Hesap', 'admin@tamirbul.test', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0500 000 00 01', 'admin', '', 'İstanbul', 'Kadıköy', '', 'Site yöneticisi', 1, '2026-10-01 09:00:00'),
('Ayşe', 'Yılmaz', 'ayse@ornek.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0532 111 11 11', 'musteri', '', 'İstanbul', 'Kadıköy', '', '', 1, '2026-10-02 11:00:00'),
('Mehmet', 'Demir', 'mehmet@ornek.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0533 222 22 22', 'musteri', '', 'İstanbul', 'Ümraniye', '', '', 1, '2026-10-03 12:00:00'),
('Can', 'Kaya', 'can@tamir.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0541 333 33 33', 'tamirci', 'Güven Teknik Servis', 'İstanbul', 'Kadıköy', 'Caferağa Mah. Moda Cad. No: 10', 'Telefon, tablet ve laptop ekran, batarya ve anakart tamiri yapıyorum.', 1, '2026-10-04 13:00:00'),
('Elif', 'Şahin', 'elif@tamir.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0542 444 44 44', 'tamirci', 'Şahin Bilişim', 'Ankara', 'Çankaya', 'Kızılay Mah. Atatürk Bulvarı No: 5', 'Format, fan temizliği ve kart tamiri.', 0, '2026-10-05 14:00:00'),
('Murat', 'Öz', 'murat@tamir.com', '$2y$10$.LhiqRPRBSyXneflynE1JOASfDT5947WArL0hsMRBLP4uYyJbrwoq', '0543 555 55 55', 'tamirci', 'Öz Elektronik', 'İstanbul', 'Ümraniye', 'Atakent Mah. Alemdağ Cad. No: 22', 'Beyaz eşya, küçük ev aletleri ve telefon tamiri.', 1, '2026-10-06 10:00:00');

-- İller. id = plaka numarası.
INSERT INTO iller (id, ad) VALUES
(1, 'Adana'),
(6, 'Ankara'),
(7, 'Antalya'),
(16, 'Bursa'),
(34, 'İstanbul'),
(35, 'İzmir'),
(38, 'Kayseri'),
(46, 'Kahramanmaraş'),
(66, 'Yozgat');

-- İlçeler. İlk sayı ilin plaka numarası.
INSERT INTO ilceler (il_id, ad) VALUES
(1, 'Aladağ'), (1, 'Ceyhan'), (1, 'Çukurova'), (1, 'Feke'), (1, 'İmamoğlu'), (1, 'Karaisalı'), (1, 'Karataş'), (1, 'Kozan'),
(1, 'Pozantı'), (1, 'Saimbeyli'), (1, 'Sarıçam'), (1, 'Seyhan'), (1, 'Tufanbeyli'), (1, 'Yumurtalık'), (1, 'Yüreğir'),
(6, 'Akyurt'), (6, 'Altındağ'), (6, 'Ayaş'), (6, 'Bala'), (6, 'Beypazarı'), (6, 'Çamlıdere'), (6, 'Çankaya'), (6, 'Çubuk'),
(6, 'Elmadağ'), (6, 'Etimesgut'), (6, 'Evren'), (6, 'Gölbaşı'), (6, 'Güdül'), (6, 'Haymana'), (6, 'Kahramankazan'), (6, 'Kalecik'),
(6, 'Keçiören'), (6, 'Kızılcahamam'), (6, 'Mamak'), (6, 'Nallıhan'), (6, 'Polatlı'), (6, 'Pursaklar'), (6, 'Sincan'), (6, 'Şereflikoçhisar'),
(6, 'Yenimahalle'),
(7, 'Akseki'), (7, 'Aksu'), (7, 'Alanya'), (7, 'Demre'), (7, 'Döşemealtı'), (7, 'Elmalı'), (7, 'Finike'), (7, 'Gazipaşa'),
(7, 'Gündoğmuş'), (7, 'İbradı'), (7, 'Kaş'), (7, 'Kemer'), (7, 'Kepez'), (7, 'Konyaaltı'), (7, 'Korkuteli'), (7, 'Kumluca'),
(7, 'Manavgat'), (7, 'Muratpaşa'), (7, 'Serik'),
(16, 'Büyükorhan'), (16, 'Gemlik'), (16, 'Gürsu'), (16, 'Harmancık'), (16, 'İnegöl'), (16, 'İznik'), (16, 'Karacabey'), (16, 'Keles'),
(16, 'Kestel'), (16, 'Mudanya'), (16, 'Mustafakemalpaşa'), (16, 'Nilüfer'), (16, 'Orhaneli'), (16, 'Orhangazi'), (16, 'Osmangazi'), (16, 'Yenişehir'),
(16, 'Yıldırım'),
(34, 'Adalar'), (34, 'Arnavutköy'), (34, 'Ataşehir'), (34, 'Avcılar'), (34, 'Bağcılar'), (34, 'Bahçelievler'), (34, 'Bakırköy'), (34, 'Başakşehir'),
(34, 'Bayrampaşa'), (34, 'Beşiktaş'), (34, 'Beykoz'), (34, 'Beylikdüzü'), (34, 'Beyoğlu'), (34, 'Büyükçekmece'), (34, 'Çatalca'), (34, 'Çekmeköy'),
(34, 'Esenler'), (34, 'Esenyurt'), (34, 'Eyüpsultan'), (34, 'Fatih'), (34, 'Gaziosmanpaşa'), (34, 'Güngören'), (34, 'Kadıköy'), (34, 'Kağıthane'),
(34, 'Kartal'), (34, 'Küçükçekmece'), (34, 'Maltepe'), (34, 'Pendik'), (34, 'Sancaktepe'), (34, 'Sarıyer'), (34, 'Silivri'), (34, 'Sultanbeyli'),
(34, 'Sultangazi'), (34, 'Şile'), (34, 'Şişli'), (34, 'Tuzla'), (34, 'Ümraniye'), (34, 'Üsküdar'), (34, 'Zeytinburnu'),
(35, 'Aliağa'), (35, 'Balçova'), (35, 'Bayındır'), (35, 'Bayraklı'), (35, 'Bergama'), (35, 'Beydağ'), (35, 'Bornova'), (35, 'Buca'),
(35, 'Çeşme'), (35, 'Çiğli'), (35, 'Dikili'), (35, 'Foça'), (35, 'Gaziemir'), (35, 'Güzelbahçe'), (35, 'Karabağlar'), (35, 'Karaburun'),
(35, 'Karşıyaka'), (35, 'Kemalpaşa'), (35, 'Kınık'), (35, 'Kiraz'), (35, 'Konak'), (35, 'Menderes'), (35, 'Menemen'), (35, 'Narlıdere'),
(35, 'Ödemiş'), (35, 'Seferihisar'), (35, 'Selçuk'), (35, 'Tire'), (35, 'Torbalı'), (35, 'Urla'),
(38, 'Akkışla'), (38, 'Bünyan'), (38, 'Develi'), (38, 'Felahiye'), (38, 'Hacılar'), (38, 'İncesu'), (38, 'Kocasinan'), (38, 'Melikgazi'),
(38, 'Özvatan'), (38, 'Pınarbaşı'), (38, 'Sarıoğlan'), (38, 'Sarız'), (38, 'Talas'), (38, 'Tomarza'), (38, 'Yahyalı'), (38, 'Yeşilhisar'),
(46, 'Afşin'), (46, 'Andırın'), (46, 'Çağlayancerit'), (46, 'Dulkadiroğlu'), (46, 'Ekinözü'), (46, 'Elbistan'), (46, 'Göksun'), (46, 'Nurhak'),
(46, 'Onikişubat'), (46, 'Pazarcık'), (46, 'Türkoğlu'),
(66, 'Akdağmadeni'), (66, 'Aydıncık'), (66, 'Boğazlıyan'), (66, 'Çandır'), (66, 'Çayıralan'), (66, 'Çekerek'), (66, 'Kadışehri'), (66, 'Merkez'),
(66, 'Saraykent'), (66, 'Sarıkaya'), (66, 'Sorgun'), (66, 'Şefaatli'), (66, 'Yenifakılı'), (66, 'Yerköy');

-- Altı ana kategori. Sıra önemli: ana sayfadaki kartlar bu id'leri kullanır.
-- Resimler unsplash.com sitesinden, ücretsiz kullanım lisanslı. Yönetici panelden değiştirilebilir.
INSERT INTO kategoriler (id, ad, aciklama, resim) VALUES
(1, 'Mobil & Bilişim', 'Telefon, laptop, tablet, masaüstü bilgisayar, ekran ve batarya değişimi.', 'https://images.unsplash.com/photo-1550041473-d296a3a8a18a?auto=format&fit=crop&w=800&q=70'),
(2, 'Beyaz Eşya', 'Çamaşır ve bulaşık makinesi, buzdolabı, fırın, ankastre sistemler.', 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=800&q=70'),
(3, 'Küçük Ev Aletleri', 'Robot/dikey süpürge, kahve makinesi, ütü, mikser ve mutfak robotları.', 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&w=800&q=70'),
(4, 'TV & Ses Sistemleri', 'Televizyon, LED panel değişimi, amfi, projeksiyon ve ses sistemleri.', 'https://images.unsplash.com/photo-1593784991251-92ded75ea290?auto=format&fit=crop&w=800&q=70'),
(5, 'Oyun & Eğlence', 'PlayStation, Xbox, Nintendo, kol tamiri ve ısınma/fan bakımı.', 'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?auto=format&fit=crop&w=800&q=70'),
(6, 'Diğer Elektronikler', 'Güç kaynakları, kart tamirleri ve tüm özel elektronik cihazlar.', 'https://images.unsplash.com/photo-1640955785023-1854685dae05?auto=format&fit=crop&w=800&q=70');

-- Her kategorinin cihaz türleri. İlk sayı kategori numarası.
INSERT INTO cihaz_turleri (kategori_id, ad) VALUES
(1, 'Akıllı Telefon'), (1, 'Laptop / Dizüstü Bilgisayar'), (1, 'Tablet'), (1, 'Masaüstü Bilgisayar'),
(2, 'Çamaşır Makinesi'), (2, 'Bulaşık Makinesi'), (2, 'Buzdolabı'), (2, 'Fırın / Ankastre Set'),
(3, 'Robot / Dikey Süpürge'), (3, 'Kahve Makinesi'), (3, 'Ütü'), (3, 'Mutfak Robotu / Mikser'),
(4, 'Televizyon (LED / OLED)'), (4, 'Ses Sistemi / Amfi'), (4, 'Projeksiyon Cihazı'),
(5, 'PlayStation (PS4 / PS5)'), (5, 'Xbox Konsol'), (5, 'Oyun Kolu (Gamepad)'), (5, 'Nintendo Switch'),
(6, 'Elektronik Kart / Güç Kaynağı'), (6, 'Özel Tasarım / Diğer');

-- Can sadece mobil. Elif mobil ve diğer elektronik. Murat mobil, beyaz eşya ve küçük ev aletleri.
INSERT INTO tamirci_yetkinlikleri (tamirci_id, kategori_id) VALUES
(4, 1),
(5, 1), (5, 6),
(6, 1), (6, 2), (6, 3);

-- Örnek hizmetler ve kart resimleri.
INSERT INTO hizmetler (ad, kategori_id, aciklama, baslangic_fiyat, resim, aktif) VALUES
('Telefon ekran değişimi', 1, 'Kırık veya dokunmayan ekranın değişimi. Fiyat modele göre artar.', 1500, 'https://images.unsplash.com/photo-1539331586018-346b53b2aaa4?auto=format&fit=crop&w=800&q=70', 1),
('Telefon batarya değişimi', 1, 'Çabuk biten bataryanın değişimi.', 800, 'https://images.unsplash.com/photo-1746005514011-ea00280f3b6e?auto=format&fit=crop&w=800&q=70', 1),
('Bilgisayar formatı', 1, 'Yedek alındıktan sonra sistem kurulumu ve temel programlar.', 500, 'https://images.unsplash.com/photo-1721333089073-215a56fd710c?auto=format&fit=crop&w=800&q=70', 1),
('Bilgisayar fan temizliği', 1, 'Isınan bilgisayarda fan ve termal macun bakımı.', 400, 'https://images.unsplash.com/photo-1769085794153-54fd3d57efaf?auto=format&fit=crop&w=800&q=70', 1),
('Çamaşır makinesi pompa değişimi', 2, 'Su boşaltmayan makinede pompa ve filtre tamiri.', 900, 'https://images.unsplash.com/photo-1622473590925-e3616c0a41bf?auto=format&fit=crop&w=800&q=70', 1),
('Süpürge tetik ve batarya tamiri', 3, 'Dikey ve robot süpürgelerde tetik, batarya ve motor bakımı.', 650, 'https://images.unsplash.com/photo-1722710070534-e31f0290d8de?auto=format&fit=crop&w=800&q=70', 1),
('TV panel ve LED tamiri', 4, 'Görüntü gelmeyen televizyonda LED şerit ve panel tamiri.', 1200, 'https://images.unsplash.com/photo-1595935736128-db1f0a261263?auto=format&fit=crop&w=800&q=70', 1),
('Konsol fan bakımı', 5, 'Isınan PlayStation ve Xbox konsollarında temizlik ve macun.', 600, 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?auto=format&fit=crop&w=800&q=70', 1);

-- Hizmetlerin cihaz türleri. Numaralar yukarıdaki sıraya göre:
-- cihaz: 1 Akıllı Telefon, 2 Laptop, 3 Tablet, 4 Masaüstü, 5 Çamaşır Makinesi, 9 Süpürge, 13 Televizyon, 16 PlayStation, 17 Xbox
-- hizmet: 1 ekran, 2 batarya, 3 format, 4 fan temizliği, 5 pompa, 6 süpürge, 7 TV, 8 konsol
INSERT INTO hizmet_cihazlari (hizmet_id, cihaz_turu_id) VALUES
(1, 1), (1, 3),
(2, 1), (2, 3),
(3, 2), (3, 4),
(4, 2), (4, 4),
(5, 5),
(6, 9),
(7, 13),
(8, 16), (8, 17);

-- Nasıl çalışır kartları.
INSERT INTO adimlar (sira, baslik, aciklama, resim) VALUES
(1, 'Talep Oluştur', 'Cihaz türü, marka, model ve arıza detaylarını girerek fotoğraf ekle.', 'https://images.unsplash.com/photo-1512428559087-560fa5ceab42?auto=format&fit=crop&w=800&q=70'),
(2, 'Teklifleri Al', 'Konumuna en yakın onaylı tamircilerden fiyat ve süre teklifleri gelsin.', 'https://images.unsplash.com/photo-1648201637025-1c77b9be3013?auto=format&fit=crop&w=800&q=70'),
(3, 'Tamirciyi Seç', 'Fiyat, puan ve uzmanlıkları karşılaştırarak en uygun tamirciyi seç.', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=800&q=70'),
(4, 'Mesajlaş ve Tamir Et', 'Uygulama içi mesajlaşarak cihazını teslim et, iş bitince puanla!', 'https://images.unsplash.com/photo-1721332154191-ba5f1534266e?auto=format&fit=crop&w=800&q=70');

-- Ana sayfa slayt resimleri.
INSERT INTO slaytlar (sira, resim, aciklama) VALUES
(1, 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1600&q=80', 'Tamir görseli 1'),
(2, 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?auto=format&fit=crop&w=1600&q=80', 'Tamir görseli 2'),
(3, 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?auto=format&fit=crop&w=1600&q=80', 'Tamir görseli 3');

-- Ayşe'nin telefon talebine iki teklif geldi. Mehmet'in laptop talebinde Can seçildi ve randevu kuruldu.
-- Ayşe'nin süpürge talebi tamamlandi ve 5 puan verildi.
INSERT INTO talepler (musteri_id, hizmet_id, tamirci_id, kategori_id, cihaz_turu, marka, model, ariza, resim, sehir, ilce, durum, takip_kodu, puan, olusturma_tarihi) VALUES
(2, 1, NULL, 1, 'Akıllı Telefon', 'Apple', 'iPhone 13', 'Cihaz yere düştü, camı tamamen çatladı ve dokunmatik çalışmıyor. Orijinal ekran değişimi istiyorum.', '', 'İstanbul', 'Kadıköy', 'beklemede', 'TB1001', 0, '2026-10-10 15:00:00'),
(3, 4, 4, 1, 'Laptop / Dizüstü Bilgisayar', 'Lenovo', 'ThinkPad', 'Çok ısınıyor ve kendi kendine kapanıyor.', '', 'İstanbul', 'Ümraniye', 'randevu_kuruldu', 'TB1002', 0, '2026-10-11 16:30:00'),
(2, 6, 6, 3, 'Robot / Dikey Süpürge', 'Dyson', 'V15', 'Süpürgenin tetik mekanizması kırıldı, cihaz çalışmıyor.', '', 'İstanbul', 'Kadıköy', 'tamamlandi', 'TB1003', 5, '2026-09-28 10:00:00');

-- Örnek teklifler. TB1001'e iki teklif geldi.
INSERT INTO teklifler (talep_id, tamirci_id, fiyat, sure, aciklama, durum, tarih) VALUES
(1, 4, 2500, '1 iş günü', 'Orijinal yedek parça, 6 ay garanti.', 'bekliyor', '2026-10-10 17:00:00'),
(1, 6, 2200, 'Aynı gün', 'Muadil ekran, 3 ay garanti.', 'bekliyor', '2026-10-10 18:30:00'),
(2, 4, 600, '2 saat', 'Fan temizliği ve termal macun.', 'kabul', '2026-10-11 18:00:00'),
(3, 6, 850, '2 saatte teslim', 'Güçlendirilmiş metal tetik takılacak.', 'kabul', '2026-09-28 12:00:00');

-- Ayşe ile Can arasındaki örnek sohbet.
INSERT INTO sohbet_mesajlari (talep_id, tamirci_id, gonderen_id, mesaj, tarih) VALUES
(1, 4, 4, 'Merhaba, teklifimizi ilettik. Orijinal ekran değişimi için yardımcı olabiliriz.', '2026-10-10 17:05:00'),
(1, 4, 2, 'Merhaba, ekranı bugün bırakırsam ne zaman teslim alabilirim?', '2026-10-10 17:20:00');

-- Örnek randevular.
INSERT INTO randevular (talep_id, tamirci_id, randevu_tarihi, randevu_saati, durum, not_metni) VALUES
(2, 4, '2026-10-20', '14:00', 'bekliyor', 'Şarj aletini de yanında getirsin.'),
(3, 6, '2026-09-29', '11:00', 'tamamlandi', '');

-- Örnek iletişim mesajı.
INSERT INTO mesajlar (ad, eposta, konu, mesaj, okundu, tarih) VALUES
('Ali Vural', 'ali@ornek.com', 'Fiyat sorusu', 'iPhone 11 ekran değişimi ne kadar sürer?', 0, '2026-10-12 10:15:00');

-- Sitedeki kısa yazılar. Yönetici "Site ayarları" sayfasında hepsini değiştirir.
-- Yeni bir yazı gerekirse buraya bir satır eklenir, sayfada ayar_getir ile okunur.
INSERT INTO site_ayarlari (anahtar, deger, etiket) VALUES
('site_adi', 'TamirBul', 'Site adı (logo)'),
('telefon', '0500 000 00 00', 'Telefon'),
('eposta', 'info@tamirbul.test', 'E-posta'),
('adres', 'Örnek Mahallesi, Okul Sokak No: 1', 'Adres'),
('calisma_saatleri', 'Hafta içi 09:00 - 18:00', 'Çalışma saatleri'),
('anasayfa_baslik', 'Teknolojik Cihazınız veya Ev Aletiniz mi Bozuldu?', 'Ana sayfa büyük başlık'),
('anasayfa_aciklama', 'Cihaz türünüzü, markasını ve arızasını seçin. Yakınınızdaki onaylı tamircilerden hızlıca teklif alın, karşılaştırın ve seçin!', 'Ana sayfa açıklama'),
('anasayfa_buton', 'Hemen Tamir Talebi Oluştur', 'Ana sayfa birinci düğme yazısı'),
('anasayfa_buton2', 'Talebimi Sorgula', 'Ana sayfa ikinci düğme yazısı'),
('slayt_suresi', '5', 'Slayt değişme süresi (saniye)'),
('adimlar_baslik', 'Nasıl Çalışır?', 'Adımlar bölümü başlığı'),
('kategoriler_baslik', 'Hangi Cihazlar İçin Hizmet Veriyoruz?', 'Kategoriler bölümü başlığı'),
('hizmetler_baslik', 'Popüler Hizmetler', 'Ana sayfa hizmetler bölümü başlığı'),
('anasayfa_hizmet_sayisi', '3', 'Ana sayfada gösterilecek hizmet sayısı'),
('hizmetler_sayfa_aciklama', 'Tamir hizmetleri ve başlangıç fiyatları. Son fiyatı tamirciler talebinize teklif olarak verir.', 'Hizmetler sayfası açıklaması'),
('tamirciler_sayfa_aciklama', 'Yönetici onayından geçmiş tamirciler ve teknik servisler. Puanlar müşterilerin verdiği değerlendirmelerden hesaplanır.', 'Tamirciler sayfası açıklaması'),
('talep_formu_aciklama', 'Cihaz bilgilerinizi ne kadar detaylı girerseniz, tamircilerden o kadar doğru fiyat teklifi alırsınız.', 'Talep formu açıklaması'),
('altlik_aciklama', 'Arızalı cihazı olan müşteri ile bölgesindeki onaylı tamirciyi buluşturur.', 'Sayfa altı açıklaması'),
('altlik_not', 'Bilgisayar programcılığı dönem projesi. İçerikler yönetim panelinden değiştirilir.', 'Sayfa altı en alt not');

-- Hakkımızda yazısı.
INSERT INTO sayfa_icerikleri (sayfa, baslik, icerik) VALUES
('hakkimizda', 'Hakkımızda', 'TamirBul, bilgisayar programcılığı dönem projesidir. Amaç arızalı cihazı olan müşteri ile o bölgedeki tamirciyi aynı sayfada buluşturmaktır.\n\nMüşteri talebini açar, bölgedeki onaylı tamirciler fiyat teklifi verir, müşteri en uygun teklifi seçer. Sitedeki hizmetler, kategoriler, sayfa yazıları ve iletişim bilgileri veritabanından gelir. Yönetici bu bilgileri panelden değiştirir.');
