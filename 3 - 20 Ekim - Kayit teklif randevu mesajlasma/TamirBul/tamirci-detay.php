<?php
/*
SAYFA: Tamirci detayı
DOSYA: tamirci-detay.php
KİM: Kişi 1

BU SAYFADA BUNLAR OLACAK:
- İşletme, yetkili, il/ilçe, adres, yetkinlikler, puan, tamamlanan iş sayısı, tanıtım
- Sadece onaylı tamirci açılır. Adres: tamirci-detay.php?id=4
*/

// Sekme başlığı.
$sayfa_basligi = "Tamirci";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "ortak/hazirla.php";

// Adresteki numara.
$id = gelen_sayi("id");
// Onaylı tamirciyi okuruz.
$tamirci = tek_satir("SELECT * FROM kullanicilar WHERE id = $id AND rol = 'tamirci' AND onayli = 1");
// Bitirdiği iş sayısı.
$biten_is = sayi_getir("SELECT COUNT(*) FROM talepler WHERE tamirci_id = $id AND durum = 'tamamlandi'");

// Üst menü.
include "ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Tamirci bulunduysa: -->
        <?php if ($tamirci) { ?>
            <!-- Bilgi kartı. -->
            <div class="request-item-card">
                <!-- Kart üstü. -->
                <div class="request-item-header">
                    <!-- Sol: ad bilgileri. -->
                    <div class="request-info">
                        <!-- Onay etiketi. -->
                        <span class="badge badge-success">Onaylı Tamirci</span>
                        <!-- İşletme adı. -->
                        <h3><?php echo temiz_yaz($tamirci["isletme_adi"]); ?></h3>
                        <!-- Yetkili. -->
                        <p class="request-date">Yetkili: <?php echo temiz_yaz($tamirci["ad"] . " " . $tamirci["soyad"]); ?></p>
                    </div>
                    <!-- Sağ: konum. -->
                    <span class="location-badge"><?php echo temiz_yaz($tamirci["sehir"] . " / " . $tamirci["ilce"]); ?></span>
                </div>
                <!-- Ayrıntılar. -->
                <div class="request-item-body">
                    <!-- Puan. -->
                    <p class="rating"><strong>Puan:</strong> <?php echo tamirci_puan_metni($id); ?></p>
                    <!-- Biten iş. -->
                    <p><strong>Tamamlanan iş:</strong> <?php echo $biten_is; ?></p>
                    <!-- Yetkinlikler. -->
                    <p><strong>Yetkinlikler:</strong> <?php echo temiz_yaz(yetkinlik_metni($id)); ?></p>
                    <!-- Adres. -->
                    <p><strong>Adres:</strong> <?php echo temiz_yaz($tamirci["adres"]); ?></p>
                    <!-- Tanıtım yazısı. -->
                    <p><?php echo temiz_yaz($tamirci["hakkinda"]); ?></p>
                </div>
                <!-- Düğmeler. -->
                <div class="detail-actions">
                    <!-- Talep oluştur. -->
                    <a class="btn btn-primary" href="talep-olustur.php">Tamir talebi oluştur</a>
                    <!-- Listeye dön. -->
                    <a class="btn btn-outline" href="tamirciler.php">Listeye dön</a>
                </div>
            </div>
        <!-- Bulunmadıysa: -->
        <?php } else { ?>
            <!-- Bilgi. -->
            <p class="bos-liste">Tamirci bulunamadı. <a href="tamirciler.php">Tamircilere dön</a></p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "ortak/altlik.php";
?>
