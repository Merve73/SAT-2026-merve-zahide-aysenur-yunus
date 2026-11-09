<?php
/*
DOSYA: ortak/islem-kaydi.php
KİM: Kişi 4
NE YAPAR: Sitede yapılan önemli işleri islem_kayitlari tablosuna yazar (log kaydı).
Kim, ne zaman, hangi işi yaptı, hangi bilgisayardan (IP) yaptı görünür.
Yönetici admin/kayitlar.php sayfasından okur. ortak/hazirla.php bu dosyayı her sayfada yükler.

KULLANIM (işin başarıyla bittiği satırdan hemen önce):
  log_yaz("Teklif verildi", "Talep TB1004, 1.500 TL");

Neler kaydedilir: giriş, hatalı giriş, çıkış, kayıt olma, talep, teklif, randevu, puan,
iletişim mesajı, profil ve şifre değişikliği, yöneticinin bütün değişiklikleri, reddedilen formlar.
ŞİFRE GİBİ GİZLİ BİLGİ ASLA KAYDA YAZILMAZ.
*/

// HARİTA: İşlem kaydı tablosu (log)
// Tablo yoksa oluşturur. baslat.bat eski veritabanını yeniden yüklemediği için gerekir.
function log_tablosunu_hazirla() {
    // IF NOT EXISTS: tablo zaten varsa hiçbir şey yapmaz.
    sorgula("CREATE TABLE IF NOT EXISTS islem_kayitlari (id INT AUTO_INCREMENT PRIMARY KEY, kullanici_id INT NULL, kisi VARCHAR(120) NOT NULL DEFAULT '', rol VARCHAR(20) NOT NULL DEFAULT '', islem VARCHAR(100) NOT NULL, ayrinti VARCHAR(255) NOT NULL DEFAULT '', ip VARCHAR(45) NOT NULL DEFAULT '', tarih DATETIME NOT NULL)");
}

// HARİTA: Log kaydı yazma (log_yaz)
// Bir işi kayda yazar. $islem kısa ad (örnek: "Giriş yapıldı"), $ayrinti ek bilgi (örnek: "ayse@ornek.com").
// Kimin yaptığı oturumdan okunur. Giriş yapılmadıysa "Ziyaretçi" yazılır.
function log_yaz($islem, $ayrinti) {
    // Tablo yoksa oluştururuz.
    log_tablosunu_hazirla();
    // Giriş yapan kişinin numarası. Giriş yoksa NULL (boş).
    $kullanici_id = null;
    // Kişinin adı. Giriş yoksa "Ziyaretçi".
    $kisi = "Ziyaretçi";
    // Rolü. Giriş yoksa boş.
    $rol = "";
    // Giriş yapıldıysa oturumdan okuruz:
    if (isset($_SESSION["kullanici_id"])) {
        // Numara.
        $kullanici_id = (int) $_SESSION["kullanici_id"];
        // Ad ve soyad.
        $kisi = $_SESSION["ad"] . " " . $_SESSION["soyad"];
        // Rol.
        $rol = $_SESSION["rol"];
    }
    // İsteği gönderen bilgisayarın adresi (IP). Örnek: 127.0.0.1
    $ip = "";
    // Adres varsa:
    if (isset($_SERVER["REMOTE_ADDR"])) {
        // Adresi alırız.
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    // mb_substr: ayrıntı sütuna sığsın diye en fazla 255 harf alırız (Türkçe harf bozulmaz).
    $ayrinti = mb_substr($ayrinti, 0, 255, "UTF-8");
    // Kaydı tabloya ekleriz.
    kaydet("islem_kayitlari", array("kullanici_id" => $kullanici_id, "kisi" => $kisi, "rol" => $rol, "islem" => $islem, "ayrinti" => $ayrinti, "ip" => $ip, "tarih" => simdi()));
}
