<?php
/*
SAYFA: Tamirci ozeti
DOSYA: tamirci/panel.php
KIM: Kisi 2

BU SAYFADA BUNLAR OLACAK:
- Hos geldin
- Onay durumu (menu.php icindeki sari kutu)
- Bekleyen talep sayisi
- Bu tamircinin randevu sayisi
- Kisayol linkleri
*/

$kok = "../";
$sayfa_basligi = "Tamirci paneli";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_tamirci();

$benim_id = (int) $_SESSION["kullanici_id"];
$bekleyen = 0;
$randevu_adet = 0;

if ($veritabani_hazir) {
    $sonuc1 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM talepler WHERE durum = 'beklemede'");
    if ($sonuc1) {
        $satir = mysqli_fetch_assoc($sonuc1);
        $bekleyen = $satir["adet"];
    }
    $sonuc2 = mysqli_query($baglanti, "SELECT COUNT(*) AS adet FROM randevular WHERE tamirci_id = $benim_id");
    if ($sonuc2) {
        $satir2 = mysqli_fetch_assoc($sonuc2);
        $randevu_adet = $satir2["adet"];
    }
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Merhaba <?php echo temiz_yaz($_SESSION["ad"]); ?></h1>
        <p class="giris-metni">Bekleyen tamir taleplerini buradan görüp randevu kurarsınız.</p>

        <div class="ozet-alani">
            <article class="ozet">
                <span>Bekleyen talep</span>
                <strong><?php echo (int) $bekleyen; ?></strong>
            </article>
            <article class="ozet">
                <span>Randevularım</span>
                <strong><?php echo (int) $randevu_adet; ?></strong>
            </article>
        </div>

        <p>
            <a class="buton" href="gelen-talepler.php">Taleplere bak</a>
            <a class="buton-ikincil" href="randevularim.php">Randevularım</a>
        </p>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
