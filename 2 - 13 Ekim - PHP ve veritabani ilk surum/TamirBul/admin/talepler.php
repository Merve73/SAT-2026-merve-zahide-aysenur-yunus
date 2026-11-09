<?php
/*
SAYFA: Talepler
DOSYA: admin/talepler.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Tum tamir talepleri
- Musteri adi (JOIN ile)
- Durum degistirme formu. Kayit talep-durum.php icinde.
*/

$kok = "../";
$sayfa_basligi = "Talepler";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$talepler = false;
if ($veritabani_hazir) {
    $sorgu = "SELECT talepler.*, kullanicilar.ad, kullanicilar.soyad
              FROM talepler
              INNER JOIN kullanicilar ON kullanicilar.id = talepler.musteri_id
              ORDER BY talepler.id DESC";
    $talepler = mysqli_query($baglanti, $sorgu);
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Talepler</h1>
        <p class="giris-metni">Bütün müşteri kayıtları. Durumu buradan elle de değiştirebilirsiniz.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Kod</th>
                    <th>Müşteri</th>
                    <th>Cihaz</th>
                    <th>Arıza</th>
                    <th>Durum</th>
                </tr>
                <?php if ($talepler) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($talepler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["takip_kodu"]); ?></td>
                            <td><?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?></td>
                            <td><?php echo temiz_yaz($satir["marka"] . " " . $satir["model"]); ?></td>
                            <td><?php echo temiz_yaz($satir["ariza"]); ?></td>
                            <td>
                                <form method="post" action="talep-durum.php">
                                    <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                    <select name="durum">
                                        <option value="beklemede" <?php if ($satir["durum"] == "beklemede") { echo "selected"; } ?>>Beklemede</option>
                                        <option value="tamirci_atandi" <?php if ($satir["durum"] == "tamirci_atandi") { echo "selected"; } ?>>Tamirci atandı</option>
                                        <option value="randevu_kuruldu" <?php if ($satir["durum"] == "randevu_kuruldu") { echo "selected"; } ?>>Randevu kuruldu</option>
                                        <option value="tamirde" <?php if ($satir["durum"] == "tamirde") { echo "selected"; } ?>>Tamirde</option>
                                        <option value="tamamlandi" <?php if ($satir["durum"] == "tamamlandi") { echo "selected"; } ?>>Tamamlandı</option>
                                        <option value="iptal" <?php if ($satir["durum"] == "iptal") { echo "selected"; } ?>>İptal</option>
                                    </select>
                                    <button class="buton buton-kucuk" type="submit">Kaydet</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
