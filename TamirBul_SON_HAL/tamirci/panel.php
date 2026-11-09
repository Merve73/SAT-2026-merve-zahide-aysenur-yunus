<?php
/*
SAYFA: Tamirci kontrol paneli
DOSYA: tamirci/panel.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Üç sayaç: bölgedeki talepler, cevap bekleyen teklifler, kabul edilen işler
- Size verilen işler: Randevu kur ve Mesajlar düğmesi
- Açık talepler: her kartın altında teklif formu (kayıt teklif-ver.php dosyasında)
*/

// Sekme başlığı.
$sayfa_basligi = "Tamirci paneli";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// Teklif verildiyse:
if (isset($_GET["mesaj"])) {
    // Başarı yazısı.
    $tamam = "Teklifiniz müşteriye iletildi.";
}

// $uygun_talepler dizisini doldurur.
include "uygun-talepler-sorgu.php";
// Cevap bekleyen tekliflerim.
$aktif_teklif = sayi_getir("SELECT COUNT(*) FROM teklifler WHERE tamirci_id = $benim_id AND durum = 'bekliyor'");
// Kabul edilen tekliflerim.
$kabul_edilen = sayi_getir("SELECT COUNT(*) FROM teklifler WHERE tamirci_id = $benim_id AND durum = 'kabul'");
// Bana verilmiş, bitmemiş işler. Müşterinin telefonu artık görünür.
$islerim = satirlar("SELECT talepler.*, kategoriler.ad AS kategori_adi, kullanicilar.ad AS musteri_ad, kullanicilar.soyad AS musteri_soyad, kullanicilar.telefon AS musteri_telefon, teklifler.fiyat FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id INNER JOIN teklifler ON teklifler.talep_id = talepler.id AND teklifler.durum = 'kabul' WHERE talepler.tamirci_id = $benim_id AND talepler.durum IN ('tamirci_atandi', 'randevu_kuruldu', 'tamirde') ORDER BY talepler.id DESC");

// Üst menü.
include "../ortak/baslik.php";
// Tamircinin onay durumu ($onayli) ve onay uyarısı. Menü artık üstteki açılır menüde.
include "onay-durumu.php";
?>

<!-- Panel bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Tamirci / Servis Kontrol Paneli</h2>
            <!-- Açıklama. -->
            <p>İlinizdeki ve uzmanlık alanınızdaki talepleri inceleyin, teklif verin.</p>
        </div>
        <!-- Başarı kutusu. -->
        <?php include "../ortak/mesajlar.php"; ?>
        <!-- Sayaç kutuları. -->
        <div class="stats-cards-grid">
            <!-- Bölgedeki talepler. -->
            <div class="stat-card"><span class="sayac-ikonu"><?php echo ikon("gelen"); ?></span><span class="stat-number"><?php echo count($uygun_talepler); ?></span><span class="stat-label">Bölgenizdeki Talepler</span></div>
            <!-- Cevap bekleyen teklifler. -->
            <div class="stat-card highlight"><span class="sayac-ikonu"><?php echo ikon("saat"); ?></span><span class="stat-number"><?php echo $aktif_teklif; ?></span><span class="stat-label">Cevap Bekleyen Teklifleriniz</span></div>
            <!-- Kabul edilenler. -->
            <div class="stat-card success"><span class="sayac-ikonu"><?php echo ikon("secim"); ?></span><span class="stat-number"><?php echo $kabul_edilen; ?></span><span class="stat-label">Kabul Edilen Tamirler</span></div>
        </div>

        <!-- Bana verilmiş işler varsa liste. -->
        <?php if (count($islerim) > 0) { ?>
            <!-- Bölüm başlığı. -->
            <h3 class="content-title">Size Verilen İşler</h3>
        <?php } ?>
        <!-- Her iş bir kart. -->
        <?php foreach ($islerim as $is) { ?>
            <!-- İş kartı. -->
            <div class="request-item-card">
                <!-- Kart üstü. -->
                <div class="request-item-header">
                    <!-- Sol. -->
                    <div class="request-info">
                        <!-- Durum etiketi. -->
                        <span class="<?php echo durum_rozeti($is["durum"]); ?>"><?php echo durum_adi($is["durum"]); ?></span>
                        <!-- Cihaz. -->
                        <h4><?php echo temiz_yaz($is["marka"] . " " . $is["model"] . " - " . $is["cihaz_turu"]); ?></h4>
                        <!-- Müşteri, telefon, konum. -->
                        <p class="request-date">Müşteri: <?php echo temiz_yaz($is["musteri_ad"] . " " . $is["musteri_soyad"]); ?> | Tel: <?php echo temiz_yaz($is["musteri_telefon"]); ?> | <?php echo temiz_yaz($is["sehir"] . " / " . $is["ilce"]); ?></p>
                    </div>
                    <!-- Sağ: anlaşılan fiyat. -->
                    <span class="offer-price"><?php echo fiyat_yaz($is["fiyat"]); ?></span>
                </div>
                <!-- Arıza. -->
                <div class="request-item-body"><strong>Arıza:</strong> <?php echo temiz_yaz($is["ariza"]); ?></div>
                <!-- Düğmeler. -->
                <div class="kart-dugmeleri">
                    <!-- Randevu daha kurulmadıysa Randevu Kur, kurulduysa Randevuya Git. -->
                    <?php if ($is["durum"] == "tamirci_atandi") { ?>
                        <!-- Randevu kur. -->
                        <a class="btn btn-sm btn-secondary" href="randevu-ekle.php?talep_id=<?php echo (int) $is["id"]; ?>">Randevu Kur</a>
                    <?php } else { ?>
                        <!-- Randevulara git. -->
                        <a class="btn btn-sm btn-secondary" href="randevularim.php">Randevuya Git</a>
                    <?php } ?>
                    <!-- Sohbet. -->
                    <a class="btn btn-sm btn-outline" href="../sohbet.php?talep=<?php echo (int) $is["id"]; ?>">Mesajlar</a>
                </div>
            </div>
        <?php } ?>

        <!-- Bölüm başlığı. -->
        <h3 class="content-title">Açık Arıza Talepleri (Teklif Bekleyenler)</h3>
        <!-- Onaysızsa uyarı. -->
        <?php if ($onayli != 1) { ?>
            <!-- Uyarı. -->
            <p class="bos-liste">Talepleri görmek ve teklif vermek için yönetici onayı gerekiyor.</p>
        <!-- Onaylıysa talepler. -->
        <?php } else { ?>
            <!-- Her talep bir kart. -->
            <?php foreach ($uygun_talepler as $talep) { ?>
                <!-- Talep kartı. -->
                <div class="request-item-card">
                    <!-- Kart üstü. -->
                    <div class="request-item-header">
                        <!-- Sol. -->
                        <div class="request-info">
                            <!-- Kategori. -->
                            <span class="badge badge-info"><?php echo temiz_yaz($talep["kategori_adi"]); ?></span>
                            <!-- Cihaz. -->
                            <h4><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"] . " - " . $talep["cihaz_turu"]); ?></h4>
                            <!-- Müşterinin kısa adı, konum, tarih. -->
                            <p class="request-date">Müşteri: <?php echo temiz_yaz(kisa_ad($talep["musteri_ad"], $talep["musteri_soyad"])); ?> | <?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?> | <?php echo tarih_yaz($talep["olusturma_tarihi"]); ?></p>
                        </div>
                        <!-- Detay sayfası. -->
                        <a class="btn btn-sm btn-outline" href="teklif-ver.php?talep_id=<?php echo (int) $talep["id"]; ?>">Detay</a>
                    </div>
                    <!-- Arıza. -->
                    <div class="request-item-body"><strong>Müşteri Arıza Açıklaması:</strong> <?php echo temiz_yaz($talep["ariza"]); ?></div>
                    <!-- Daha önce teklif verdiysem fiyatım, vermediysem form. -->
                    <?php if ($talep["benim_fiyat"] !== null) { ?>
                        <!-- Teklif kutusu. -->
                        <div class="offer-form-box">
                            <!-- Verdiğim fiyat. -->
                            <h5>Bu talebe teklif verdiniz: <span class="offer-price"><?php echo fiyat_yaz($talep["benim_fiyat"]); ?></span> (<?php echo temiz_yaz($talep["benim_sure"]); ?>)</h5>
                            <!-- Sohbet. -->
                            <a class="btn btn-sm btn-outline" href="../sohbet.php?talep=<?php echo (int) $talep["id"]; ?>">Müşteriye Mesaj Yaz</a>
                        </div>
                    <?php } else { ?>
                        <!-- Teklif formu kutusu. -->
                        <div class="offer-form-box">
                            <!-- Başlık. -->
                            <h5>Bu Talebe Teklif Ver</h5>
                            <!-- HARİTA: Teklif formu (tamirci paneli) -->
                            <!-- Form teklif-ver.php dosyasına gider. -->
                            <form class="form-row-align" method="post" action="teklif-ver.php">
                                <!-- Gizli güvenlik kodu (CSRF koruması). Formun bizim sitemizden geldiğini gösterir. -->
                                <?php echo guvenlik_alani(); ?>
                                <!-- Talep numarası gizli gider. -->
                                <input type="hidden" name="talep_id" value="<?php echo (int) $talep["id"]; ?>">
                                <!-- Fiyat. -->
                                <div class="form-group"><label>Teklif Fiyatı (TL)</label><input name="fiyat" type="number" min="1" placeholder="Örn: 2500" required></div>
                                <!-- Süre. -->
                                <div class="form-group"><label>Tahmini Süre</label><input name="sure" maxlength="50" type="text" placeholder="Örn: Aynı gün" required></div>
                                <!-- Not. -->
                                <div class="form-group"><label>Not (İsteğe Bağlı)</label><input name="aciklama" type="text" placeholder="Örn: 6 ay garanti"></div>
                                <!-- Gönder. -->
                                <div class="form-group button-group"><button class="btn btn-primary" type="submit">Teklifi Gönder</button></div>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <!-- Hiç talep yoksa bilgi. -->
            <?php if (count($uygun_talepler) == 0) { ?>
                <!-- Bilgi. -->
                <p class="bos-liste">Şu an ilinizde ve yetkinlik alanınızda teklif bekleyen talep yok. Yetkinliklerinizi <a href="profil.php">profilden</a> değiştirebilirsiniz.</p>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
