<?php
/*
SAYFA: Hakkimizda
DOSYA: hakkimizda.php
KIM: Kisi 1 gorunum, Kisi 4 metni panelden degistirir.

BU SAYFADA BUNLAR OLACAK:
- Baslik ve uzun metin
- Ikisi de sayfa_icerikleri tablosundan gelir (sayfa = hakkimizda)
- Yonetim: admin/sayfa-icerik.php
*/

$kok = "";
$sayfa_basligi = "Hakkımızda";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$baslik = "Hakkımızda";
$icerik = "Hakkımızda yazısı veritabanı açılınca burada görünecek.";

if ($veritabani_hazir) {
    $sorgu = "SELECT * FROM sayfa_icerikleri WHERE sayfa = 'hakkimizda' LIMIT 1";
    $sonuc = mysqli_query($baglanti, $sorgu);
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $satir = mysqli_fetch_assoc($sonuc);
        $baslik = $satir["baslik"];
        $icerik = $satir["icerik"];
        $sayfa_basligi = $baslik;
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1><?php echo temiz_yaz($baslik); ?></h1>
        <!-- nl2br: metindeki Enter tuslarini ekranda satir basi yapar. -->
        <div class="bilgi-kutu">
            <?php echo nl2br(temiz_yaz($icerik)); ?>
        </div>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
