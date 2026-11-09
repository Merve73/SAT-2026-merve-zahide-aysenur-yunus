<?php
/*
SAYFA: Kullanicilar
DOSYA: admin/kullanicilar.php
KIM: Kisi 4

BU SAYFADA BUNLAR OLACAK:
- Tum uyelerin listesi: ad, e-posta, rol, sehir, uzmanlik, onay
- Onaysiz tamirci icin Onayla butonu
- Buton kullanici-onay.php dosyasina gider

SIRADAKI IS:
- Kullanici silme ekleyebilirsiniz.
- Silmeden once bu kullanicinin talebi var mi diye bakin.
- Yonetici kendini silmesin: id, oturumdaki kullanici_id ile ayniysa butonu gostermeyin.
*/

$kok = "../";
$sayfa_basligi = "Kullanıcılar";

include "../ortak/baglanti.php";
include "../ortak/guvenlik.php";
sadece_admin();

$kisiler = false;
if ($veritabani_hazir) {
    $kisiler = mysqli_query($baglanti, "SELECT * FROM kullanicilar ORDER BY id DESC");
}

include "../ortak/baslik.php";
include "menu.php";
?>

<section class="sayfa">
    <div class="kapsayici">
        <h1>Kullanıcılar</h1>
        <p class="giris-metni">Tamirci onaylanınca sitedeki tamirciler listesinde görünür ve talep alabilir.</p>

        <div class="tablo-kutu">
            <table>
                <tr>
                    <th>Ad</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>Şehir</th>
                    <th>Uzmanlık</th>
                    <th>Onay</th>
                </tr>
                <?php if ($kisiler) { ?>
                    <?php while ($satir = mysqli_fetch_assoc($kisiler)) { ?>
                        <tr>
                            <td><?php echo temiz_yaz($satir["ad"] . " " . $satir["soyad"]); ?></td>
                            <td><?php echo temiz_yaz($satir["eposta"]); ?></td>
                            <td><?php echo temiz_yaz($satir["rol"]); ?></td>
                            <td><?php echo temiz_yaz($satir["sehir"]); ?></td>
                            <td><?php echo temiz_yaz(cihaz_adi($satir["uzmanlik"])); ?></td>
                            <td>
                                <?php if ($satir["rol"] == "tamirci" && (int) $satir["onayli"] == 0) { ?>
                                    <form method="post" action="kullanici-onay.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $satir["id"]; ?>">
                                        <button class="buton buton-kucuk" type="submit">Onayla</button>
                                    </form>
                                <?php } else if ((int) $satir["onayli"] == 1) { ?>
                                    Onaylı
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
</section>

<?php include "../ortak/altlik.php"; ?>
