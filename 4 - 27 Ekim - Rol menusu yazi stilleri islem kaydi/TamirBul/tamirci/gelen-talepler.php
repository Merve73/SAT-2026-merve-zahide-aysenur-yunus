<?php
/*
SAYFA: Uygun talepler
DOSYA: tamirci/gelen-talepler.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Tamircinin ilindeki ve yetkinlik alanındaki, teklif bekleyen talepler
- Teklif verilmediyse "Hemen Fiyat Teklifi Ver", verildiyse verilen fiyat
- Hangi talebin listeye girdiğini uygun-talepler-sorgu.php anlatır
*/

// Sekme başlığı.
$sayfa_basligi = "Uygun talepler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// $uygun_talepler dizisini doldurur.
include "uygun-talepler-sorgu.php";

// Üst menü.
include "../ortak/baslik.php";
// Tamircinin onay durumu ($onayli) ve onay uyarısı. Menü artık üstteki açılır menüde.
include "onay-durumu.php";
?>

<!-- Sayfa bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Bölgenizdeki Uygun Arıza Talepleri</h2>
            <!-- Açıklama. -->
            <p>Uzmanlık alanınıza ve ilinize uygun, teklif bekleyen talepler.</p>
        </div>
        <!-- Liste. -->
        <div class="requests-list">
            <!-- Onaysızsa uyarı. -->
            <?php if ($onayli != 1) { ?>
                <!-- Uyarı. -->
                <p class="bos-liste">Talepleri görmek için yönetici onayı gerekiyor.</p>
            <!-- Onaylıysa talepler. -->
            <?php } else { ?>
                <!-- Her talep bir kart. -->
                <?php foreach ($uygun_talepler as $talep) { ?>
                    <!-- Kart. -->
                    <div class="request-item">
                        <!-- Üst satır. -->
                        <div class="request-header">
                            <!-- Cihaz. -->
                            <span class="request-title"><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></span>
                            <!-- Teklif verdiysem fiyatım, vermediysem "bekliyor". -->
                            <?php if ($talep["benim_fiyat"] !== null) { ?>
                                <!-- Yeşil etiket. -->
                                <span class="badge badge-success">Teklif verildi: <?php echo fiyat_yaz($talep["benim_fiyat"]); ?></span>
                            <?php } else { ?>
                                <!-- Sarı etiket. -->
                                <span class="badge badge-warning">Teklif Bekliyor</span>
                            <?php } ?>
                        </div>
                        <!-- Kategori, müşteri, konum, tarih. -->
                        <div class="request-meta"><?php echo temiz_yaz($talep["kategori_adi"]); ?> | Müşteri: <?php echo temiz_yaz(kisa_ad($talep["musteri_ad"], $talep["musteri_soyad"])); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?> | <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?></div>
                        <!-- Arıza. -->
                        <div class="request-desc"><?php echo temiz_yaz($talep["ariza"]); ?></div>
                        <!-- Düğme. -->
                        <div class="offer-actions">
                            <!-- Teklif yoksa teklif ver, varsa mesaj yaz. -->
                            <?php if ($talep["benim_fiyat"] === null) { ?>
                                <!-- Teklif ver. -->
                                <a class="btn btn-sm btn-secondary" href="teklif-ver.php?talep_id=<?php echo (int) $talep["id"]; ?>">Hemen Fiyat Teklifi Ver</a>
                            <?php } else { ?>
                                <!-- Mesaj yaz. -->
                                <a class="btn btn-sm btn-outline" href="../sohbet.php?talep=<?php echo (int) $talep["id"]; ?>">Müşteriye Mesaj Yaz</a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <!-- Hiç yoksa bilgi. -->
                <?php if (count($uygun_talepler) == 0) { ?>
                    <!-- Bilgi. -->
                    <p class="bos-liste">Şu an ilinizde ve yetkinlik alanınızda teklif bekleyen talep yok.</p>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
