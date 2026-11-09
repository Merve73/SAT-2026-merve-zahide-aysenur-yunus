<?php
/*
SAYFA: Hizmet detayi
DOSYA: hizmet-detay.php
KIM: Kisi 1

BU SAYFADA BUNLAR OLACAK:
- Hizmetin adi, cihaz turu, aciklamasi, baslangic fiyati
- Bu hizmet icin talep olusturma linki
- id adres cubugundan gelir: hizmet-detay.php?id=3
*/

$kok = "";
$sayfa_basligi = "Hizmet";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

// id sayi olsun diye (int) kullaniriz. Yazi gelirse 0 olur.
$id = 0;
if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
}

$hizmet = null;
if ($veritabani_hazir && $id > 0) {
    // id zaten sayi. Tirnak icinde yazmaya gerek yok.
    $sorgu = "SELECT * FROM hizmetler WHERE id = $id AND aktif = 1 LIMIT 1";
    $sonuc = mysqli_query($baglanti, $sorgu);
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $hizmet = mysqli_fetch_assoc($sonuc);
        $sayfa_basligi = $hizmet["ad"];
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <?php if ($hizmet) { ?>
            <!-- Bu bolum: tek hizmetin bilgisi. -->
            <p class="etiket"><?php echo temiz_yaz(cihaz_adi($hizmet["cihaz_turu"])); ?></p>
            <h1><?php echo temiz_yaz($hizmet["ad"]); ?></h1>
            <p class="fiyat"><?php echo fiyat_yaz($hizmet["baslangic_fiyat"]); ?> başlangıç fiyatı</p>
            <p><?php echo temiz_yaz($hizmet["aciklama"]); ?></p>
            <p>Son fiyat, cihazın marka ve modeline göre tamir sırasında netleşir.</p>
            <a class="buton" href="talep-olustur.php?hizmet_id=<?php echo (int) $hizmet["id"]; ?>">Bu hizmet için talep oluştur</a>
            <a href="hizmetler.php">Listeye dön</a>
        <?php } else { ?>
            <h1>Hizmet bulunamadı</h1>
            <p>Bu numara ile açık bir hizmet yok.</p>
            <a href="hizmetler.php">Hizmetlere dön</a>
        <?php } ?>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
