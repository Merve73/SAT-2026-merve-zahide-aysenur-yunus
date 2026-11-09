<?php
/*
DOSYA: ortak/hazirla.php
KİM: Kişi 3
NE YAPAR: Her sayfanın en başında çağrılan tek dosya. Altı iş yapar:
1) $kok değişkenini hesaplar (ana klasörde "", alt klasörde "../")
2) Veritabanına bağlanır (ortak/baglanti.php)
3) Oturumu açar ve yardımcı fonksiyonları yükler (ortak/guvenlik.php, ortak/yazi-stili.php, ortak/ikonlar.php, ortak/islem-kaydi.php)
4) $hata ve $tamam yazılarını boş başlatır (ortak/mesajlar.php bunları gösterir)
5) Tarayıcıya güvenlik başlıklarını gönderir
6) Gönderilen formun güvenlik kodunu (CSRF) kontrol eder. Kod yanlışsa form çalışmaz.
Ana klasördeki sayfa:  include "ortak/hazirla.php";
Alt klasördeki sayfa:  include "../ortak/hazirla.php";
*/

// HARİTA: Sayfa hazırlama ($kok, bağlantı, oturum)
// Sayfanın bulunduğu klasörde "ortak" klasörü varsa ana klasördeyiz.
if (is_dir("ortak")) {
    // Ana klasör: dosya yollarının önüne bir şey eklenmez. Örnek: css/stil.css
    $kok = "";
} else {
    // Alt klasör (admin, musteri, tamirci): bir üst klasöre çıkılır. Örnek: ../css/stil.css
    $kok = "../";
}
// __DIR__: bu dosyanın klasörü (ortak). Veritabanına bağlanırız.
include __DIR__ . "/baglanti.php";
// Oturumu açar, fonksiyonları yükleriz.
include __DIR__ . "/guvenlik.php";
// Yazıların renk, boyut gibi stil fonksiyonlarını yükleriz.
include __DIR__ . "/yazi-stili.php";
// Yönetimden değişen site yazılarının listesini ve yazi_getir fonksiyonunu yükleriz.
include __DIR__ . "/site-yazilari.php";
// Kategori, adım ve durum ikonlarını seçen fonksiyonları yükleriz.
include __DIR__ . "/ikonlar.php";
// İşlem kaydı (log) fonksiyonunu yükleriz: log_yaz.
include __DIR__ . "/islem-kaydi.php";
// Hata yazısı. Sayfa doldurursa kırmızı kutuda görünür.
$hata = "";
// Başarı yazısı. Sayfa doldurursa yeşil kutuda görünür.
$tamam = "";
// Önceki sayfa bir uyarı bıraktıysa (örnek: güvenlik kodu tutmadı) onu gösteririz.
if (isset($_SESSION["sonraki_uyari"])) {
    // Uyarıyı hata kutusuna koyarız.
    $hata = $_SESSION["sonraki_uyari"];
    // Bir kez gösterilsin diye oturumdan sileriz.
    unset($_SESSION["sonraki_uyari"]);
}

// HARİTA: Güvenlik başlıkları (header)
// X-Frame-Options: sitemiz başka bir sitenin içinde (iframe) açılamaz. Tıklama tuzağını önler.
header("X-Frame-Options: SAMEORIGIN");
// nosniff: tarayıcı dosya türünü tahmin etmeye çalışmaz. Resim diye yüklenen dosya kod gibi çalışamaz.
header("X-Content-Type-Options: nosniff");
// Referrer-Policy: başka siteye giderken adres çubuğundaki bilgilerimiz gönderilmez.
header("Referrer-Policy: same-origin");
// Content-Security-Policy: sayfaya eklenti nesnesi (object) gömülemez, sayfanın temel adresi (base) değiştirilemez,
// sitemiz başka sitenin içinde açılamaz. Kendi yazı, stil ve resimlerimizi etkilemez.
header("Content-Security-Policy: object-src 'none'; base-uri 'self'; frame-ancestors 'self'");

// HARİTA: Form güvenlik kodu kontrolü (CSRF)
// Form gönderildiyse ve güvenlik kodu tutmuyorsa:
if (form_geldi_mi() && !guvenlik_kodu_dogru_mu()) {
    // Reddedilen formu kayda yazarız (başka siteden gelen saldırı denemesi olabilir).
    log_yaz("Form reddedildi", "Güvenlik kodu tutmadı: " . $_SERVER["REQUEST_URI"]);
    // Uyarıyı bir sonraki sayfada göstermek için oturuma yazarız.
    $_SESSION["sonraki_uyari"] = "Form gönderilemedi. Sayfa çok uzun süre açık kalmış ya da dosya çok büyük olabilir. Lütfen tekrar deneyin.";
    // Aynı sayfayı form gönderilmeden yeniden açarız. REQUEST_URI: şu anki adres.
    yonlendir($_SERVER["REQUEST_URI"]);
}
