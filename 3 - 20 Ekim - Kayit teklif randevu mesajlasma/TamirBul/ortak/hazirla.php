<?php
/*
DOSYA: ortak/hazirla.php
KİM: Kişi 3
NE YAPAR: Her sayfanın en başında çağrılan tek dosya. Dört iş yapar:
1) $kok değişkenini hesaplar (ana klasörde "", alt klasörde "../")
2) Veritabanına bağlanır (ortak/baglanti.php)
3) Oturumu açar ve yardımcı fonksiyonları yükler (ortak/guvenlik.php)
4) $hata ve $tamam yazılarını boş başlatır (ortak/mesajlar.php bunları gösterir)
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
// Hata yazısı. Sayfa doldurursa kırmızı kutuda görünür.
$hata = "";
// Başarı yazısı. Sayfa doldurursa yeşil kutuda görünür.
$tamam = "";
