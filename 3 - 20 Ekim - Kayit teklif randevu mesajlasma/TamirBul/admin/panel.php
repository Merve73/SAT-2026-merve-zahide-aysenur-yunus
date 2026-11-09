<?php
/*
SAYFA: Yönetim paneli
DOSYA: admin/panel.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Sayaçlar: müşteri, aktif tamirci, onay bekleyen usta, talep, teklif, okunmamış mesaj
- Onay bekleyen tamirci başvuruları: Onayla ve Reddet düğmeleri
- Son 5 talep ve son 5 mesaj
*/

// Sekme başlığı.
$sayfa_basligi = "Yönetim";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// HARİTA: Yönetim paneli sayaçları
// Sayaç kutuları: yazi => sayı. Döngü ile ekrana yazılır.
$sayaclar = array(
    "Toplam Müşteri" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'musteri'"),
    "Aktif Tamirci" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 1"),
    "Onay Bekleyen Usta" => sayi_getir("SELECT COUNT(*) FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 0"),
    "Talep" => sayi_getir("SELECT COUNT(*) FROM talepler"),
    "Verilen Teklif" => sayi_getir("SELECT COUNT(*) FROM teklifler"),
    "Okunmamış Mesaj" => sayi_getir("SELECT COUNT(*) FROM mesajlar WHERE okundu = 0")
);
// Onay bekleyen tamirciler, ilk başvuran önce.
$basvurular = satirlar("SELECT * FROM kullanicilar WHERE rol = 'tamirci' AND onayli = 0 ORDER BY id");
// Son 5 talep ve müşteri adı.
$talepler = satirlar("SELECT talepler.*, kullanicilar.ad, kullanicilar.soyad FROM talepler INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id ORDER BY talepler.id DESC LIMIT 5");
// Son 5 iletişim mesajı.
$mesajlar = satirlar("SELECT * FROM mesajlar ORDER BY id DESC LIMIT 5");

// Üst menü.
include "../ortak/baslik.php";
// Yönetim menüsü.
include "menu.php";
?>

<!-- Panel bölümü. -->
<section class="dashboard-section">
    <!-- Kapsayıcı. -->
    <div class="container">
        <!-- Başlık alanı. -->
        <div class="dashboard-header">
            <!-- Başlık. -->
            <h2>Sistem Yönetici Paneli</h2>
            <!-- Açıklama. -->
            <p>Bekleyen tamirci başvurularını onaylayın ve sistemi denetleyin.</p>
        </div>
        <!-- Sayaç kutuları. -->
        <div class="stats-cards-grid">
            <!-- Her sayaç bir kutu. -->
            <?php foreach ($sayaclar as $yazi => $sayi) { ?>
                <!-- Kutu: sayı ve yazı. -->
                <div class="stat-card"><span class="stat-number"><?php echo $sayi; ?></span><span class="stat-label"><?php echo $yazi; ?></span></div>
            <?php } ?>
        </div>

        <!-- Bölüm başlığı. -->
        <h3 class="section-subtitle">Onay Bekleyen Tamirci Başvuruları</h3>
        <!-- HARİTA: Tamirci başvurusu onaylama / reddetme (düğmeler) -->
        <!-- Başvuru listesi. -->
        <div class="requests-list">
            <!-- Her başvuru bir kart. -->
            <?php foreach ($basvurular as $usta) { ?>
                <!-- Kart. -->
                <div class="request-item">
                    <!-- Üst satır. -->
                    <div class="request-header">
                        <!-- İşletme ve yetkili. -->
                        <span class="request-title"><?php echo temiz_yaz($usta["isletme_adi"] . " (" . $usta["ad"] . " " . $usta["soyad"] . ")"); ?></span>
                        <!-- Etiket. -->
                        <span class="badge badge-warning">Onay Bekliyor</span>
                    </div>
                    <!-- E-posta, telefon, konum, tarih. -->
                    <div class="request-meta"><?php echo temiz_yaz($usta["eposta"] . " | " . $usta["telefon"] . " | " . $usta["sehir"] . " / " . $usta["ilce"]); ?> | <?php echo tarih_yaz($usta["kayit_tarihi"]); ?></div>
                    <!-- Uzmanlık ve adres. -->
                    <div class="request-desc"><strong>Uzmanlık:</strong> <?php echo temiz_yaz(yetkinlik_metni($usta["id"])); ?><br><strong>Adres:</strong> <?php echo temiz_yaz($usta["adres"]); ?></div>
                    <!-- Düğmeler. -->
                    <div class="offer-actions">
                        <!-- Onayla formu. kullanici-onay.php kaydeder. -->
                        <form method="post" action="kullanici-onay.php">
                            <!-- Tamircinin numarası. -->
                            <input type="hidden" name="id" value="<?php echo (int) $usta["id"]; ?>">
                            <!-- Yapılacak iş. -->
                            <input type="hidden" name="islem" value="onayla">
                            <!-- İşlemden sonra bu sayfaya dön. -->
                            <input type="hidden" name="donus" value="panel">
                            <!-- Düğme. -->
                            <button class="btn btn-sm btn-secondary" type="submit">Başvuruyu Onayla</button>
                        </form>
                        <!-- Reddet formu. confirm: emin mısınız kutusu. -->
                        <form method="post" action="kullanici-onay.php" onsubmit="return confirm('Başvuru reddedilsin mi?');">
                            <!-- Tamircinin numarası. -->
                            <input type="hidden" name="id" value="<?php echo (int) $usta["id"]; ?>">
                            <!-- Yapılacak iş. -->
                            <input type="hidden" name="islem" value="reddet">
                            <!-- İşlemden sonra bu sayfaya dön. -->
                            <input type="hidden" name="donus" value="panel">
                            <!-- Düğme. -->
                            <button class="btn btn-sm btn-danger" type="submit">Reddet</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
            <!-- Başvuru yoksa bilgi. -->
            <?php if (count($basvurular) == 0) { ?>
                <!-- Bilgi. -->
                <p class="bos-liste">Onay bekleyen başvuru yok.</p>
            <?php } ?>
        </div>

        <!-- Bölüm başlığı. -->
        <h3 class="section-subtitle">Son Talepler</h3>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kod</th><th>Müşteri</th><th>Cihaz</th><th>Durum</th></tr>
                <!-- Her talep bir satır. -->
                <?php foreach ($talepler as $talep) { ?>
                    <!-- Kod, müşteri, cihaz, durum. -->
                    <tr><td><?php echo temiz_yaz($talep["takip_kodu"]); ?></td><td><?php echo temiz_yaz($talep["ad"] . " " . $talep["soyad"]); ?></td><td><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"]); ?></td><td><span class="<?php echo durum_rozeti($talep["durum"]); ?>"><?php echo durum_adi($talep["durum"]); ?></span></td></tr>
                <?php } ?>
            </table>
        </div>

        <!-- Bölüm başlığı. -->
        <h3 class="section-subtitle">Son İletişim Mesajları</h3>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kim</th><th>Konu</th></tr>
                <!-- Her mesaj bir satır. -->
                <?php foreach ($mesajlar as $mesaj) { ?>
                    <!-- Ad ve konu. -->
                    <tr><td><?php echo temiz_yaz($mesaj["ad"]); ?></td><td><?php echo temiz_yaz($mesaj["konu"]); ?></td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
