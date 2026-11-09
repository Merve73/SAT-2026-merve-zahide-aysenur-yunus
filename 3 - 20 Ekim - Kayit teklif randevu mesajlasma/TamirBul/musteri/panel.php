<?php
/*
SAYFA: Müşteri kontrol paneli
DOSYA: musteri/panel.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Üç sayaç: toplam talep, cevap bekleyen teklif, tamamlanan tamir
- Son 5 talebin kartı
- Teklif bekleyen talepte gelen teklifler (teklif-kartlari.php)
- Tamirci seçilen talepte seçilen tamirci, randevu, puan (is-durumu.php)
*/

// Sekme başlığı.
$sayfa_basligi = "Müşteri paneli";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Bu sayfayı sadece müşteri açar.
sadece_rol("musteri");

// Giriş yapan müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// Puan verildiyse:
if (isset($_GET["mesaj"])) {
    // Teşekkür yazısı.
    $tamam = "Puanınız kaydedildi. Teşekkürler!";
}

// HARİTA: Müşteri paneli sayaçları (COUNT)
// Toplam talep sayısı.
$toplam_talep = sayi_getir("SELECT COUNT(*) FROM talepler WHERE musteri_id = $musteri_id");
// Taleplerime gelen ve daha cevaplamadığım teklifler.
$gelen_teklif = sayi_getir("SELECT COUNT(*) FROM teklifler INNER JOIN talepler ON talepler.id = teklifler.talep_id WHERE talepler.musteri_id = $musteri_id AND teklifler.durum = 'bekliyor'");
// Biten tamirler.
$tamamlanan = sayi_getir("SELECT COUNT(*) FROM talepler WHERE musteri_id = $musteri_id AND durum = 'tamamlandi'");
// Son 5 talep, kategori adıyla.
$talepler = satirlar("SELECT talepler.*, kategoriler.ad AS kategori_adi FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id WHERE musteri_id = $musteri_id ORDER BY talepler.id DESC LIMIT 5");

// Üst menü.
include "../ortak/baslik.php";
// Müşteri menüsü.
include "menu.php";
?>

<!-- Panel bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Hoş geldin. -->
            <h2>Merhaba <?php echo temiz_yaz($_SESSION["ad"]); ?></h2>
            <!-- Açıklama. -->
            <p>Tamir taleplerinizi ve tamircilerden gelen teklifleri buradan takip edin.</p>
        </div>
        <!-- Başarı kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Sayaç kutuları. -->
        <div class="stats-cards-grid">
            <!-- Toplam talep. -->
            <div class="stat-card"><span class="stat-number"><?php echo $toplam_talep; ?></span><span class="stat-label">Toplam Talep</span></div>
            <!-- Cevap bekleyen teklif. -->
            <div class="stat-card highlight"><span class="stat-number"><?php echo $gelen_teklif; ?></span><span class="stat-label">Cevap Bekleyen Teklif</span></div>
            <!-- Tamamlanan. -->
            <div class="stat-card success"><span class="stat-number"><?php echo $tamamlanan; ?></span><span class="stat-label">Tamamlanan Tamir</span></div>
        </div>
        <!-- Düğmeler. -->
        <div class="kart-dugmeleri">
            <!-- Yeni talep. -->
            <a class="btn btn-primary" href="../talep-olustur.php">+ Yeni Talep Oluştur</a>
            <!-- Tüm talepler. -->
            <a class="btn btn-outline" href="taleplerim.php">Tüm Taleplerim</a>
        </div>
        <!-- Bölüm başlığı. -->
        <h3 class="content-title">Son Taleplerim ve Gelen Teklifler</h3>
        <!-- Her talep bir kart. -->
        <?php foreach ($talepler as $talep) { ?>
            <!-- Talep kartı. -->
            <div class="request-item-card">
                <!-- Kart üstü. -->
                <div class="request-item-header">
                    <!-- Sol: bilgiler. -->
                    <div class="request-info">
                        <!-- Durum etiketi. -->
                        <span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span>
                        <!-- Kategori etiketi. -->
                        <span class="badge badge-gri"><?php echo temiz_yaz($talep["kategori_adi"]); ?></span>
                        <!-- Cihaz. -->
                        <h4><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></h4>
                        <!-- Kod, tarih, konum. -->
                        <p class="request-date">Kod: <?php echo temiz_yaz($talep["takip_kodu"]); ?> | <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?></p>
                    </div>
                    <!-- Sağ: düğmeler. -->
                    <div class="kart-dugmeleri">
                        <!-- Detay. -->
                        <a class="btn btn-sm btn-primary" href="talep-detay.php?id=<?php echo (int) $talep["id"]; ?>">Detay</a>
                        <!-- Teklif bekliyorsa düzenle. -->
                        <?php if ($talep["durum"] == "beklemede") { ?>
                            <!-- Düzenle. -->
                            <a class="btn btn-sm btn-outline" href="talep-duzenle.php?id=<?php echo (int) $talep["id"]; ?>">Düzenle</a>
                        <?php } ?>
                    </div>
                </div>
                <!-- Arıza. -->
                <div class="request-item-body"><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></div>
                <!-- Teklif bekliyorsa teklifler, değilse seçilen tamirci. -->
                <?php if ($talep["durum"] == "beklemede") { ?>
                    <!-- Gelen teklifler. -->
                    <?php include "teklif-kartlari.php"; ?>
                <?php } else { ?>
                    <!-- Seçilen tamirci ve randevu. -->
                    <?php include "is-durumu.php"; ?>
                <?php } ?>
            </div>
        <?php } ?>
        <!-- Hiç talep yoksa bilgi. -->
        <?php if (count($talepler) == 0) { ?>
            <!-- Bilgi ve link. -->
            <p class="bos-liste">Henüz talebiniz yok. <a href="../talep-olustur.php">İlk talebinizi oluşturun</a>.</p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
