<?php
/*
SAYFA: Hakkımızda
DOSYA: hakkimizda.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Başlık ve uzun metin. İkisi de sayfa_icerikleri tablosundan gelir.
- Yönetim: admin/sayfa-icerik.php (yazı ve stil)
*/

// Sekme başlığı.
$sayfa_basligi = "Hakkımızda";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// HARİTA: Hakkımızda yazısı (sayfa_icerikleri tablosu)
// Hakkımızda yazısını okuruz.
$sayfa = tek_satir("SELECT * FROM sayfa_icerikleri WHERE sayfa = 'hakkimizda'");
// Bulunamazsa görünecek yedek yazı.
if (!$sayfa) {
    // Yedek başlık ve metin.
    $sayfa = array("baslik" => "Hakkımızda", "icerik" => "Yazı veritabanı açılınca burada görünecek.");
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. yazi_stili: yönetimde seçilen renk, boyut gibi stil. -->
        <h1<?php echo yazi_stili("sayfa_hakkimizda_baslik"); ?>><?php echo temiz_yaz($sayfa["baslik"]); ?></h1>
        <!-- Metin kutusu. nl2br: Enter tuşlarını satır başı yapar. -->
        <div class="bilgi-kutu"<?php echo yazi_stili("sayfa_hakkimizda_icerik"); ?>><?php echo nl2br(temiz_yaz($sayfa["icerik"])); ?></div>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
