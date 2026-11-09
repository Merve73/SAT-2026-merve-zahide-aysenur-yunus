<?php
/*
SAYFA: Randevular
DOSYA: admin/randevular.php
KİM: Kişi 4

BU SAYFADA BUNLAR OLACAK:
- Bütün randevular: kod, müşteri, tamirci, cihaz, zaman, durum
- Sadece okur. Durumu tamirci kendi panelinden değiştirir.
*/

// Sekme başlığı.
$sayfa_basligi = "Randevular";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// Randevu + talep + müşteri + tamirci. kullanicilar tablosu iki kez: müşteriler ve tamirciler.
$randevular = satirlar("SELECT randevular.*, talepler.takip_kodu, talepler.marka, talepler.model, musteriler.ad AS musteri_ad, musteriler.soyad AS musteri_soyad, tamirciler.isletme_adi FROM randevular INNER JOIN talepler ON talepler.id = randevular.talep_id INNER JOIN kullanicilar AS musteriler ON musteriler.id = talepler.musteri_id INNER JOIN kullanicilar AS tamirciler ON tamirciler.id = randevular.tamirci_id ORDER BY randevular.randevu_tarihi DESC");

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
        <h1>Randevular</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">Tamircilerin kurduğu gün ve saatler.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kod</th><th>Müşteri</th><th>Tamirci</th><th>Cihaz</th><th>Zaman</th><th>Durum</th></tr>
                <!-- Her randevu bir satır. -->
                <?php foreach ($randevular as $randevu) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Kod. -->
                        <td><?php echo temiz_yaz($randevu["takip_kodu"]); ?></td>
                        <!-- Müşteri. -->
                        <td><?php echo temiz_yaz($randevu["musteri_ad"] . " " . $randevu["musteri_soyad"]); ?></td>
                        <!-- Tamirci. -->
                        <td><?php echo temiz_yaz($randevu["isletme_adi"]); ?></td>
                        <!-- Cihaz. -->
                        <td><?php echo temiz_yaz($randevu["marka"] . " " . $randevu["model"]); ?></td>
                        <!-- Tarih ve saat. -->
                        <td><?php echo tarih_yaz($randevu["randevu_tarihi"]) . " " . temiz_yaz($randevu["randevu_saati"]); ?></td>
                        <!-- Durum. -->
                        <td><?php echo randevu_durum_adi($randevu["durum"]); ?></td>
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
