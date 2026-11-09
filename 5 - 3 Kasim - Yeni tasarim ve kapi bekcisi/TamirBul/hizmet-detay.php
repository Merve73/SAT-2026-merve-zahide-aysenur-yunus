<?php
/*
SAYFA: Hizmet detayı
DOSYA: hizmet-detay.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- Hizmetin adı, kategorisi, açıklaması, başlangıç fiyatı, arka planda resmi
- Bu hizmet için talep oluşturma linki. Adres: hizmet-detay.php?id=3
*/

// Sekme başlığı.
$sayfa_basligi = "Hizmet";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Adresteki hizmet numarası.
$id = gelen_sayi("id");
// Hizmet ve kategorisi. Gizli (aktif = 0) hizmet açılmaz.
$hizmet = tek_satir("SELECT hizmetler.*, kategoriler.ad AS kategori_adi, kategoriler.resim AS kategori_resmi FROM hizmetler INNER JOIN kategoriler ON kategoriler.id = hizmetler.kategori_id WHERE hizmetler.id = $id AND hizmetler.aktif = 1");
// Kartın resmi.
$kart_resmi = "";
// Hizmet bulunduysa:
if ($hizmet) {
    // Sekme başlığı hizmetin adı olsun.
    $sayfa_basligi = $hizmet["ad"];
    // Hizmetin resmi.
    $kart_resmi = $hizmet["resim"];
    // Yoksa kategorinin resmi.
    if ($kart_resmi == "") {
        // Kategori resmi.
        $kart_resmi = $hizmet["kategori_resmi"];
    }
}

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Hizmet bulunduysa: -->
        <?php if ($hizmet) { ?>
            <!-- Arkası resimli büyük kart. -->
            <div class="request-item-card resimli-kart" <?php echo arka_plan($kart_resmi); ?>>
                <!-- Kategori etiketi. -->
                <span class="etiket"><?php echo temiz_yaz($hizmet["kategori_adi"]); ?></span>
                <!-- Hizmet adı. -->
                <h1><?php echo temiz_yaz($hizmet["ad"]); ?></h1>
                <!-- Fiyat. -->
                <p class="fiyat"><?php echo fiyat_yaz($hizmet["baslangic_fiyat"]); ?> başlangıç fiyatı</p>
                <!-- Açıklama. -->
                <p><?php echo temiz_yaz($hizmet["aciklama"]); ?></p>
                <!-- Bilgi. -->
                <p>Son fiyatı talebinizi gören tamirciler teklif olarak verir.</p>
                <!-- Düğmeler. -->
                <div class="detail-actions">
                    <!-- Bu hizmetle talep aç. -->
                    <a class="btn btn-primary" href="talep-olustur.php?hizmet_id=<?php echo (int) $hizmet["id"]; ?>">Bu hizmet için talep oluştur</a>
                    <!-- Listeye dön. -->
                    <a class="btn btn-outline" href="hizmetler.php">Listeye dön</a>
                </div>
            </div>
        <!-- Bulunmadıysa: -->
        <?php } else { ?>
            <!-- Bilgi ve geri linki. -->
            <p class="bos-liste">Hizmet bulunamadı. <a href="hizmetler.php">Hizmetlere dön</a></p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
