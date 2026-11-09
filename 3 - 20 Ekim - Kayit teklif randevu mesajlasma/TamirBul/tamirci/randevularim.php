<?php
/*
SAYFA: Randevularım
DOSYA: tamirci/randevularim.php
KİM: Kişi 2

BU SAYFADA BUNLAR OLACAK:
- Bu tamircinin randevulari: kod, müşteri, telefon, cihaz, zaman, not, durum
- Bekleyen randevuda Tamamlandi ve İptal düğmeleri (randevu-durum.php)
*/

// Sekme başlığı.
$sayfa_basligi = "Randevularım";

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// HARİTA: Randevu listesi (üç tablo JOIN)
// Üç tablo birlikte: randevu, onun talebi, talebin müşterisi.
$randevular = satirlar("SELECT randevular.*, talepler.marka, talepler.model, talepler.ariza, talepler.takip_kodu, kullanicilar.ad, kullanicilar.soyad, kullanicilar.telefon FROM randevular INNER JOIN talepler ON talepler.id = randevular.talep_id INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id WHERE randevular.tamirci_id = $benim_id ORDER BY randevular.randevu_tarihi");

// Üst menü.
include "../ortak/baslik.php";
// Tamirci menüsü.
include "menu.php";
?>

<!-- Sayfa bölümü. -->
<section class="sayfa">
    <!-- Kapsayıcı. -->
    <div class="kapsayici">
        <!-- Başlık. -->
        <h1>Randevularım</h1>
        <!-- Açıklama. -->
        <p class="giris-metni">İşi bitirince "Tamamlandı" deyin. Müşteri sonra puan verir.</p>
        <!-- Tablo kutusu. -->
        <div class="tablo-kutu">
            <!-- Tablo. -->
            <table>
                <!-- Başlık satırı. -->
                <tr><th>Kod</th><th>Müşteri</th><th>Cihaz</th><th>Zaman</th><th>Durum</th><th></th></tr>
                <!-- Her randevu bir satır. -->
                <?php foreach ($randevular as $randevu) { ?>
                    <!-- Satır. -->
                    <tr>
                        <!-- Kod. -->
                        <td><?php echo temiz_yaz($randevu["takip_kodu"]); ?></td>
                        <!-- Müşteri, telefon, sohbet linki. -->
                        <td><?php echo temiz_yaz($randevu["ad"] . " " . $randevu["soyad"]); ?><br><?php echo temiz_yaz($randevu["telefon"]); ?><br><a href="../sohbet.php?talep=<?php echo (int) $randevu["talep_id"]; ?>">Mesajlar</a></td>
                        <!-- Cihaz ve arıza. -->
                        <td><?php echo temiz_yaz($randevu["marka"] . " " . $randevu["model"]); ?><br><?php echo temiz_yaz($randevu["ariza"]); ?></td>
                        <!-- Tarih, saat, not. -->
                        <td><?php echo tarih_yaz($randevu["randevu_tarihi"]) . " " . temiz_yaz($randevu["randevu_saati"]); ?><br><?php echo temiz_yaz($randevu["not_metni"]); ?></td>
                        <!-- Durum. -->
                        <td><?php echo randevu_durum_adi($randevu["durum"]); ?></td>
                        <!-- Bekleyen randevuda iki düğme. -->
                        <td>
                            <!-- Sadece bekleyen randevu. -->
                            <?php if ($randevu["durum"] == "bekliyor") { ?>
                                <!-- Tamamlandi formu. -->
                                <form method="post" action="randevu-durum.php">
                                    <!-- Randevu numarası. -->
                                    <input type="hidden" name="id" value="<?php echo (int) $randevu["id"]; ?>">
                                    <!-- Yeni durum. -->
                                    <input type="hidden" name="durum" value="tamamlandi">
                                    <!-- Düğme. -->
                                    <button class="btn btn-sm btn-success" type="submit">Tamamlandı</button>
                                </form>
                                <!-- İptal formu. -->
                                <form method="post" action="randevu-durum.php">
                                    <!-- Randevu numarası. -->
                                    <input type="hidden" name="id" value="<?php echo (int) $randevu["id"]; ?>">
                                    <!-- Yeni durum. -->
                                    <input type="hidden" name="durum" value="iptal">
                                    <!-- Düğme. -->
                                    <button class="btn btn-sm btn-danger" type="submit">İptal</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <!-- Randevu yoksa bilgi satırı. -->
                <?php if (count($randevular) == 0) { ?>
                    <!-- Bilgi. -->
                    <tr><td colspan="6">Randevu yok.</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php
// Sayfa altı.
include "../ortak/altlik.php";
?>
