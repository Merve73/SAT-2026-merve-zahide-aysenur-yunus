<?php
/*
SAYFA: Talepler
DOSYA: admin/talepler.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Bütün talepler: kod, müşteri, cihaz, konum, teklif sayısı, seçilen tamirci
- Durum değiştirme kutusu (kayıt talep-durum.php)
*/

// Sekme başlığı.
$sayfa_basligi = "Talepler";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// HARİTA: Aynı tabloyu iki kez kullanma (AS musteriler / AS tamirciler)
// kullanicilar tablosu iki kez gerekir: müşteri ve tamirci. Birine musteriler, diğerine tamirciler dedik.
// LEFT JOIN: tamirci seçilmediyse de talep listede kalır. Alt sorgu teklif sayısını sayar.
$talepler = satirlar("SELECT talepler.*, kategoriler.ad AS kategori_adi, musteriler.ad, musteriler.soyad, tamirciler.isletme_adi AS tamirci_isletme, (SELECT COUNT(*) FROM teklifler WHERE teklifler.talep_id = talepler.id) AS teklif_sayisi FROM talepler INNER JOIN kategoriler ON kategoriler.id = talepler.kategori_id INNER JOIN kullanicilar AS musteriler ON musteriler.id = talepler.musteri_id LEFT JOIN kullanicilar AS tamirciler ON tamirciler.id = talepler.tamirci_id ORDER BY talepler.id DESC");
// Durum kutusundaki seçenekler. Türkçe adlarını durum_adi() verir.
$durumlar = array("beklemede", "tamirci_atandi", "randevu_kuruldu", "tamirde", "tamamlandi", "iptal");

// Üst menü.
include "../ortak/baslik.php";
// Yönetim menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Talepler</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Bütün müşteri kayıtları. Durumu buradan elle de değiştirebilirsiniz.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kod</th><th>Müşteri</th><th>Cihaz</th><th>Konum</th><th>Teklif</th><th>Tamirci</th><th>Durum</th></tr>
                <!-- Her talep bir satır. -->
                <?php foreach ($talepler as $talep) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Kod. -->
                        <td><?php echo temiz_yaz($talep["takip_kodu"]); ?></td>
                        <!-- Müşteri. -->
                        <td><?php echo temiz_yaz($talep["ad"] . " " . $talep["soyad"]); ?></td>
                        <!-- Cihaz ve kategori. -->
                        <td><?php echo temiz_yaz($talep["marka"] . " " . $talep["model"]); ?><br><small><?php echo temiz_yaz($talep["kategori_adi"]); ?></small></td>
                        <!-- Konum. -->
                        <td><?php echo temiz_yaz($talep["sehir"] . " / " . $talep["ilce"]); ?></td>
                        <!-- Teklif sayısı. -->
                        <td><?php echo (int) $talep["teklif_sayisi"]; ?></td>
                        <!-- Seçilen tamirci. (string): NULL gelirse boş yazı. -->
                        <td><?php echo temiz_yaz((string) $talep["tamirci_isletme"]); ?></td>
                        <!-- Durum değiştirme. -->
                        <td>
                            <!-- Form talep-durum.php dosyasına gider. -->
                            <form method="post" action="talep-durum.php">
                                <!-- Talep numarası. -->
                                <input type="hidden" name="id" value="<?php echo (int) $talep["id"]; ?>">
                                <!-- Durum kutusu. -->
                                <select name="durum">
                                    <!-- Her durum bir seçenek. Şu anki seçili gelir. -->
                                    <?php foreach ($durumlar as $durum) { ?>
                                        <!-- Seçenek. -->
                                        <option value="<?php echo $durum; ?>" <?php if ($talep["durum"] == $durum) { echo "selected"; } ?>><?php echo durum_adi($durum); ?></option>
                                    <?php } ?>
                                </select>
                                <!-- Kaydet. -->
                                <button class="btn btn-sm btn-primary" type="submit">Kaydet</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
