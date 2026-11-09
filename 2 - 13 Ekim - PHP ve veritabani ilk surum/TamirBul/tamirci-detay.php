<?php
/*
SAYFA: Tamirci detayi
DOSYA: tamirci-detay.php
KIM: Kisi 1

BU SAYFADA BUNLAR OLACAK:
- Ad soyad, sehir, uzmanlik, hakkinda metni
- Talep olusturma linki
- Sadece rol = tamirci ve onayli = 1 olan kayit acilir
*/

$kok = "";
$sayfa_basligi = "Tamirci";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

$id = 0;
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
}

$tamirci = null;
if ($veritabani_hazir && $id > 0) {
    $sorgu = "SELECT * FROM kullanicilar WHERE id = $id AND rol = 'tamirci' AND onayli = 1 LIMIT 1";
    $sonuc = mysqli_query($baglanti, $sorgu);
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $tamirci = mysqli_fetch_assoc($sonuc);
        $sayfa_basligi = $tamirci["ad"] . " " . $tamirci["soyad"];
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <?php if ($tamirci) { ?>
            <p class="etiket"><?php echo temiz_yaz(cihaz_adi($tamirci["uzmanlik"])); ?></p>
            <h1><?php echo temiz_yaz($tamirci["ad"] . " " . $tamirci["soyad"]); ?></h1>
            <p><?php echo temiz_yaz($tamirci["sehir"]); ?></p>
            <p><?php echo temiz_yaz($tamirci["hakkinda"]); ?></p>
            <a class="buton" href="talep-olustur.php">Tamir talebi oluştur</a>
            <a href="tamirciler.php">Listeye dön</a>
        <?php } else { ?>
            <h1>Tamirci bulunamadı</h1>
            <p>Bu kişi onaylı tamirci listesinde yok.</p>
            <a href="tamirciler.php">Tamircilere dön</a>
        <?php } ?>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
