<?php
/*
SAYFA: Talep detayı
DOSYA: musteri/talep-detay.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Talebin bütün bilgileri ve yüklenen fotoğraf
- Teklif bekliyorsa teklifler ve kabul düğmesi (teklif-kartlari.php)
- Tamirci seçildiyse tamirci, randevu, puan (is-durumu.php)
- Sadece talebin sahibi açabilir. Adres: talep-detay.php?id=4
*/

// Sekme başlığı.
$sayfa_basligi = "Talep detayı";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// Adresteki talep numarası.
$id = gelen_sayi("id");
// Müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// HARİTA: Talep detayı ve fotoğraf gösterme (müşteri)
// Talep. musteri_id koşulu sayesinde başkasının talebi açılmaz.
$talep = tek_satir("SELECT talepler.*, kategoriler.ad AS kategori_adi FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id WHERE talepler.id = $id AND musteri_id = $musteri_id");
// Talep yeni açıldıysa (talep-olustur.php ?yeni=1 ile gönderir):
if ($talep && isset($_GET["yeni"])) {
    // Yeşil kutuda takip kodu görünür.
    $tamam = "Talebiniz yayınlandı. Takip kodunuz: " . $talep["takip_kodu"];
}

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
            <h2>Talep Detayları ve Durumu</h2>
            <!-- Açıklama. -->
            <p>Arıza bilgileri, fotoğraf ve gelen teklifler.</p>
        </div>
        <!-- Yeni talepte takip kodu kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Talep bulunduysa: -->
        <?php if ($talep) { ?>
            <!-- Talep kartı. -->
            <div class="request-item-card">
                <!-- Kart üstü. -->
                <div class="request-item-header">
                    <!-- Sol. -->
                    <div class="request-info">
                        <!-- Durum etiketi. -->
                        <span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span>
                        <!-- Cihaz. -->
                        <h3><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></h3>
                        <!-- Kod ve tarih. -->
                        <p class="request-date">Takip Kodu: <?php echo temiz_yaz($talep["takip_kodu"]); ?> | Tarih: <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?></p>
                    </div>
                    <!-- Sağ: konum. -->
                    <span class="location-badge">Konum: <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?></span>
                </div>
                <!-- Solda bilgiler, sağda fotoğraf. -->
                <div class="request-details-grid">
                    <!-- Bilgiler. -->
                    <div>
                        <!-- Başlık. -->
                        <h4>Arıza ve Cihaz Bilgileri</h4>
                        <!-- Liste. -->
                        <ul class="detail-list">
                            <!-- Kategori. -->
                            <li><strong>Ana Kategori:</strong> <?php echo temiz_yaz($talep["kategori_adi"]); ?></li>
                            <!-- Cihaz türü. -->
                            <li><strong>Cihaz Türü:</strong> <?php echo temiz_yaz($talep["cihaz_turu"]); ?></li>
                            <!-- Marka / model. -->
                            <li><strong>Marka / Model:</strong> <?php echo temiz_yaz($talep["marka"] . " / " . $talep["model"]); ?></li>
                            <!-- Arıza. -->
                            <li><strong>Arıza:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></li>
                        </ul>
                    </div>
                    <!-- Fotoğraf. -->
                    <div>
                        <!-- Başlık. -->
                        <h4>Cihaz / Hasar Görseli</h4>
                        <!-- Çerçeve. -->
                        <div class="image-preview-frame">
                            <!-- Fotoğraf varsa resim, yoksa yazı. -->
                            <?php if ($talep["resim"] != "") { ?>
                                <!-- Fotoğraf. -->
                                <img class="buyutulebilir" src="<?php echo temiz_yaz(resim_adresi($talep["resim"])); ?>" alt="Arıza fotoğrafı" title="Büyütmek için tıklayın">
                            <?php } else { ?>
                                <!-- Bilgi. -->
                                <span>Fotoğraf eklenmedi</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- Teklif bekliyorsa teklifler, değilse seçilen tamirci. -->
                <?php if ($talep["durum"] == "beklemede") { ?>
                    <!-- Teklifler. -->
                    <?php include "teklif-kartlari.php"; ?>
                <?php } else { ?>
                    <!-- Seçilen tamirci. -->
                    <?php include "is-durumu.php"; ?>
                <?php } ?>
                <!-- Alt düğmeler. -->
                <div class="detail-actions">
                    <!-- Panele dön. -->
                    <a class="btn btn-outline" href="panel.php">← Panele Geri Dön</a>
                    <!-- Teklif bekliyorsa düzenle. -->
                    <?php if ($talep["durum"] == "beklemede") { ?>
                        <!-- Düzenle. -->
                        <a class="btn btn-secondary" href="talep-duzenle.php?id=<?php echo $id; ?>">Talebi Düzenle</a>
                    <?php } ?>
                </div>
            </div>
        <!-- Bulunmadıysa: -->
        <?php } else { ?>
            <!-- Bilgi. -->
            <p class="bos-liste">Talep bulunamadı. <a href="taleplerim.php">Taleplerime dön</a>.</p>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
