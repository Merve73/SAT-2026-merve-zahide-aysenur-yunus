<?php
/*
SAYFA: Randevularim
DOSYA: tamirci/randevularim.php
KIM: Kisi 2

BU SAYFADA BUNLAR OLACAK:
- Sadece bu tamircinin randevulari
- Musteri adi, cihaz, tarih, saat, not, durum
- Durumu tamamlandi veya iptal yapacak kucuk form

SIRADAKI IS:
- Iptal edince talebin durumunu tekrar beklemede yapmak isterseniz
  randevu-durum.php icindeki UPDATE satirinin yanina ikinci bir UPDATE ekleyin.
  Su an iptal sadece randevuyu kapatir, talebi geri acmaz.
*/

$kok = "../";
$sayfa_basligi = "Randevularım";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_tamirci();

$benim_id = (int) $_SESSION["kullanici_id"];
$randevular = false;

if ($veritabani_hazir) {
    // Uc tablo: randevu, onun talebi, talebin musterisi.
    $sorgu = "SELECT randevular.*, talepler.marka, talepler.model, talepler.ariza, talepler.takip_kodu,
                     kullanicilar.ad, kullanicilar.soyad, kullanicilar.telefon
              FROM randevular
              INNER JOIN talepler ON talepler.id = randevular.talep_id
              INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id
              WHERE randevular.tamirci_id = $benim_id
              ORDER BY randevular.randevu_tarihi";
    $randevular = mysqli_query($baglanti, $sorgu);
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Randevularım</h1>
        <p class="giris-metni">Kurduğunuz randevular. İşi bitirince durumu tamamlandı yapın.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Müşteri</th>
                    <th>Cihaz</th>
                    <th>Zaman</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
                <?php if ($randevular && mysqli_num_rows($randevular) > 0) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($randevular)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td>
                                <?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?><br>
                                <?php echo temiz_yaz($satir["telefon"]); ?>
                            </td>
                            <td>
                                <?php echo temiz_yaz($satir["marka"] . " " . $satir["model"]); ?><br>
                                <?php echo temiz_yaz($satir["ariza"]); ?>
                            </td>
                            <td>
                                <?php echo temiz_yaz($satir["randevu_tarihi"] . " " . $satir["randevu_saati"]); ?><br>
                                <?php echo temiz_yaz($satir["not_metni"]); ?>
                            </td>
                            <td><?php echo temiz_yaz(randevu_durum_adi($satir["durum"])); ?></td>
                            <td>
                                <?php if ($satir["durum"] == "bekliyor") { ?>
                                    <form method="post" action="randevu-durum.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                        <input type="hidden" name="durum" value="tamamlandi">
                                        <button class="buton buton-kucuk" type="submit">Tamamlandı</button>
                                    </form>
                                    <form method="post" action="randevu-durum.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                        <input type="hidden" name="durum" value="iptal">
                                        <button class="buton buton-kucuk tehlike" type="submit">İptal</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6">Randevu yok.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
