<?php
/*
SAYFA: Hizmetler
DOSYA: hizmetler.php
KIM: Kisi 1

BU SAYFADA BUNLAR OLACAK:
- Kisa aciklama
- Filtre linkleri: hepsi, bilgisayar, telefon, tablet
- Hizmet kartlari: tur, ad, aciklama, baslangic fiyati, detay linki
- Kartlar hizmetler tablosundan gelir. aktif = 0 olanlar gorunmez.

SIRADAKI IS (Kisi 1):
- Arama kutusu ekleyin. Form GET ile gelsin, ad LIKE ile sorgulansin.
- Ornek sorgu hizmet-detay.php dosyasindaki tek kayit okuma ile ayni mantikta.
*/

$kok = "";
$sayfa_basligi = "Hizmetler";

include "ortak/baglanti.php";
include "ortak/guvenlik.php";

// Adres cubugundaki ?tur= degerini okuruz. Yoksa bos kalir, yani hepsi.
$secilen_tur = "";
if (isset($_GET["tur"])) {
    $secilen_tur = $_GET["tur"];
}

$hizmetler = false;
$sorgu_hata = "";

if ($veritabani_hazir) {
    // Sadece bildigimiz uc turden biri geldiyse filtreleriz. Baska yazi gelirse tumunu listeleriz.
    if ($secilen_tur == "bilgisayar" || $secilen_tur == "telefon" || $secilen_tur == "tablet") {
        $tur_temiz = mysqli_real_escape_string($baglanti, $secilen_tur);
        $sorgu = "SELECT * FROM hizmetler WHERE aktif = 1 AND cihaz_turu = '$tur_temiz' ORDER BY id DESC";
    } else {
        $sorgu = "SELECT * FROM hizmetler WHERE aktif = 1 ORDER BY id DESC";
    }

    $hizmetler = mysqli_query($baglanti, $sorgu);
    if (!$hizmetler) {
        $sorgu_hata = mysqli_error($baglanti);
    }
}

include "ortak/baslik.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Hizmetler</h1>
        <p class="giris-metni">Bilgisayar, telefon ve tablet için tamir hizmetleri. Liste veritabanından gelir, fiyatı yönetici değiştirir.</p>

        <!-- Filtre linkleri sayfayi yeniden acar. PHP yukarida tur degerine bakar. -->
        <div class="filtre">
            <a href="hizmetler.php">Hepsi</a>
            <a href="hizmetler.php?tur=bilgisayar">Bilgisayar</a>
            <a href="hizmetler.php?tur=telefon">Telefon</a>
            <a href="hizmetler.php?tur=tablet">Tablet</a>
        </div>

        <?php if ($sorgu_hata != "") { ?>
            <p class="hata">Liste alınamadı: <?php echo temiz_yaz($sorgu_hata); ?></p>
        <?php } ?>

        <div class="kart-alani">
            <?php if ($hizmetler && mysqli_num_rows($hizmetler) > 0) { ?>
                <?php while ($satir = mysqli_fetch_assoc($hizmetler)) { ?>
                    <article class="kart">
                        <p class="etiket"><?php echo temiz_yaz(cihaz_adi($satir["cihaz_turu"])); ?></p>
                        <h2><?php echo temiz_yaz($satir["ad"]); ?></h2>
                        <p><?php echo temiz_yaz($satir["aciklama"]); ?></p>
                        <p class="fiyat"><?php echo fiyat_yaz($satir["baslangic_fiyat"]); ?> başlangıç</p>
                        <a class="buton" href="hizmet-detay.php?id=<?php echo (int) $satir["id"]; ?>">İncele</a>
                    </article>
                <?php } ?>
            <?php } else { ?>
                <p>Bu filtrede görünecek hizmet yok.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php include "ortak/altlik.php"; ?>
