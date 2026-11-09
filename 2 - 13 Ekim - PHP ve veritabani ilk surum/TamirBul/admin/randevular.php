<?php
/*
SAYFA: Randevular
DOSYA: admin/randevular.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Tum randevular
- Tamirci adi, musteri adi, tarih, saat, durum
- Bu sayfa sadece okur. Durumu tamirci kendi panelinden degistirir.
*/

$kok = "../";
$sayfa_basligi = "Randevular";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$randevular = false;
if ($veritabani_hazir) {
    $sorgu = "SELECT randevular.*, talepler.takip_kodu, talepler.marka, talepler.model,
                     musteriler.ad AS musteri_ad, musteriler.soyad AS musteri_soyad,
                     tamirciler.ad AS tamirci_ad, tamirciler.soyad AS tamirci_soyad
              FROM randevular
              INNER JOIN talepler ON talepler.id = randevular.talep_id
              INNER JOIN kullanicilar AS musteriler ON musteriler.id = talepler.musteri_id
              INNER JOIN kullanicilar AS tamirciler ON tamirciler.id = randevular.tamirci_id
              ORDER BY randevular.randevu_tarihi DESC";
    $randevular = mysqli_query($baglanti, $sorgu);
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Randevular</h1>
        <p class="giris-metni">Tamircilerin kurduğu gün ve saatler. Aynı kullanıcı tablosu iki kez kullanıldığı için birine musteriler, diğerine tamirciler dedik.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Müşteri</th>
                    <th>Tamirci</th>
                    <th>Cihaz</th>
                    <th>Zaman</th>
                    <th>Durum</th>
                </tr>
                <?php if ($randevular && mysqli_num_rows($randevular) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($randevular)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td><?php echo temiz_yaz($satir["musteri_ad"] . " " . $satir["musteri_soyad"]); ?></td>
                            <td><?php echo temiz_yaz($satir["tamirci_ad"] . " " . $satir["tamirci_soyad"]); ?></td>
                            <td><?php echo temiz_yaz($satir["marka"] . " " . $satir["model"]); ?></td>
                            <td><?php echo temiz_yaz($satir["randevu_tarihi"] . " " . $satir["randevu_saati"]); ?></td>
                            <td><?php echo temiz_yaz(randevu_durum_adi($satir["durum"])); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="6">Randevu yok.</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
