<?php
/*
SAYFA: Taleplerim
DOSYA: musteri/taleplerim.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Bu müşterinin bütün talepleri: kod, kategori, cihaz, arıza, konum, durum, tarih, teklif sayısı
- Detay düğmesi, teklif bekleyen talepte Düzenle düğmesi
- musteri_id oturumdan okunur, başkasının talebi gelmez

SIRADAKİ İŞ (Kişi 2 ekran, Kişi 3 kayıt):
- Beklemedeki talebe "İptal et" butonu ekleyin. Ayrı bir dosyaya POST etsin. O dosya:
  UPDATE talepler SET durum = 'iptal' WHERE id = ... AND musteri_id = ... AND durum = 'beklemede'
*/

// Sekme başlığı.
$sayfa_basligi = "Taleplerim";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// Müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// Bütün talepler. Alt sorgu her talebin teklif sayısını sayar.
$talepler = satirlar("SELECT talepler.*, kategoriler.ad AS kategori_adi, (SELECT COUNT(*) FROM teklifler WHERE teklifler.talep_id = talepler.id) AS teklif_sayisi FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id WHERE musteri_id = $musteri_id ORDER BY talepler.id DESC");

// Üst menü.
include "../ortak/baslik.php";
?>

<!-- Sayfa bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Tüm Tamir Taleplerim</h2>
            <!-- Açıklama. -->
            <p>Açtığınız bütün aktif ve geçmiş talepler.</p>
        </div>
        <!-- Liste. -->
        <div class="requests-list">
            <!-- Her talep bir satır-kart. -->
            <?php foreach ($talepler as $talep) { ?>
                <!-- Kart. -->
                <div class="request-item">
                    <!-- Üst satır: cihaz ve durum. -->
                    <div class="request-header">
                        <!-- Cihaz. -->
                        <span class="request-title"><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></span>
                        <!-- Durum etiketi. -->
                        <span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span>
                    </div>
                    <!-- Kod, kategori, tarih, konum, teklif sayısı. -->
                    <div class="request-meta">Kod: <?php echo temiz_yaz($talep["takip_kodu"]); ?> | <?php echo temiz_yaz($talep["kategori_adi"]); ?> | <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?> | Gelen teklif: <?php echo (int) $talep["teklif_sayisi"]; ?></div>
                    <!-- Arıza. -->
                    <div class="request-desc"><?php echo temiz_yaz($talep["ariza"]); ?></div>
                    <!-- Düğmeler. -->
                    <div class="offer-actions">
                        <!-- Detay. -->
                        <a class="btn btn-sm btn-primary" href="talep-detay.php?id=<?php echo (int) $talep["id"]; ?>">Detayları Gör & Teklifleri İncele</a>
                        <!-- Teklif bekliyorsa düzenle. -->
                        <?php if ($talep["durum"] == "beklemede") { ?>
                            <!-- Düzenle. -->
                            <a class="btn btn-sm btn-outline" href="talep-duzenle.php?id=<?php echo (int) $talep["id"]; ?>">Düzenle</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <!-- Hiç talep yoksa bilgi. -->
            <?php if (count($talepler) == 0) { ?>
                <!-- Bilgi. -->
                <p class="bos-liste">Henüz talep yok. <a href="../talep-olustur.php">Yeni talep açın</a>.</p>
            <?php } ?>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
